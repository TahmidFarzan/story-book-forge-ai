<?php

namespace App\Services\BackOffice;

use App\Helpers\AiPromptGeneratorHelper;
use App\Helpers\StoryBookHelper;
use App\Http\Requests\StoryBookFoundationRequest;
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
use Illuminate\Support\Str;

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
            'language',
            'storyBookType',
            'audience',
            'genres',

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
                'title',
                'sub_title',
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

    public function generateFoundation(StoryBookFoundationRequest $request, StoryBook $storyBook): array
    {
        $isNew       = empty($storyBook->id);
        $statusEvent = $isNew ? "save" : "update";

        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_FOUNDATION_GENERATOR));
            $aiBrain  = $this->aiBrainService->findById($request->input("ai_brain_id"));

            $receivedInputs = $this->receivedInputsFormatter($request->input("language_id"), $request->input("audience_id"), $request->input("story_book_type_id"), $request->input("genre_ids"), $request->input("additional_information", "Auto"));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $receivedInputs);

            // json_encode($storyBook->plot, JSON_PRETTY_PRINT)

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($request, $apiResponse, $storyBook, $isNew) {
                $storyObject = $this->extractStoryPlotFromResponse($apiResponse);

                $storyBook->title     = $storyObject->title;
                $storyBook->sub_title = $storyObject->subtitle;
                $storyBook->plot      = $storyObject->plot;

                $storyBook->audience_id   = $request->input("audience_id");
                $storyBook->story_book_type_id = $request->input("story_book_type_id");
                $storyBook->language_id   = $request->input("language_id");

                $storyBook->additional_information   = $request->input("additional_information");

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
                "story_book" => $storyBook,
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
                "story_book" => null,
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

    private function extractStoryPlotFromResponse($apiResponse): object
    {
        $content = data_get(
            $apiResponse,
            'choices.0.message.content'
        );

        if (! is_string($content) || trim($content) === '') {
            throw new Exception('Invalid AI response structure.');
        }

        $content = trim($content);

        $content = preg_replace(
            '/^```(?:json)?\s*|\s*```$/i',
            '',
            $content
        );

        $content = trim($content);

        $decoded = json_decode(
            $content,
            true
        );

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            ! is_array($decoded)
        ) {
            throw new Exception(
                'AI response is not valid JSON: ' . json_last_error_msg()
            );
        }

        return (object) [
            'title' => $decoded['story_book_title'] ?? null,
            'subtitle' => $decoded['story_book_subtitle'] ?? null,
            'plot' => $decoded['story_book_plot'] ?? null,
        ];
    }

    private function receivedInputsFormatter(int|string $languageId, int|string $audienceId, int|string $storyBookTypeId, array $genreIds, string $additionalInformation): array
    {
        $receivedInputs = array();

        $language  = $this->languageService->findByIdsOrEnglish($languageId);
        $audience  = $this->audienceService->findById($audienceId);
        $storyBookType = $this->audienceService->findById($storyBookTypeId);
        $genres    = $this->genreService->findByIdsOrRandom($genreIds);

        $genrePromptInstruction = '';
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
            "language" => $language?->name,
            "additional_information" => $additionalInformation,
            "genre_prompt_instruction" => $genrePromptInstruction,
            "audience_instruction" => $audience->prompt_instruction,
            "story_book_type_instruction" => $storyBookType->prompt_instruction,
        ];

        return $receivedInputs;
    }
}
