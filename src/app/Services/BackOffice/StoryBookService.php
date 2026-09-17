<?php

namespace App\Services\BackOffice;

use App\Http\Requests\StoryBookCharactersRequest;
use App\Helpers\AiPromptGeneratorHelper;
use App\Helpers\StoryBookHelper;
use App\Http\Requests\StoryBookFoundationRequest;
use App\Http\Requests\StoryBookLocationsRequest;
use App\Http\Requests\StoryBookWorldVibeRequest;
use App\Http\Requests\StoryBookFactionsRequest;
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

            $requestInputs = $this->foundationRequestInputsFormatter($request->input("language_id"), $request->input("audience_id"), $request->input("story_book_type_id"), $request->input("genre_ids"), $request->input("additional_information", "Auto"));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($request, $apiResponse, $storyBook, $isNew) {
                $foundationObject = $this->extractFoundationFromResponse($apiResponse);

                $storyBook->title     = $foundationObject->title;
                $storyBook->sub_title = $foundationObject->subtitle;
                $storyBook->foundation      = $foundationObject->foundation;

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

    public function generateCharacters(StoryBookCharactersRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_CHARACTER_GENERATOR));
            $aiBrain  = $this->aiBrainService->findById($request->input("ai_brain_id"));

            $requestInputs = $this->charactersRequestInputsFormatter($storyBook, $request->input("additional_information", "Auto"));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $characterObject = $this->extractCharactersFromResponse($apiResponse);
                $storyBook->characters      = $characterObject;
                $storyBook->status        = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                "story_book" => $storyBook,
                'status'  => 'success',
                'message' =>  'Story characters generate successfully.'
            ];
        } catch (Exception $exception) {

            Log::error("Failed to generate Story characters", [
                "exception" => $exception->getMessage(),
            ]);

            return [
                "story_book" => null,
                'status'  => 'error',
                'message' => 'Failed to generate Story characters. Please try again.',
            ];
        }
    }

    public function generateWorldBible(StoryBookWorldVibeRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_WORLD_BIBLE_GENERATOR));
            $aiBrain  = $this->aiBrainService->findById($request->input("ai_brain_id"));

            $requestInputs = $this->worldBibleRequestInputsFormatter($storyBook, $request->input("additional_information", "Auto"));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $worldBibleObject = $this->extractWorldBibleFromResponse($apiResponse);
                $storyBook->world_bible      = $worldBibleObject;
                $storyBook->status        = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                "story_book" => $storyBook,
                'status'  => 'success',
                'message' =>  'Story world bible generated successfully.'
            ];
        } catch (Exception $exception) {

            Log::error("Failed to generate Story world bible", [
                "exception" => $exception->getMessage(),
            ]);

            return [
                "story_book" => null,
                'status'  => 'error',
                'message' => 'Failed to generate Story world bible. Please try again.',
            ];
        }
    }

    public function generateLocations(StoryBookLocationsRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_LOCATION_GENERATOR));
            $aiBrain  = $this->aiBrainService->findById($request->input("ai_brain_id"));

            $requestInputs = $this->locationsRequestInputsFormatter($storyBook, $request->input("additional_information", "Auto"));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $locationsObject = $this->extractLocationsFromResponse($apiResponse);
                $storyBook->locations      = $locationsObject;
                $storyBook->status        = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                "story_book" => $storyBook,
                'status'  => 'success',
                'message' =>  'Story locations generated successfully.'
            ];
        } catch (Exception $exception) {

            Log::error("Failed to generate Story locations", [
                "exception" => $exception->getMessage(),
            ]);

            return [
                "story_book" => null,
                'status'  => 'error',
                'message' => 'Failed to generate Story locations. Please try again.',
            ];
        }
    }

    public function generateFactions(StoryBookFactionsRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_FACTION_GENERATOR));
            $aiBrain  = $this->aiBrainService->findById($request->input("ai_brain_id"));

            $requestInputs = $this->factionsRequestInputsFormatter($storyBook, $request->input("additional_information", "Auto"));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $factionsObject = $this->extractFactionsFromResponse($apiResponse);
                $storyBook->factions       = $factionsObject;
                $storyBook->status        = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                "story_book" => $storyBook,
                'status'  => 'success',
                'message' =>  'Story factions generated successfully.'
            ];
        } catch (Exception $exception) {

            Log::error("Failed to generate Story factions", [
                "exception" => $exception->getMessage(),
            ]);

            return [
                "story_book" => null,
                'status'  => 'error',
                'message' => 'Failed to generate Story factions. Please try again.',
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

    private function extractFoundationFromResponse($apiResponse): object
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
            'foundation' => $decoded['story_book_foundation'] ?? null,
        ];
    }

    private function foundationRequestInputsFormatter(int|string $languageId, int|string $audienceId, int|string $storyBookTypeId, array $genreIds, string $additionalInformation): array
    {
        $requestInputs = array();

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

        $requestInputs = [
            "language" => $language?->name,
            "additional_information" => $additionalInformation,
            "genre_prompt_instruction" => $genrePromptInstruction,
            "audience_instruction" => $audience->prompt_instruction,
            "story_book_type_instruction" => $storyBookType->prompt_instruction,
        ];

        return $requestInputs;
    }


    private function charactersRequestInputsFormatter(StoryBook $storyBook, string $additionalIinformation): array
    {
        $requestInputs = array();

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $requestInputs = [
            "foundation" => $formatedFoundation,
            "additional_information" => $additionalIinformation,
        ];

        return $requestInputs;
    }

    private function worldBibleRequestInputsFormatter(StoryBook $storyBook, string $additionalInformation): array
    {
        $requestInputs = array();

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);

        $requestInputs = [
            "foundation" => $formatedFoundation,
            "characters" => $formatedCharacters,
            "additional_information" => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function locationsRequestInputsFormatter(StoryBook $storyBook, string $additionalInformation): array
    {
        $requestInputs = array();

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);

        $requestInputs = [
            "foundation" => $formatedFoundation,
            "characters" => $formatedCharacters,
            "world_bible" => $formatedWorldBible,
            "additional_information" => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function factionsRequestInputsFormatter(StoryBook $storyBook, string $additionalInformation): array
    {
        $requestInputs = array();

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations  = json_encode($storyBook->locations, JSON_PRETTY_PRINT);

        $requestInputs = [
            "foundation" => $formatedFoundation,
            "characters" => $formatedCharacters,
            "world_bible" => $formatedWorldBible,
            "locations" => $formatedLocations,
            "additional_information" => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function extractCharactersFromResponse($apiResponse): object
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
            'characters' => $decoded['characters'] ?? [],
            'relationship_dynamics' => $decoded['relationship_dynamics'] ?? [],
        ];
    }

    private function extractWorldBibleFromResponse($apiResponse): object
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
            'world_overview' => $decoded['world_overview'] ?? [],
            'world_rules' => $decoded['world_rules'] ?? [],
            'culture_and_history' => $decoded['culture_and_history'] ?? [],
            'lore' => $decoded['lore'] ?? [],
        ];
    }

    private function extractLocationsFromResponse($apiResponse): object
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
            'locations' => $decoded['locations'] ?? [],
            'regions' => $decoded['regions'] ?? [],
            'landmarks' => $decoded['landmarks'] ?? [],
            'environment_details' => $decoded['environment_details'] ?? [],
        ];
    }

    private function extractFactionsFromResponse($apiResponse): object
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
            'factions' => $decoded['factions'] ?? [],
            'goals_and_values' => $decoded['goals_and_values'] ?? [],
            'conflicts' => $decoded['conflicts'] ?? [],
            'alliances' => $decoded['alliances'] ?? [],
        ];
    }
}
