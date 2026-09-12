<?php

namespace App\Services\BackOffice;

use App\Helpers\StoryHelper;
use App\Http\Requests\StoryRequest;
use App\Models\Story;
use App\Services\BackOffice\AiBrainService;
use App\Services\BackOffice\AiPromptService;
use App\Services\BackOffice\AudienceService;
use App\Services\BackOffice\GenreService;
use App\Services\BackOffice\LanguageService;
use App\Services\BackOffice\StoryTypeService;
use App\Services\BackOffice\HuggingFaceApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoryService
{
    protected AiBrainService $aiBrainService;
    protected AiPromptService $aiPromptService;
    protected AudienceService $audienceService;
    protected GenreService $genreService;
    protected StoryTypeService $storyTypeService;
    protected HuggingFaceApiService $huggingFaceApiService;
    protected LanguageService $languageService;

    public function __construct(AiBrainService $aiBrainService, AiPromptService $aiPromptService, AudienceService $audienceService, GenreService $genreService, StoryTypeService $storyTypeService, HuggingFaceApiService $huggingFaceApiService, LanguageService $languageService)
    {
        $this->aiBrainService   = $aiBrainService;
        $this->aiPromptService  = $aiPromptService;
        $this->audienceService  = $audienceService;
        $this->genreService     = $genreService;
        $this->storyTypeService = $storyTypeService;
        $this->huggingFaceApiService = $huggingFaceApiService;
        $this->languageService  = $languageService;
    }

    public function new(): Story
    {
        return new Story();
    }

    public function find(string $slug): Story
    {
        return Story::with([
            'createdBy',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('slug', $slug)->firstOrFail();
    }

    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = Story::query();

        if ($request->filled('created_by_id')) {
            $query->where('created_by_id', $request->input('created_by_id'));
        }

        if ($request->filled('date')) {
            $date = $request->input('date');
            $date = is_string($date) ? new \DateTime($date) : $date;
            $query->whereDate('created_at', '<=', $date);
        }

        if ($request->filled('search')) {
            $search     = $request->input('search');
            $likeSearch = "%{$search}%";

            $query->whereAny([
                'name',
            ], 'like', $likeSearch);
        }

        if ($request->filled('genre_id')) {
            $query->whereHas(
                'genres',
                fn($query) => $query->where('genres.id', $request->input('genre_id'))
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        return $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());
    }

    public function save(StoryRequest $request, Story $story): array
    {
        $isNew       = empty($story->id);
        $statusEvent = $isNew ? "save" : "update";

        try {
            $aiPrompt = $this->aiPromptService->findByCode("PlotGenerator");
            $aiBrain  = $this->aiBrainService->findById($request->input("ai_brain_id"));

            $language  = $this->languageService->findByIdsOrEnglish($request->input("language_id"));
            $audience  = $this->audienceService->findById($request->input("audience_id"));
            $storyType = $this->audienceService->findById($request->input("story_type_id"));
            $genres    = $this->genreService->findByIdsOrRandom($request->input("genre_ids"));

            $genrePromptInstruction = '';
            $is18Plus               = $request->boolean("is_18_plus", false) ? "True" : "False";
            $enableMatureContent    = $request->boolean("enable_mature_content", false) ? "True" : "False";

            $additionalInformation = $request->input("additional_information", "Auto");
            $storyContinuity       = $request->input("story_continuity", StoryHelper::CONTINUITY_STANDALONE);

            foreach ($genres as $genre) {

                $gInstruction = trim($genre->prompt_instruction);

                if (! str_ends_with($gInstruction, '.')) {
                    $gInstruction .= '.';
                }

                if ($genrePromptInstruction !== '') {
                    $genrePromptInstruction .= ' ';
                }

                $genrePromptInstruction .= $gInstruction;
            }

            $receivedInputs = [
                "is_18_plus" => $is18Plus,
                "enable_mature_content" => $enableMatureContent,
                "language" => $language?->name,
                "story_continuity" => $storyContinuity,
                "additional_information" => $additionalInformation,
                "genre_prompt_instruction" => $genrePromptInstruction,
                "audience_instruction" => $audience->prompt_instruction,
                "story_type_instruction" => $storyType->prompt_instruction,
            ];

            $prompt = str_replace(
                [
                    '{{is_18_plus}}',
                    '{{enable_mature_content}}',
                    '{{language}}',
                    '{{story_continuity}}',
                    '{{additional_information}}',
                    '{{genre_instructions}}',
                    '{{audience_instruction}}',
                    '{{story_type_instruction}}',
                ],
                [
                    $is18Plus,
                    $enableMatureContent,
                    $language?->name,
                    $storyContinuity,
                    $additionalInformation,
                    $genrePromptInstruction,
                    $audience->prompt_instruction,
                    $storyType->prompt_instruction,
                ],
                $aiPrompt->prompt
            );

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            Log::info("Story AI Response", ["apiResponse" => $apiResponse]);

            $story = DB::transaction(function () use ($request, $apiResponse, $receivedInputs, $prompt, $story, $isNew) {
                $apiResponseFormated = $this->extractStoryResponse($apiResponse);

                $story->title     = $apiResponseFormated['title'];
                $story->sub_title = $apiResponseFormated['subtitle'];
                $story->plot      = $apiResponseFormated['plot'];

                $story->received_inputs      = $receivedInputs;
                $story->ai_prompt      = $prompt;

                $story->audience_id   = $request->input("audience_id");
                $story->story_type_id = $request->input("story_type_id");
                $story->language_id   = $request->input("language_id");
                $story->status        = StoryHelper::STATUS_ONGOING;

                if ($isNew) {
                    $story->datetime      = now();
                    $story->created_by_id = Auth::id();
                }

                $story->save();

                if ($request->has('genre_ids')) {
                    $story->genres()->sync((array) $request->input('genre_ids', []));
                }

                return $story;
            });

            return [
                'status'  => 'success',
                'message' => $isNew
                    ? 'Story created successfully.'
                    : 'Story updated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error("Failed to {$statusEvent} story.", [
                "exception" => $exception->getMessage(),
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to save story. Please try again.',
            ];
        }
    }

    public function delete(Story $story): array
    {

        try {

            DB::transaction(function () use ($story) {
                $story->delete();
            });

            return [
                'status'  => 'success',
                'message' => 'Story deleted successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Story delete failed.', [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to delete story. Please try again.',
            ];
        }
    }

    private function extractStoryResponse($apiResponse): array
    {
        $content = data_get(
            $apiResponse,
            'choices.0.message.content'
        );

        if (! $content) {
            throw new Exception("Invalid AI response structure.");
        }

        $content = trim($content);

        $content = str_replace(
            [
                '```json',
                '```',
            ],
            '',
            $content
        );

        $decoded = json_decode(
            trim($content),
            true
        );

        if (! is_array($decoded)) {
            throw new Exception("AI response is not valid JSON.");
        }

        return [
            'title'    => $decoded['story_title'] ?? null,
            'subtitle' => $decoded['story_subtitle'] ?? null,
            'plot'     => $decoded['story_plot'] ?? null,
        ];
    }
}
