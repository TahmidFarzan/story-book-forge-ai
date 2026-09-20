<?php

namespace App\Services\BackOffice;

use App\Helpers\AiPromptGeneratorHelper;
use App\Helpers\StoryBookHelper;
use App\Http\Requests\StoryBookStep2CharactersRequest;
use App\Http\Requests\StoryBookStep6CreatureRequest;
use App\Http\Requests\StoryBookStep12DialoguePlanRequest;
use App\Http\Requests\StoryBookStep5FactionsRequest;
use App\Http\Requests\StoryBookStep1FoundationRequest;
use App\Http\Requests\StoryBookStep4LocationsRequest;
use App\Http\Requests\StoryBookStep13PagePlanRequest;
use App\Http\Requests\StoryBookStep11ScenePlanRequest;
use App\Http\Requests\StoryBookStep14PageNarrationRequest;
use App\Http\Requests\StoryBookStep15IllustrationPlanningRequest;
use App\Http\Requests\StoryBookStep16IllustrationGenerationRequest;
use App\Http\Requests\StoryBookStep9StoryStructureRequest;
use App\Http\Requests\StoryBookStep7SystemRequest;
use App\Http\Requests\StoryBookStep8TimelineRequest;
use App\Http\Requests\StoryBookStep10TwistsAndForeshadowingRequest;
use App\Http\Requests\StoryBookStep3WorldVibeRequest;
use App\Models\AiBrain;
use App\Models\StoryBook;
use App\Models\StoryBookPage;
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

    protected IllustrationTypeService $illustrationTypeService;

    protected HuggingFaceApiService $huggingFaceApiService;

    protected StoryBookPageService $storyBookPageService;

    public function __construct(AiBrainService $aiBrainService, AiPromptService $aiPromptService, IllustrationTypeService $illustrationTypeService, HuggingFaceApiService $huggingFaceApiService, StoryBookPageService $storyBookPageService)
    {
        $this->aiBrainService = $aiBrainService;
        $this->aiPromptService = $aiPromptService;
        $this->illustrationTypeService = $illustrationTypeService;
        $this->huggingFaceApiService = $huggingFaceApiService;
        $this->storyBookPageService = $storyBookPageService;
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

            'storyBookPages' => fn($query) => $query->orderBy('no', 'asc'),
            'storyBookPages.illustrationImage',

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

    public function generateStep1(StoryBookStep1FoundationRequest $request, StoryBook $storyBook): array
    {
        $isNew = empty($storyBook->id);
        $statusEvent = $isNew ? 'save' : 'update';

        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP1));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP1, [
                'language_id' => $request->input('language_id'),
                'audience_id' => $request->input('audience_id'),
                'story_book_type_id' => $request->input('story_book_type_id'),
                'genre_ids' => $request->input('genre_ids'),
                'additional_information' => $request->input('additional_information', 'Auto'),
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP1);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($request, $apiResponse, $storyBook, $isNew) {
                $storyBook->title = $apiResponse->title;
                $storyBook->sub_title = $apiResponse->subtitle;
                $storyBook->foundation = $apiResponse->foundation;

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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep2(StoryBookStep2CharactersRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP2));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP2, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP2);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->characters = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep3(StoryBookStep3WorldVibeRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP3));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP3, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP3);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->world_bible = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep4(StoryBookStep4LocationsRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP4));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP4, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP4);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->locations = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep5(StoryBookStep5FactionsRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP5));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP5, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP5);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->factions = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep6(StoryBookStep6CreatureRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP6));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP6, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP6);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->creatures = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep7(StoryBookStep7SystemRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP7));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP7, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP7);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->systems = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep8(StoryBookStep8TimelineRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP8));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP8, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP8);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->timeline = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep9(StoryBookStep9StoryStructureRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP9));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP9, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP9);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->story_structure = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep10(StoryBookStep10TwistsAndForeshadowingRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP10));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP10, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP10);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->twists_and_foreshadowing = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep11(StoryBookStep11ScenePlanRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP11));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP11, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP11);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->scene_plans = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep12(StoryBookStep12DialoguePlanRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP12));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP12, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP12);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->dialogue_plans = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep13(StoryBookStep13PagePlanRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP13));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP13, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP13);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $storyBook->page_plan = $apiResponse;
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep14(StoryBookStep14PageNarrationRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_14));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_14, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_14);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook) {
                $this->storyBookPageService->syncStoryBookPages($storyBook, (array) $apiResponse->pages);
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep15(StoryBookStep15IllustrationPlanningRequest $request, StoryBook $storyBook): array
    {
        try {
            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_15));
            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));
            $illustrationType = $this->illustrationTypeService->findById($request->input('illustration_type_id'));

            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_15, [
                'story_book' => $storyBook,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $existingPages = $storyBook->storyBookPages->map(fn(StoryBookPage $storyBookPage) => [
                'no' => $storyBookPage->no,
                'narration' => $storyBookPage->narration,
            ])->values()->all();

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_15, ['existing_pages' => $existingPages]);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $storyBook = DB::transaction(function () use ($apiResponse, $storyBook, $illustrationType) {
                $this->storyBookPageService->applyIllustrationPlanning($storyBook, (array) $apiResponse->pages, $illustrationType->prompt_instruction);
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status' => 'error',
                'message' => $exception->getMessage(),
            ];
        }
    }

    public function generateStep16(StoryBookStep16IllustrationGenerationRequest $request, StoryBook $storyBook): array
    {
        try {
            $pageNo = (int) $request->input('page_no');

            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $this->assertImageOutputAiBrain($aiBrain);

            $page = $this->storyBookPageService->findByNo($storyBook, $pageNo);

            $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_16));
            $requestInputs = $this->huggingFaceApiService->formatRequestInputs($storyBook,AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_16, [
                'story_book' => $storyBook,
                'page' => $page,
            ]);
            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $image = $this->huggingFaceApiService->sendImageRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->timeout_seconds);

            DB::transaction(function () use ($page, $image) {
                $this->storyBookPageService->replaceIllustrationImage($page, $image);
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
                'trace' => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'page_no' => $request->input('page_no'),
                'status' => 'error',
                'message' => $exception->getMessage(),
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

    private function assertImageOutputAiBrain(AiBrain $aiBrain): void
    {
        $outputTypeCodes = $aiBrain->aiBrainOutputTypes->pluck('code');

        if (! $outputTypeCodes->contains('Image')) {
            throw new Exception('Selected ai brain is not an image-output ai brain.');
        }
    }
}
