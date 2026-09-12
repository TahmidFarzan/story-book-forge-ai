<?php

namespace App\Services\BackOffice;

use App\Helpers\StoryBookHelper;
use App\Http\Requests\StoryBookRequest;
use App\Models\StoryBook;
use App\Services\BackOffice\AiBrainService;
use App\Services\BackOffice\AiPromptService;
use App\Services\BackOffice\AudienceService;
use App\Services\BackOffice\GenreService;
use App\Services\BackOffice\LanguageService;
use App\Services\BackOffice\StoryBookTypeService;
use App\Services\BackOffice\HuggingFaceApiService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoryBookService
{
    protected AiBrainService $aiBrainService;
    protected AiPromptService $aiPromptService;
    protected AudienceService $audienceService;
    protected GenreService $genreService;
    protected StoryBookTypeService $storyBookTypeService;
    protected HuggingFaceApiService $huggingFaceApiService;
    protected LanguageService $languageService;

    public function __construct(AiBrainService $aiBrainService, AiPromptService $aiPromptService, AudienceService $audienceService, GenreService $genreService, StoryBookTypeService $storyBookTypeService, HuggingFaceApiService $huggingFaceApiService, LanguageService $languageService)
    {
        $this->aiBrainService   = $aiBrainService;
        $this->aiPromptService  = $aiPromptService;
        $this->audienceService  = $audienceService;
        $this->genreService     = $genreService;
        $this->storyBookTypeService = $storyBookTypeService;
        $this->huggingFaceApiService = $huggingFaceApiService;
        $this->languageService  = $languageService;
    }

    public function new(): StoryBook
    {
        return new StoryBook();
    }

    public function find(string $slug): StoryBook
    {
        return StoryBook::with([
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

        $query = StoryBook::query();

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

    public function save(StoryBookRequest $request, StoryBook $storyBook): array
    {
        $isNew       = empty($storyBook->id);
        $statusEvent = $isNew ? "save" : "update";

        try {
            $aiPrompt = $this->aiPromptService->findByCode("PlotGenerator");
            $aiBrain  = $this->aiBrainService->findById($request->input("ai_brain_id"));

            $language  = $this->languageService->findByIdsOrEnglish($request->input("language_id"));
            $audience  = $this->audienceService->findById($request->input("audience_id"));
            $storyBookType = $this->audienceService->findById($request->input("story_book_type_id"));
            $genres    = $this->genreService->findByIdsOrRandom($request->input("genre_ids"));

            $genrePromptInstruction = '';
            $is18Plus               = $request->boolean("is_18_plus", false) ? "True" : "False";
            $enableMatureContent    = $request->boolean("enable_mature_content", false) ? "True" : "False";

            $additionalInformation = $request->input("additional_information", "Auto");
            $storyContinuity       = $request->input("story_continuity", StoryBookHelper::CONTINUITY_STANDALONE);

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
                "story_book_type_instruction" => $storyBookType->prompt_instruction,
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
                    '{{story_book_type_instruction}}',
                ],
                [
                    $is18Plus,
                    $enableMatureContent,
                    $language?->name,
                    $storyContinuity,
                    $additionalInformation,
                    $genrePromptInstruction,
                    $audience->prompt_instruction,
                    $storyBookType->prompt_instruction,
                ],
                $aiPrompt->prompt
            );

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            Log::info("Story AI Response", ["apiResponse" => $apiResponse]);

            $storyBook = DB::transaction(function () use ($request, $apiResponse, $receivedInputs, $prompt, $storyBook, $isNew) {
                $apiResponseFormated = $this->extractStoryResponse($apiResponse);

                $storyBook->title     = $apiResponseFormated['title'];
                $storyBook->sub_title = $apiResponseFormated['subtitle'];
                $storyBook->plot      = $apiResponseFormated['plot'];

                $storyBook->received_inputs      = $receivedInputs;
                $storyBook->ai_prompt      = $prompt;

                $storyBook->audience_id   = $request->input("audience_id");
                $storyBook->story_book_type_id = $request->input("story_book_type_id");
                $storyBook->language_id   = $request->input("language_id");
                $storyBook->status        = StoryBookHelper::STATUS_ONGOING;

                if ($isNew) {
                    $storyBook->datetime      = now();
                    $storyBook->created_by_id = Auth::id();
                }

                $storyBook->save();

                if ($request->has('genre_ids')) {
                    $storyBook->genres()->sync((array) $request->input('genre_ids', []));
                }

                return $storyBook;
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

    public function delete(StoryBook $storyBook): array
    {

        try {

            DB::transaction(function () use ($storyBook) {
                $storyBook->delete();
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
