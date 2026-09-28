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

            $firstGeneration = $this->generateFirstGeneration($request);

            $storyBook = DB::transaction(function () use ($request, $firstGeneration) {
                $storyBook                    = new StoryBook;
                $storyBook->title             = $firstGeneration->title;
                $storyBook->sub_title         = $firstGeneration->subtitle;
                $storyBook->foundation        = $firstGeneration->foundation;
                $storyBook->characters        = $firstGeneration->characters;
                $storyBook->world_bible       = $firstGeneration->world_bible;
                $storyBook->locations         = $firstGeneration->locations;
                $storyBook->factions          = $firstGeneration->factions;
                $storyBook->creatures         = $firstGeneration->creatures;
                $storyBook->systems           = $firstGeneration->systems;
                $storyBook->timeline          = $firstGeneration->timeline;
                $storyBook->audience_id       = $request->input('audience_id');
                $storyBook->language_id       = $request->input('language_id');
                $storyBook->story_book_type_id = $request->input('story_book_type_id');
                $storyBook->illustration_type_id = $request->input('illustration_type_id');
                $storyBook->ai_brain_text_id  = $request->input('ai_brain_text_id');
                $storyBook->additional_information = $request->input('additional_information');
                $storyBook->status            = StoryBookHelper::STATUS_PROCESSING_TEXT;
                $storyBook->current_step      = StoryBookHelper::FIRST_GENERATION_STAGE;
                $storyBook->completed_steps_count = StoryBookHelper::FIRST_GENERATION_STAGE;
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

        if (! $this->storyBookGeneratorStepService->isTextGenerationComplete($storyBook)) {
            $this->storyBookGeneratorStepService->stopTextGeneration($storyBook);
        }

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

                $storyBook->save();

                $this->storyBookGeneratorStepService->startFinalGeneration($storyBook);
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

        if (! $this->storyBookGeneratorStepService->isTextGenerationComplete($storyBook)) {
            return [
                'story_book' => $storyBook,
                'status'     => 'error',
                'message'    => 'Illustration generation can only be stopped after text generation is complete.',
            ];
        }

        $this->storyBookGeneratorStepService->stopFinalGeneration($storyBook);

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

        $stages      = $this->storyBookGeneratorStepService->textStages();
        $startNumber = $storyBook->completed_steps_count + 1;

        $this->storyBookGeneratorStepService->startTextGeneration($storyBook);

        foreach ($stages as $index => $stage) {
            $number = $index + 1;

            if ($number < $startNumber) {
                continue;
            }

            $storyBook->refresh();

            if ($this->storyBookGeneratorStepService->isTextGenerationStopped($storyBook)) {
                return;
            }

            $this->storyBookGeneratorStepService->startStage($storyBook, $number);

            try {
                $this->generateTextStage($number, $stage, $storyBook, $aiBrain);

                $this->storyBookGeneratorStepService->completeStage($storyBook, $number);
            } catch (Exception $exception) {

                $this->storyBookGeneratorStepService->failStage($storyBook, $number, $exception->getMessage());

                Log::error("Story book stage {$number} failed.", [
                    'slug'      => $storyBook->slug,
                    'stage'     => $stage->name,
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

        $pendingPages = $this->pendingIllustrationPages($storyBook);

        $storyBook->completed_illustration_pages = max(
            $storyBook->storyBookPages()->count() - $pendingPages->count(),
            0
        );
        $storyBook->current_illustration_page = null;

        $storyBook->save();

        $this->storyBookGeneratorStepService->startFinalGeneration($storyBook);

        foreach ($pendingPages as $storyBookPage) {
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

                $this->storyBookGeneratorStepService->failFinalGeneration($storyBook, $exception->getMessage());

                Log::error("Story book page {$storyBookPage->no} illustration failed.", [
                    'slug'      => $storyBook->slug,
                    'exception' => $exception->getMessage(),
                    'trace'     => $exception->getTraceAsString(),
                ]);

                return;
            }
        }

        $this->storyBookGeneratorStepService->completeFinalGeneration($storyBook);
    }

    private function generateFirstGeneration(StoryBookGenerate $request): object
    {
        $aiBrain = $this->aiBrainService->findById($request->input('ai_brain_text_id'));

        $this->assertAiBrainOutputType($aiBrain, self::AI_BRAIN_OUTPUT_TYPE_TEXT);

        $aiPrompt = $this->aiPromptService->findByCode(
            Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_FIRST_GENERATION)
        );

        $inputs = $this->huggingFaceApiService->firstGenerationInputsFormatter([
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
            AiPromptGeneratorHelper::AI_PROMPT_NAME_FIRST_GENERATION,
            $prompt,
            $aiBrain->max_output_tokens,
            $aiBrain->timeout_seconds
        );

        if (! $response['success']) {
            throw new Exception($response['message']);
        }

        $firstGeneration = (array) $response['data'];

        $requiredKeys = [
            'title'      => 'title',
            'subtitle'   => 'subtitle',
            'foundation' => 'foundation',
        ];

        foreach ($requiredKeys as $key => $label) {
            if (blank($firstGeneration[$key] ?? null)) {
                throw new Exception("Story foundation response is missing the {$label}. Please try again.");
            }
        }

        return (object) $firstGeneration;
    }

    private function generateTextStage(int $number, StoryBookGeneratorStep $stage, StoryBook $storyBook, AiBrain $aiBrain): void
    {
        if ($number !== StoryBookHelper::SECOND_GENERATION_STAGE) {
            throw new Exception("Story book stage {$number} cannot be generated for an existing story book.");
        }

        $aiPrompt = $this->stageAiPrompt($stage);

        $inputs = $this->huggingFaceApiService->secondGenerationInputsFormatter($storyBook);

        $prompt = AiPromptGeneratorHelper::generateFullPrompt($aiPrompt, $inputs);

        $response = $this->huggingFaceApiService->sendPostRequest(
            $aiBrain->api_url,
            $aiBrain->api_key,
            $aiBrain->model,
            $stage->name,
            $prompt,
            $aiBrain->max_output_tokens,
            $aiBrain->timeout_seconds
        );

        if (! $response['success']) {
            throw new Exception($response['message']);
        }

        $this->persistSecondGeneration($storyBook, (array) $response['data']);
    }

    private function persistSecondGeneration(StoryBook $storyBook, array $data): void
    {
        DB::transaction(function () use ($storyBook, $data) {
            $illustrationType = $storyBook->illustrationType;

            if (! $illustrationType) {
                throw new Exception('Story book illustration type is missing.');
            }

            $storyBook->story_structure          = $data['story_structure'] ?? null;
            $storyBook->twists_and_foreshadowing = $data['twists_and_foreshadowing'] ?? null;
            $storyBook->scene_plans              = $data['scene_plans'] ?? null;
            $storyBook->dialogue_plans           = $data['dialogue_plans'] ?? null;
            $storyBook->page_plan                = $data['page_plan'] ?? null;

            $storyBook->save();

            $this->storyBookPageService->syncGeneratedPages(
                $storyBook,
                (array) ($data['pages'] ?? []),
                $illustrationType->prompt_instruction
            );
        });
    }

    private function generateIllustration(AiBrain $aiBrain, StoryBook $storyBook, StoryBookPage $storyBookPage): array
    {
        $storyBook->loadMissing('storyBookPages');

        $aiPrompt = $this->aiPromptService->findByCode(
            Str::studly(AiPromptGeneratorHelper::AI_PROMPT_NAME_FINAL_GENERATION)
        );

        $inputs = $this->huggingFaceApiService->finalGenerationInputsFormatter($storyBook, $storyBookPage);

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

    private function stageAiPrompt(StoryBookGeneratorStep $stage): string
    {
        $aiPrompt = $stage->aiPrompt;

        if (! $aiPrompt instanceof AiPrompt) {
            throw new Exception("Stage \"{$stage->name}\" has no AI prompt configured.");
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
            (bool) $storyBook->error_message                       => "Stage {$storyBook->current_step} failed. {$storyBook->error_message}",
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
