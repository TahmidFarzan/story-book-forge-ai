<?php
namespace App\Services\BackOffice;

use App\Helpers\AiPromptGeneratorHelper;
use App\Helpers\StoryBookHelper;
use App\Http\Requests\StoryBookStep1;
use App\Http\Requests\StoryBookStep2;
use App\Http\Requests\StoryBookStep3;
use App\Http\Requests\StoryBookStep4;
use App\Http\Requests\StoryBookStep5;
use App\Http\Requests\StoryBookStep6;
use App\Http\Requests\StoryBookStep7;
use App\Http\Requests\StoryBookStep8;
use App\Http\Requests\StoryBookStep9;
use App\Http\Requests\StoryBookStep10;
use App\Http\Requests\StoryBookStep11;
use App\Http\Requests\StoryBookStep12;
use App\Http\Requests\StoryBookStep13;
use App\Http\Requests\StoryBookStep14;
use App\Http\Requests\StoryBookStep15;
use App\Http\Requests\StoryBookStep16;
use App\Models\AiBrain;
use App\Models\StoryBook;
use App\Models\StoryBookPage;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoryBookGeneratorService
{
    protected AiBrainService $aiBrainService;

    protected AiPromptService $aiPromptService;

    protected IllustrationTypeService $illustrationTypeService;

    protected HuggingFaceApiService $huggingFaceApiService;

    protected StoryBookPageService $storyBookPageService;

    public function __construct(AiBrainService $aiBrainService, AiPromptService $aiPromptService, IllustrationTypeService $illustrationTypeService, HuggingFaceApiService $huggingFaceApiService, StoryBookPageService $storyBookPageService)
    {
        $this->aiBrainService          = $aiBrainService;
        $this->aiPromptService         = $aiPromptService;
        $this->illustrationTypeService = $illustrationTypeService;
        $this->huggingFaceApiService   = $huggingFaceApiService;
        $this->storyBookPageService    = $storyBookPageService;
    }

    public function step1Prompt(StoryBookStep1 $request, StoryBook $storyBook)
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP1;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step1InputsFormatter([
            'language_id'            => $request->input('language_id'),
            'audience_id'            => $request->input('audience_id'),
            'story_book_type_id'     => $request->input('story_book_type_id'),
            'genre_ids'              => $request->input('genre_ids'),
            'additional_information' => $request->input('additional_information', 'Auto'),
        ]);
        $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step2Prompt(StoryBookStep2 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP2;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP2));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step2InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

    }

    public function step3Prompt(StoryBookStep3 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP3;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step3InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step4Prompt(StoryBookStep4 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP4;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step4InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step5Prompt(StoryBookStep5 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP5;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step5InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step6Prompt(StoryBookStep6 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP6;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step6InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step7Prompt(StoryBookStep7 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP7;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step7InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step8Prompt(StoryBookStep8 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP8;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step8InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step9Prompt(StoryBookStep9 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP9;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step9InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step10Prompt(StoryBookStep10 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP10;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step10InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step11Prompt(StoryBookStep11 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP11;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step11InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step12Prompt(StoryBookStep12 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP12;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step12InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step13Prompt(StoryBookStep13 $request, StoryBook $storyBook): array
    {
        $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP13;

        $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
        $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

        $requestInputs = $this->huggingFaceApiService->step13InputsFormatter($storyBook);
        $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

        return $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);
    }

    public function step14Prompt(StoryBookStep14 $request, StoryBook $storyBook): array
    {
        try {
            $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_14;

            $aiPrompt = $this->aiPromptService->findByCode(Str::studly($step));
            $aiBrain  = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $requestInputs = $this->huggingFaceApiService->step14InputsFormatter($storyBook);
            $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds);

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
                'status'     => 'success',
                'message'    => 'Story page narration generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story page narration', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step15Prompt(StoryBookStep15 $request, StoryBook $storyBook): array
    {
        try {
            $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_15;

            $aiPrompt         = $this->aiPromptService->findByCode(Str::studly($step));
            $aiBrain          = $this->aiBrainService->findById($request->input('ai_brain_id'));
            $illustrationType = $this->illustrationTypeService->findById($request->input('illustration_type_id'));

            $requestInputs = $this->huggingFaceApiService->step15InputsFormatter($storyBook);
            $prompt        = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $existingPages = $storyBook->storyBookPages->map(fn(StoryBookPage $storyBookPage) => [
                'no'        => $storyBookPage->no,
                'narration' => $storyBookPage->narration,
            ])->values()->all();

            $apiResponse = $this->huggingFaceApiService->sendPostRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $step, $prompt, $aiBrain->max_output_tokens, $aiBrain->timeout_seconds, ['existing_pages' => $existingPages]);

            if (! $apiResponse['success']) {
                throw new Exception($apiResponse['message']);
            }

            $apiResponse = $apiResponse['data'];

            $this->storyBookPageService->applyIllustrationPlanning($storyBook, (array) $apiResponse->pages, $illustrationType->prompt_instruction);

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story illustration planning generated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story illustration planning', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function step16Prompt(StoryBookStep16 $request, StoryBook $storyBook): array
    {
        try {
            $step = AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_16;

            $pageNo = (int) $request->input('page_no');

            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_id'));

            $this->assertImageOutputAiBrain($aiBrain);

            $storyBookPage = $this->storyBookPageService->findByNo($storyBook, $pageNo);
            $aiPrompt      = $this->aiPromptService->findByCode(Str::studly($step));

            $requestInputs = $this->huggingFaceApiService->step16InputsFormatter($storyBook, $storyBookPage);

            $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $requestInputs);

            $image = $this->huggingFaceApiService->sendImageRequest($aiBrain->api_url, $aiBrain->api_key, $aiBrain->model, $prompt, $aiBrain->timeout_seconds);

            DB::transaction(function () use ($storyBookPage, $image) {
                $this->storyBookPageService->replaceIllustrationImage($storyBookPage, $image);
            });

            $storyBook = $storyBook->fresh();

            return [
                'story_book' => $storyBook,
                'page_no'    => $pageNo,
                'status'     => 'success',
                'message'    => "Story book page {$pageNo} illustration generated successfully.",
            ];
        } catch (Exception $exception) {

            Log::error('Failed to generate Story book page illustration', [
                'page_no'   => $request->input('page_no'),
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => null,
                'page_no'    => $request->input('page_no'),
                'status'     => 'error',
                'message'    => $exception->getMessage(),
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

    private function assertImageOutputAiBrain(AiBrain $aiBrain): void
    {
        $outputTypeCodes = $aiBrain->aiBrainOutputTypes->pluck('code');

        if (! $outputTypeCodes->contains('Image')) {
            throw new Exception('Selected ai brain is not an image-output ai brain.');
        }
    }
}
