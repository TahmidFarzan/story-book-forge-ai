<?php
namespace App\Services\BackOffice;

use App\Helpers\AiPromptGeneratorHelper;
use App\Helpers\StoryBookHelper;
use App\Http\Requests\StoryBookGenerate;
use App\Http\Requests\StoryBookIllustrationStart;
use App\Jobs\GenerateStoryBookIllustrationJob;
use App\Jobs\GenerateStoryBookTextJob;
use App\Models\AiBrain;
use App\Models\AiPrompt;
use App\Models\StoryBook;
use App\Models\StoryBookGeneratorStep;
use App\Models\StoryBookPage;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StoryBookGeneratorService
{
    protected const TEXT_STEP_COLUMNS = [
        2  => 'characters',
        3  => 'world_bible',
        4  => 'locations',
        5  => 'factions',
        6  => 'creatures',
        7  => 'systems',
        8  => 'timeline',
        9  => 'story_structure',
        10 => 'twists_and_foreshadowing',
        11 => 'scene_plans',
        12 => 'dialogue_plans',
        13 => 'page_plan',
    ];

    protected const AI_BRAIN_OUTPUT_TYPE_TEXT  = 'Text';

    protected const AI_BRAIN_OUTPUT_TYPE_IMAGE = 'Image';

    protected AiBrainService $aiBrainService;

    protected AiPromptService $aiPromptService;

    protected HuggingFaceApiService $huggingFaceApiService;

    protected StoryBookService $storyBookService;

    protected StoryBookPageService $storyBookPageService;

    protected StoryBookGeneratorStepService $storyBookGeneratorStepService;

    public function __construct(
        AiBrainService $aiBrainService,
        AiPromptService $aiPromptService,
        HuggingFaceApiService $huggingFaceApiService,
        StoryBookService $storyBookService,
        StoryBookPageService $storyBookPageService,
        StoryBookGeneratorStepService $storyBookGeneratorStepService
    ) {
        $this->aiBrainService                   = $aiBrainService;
        $this->aiPromptService                  = $aiPromptService;
        $this->huggingFaceApiService            = $huggingFaceApiService;
        $this->storyBookService                 = $storyBookService;
        $this->storyBookPageService             = $storyBookPageService;
        $this->storyBookGeneratorStepService    = $storyBookGeneratorStepService;
    }

    public function startTextGeneration(StoryBookGenerate $request): array
    {
        try {

            $foundation = $this->generateFoundation($request);

            $storyBook = DB::transaction(function () use ($request, $foundation) {
                $storyBook                    = new StoryBook;
                $storyBook->title             = $foundation->title;
                $storyBook->sub_title         = $foundation->subtitle;
                $storyBook->foundation        = $foundation->foundation;
                $storyBook->audience_id       = $request->input('audience_id');
                $storyBook->language_id       = $request->input('language_id');
                $storyBook->story_book_type_id = $request->input('story_book_type_id');
                $storyBook->illustration_type_id = $request->input('illustration_type_id');
                $storyBook->ai_brain_text_id  = $request->input('ai_brain_text_id');
                $storyBook->additional_information = $request->input('additional_information');
                $storyBook->status            = StoryBookHelper::STATUS_PROCESSING_TEXT;
                $storyBook->current_step      = 1;
                $storyBook->completed_steps_count = 1;
                $storyBook->text_generation_started_at = now();
                $storyBook->text_generation_completed_at = null;
                $storyBook->datetime          = now();
                $storyBook->created_by_id     = Auth::id();

                $storyBook->save();

                $storyBook->genres()->sync((array) $request->input('genre_ids', []));

                return $storyBook;
            });

            GenerateStoryBookTextJob::dispatchSync($storyBook->slug);

            return $this->textGenerationResult($storyBook);
        } catch (Exception $exception) {

            Log::error('Story book text generation failed to start.', [
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

    public function resumeTextGeneration(StoryBook $storyBook): array
    {
        if ($storyBook->isLocked()) {
            return $this->lockedResult();
        }

        if ($this->storyBookGeneratorStepService->isTextGenerationComplete($storyBook)) {
            return $this->textGenerationResult($storyBook);
        }

        if (blank($storyBook->title) || blank($storyBook->foundation)) {
            return [
                'story_book' => $storyBook,
                'status'     => 'error',
                'message'    => 'Story book foundation is missing. Please generate the foundation again.',
            ];
        }

        try {
            GenerateStoryBookTextJob::dispatchSync($storyBook->slug);

            return $this->textGenerationResult($storyBook);
        } catch (Exception $exception) {

            Log::error('Story book text generation failed to resume.', [
                'slug'      => $storyBook->slug,
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => $storyBook,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function stopTextGeneration(StoryBook $storyBook): array
    {
        if ($storyBook->isLocked()) {
            return $this->lockedResult();
        }

        $this->storyBookGeneratorStepService->stopTextGeneration($storyBook);

        return $this->textGenerationResult($storyBook);
    }

    public function startIllustrationGeneration(StoryBookIllustrationStart $request, StoryBook $storyBook): array
    {
        if ($storyBook->isLocked()) {
            return $this->lockedResult();
        }

        if (! $this->storyBookGeneratorStepService->isTextGenerationComplete($storyBook)) {
            return [
                'story_book' => $storyBook,
                'status'     => 'error',
                'message'    => 'Text generation must be completed before starting illustration generation.',
            ];
        }

        try {

            $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_illustration_id'));

            $this->assertAiBrainOutputType($aiBrain, self::AI_BRAIN_OUTPUT_TYPE_IMAGE);

            DB::transaction(function () use ($aiBrain, $storyBook) {
                $storyBook->ai_brain_illustration_id = $aiBrain->id;
                $storyBook->status = StoryBookHelper::STATUS_PROCESSING_ILLUSTRATION;
                $storyBook->error_message = null;
                $storyBook->stopped_at = null;
                $storyBook->illustration_generation_started_at = now();
                $storyBook->illustration_generation_completed_at = null;

                $storyBook->save();
            });

            GenerateStoryBookIllustrationJob::dispatchSync($storyBook->slug, $aiBrain->id);

            return $this->illustrationGenerationResult($storyBook);
        } catch (Exception $exception) {

            Log::error('Story book illustration generation failed to start.', [
                'exception' => $exception->getMessage(),
                'trace'     => $exception->getTraceAsString(),
            ]);

            return [
                'story_book' => $storyBook,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function stopIllustrationGeneration(StoryBook $storyBook): array
    {
        if ($storyBook->isLocked()) {
            return $this->lockedResult();
        }

        $storyBook->status        = StoryBookHelper::STATUS_STOP_ILLUSTRATION;
        $storyBook->stopped_at    = now();
        $storyBook->error_message = null;

        $storyBook->save();

        return $this->illustrationGenerationResult($storyBook);
    }

    public function progress(StoryBook $storyBook): array
    {
        return $this->storyBookGeneratorStepService->progress($storyBook);
    }

    public function runTextGeneration(string $slug): void
    {
        $storyBook = $this->storyBookService->find($slug);

        if ($storyBook->isLocked()) {
            return;
        }

        if ($this->storyBookGeneratorStepService->isTextGenerationComplete($storyBook)) {
            $this->storyBookGeneratorStepService->completeTextGeneration($storyBook);

            return;
        }

        $aiBrain = $this->aiBrainService->findById($storyBook->ai_brain_text_id);

        $this->assertAiBrainOutputType($aiBrain, self::AI_BRAIN_OUTPUT_TYPE_TEXT);

        $steps       = $this->storyBookGeneratorStepService->textSteps();
        $startNumber = $storyBook->completed_steps_count + 1;

        $this->storyBookGeneratorStepService->startTextGeneration($storyBook);

        foreach ($steps as $index => $step) {
            $number = $index + 1;

            if ($number < $startNumber) {
                continue;
            }

            $storyBook->refresh();

            if ($this->storyBookGeneratorStepService->isTextGenerationStopped($storyBook)) {
                return;
            }

            $this->storyBookGeneratorStepService->startStep($storyBook, $number);

            try {
                $this->generateTextStep($number, $step, $storyBook, $aiBrain);

                $this->storyBookGeneratorStepService->completeStep($storyBook, $number);
            } catch (Exception $exception) {

                $this->storyBookGeneratorStepService->failStep($storyBook, $number, $exception->getMessage());

                Log::error("Story book step {$number} failed.", [
                    'slug'      => $storyBook->slug,
                    'step'      => $step->name,
                    'exception' => $exception->getMessage(),
                    'trace'     => $exception->getTraceAsString(),
                ]);

                return;
            }
        }

        $this->storyBookGeneratorStepService->completeTextGeneration($storyBook);
    }

    public function runIllustrationGeneration(string $slug, int $aiBrainIllustrationId): void
    {
        $storyBook = $this->storyBookService->find($slug);

        if ($storyBook->isLocked()) {
            return;
        }

        $aiBrain = $this->aiBrainService->findById($aiBrainIllustrationId);

        $this->assertAiBrainOutputType($aiBrain, self::AI_BRAIN_OUTPUT_TYPE_IMAGE);

        $storyBook->completed_illustration_pages = 0;
        $storyBook->current_illustration_page = null;
        $storyBook->error_message = null;
        $storyBook->stopped_at = null;
        $storyBook->status = StoryBookHelper::STATUS_PROCESSING_ILLUSTRATION;

        $storyBook->save();

        foreach ($this->pendingIllustrationPages($storyBook) as $storyBookPage) {
            $storyBook->refresh();

            if ($storyBook->stopped_at !== null || $storyBook->status === StoryBookHelper::STATUS_STOP_ILLUSTRATION) {
                return;
            }

            $storyBook->current_illustration_page = $storyBookPage->no;
            $storyBook->save();

            try {
                $image = $this->generateIllustration($aiBrain, $storyBook, $storyBookPage);

                DB::transaction(function () use ($storyBookPage, $image) {
                    $this->storyBookPageService->replaceIllustrationImage($storyBookPage, $image);
                });

                $storyBook->completed_illustration_pages = $storyBook->completed_illustration_pages + 1;
                $storyBook->error_message = null;

                $storyBook->save();
            } catch (Exception $exception) {

                $storyBook->status        = StoryBookHelper::STATUS_STOP_ILLUSTRATION;
                $storyBook->error_message = $exception->getMessage();

                $storyBook->save();

                Log::error("Story book page {$storyBookPage->no} illustration failed.", [
                    'slug'      => $storyBook->slug,
                    'exception' => $exception->getMessage(),
                    'trace'     => $exception->getTraceAsString(),
                ]);

                return;
            }
        }

        $storyBook->status                              = StoryBookHelper::STATUS_COMPLETE;
        $storyBook->current_illustration_page           = null;
        $storyBook->illustration_generation_completed_at = now();

        $storyBook->save();
    }

    private function generateFoundation(StoryBookGenerate $request): object
    {
        $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_text_id'));

        $this->assertAiBrainOutputType($aiBrain, self::AI_BRAIN_OUTPUT_TYPE_TEXT);

        $aiPrompt = $this->aiPromptService->findByCode(
            Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP1)
        );

        $inputs = $this->huggingFaceApiService->step1InputsFormatter([
            'language_id'            => $request->input('language_id'),
            'audience_id'            => $request->input('audience_id'),
            'story_book_type_id'     => $request->input('story_book_type_id'),
            'genre_ids'              => $request->input('genre_ids'),
            'additional_information' => $request->input('additional_information', 'Auto'),
        ]);

        $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $inputs);

        $response = $this->huggingFaceApiService->sendPostRequest(
            $aiBrain->api_url,
            $aiBrain->api_key,
            $aiBrain->model,
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP1,
            $prompt,
            $aiBrain->max_output_tokens,
            $aiBrain->timeout_seconds
        );

        if (! $response['success']) {
            throw new Exception($response['message']);
        }

        $foundation = (array) $response['data'];

        $requiredKeys = [
            'title'      => 'title',
            'subtitle'   => 'subtitle',
            'foundation' => 'foundation',
        ];

        foreach ($requiredKeys as $key => $label) {
            if (blank($foundation[$key] ?? null)) {
                throw new Exception("Story foundation response is missing the {$label}. Please try again.");
            }
        }

        return (object) [
            'title'      => $foundation['title'],
            'subtitle'   => $foundation['subtitle'],
            'foundation' => $foundation['foundation'],
        ];
    }

    private function generateTextStep(int $number, StoryBookGeneratorStep $step, StoryBook $storyBook, AiBrain $aiBrain): void
    {
        $inputs = $this->textStepInputs($number, $storyBook);
        $prompt = AiPromptGeneratorHelper::generateFullPrompt($this->stepAiPrompt($step), $inputs);

        $response = $this->huggingFaceApiService->sendPostRequest(
            $aiBrain->api_url,
            $aiBrain->api_key,
            $aiBrain->model,
            $step->name,
            $prompt,
            $aiBrain->max_output_tokens,
            $aiBrain->timeout_seconds,
            $this->textStepData($number, $storyBook)
        );

        if (! $response['success']) {
            throw new Exception($response['message']);
        }

        $this->persistTextStepResult($number, $storyBook, (array) $response['data']);
    }

    private function persistTextStepResult(int $number, StoryBook $storyBook, array $data): void
    {
        DB::transaction(function () use ($number, $storyBook, $data) {
            if (isset(self::TEXT_STEP_COLUMNS[$number])) {
                $storyBook->{self::TEXT_STEP_COLUMNS[$number]} = $data;

                $storyBook->save();

                return;
            }

            if ($number === 14) {
                $this->storyBookPageService->syncStoryBookPages($storyBook, (array) ($data['pages'] ?? []));

                $storyBook->save();

                return;
            }

            if ($number === 15) {
                $illustrationType = $storyBook->illustrationType;

                if (! $illustrationType) {
                    throw new Exception('Story book illustration type is missing.');
                }

                $this->storyBookPageService->applyIllustrationPlanning(
                    $storyBook,
                    (array) ($data['pages'] ?? []),
                    $illustrationType->prompt_instruction
                );
            }
        });
    }

    private function generateIllustration(AiBrain $aiBrain, StoryBook $storyBook, StoryBookPage $storyBookPage): array
    {
        $storyBook->loadMissing('storyBookPages');

        $aiPrompt  = $this->aiPromptService->findByCode(
            Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_16)
        );

        $inputs = $this->huggingFaceApiService->step16InputsFormatter($storyBook, $storyBookPage);
        $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt->prompt, $inputs);

        return $this->huggingFaceApiService->sendImageRequest(
            $aiBrain->api_url,
            $aiBrain->api_key,
            $aiBrain->model,
            $prompt,
            $aiBrain->timeout_seconds
        );
    }

    private function pendingIllustrationPages(StoryBook $storyBook): Collection
    {
        return $storyBook->storyBookPages()
            ->with('illustrationImage')
            ->get()
            ->filter(fn(StoryBookPage $storyBookPage) => $storyBookPage->illustrationImage === null)
            ->sortBy('no')
            ->values();
    }

    private function textStepInputs(int $number, StoryBook $storyBook): array
    {
        $method = 'step' . $number . 'InputsFormatter';

        if (! method_exists($this->huggingFaceApiService, $method)) {
            throw new Exception("Step {$number} inputs are not supported.");
        }

        return $this->huggingFaceApiService->{$method}($storyBook);
    }

    private function textStepData(int $number, StoryBook $storyBook): array
    {
        if ($number !== 15) {
            return [];
        }

        $storyBook->loadMissing('storyBookPages');

        return [
            'existing_pages' => $storyBook->storyBookPages
                ->map(fn(StoryBookPage $storyBookPage) => [
                    'no'        => $storyBookPage->no,
                    'narration' => $storyBookPage->narration,
                ])->values()->all(),
        ];
    }

    private function stepAiPrompt(StoryBookGeneratorStep $step): string
    {
        $aiPrompt = $step->aiPrompt;

        if (! $aiPrompt instanceof AiPrompt) {
            throw new Exception("Step \"{$step->name}\" has no AI prompt configured.");
        }

        return $aiPrompt->prompt;
    }

    private function assertAiBrainOutputType(AiBrain $aiBrain, string $code): void
    {
        if (! $aiBrain->aiBrainOutputTypes->pluck('code')->contains($code)) {
            throw new Exception("Selected AI brain is not a {$code}-output AI brain.");
        }
    }

    private function textGenerationResult(StoryBook $storyBook): array
    {
        $storyBook->refresh();

        $message = match (true) {
            $storyBook->isLocked()                                => 'Story book is complete.',
            (bool) $storyBook->error_message                       => "Step {$storyBook->current_step} failed. {$storyBook->error_message}",
            $storyBook->status === StoryBookHelper::STATUS_STOP_TEXT => 'Story book text generation stopped.',
            default                                                => 'Story Book is created successfully, You can review first then start Illustration page.',
        };

        return [
            'story_book' => $storyBook,
            'progress'   => $this->progress($storyBook),
            'status'     => $storyBook->error_message ? 'error' : 'success',
            'message'    => $message,
        ];
    }

    private function illustrationGenerationResult(StoryBook $storyBook): array
    {
        $storyBook->refresh();

        $message = match (true) {
            $storyBook->isLocked()                                       => 'Story book is complete.',
            (bool) $storyBook->error_message                              => "Page {$storyBook->current_illustration_page} illustration failed. {$storyBook->error_message}",
            $storyBook->status === StoryBookHelper::STATUS_STOP_ILLUSTRATION => 'Story book illustration generation stopped.',
            default                                                       => 'Story book illustrations generated successfully.',
        };

        return [
            'story_book' => $storyBook,
            'progress'   => $this->progress($storyBook),
            'status'     => $storyBook->error_message ? 'error' : 'success',
            'message'    => $message,
        ];
    }

    private function lockedResult(): array
    {
        return [
            'story_book' => null,
            'status'     => 'error',
            'message'    => 'Story book is complete and can no longer be updated.',
        ];
    }
}
