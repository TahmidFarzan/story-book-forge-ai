<?php

namespace App\Services\BackOffice;

use App\Helpers\AiPromptGeneratorHelper;
use App\Helpers\MediaHelper;
use App\Helpers\StoryBookHelper;
use App\Http\Requests\StoryBookStep2CharactersRequest;
use App\Http\Requests\StoryBookStep6CreatureRequest;
use App\Http\Requests\StoryBookStep12DialoguePlanRequest;
use App\Http\Requests\StoryBookStep5FactionsRequest;
use App\Http\Requests\StoryBookStep1FoundationRequest;
use App\Http\Requests\StoryBookStep4LocationsRequest;
use App\Http\Requests\StoryBookStep13PagePlanRequest;
use App\Http\Requests\StoryBookStep11ScenePlanRequest;
use App\Http\Requests\StoryBookStep14_1PageNarrationRequest;
use App\Http\Requests\StoryBookStep14_2IllustrationPlanningRequest;
use App\Http\Requests\StoryBookStep14_3IllustrationGenerationRequest;
use App\Http\Requests\StoryBookStep9StoryStructureRequest;
use App\Http\Requests\StoryBookStep7SystemRequest;
use App\Http\Requests\StoryBookStep8TimelineRequest;
use App\Http\Requests\StoryBookStep10TwistsAndForeshadowingRequest;
use App\Http\Requests\StoryBookStep3WorldVibeRequest;
use App\Models\AiBrain;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use App\Models\StoryBook;
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

    protected IllustrationTypeService $illustrationTypeService;

    protected StoryBookTypeService $storyBookTypeService;

    protected HuggingFaceApiService $huggingFaceApiService;

    protected LanguageService $languageService;

    public function __construct(AiBrainService $aiBrainService, AiPromptService $aiPromptService, AudienceService $audienceService, GenreService $genreService, IllustrationTypeService $illustrationTypeService, StoryBookTypeService $storyBookTypeService, HuggingFaceApiService $huggingFaceApiService, LanguageService $languageService)
    {
        $this->aiBrainService = $aiBrainService;
        $this->aiPromptService = $aiPromptService;
        $this->audienceService = $audienceService;
        $this->genreService = $genreService;
        $this->illustrationTypeService = $illustrationTypeService;
        $this->storyBookTypeService = $storyBookTypeService;
        $this->huggingFaceApiService = $huggingFaceApiService;
        $this->languageService = $languageService;
    }

    public function new(): StoryBook
    {
        return new StoryBook;
    }

    public function find(string $slug): StoryBook
    {
        return StoryBook::with([
            'language',
            'storyBookType',
            'audience',
            'genres',

            'createdBy',

            'activityLogs' => fn ($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'storyBookPageImages' => fn ($query) => $query->orderBy('order_column'),

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
            $search = $request->input('search');
            $likeSearch = "%{$search}%";

            $query->whereAny([
                'title',
                'sub_title',
            ], 'like', $likeSearch);
        }

        if ($request->filled('genre_id')) {
            $query->whereHas(
                'genres',
                fn ($query) => $query->where('genres.id', $request->input('genre_id'))
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());
    }

    public function generateStep1Foundation(StoryBookStep1FoundationRequest $request, StoryBook $storyBook): array
    {
        $isNew = empty($storyBook->id);
        $statusEvent = $isNew ? 'save' : 'update';

        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP1_FOUNDATION_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step1FoundationRequestInputsFormatter($request->input('language_id'), $request->input('audience_id'), $request->input('story_book_type_id'), $request->input('genre_ids'), $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($request, $apiResponse, $storyBook, $isNew) {
                $foundationObject = $this->extractStep1FoundationFromResponse($apiResponse);

                $storyBook->title = $foundationObject->title;
                $storyBook->sub_title = $foundationObject->subtitle;
                $storyBook->foundation = $foundationObject->foundation;

                $storyBook->audience_id = $request->input('audience_id');
                $storyBook->story_book_type_id = $request->input('story_book_type_id');
                $storyBook->language_id = $request->input('language_id');

                $storyBook->additional_information = $request->input('additional_information');

                $storyBook->status = StoryBookHelper::STATUS_ONGOING;

                if ($isNew) {
                    $storyBook->datetime = now();
                    $storyBook->created_by_id = Auth::id();
                }

                $storyBook->save();

                if ($request->has('genre_ids')) {
                    $storyBook->genres()->sync((array) $request->input('genre_ids', []));
                }

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => $isNew
                    ? 'Story created successfully.'
                    : 'Story updated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error("Failed to {$statusEvent} story.", [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to save story. Please try again.',
            ];
        }
    }

    public function generateStep2Characters(StoryBookStep2CharactersRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP2_CHARACTERS_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step2CharactersRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $characterObject = $this->extractStep2CharactersFromResponse($apiResponse);
                $storyBook->characters = $characterObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story characters generate successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story characters', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story characters. Please try again.',
            ];
        }
    }

    public function generateStep3WorldVibe(StoryBookStep3WorldVibeRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP3_WORLD_VIBE_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step3WorldVibeRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $worldBibleObject = $this->extractStep3WorldVibeFromResponse($apiResponse);
                $storyBook->world_bible = $worldBibleObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story world bible generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story world bible', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story world bible. Please try again.',
            ];
        }
    }

    public function generateStep4Locations(StoryBookStep4LocationsRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP4_LOCATIONS_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step4LocationsRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $locationsObject = $this->extractStep4LocationsFromResponse($apiResponse);
                $storyBook->locations = $locationsObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story locations generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story locations', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story locations. Please try again.',
            ];
        }
    }

    public function generateStep5Factions(StoryBookStep5FactionsRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP5_FACTIONS_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step5FactionsRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $factionsObject = $this->extractStep5FactionsFromResponse($apiResponse);
                $storyBook->factions = $factionsObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story factions generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story factions', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story factions. Please try again.',
            ];
        }
    }

    public function generateStep6Creature(StoryBookStep6CreatureRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP6_CREATURE_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step6CreatureRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $creaturesObject = $this->extractStep6CreatureFromResponse($apiResponse);
                $storyBook->creatures = $creaturesObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story creatures generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story creatures', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story creatures. Please try again.',
            ];
        }
    }

    public function generateStep7System(StoryBookStep7SystemRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP7_SYSTEM_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step7SystemRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $systemsObject = $this->extractStep7SystemFromResponse($apiResponse);
                $storyBook->systems = $systemsObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story systems generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story systems', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story systems. Please try again.',
            ];
        }
    }

    public function generateStep8Timeline(StoryBookStep8TimelineRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP8_TIMELINE_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step8TimelineRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $timelineObject = $this->extractStep8TimelineFromResponse($apiResponse);
                $storyBook->timeline = $timelineObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story timeline generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story timeline', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story timeline. Please try again.',
            ];
        }
    }

    public function generateStep9StoryStructure(StoryBookStep9StoryStructureRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP9_STORY_STRUCTURE_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step9StoryStructureRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyStructureObject = $this->extractStep9StoryStructureFromResponse($apiResponse);
                $storyBook->story_structure = $storyStructureObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story structure generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story structure', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story structure. Please try again.',
            ];
        }
    }

    public function generateStep10TwistsAndForeshadowing(StoryBookStep10TwistsAndForeshadowingRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP10_TWISTS_AND_FORESHADOWING_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step10TwistsAndForeshadowingRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $twistsObject = $this->extractStep10TwistsAndForeshadowingFromResponse($apiResponse);
                $storyBook->twists_and_foreshadowing = $twistsObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story twists and foreshadowing generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story twists and foreshadowing', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story twists and foreshadowing. Please try again.',
            ];
        }
    }

    public function generateStep11ScenePlan(StoryBookStep11ScenePlanRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP11_SCENE_PLAN_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step11ScenePlanRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $scenePlanObject = $this->extractStep11ScenePlanFromResponse($apiResponse);
                $storyBook->scene_plans = $scenePlanObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story scene plan generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story scene plan', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story scene plan. Please try again.',
            ];
        }
    }

    public function generateStep12DialoguePlan(StoryBookStep12DialoguePlanRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP12_DIALOGUE_PLAN_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step12DialoguePlanRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $dialoguePlanObject = $this->extractStep12DialoguePlanFromResponse($apiResponse);
                $storyBook->dialogue_plans = $dialoguePlanObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story dialogue plan generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story dialogue plan', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story dialogue plan. Please try again.',
            ];
        }
    }

    public function generateStep13PagePlan(StoryBookStep13PagePlanRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP13_PAGE_PLAN_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step13PagePlanRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $pagePlanObject = $this->extractStep13PagePlanFromResponse($apiResponse);
                $storyBook->page_plan = $pagePlanObject;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story page plan generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story page plan', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story page plan. Please try again.',
            ];
        }
    }

    public function generateStep14_1PageNarration(StoryBookStep14_1PageNarrationRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_14_1_PAGE_NARRATION_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->step14_1PageNarrationRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $pages = $this->extractStep14_1PagesFromResponse($apiResponse);
                $storyBook->pages = $pages;
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story page narration generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story page narration', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story page narration. Please try again.',
            ];
        }
    }

    public function generateStep14_2IllustrationPlanning(StoryBookStep14_2IllustrationPlanningRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_14_2_ILLUSTRATION_PLANNING_GENERATOR));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));
            $illustrationType = $this->illustrationTypeService->findById($request->input('illustration_type_id'));

            $requestInputs = $this->step14_2IllustrationPlanningRequestInputsFormatter($storyBook, $request->input('additional_information', 'Auto'));
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook, $illustrationType) {
                $illustrationPlanning = $this->extractStep14_2IllustrationPlanningFromResponse($apiResponse, (array) ($storyBook->pages ?? []));
                $storyBook->pages = $this->applyIllustrationPlanningToPages((array) ($storyBook->pages ?? []), $illustrationPlanning, $illustrationType->prompt_instruction);
                $storyBook->status = StoryBookHelper::STATUS_ONGOING;
                $storyBook->save();

                return $storyBook;
            });

            return [
                'story_book' => $storyBook,
                'status' => 'success',
                'message' => 'Story illustration planning generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story illustration planning', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => 'Failed to generate Story illustration planning. Please try again.',
            ];
        }
    }

    public function generateStep14_3Illustration(StoryBookStep14_3IllustrationGenerationRequest $request, StoryBook $storyBook): array
    {
        try {
            $pageNo = (int) $request->input('page_no');

            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $this->assertImageOutputAiBrain($aiBrain);

            $page = $this->findPageByNo($storyBook, $pageNo);

            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_14_3_ILLUSTRATION_GENERATOR));
            $requestInputs = $this->step14_3PageIllustrationRequestInputsFormatter($storyBook, $page);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $image = $this->huggingFaceApiService->sendImageRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->timeout_seconds);

            DB::transaction(function () use ($storyBook, $pageNo, $page, $image) {
                $this->deleteExistingPageImage($storyBook, $pageNo);
                $this->storeStoryBookPageImage($storyBook, $page, $pageNo, $image);
            });

            $storyBook = $storyBook->fresh();

            return [
                'story_book' => $storyBook,
                'page_no' => $pageNo,
                'status' => 'success',
                'message' => "Story book page {$pageNo} illustration generated successfully.",
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story book page illustration', [
                'page_no' => $request->input('page_no'),
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'page_no' => $request->input('page_no'),
                'status' => 'error',
                'message' => 'Failed to generate the page illustration. Please try again.',
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
                'status' => 'success',
                'message' => 'Story deleted successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Story delete failed.', [
                'exception' => $exception,
            ]);

            return [
                'status' => 'error',
                'message' => 'Failed to delete story. Please try again.',
            ];
        }
    }

    private function extractStep1FoundationFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'title' => $decoded['story_book_title'] ?? null,
            'subtitle' => $decoded['story_book_subtitle'] ?? null,
            'foundation' => $decoded['story_book_foundation'] ?? null,
        ];
    }

    private function step1FoundationRequestInputsFormatter(int|string $languageId, int|string $audienceId, int|string $storyBookTypeId, array $genreIds, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $language = $this->languageService->findByIdsOrEnglish($languageId);
        $audience = $this->audienceService->findById($audienceId);
        $storyBookType = $this->audienceService->findById($storyBookTypeId);
        $genres = $this->genreService->findByIdsOrRandom($genreIds);

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
            'language' => $language?->name,
            'additional_information' => $additionalInformation,
            'genre_prompt_instruction' => $genrePromptInstruction,
            'audience_instruction' => $audience->prompt_instruction,
            'story_book_type_instruction' => $storyBookType->prompt_instruction,
        ];

        return $requestInputs;
    }

    private function step2CharactersRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $requestInputs = [
            'foundation' => $formatedFoundation,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step3WorldVibeRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step4LocationsRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step5FactionsRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step6CreatureRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step7SystemRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step8TimelineRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step9StoryStructureRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step10TwistsAndForeshadowingRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step11ScenePlanRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);
        $formatedTwistsAndForeshadowing = json_encode($storyBook->twists_and_foreshadowing, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'twists_and_foreshadowing' => $formatedTwistsAndForeshadowing,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step12DialoguePlanRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);
        $formatedTwistsAndForeshadowing = json_encode($storyBook->twists_and_foreshadowing, JSON_PRETTY_PRINT);
        $formatedScenePlans = json_encode($storyBook->scene_plans, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'twists_and_foreshadowing' => $formatedTwistsAndForeshadowing,
            'scene_plans' => $formatedScenePlans,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step13PagePlanRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);
        $formatedTwistsAndForeshadowing = json_encode($storyBook->twists_and_foreshadowing, JSON_PRETTY_PRINT);
        $formatedScenePlans = json_encode($storyBook->scene_plans, JSON_PRETTY_PRINT);
        $formatedDialoguePlans = json_encode($storyBook->dialogue_plans, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'twists_and_foreshadowing' => $formatedTwistsAndForeshadowing,
            'scene_plans' => $formatedScenePlans,
            'dialogue_plans' => $formatedDialoguePlans,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step14_1PageNarrationRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);
        $formatedTwistsAndForeshadowing = json_encode($storyBook->twists_and_foreshadowing, JSON_PRETTY_PRINT);
        $formatedScenePlans = json_encode($storyBook->scene_plans, JSON_PRETTY_PRINT);
        $formatedDialoguePlans = json_encode($storyBook->dialogue_plans, JSON_PRETTY_PRINT);
        $formatedPagePlan = json_encode($storyBook->page_plan, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'twists_and_foreshadowing' => $formatedTwistsAndForeshadowing,
            'scene_plans' => $formatedScenePlans,
            'dialogue_plans' => $formatedDialoguePlans,
            'page_plan' => $formatedPagePlan,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step14_2IllustrationPlanningRequestInputsFormatter(StoryBook $storyBook, string|null $additionalInformation): array
    {
        $requestInputs = [];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);
        $formatedTwistsAndForeshadowing = json_encode($storyBook->twists_and_foreshadowing, JSON_PRETTY_PRINT);
        $formatedScenePlans = json_encode($storyBook->scene_plans, JSON_PRETTY_PRINT);
        $formatedDialoguePlans = json_encode($storyBook->dialogue_plans, JSON_PRETTY_PRINT);
        $formatedPagePlan = json_encode($storyBook->page_plan, JSON_PRETTY_PRINT);

        $pages = (array) ($storyBook->pages ?? []);
        $formatedPages = json_encode(array_values(array_map(fn (array $page) => [
            'no' => $page['no'] ?? null,
            'narration' => $page['narration'] ?? null,
        ], $pages)), JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'twists_and_foreshadowing' => $formatedTwistsAndForeshadowing,
            'scene_plans' => $formatedScenePlans,
            'dialogue_plans' => $formatedDialoguePlans,
            'page_plan' => $formatedPagePlan,
            'pages' => $formatedPages,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function extractStep14_2IllustrationPlanningFromResponse($apiResponse, array $existingPages): array
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        $pages = $decoded['pages'] ?? null;

        if (! is_array($pages)) {
            throw new Exception('AI response does not contain a valid pages array.');
        }

        $existingByNo = [];

        foreach ($existingPages as $existingPage) {
            if (! is_array($existingPage)) {
                continue;
            }

            $existingByNo[(int) ($existingPage['no'] ?? null)] = true;
        }

        $expectedCount = count($existingByNo);

        if ($expectedCount === 0) {
            throw new Exception('Story book has no pages to plan illustrations for.');
        }

        $result = [];
        $seenNos = [];

        foreach ($pages as $page) {
            if (! is_array($page)) {
                throw new Exception('AI response contains an invalid page object.');
            }

            $no = $page['no'] ?? null;

            if (! is_numeric($no)) {
                throw new Exception('AI response contains an invalid page number.');
            }

            $no = (int) $no;

            if (! isset($existingByNo[$no])) {
                throw new Exception("AI response contains a page number that does not exist: {$no}.");
            }

            if (isset($seenNos[$no])) {
                throw new Exception("AI response contains a duplicate page number: {$no}.");
            }

            $seenNos[$no] = true;

            $illustrationPrompt = $page['illustration_prompt'] ?? null;

            if (! is_string($illustrationPrompt) || trim($illustrationPrompt) === '') {
                throw new Exception("AI response contains an empty illustration prompt for page {$no}.");
            }

            $result[$no] = [
                'no' => $no,
                'illustration_prompt' => $illustrationPrompt,
            ];
        }

        if (count($result) !== $expectedCount) {
            throw new Exception('AI response does not return every expected page.');
        }

        ksort($result);

        return array_values($result);
    }

    private function applyIllustrationPlanningToPages(array $existingPages, array $illustrationPlanning, string $illustrationTypePromptInstruction): array
    {
        $illustrationPlanningByNo = [];

        foreach ($illustrationPlanning as $plannedPage) {
            $illustrationPlanningByNo[(int) ($plannedPage['no'] ?? null)] = $plannedPage;
        }

        $updatedPages = [];

        foreach ($existingPages as $existingPage) {
            $no = (int) ($existingPage['no'] ?? null);

            $page = $existingPage;

            if (isset($illustrationPlanningByNo[$no])) {
                $page['illustration_prompt'] = $illustrationPlanningByNo[$no]['illustration_prompt'];
            }

            $page['illustration_type_prompt_instruction'] = $illustrationTypePromptInstruction;

            $updatedPages[] = $page;
        }

        usort(
            $updatedPages,
            fn (array $a, array $b) => (int) ($a['no'] ?? 0) <=> (int) ($b['no'] ?? 0)
        );

        return $updatedPages;
    }

    private function assertImageOutputAiBrain(AiBrain $aiBrain): void
    {
        $outputTypeCodes = $aiBrain->aiBrainOutputTypes->pluck('code');

        if (! $outputTypeCodes->contains('Image')) {
            throw new Exception('Selected ai brain is not an image-output ai brain.');
        }
    }

    private function findPageByNo(StoryBook $storyBook, int $pageNo): array
    {
        $pages = (array) ($storyBook->pages ?? []);

        foreach ($pages as $page) {
            if (is_array($page) && (int) ($page['no'] ?? null) === $pageNo) {
                return $page;
            }
        }

        throw new Exception("Story book page {$pageNo} not found.");
    }

    private function step14_3PageIllustrationRequestInputsFormatter(StoryBook $storyBook, array $page): array
    {
        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);

        $formatedPage = json_encode([
            'no' => $page['no'] ?? null,
            'narration' => $page['narration'] ?? null,
            'illustration_prompt' => $page['illustration_prompt'] ?? null,
        ], JSON_PRETTY_PRINT);

        return [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'illustration_type_prompt_instruction' => $page['illustration_type_prompt_instruction'] ?? 'No additional visual style instruction.',
            'page' => $formatedPage,
        ];
    }

    private function deleteExistingPageImage(StoryBook $storyBook, int $pageNo): void
    {
        $storyBook->storyBookPageImages()
            ->get()
            ->filter(fn (Media $media) => (int) ($media->getCustomProperty('page_no') ?? $media->order_column) === $pageNo)
            ->each(fn (Media $media) => $media->delete());
    }

    private function storeStoryBookPageImage(StoryBook $storyBook, array $page, int $pageNo, array $image): Media
    {
        $mediaBaseName = "{$storyBook->title} Page {$pageNo}";

        $mediaFileName = MediaHelper::generateMediaName($mediaBaseName, $image['extension'], 200);

        $alt = "{$storyBook->title} Page {$pageNo}";

        $narration = Str::limit((string) ($page['narration'] ?? ''), 200);

        $caption = $narration !== '' ? $narration : $alt;

        $customProperties = [
            'caption' => $caption,
            'alt' => $alt,
            'role' => MediaHelper::ROLE_STORY_BOOK_PAGE_IMAGE,
            'page_no' => $pageNo,
        ];

        if ($image['type'] === 'url') {
            $media = $storyBook
                ->addMediaFromUrl($image['url'])
                ->usingName($mediaBaseName)
                ->usingFileName($mediaFileName)
                ->withCustomProperties($customProperties)
                ->toMediaCollection($storyBook->media_collection_name);
        } else {
            $tempPath = tempnam(sys_get_temp_dir(), 'page_image_');

            file_put_contents(
                $tempPath,
                (string) (base64_decode($image['encoded'], true) ?: '')
            );

            try {
                $media = $storyBook
                    ->addMedia($tempPath)
                    ->usingName($mediaBaseName)
                    ->usingFileName($mediaFileName)
                    ->withCustomProperties($customProperties)
                    ->toMediaCollection($storyBook->media_collection_name);
            } finally {
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
            }
        }

        $media->update([
            'order_column' => $pageNo,
        ]);

        return $media;
    }

    private function extractStep2CharactersFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'characters' => $decoded['characters'] ?? [],
            'relationship_dynamics' => $decoded['relationship_dynamics'] ?? [],
        ];
    }

    private function extractStep3WorldVibeFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'world_overview' => $decoded['world_overview'] ?? [],
            'world_rules' => $decoded['world_rules'] ?? [],
            'culture_and_history' => $decoded['culture_and_history'] ?? [],
            'lore' => $decoded['lore'] ?? [],
        ];
    }

    private function extractStep4LocationsFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'locations' => $decoded['locations'] ?? [],
            'regions' => $decoded['regions'] ?? [],
            'landmarks' => $decoded['landmarks'] ?? [],
            'environment_details' => $decoded['environment_details'] ?? [],
        ];
    }

    private function extractStep5FactionsFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'factions' => $decoded['factions'] ?? [],
            'goals_and_values' => $decoded['goals_and_values'] ?? [],
            'conflicts' => $decoded['conflicts'] ?? [],
            'alliances' => $decoded['alliances'] ?? [],
        ];
    }

    private function extractStep6CreatureFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'creatures' => $decoded['creatures'] ?? [],
            'abilities' => $decoded['abilities'] ?? [],
            'behaviors' => $decoded['behaviors'] ?? [],
            'ecosystem_role' => $decoded['ecosystem_role'] ?? [],
        ];
    }

    private function extractStep7SystemFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'systems' => $decoded['systems'] ?? [],
            'mechanics' => $decoded['mechanics'] ?? [],
            'limitations' => $decoded['limitations'] ?? [],
            'rules' => $decoded['rules'] ?? [],
        ];
    }

    private function extractStep8TimelineFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'timeline' => $decoded['timeline'] ?? [],
            'major_events' => $decoded['major_events'] ?? [],
            'milestones' => $decoded['milestones'] ?? [],
            'historical_flow' => $decoded['historical_flow'] ?? [],
        ];
    }

    private function extractStep9StoryStructureFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'story_outline' => $decoded['story_outline'] ?? [],
            'acts_and_chapters' => $decoded['acts_and_chapters'] ?? [],
            'plot_progression' => $decoded['plot_progression'] ?? [],
            'pacing_guide' => $decoded['pacing_guide'] ?? [],
        ];
    }

    private function extractStep10TwistsAndForeshadowingFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'twists' => $decoded['twists'] ?? [],
            'foreshadowing' => $decoded['foreshadowing'] ?? [],
            'hidden_clues' => $decoded['hidden_clues'] ?? [],
            'reveal_points' => $decoded['reveal_points'] ?? [],
        ];
    }

    private function extractStep11ScenePlanFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'scene_list' => $decoded['scene_list'] ?? [],
            'scene_objectives' => $decoded['scene_objectives'] ?? [],
            'locations' => $decoded['locations'] ?? [],
            'pov_and_tone' => $decoded['pov_and_tone'] ?? [],
        ];
    }

    private function extractStep12DialoguePlanFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'dialogue_bank' => $decoded['dialogue_bank'] ?? [],
            'character_voice' => $decoded['character_voice'] ?? [],
            'conversation_flow' => $decoded['conversation_flow'] ?? [],
            'key_dialogues' => $decoded['key_dialogues'] ?? [],
        ];
    }

    private function extractStep13PagePlanFromResponse($apiResponse): object
    {
        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        return (object) [
            'page_layout' => $decoded['page_layout'] ?? [],
            'page_descriptions' => $decoded['page_descriptions'] ?? [],
            'illustration_notes' => $decoded['illustration_notes'] ?? [],
            'key_points' => $decoded['key_points'] ?? [],
        ];
    }

    private function extractStep14_1PagesFromResponse($apiResponse): array
    {

        $decoded = $this->huggingFaceApiService->decodeAiResponseContent($apiResponse);

        $pages = $decoded['pages'] ?? [];

        if (! is_array($pages)) {
            $pages = [];
        }

        $normalizedPages = [];

        foreach ($pages as $index => $page) {
            if (! is_array($page)) {
                continue;
            }

            $normalizedPages[] = [
                'no' => (int) ($page['no'] ?? $index + 1),
                'narration' => $page['narration'] ?? null,
                'illustration_type_prompt_instruction' => null,
                'illustration_prompt' => null,
            ];
        }

        usort(
            $normalizedPages,
            fn (array $a, array $b) => $a['no'] <=> $b['no']
        );

        return $normalizedPages;
    }
}
