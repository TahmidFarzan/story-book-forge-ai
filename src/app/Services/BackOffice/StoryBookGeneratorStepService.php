<?php

namespace App\Services\BackOffice;

use App\Helpers\StoryBookHelper;
use App\Models\StoryBook;
use App\Models\StoryBookGeneratorStep;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class StoryBookGeneratorStepService
{
    public function new(): StoryBookGeneratorStep
    {
        return new StoryBookGeneratorStep;
    }

    public function find(string $slug): StoryBookGeneratorStep
    {
        return StoryBookGeneratorStep::with([
            'aiPrompt',
            'previousStep',
            'nextStep',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('slug', $slug)->firstOrFail();
    }

    public function findById(string|int $id): StoryBookGeneratorStep
    {
        return StoryBookGeneratorStep::with([
            'previousStep',
            'nextStep',
            'aiPrompt',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('id', $id)->firstOrFail();
    }

    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = StoryBookGeneratorStep::query()
            ->with(['aiPrompt', 'previousStep', 'nextStep']);

        if ($request->filled('date')) {
            $date = $request->input('date');
            $date = is_string($date) ? new \DateTime($date) : $date;
            $query->whereDate('created_at', '<=', $date);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $likeSearch = "%{$search}%";

            $query->where(function ($subQuery) use ($likeSearch) {
                $subQuery->where('name', 'like', $likeSearch)
                    ->orWhereHas('aiPrompt', fn ($promptQuery) => $promptQuery->where('name', 'like', $likeSearch));
            });
        }

        return $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());
    }

    public function stages(): Collection
    {
        return $this->orderedStages()
            ->take(StoryBookHelper::TOTAL_GENERATION_STAGE_COUNT)
            ->values();
    }

    public function textStages(): Collection
    {
        return $this->stages()
            ->take(StoryBookHelper::TEXT_GENERATION_STAGE_COUNT)
            ->values();
    }

    public function totalStages(): int
    {
        return max($this->stages()->count(), StoryBookHelper::TOTAL_GENERATION_STAGE_COUNT);
    }

    public function totalTextStages(): int
    {
        return max($this->textStages()->count(), StoryBookHelper::TEXT_GENERATION_STAGE_COUNT);
    }

    public function stageByNumber(int $number): ?StoryBookGeneratorStep
    {
        return $this->textStages()->get($number - 1);
    }

    public function startTextGeneration(StoryBook $storyBook): void
    {
        $storyBook->status                       = StoryBookHelper::STATUS_PROCESSING_TEXT;
        $storyBook->current_step                 = $storyBook->completed_steps_count + 1;
        $storyBook->error_message                = null;
        $storyBook->stopped_at                   = null;
        $storyBook->text_generation_started_at   = $storyBook->text_generation_started_at ?? now();
        $storyBook->text_generation_completed_at = null;

        $storyBook->save();
    }

    public function startStage(StoryBook $storyBook, int $number): void
    {
        $storyBook->current_step = $number;

        $storyBook->save();
    }

    public function completeStage(StoryBook $storyBook, int $number): void
    {
        $storyBook->current_step           = $number;
        $storyBook->completed_steps_count = max($storyBook->completed_steps_count, $number);
        $storyBook->error_message          = null;

        $storyBook->save();
    }

    public function failStage(StoryBook $storyBook, int $number, string $message): void
    {
        $storyBook->current_step  = $number;
        $storyBook->error_message = $message;

        $storyBook->save();
    }

    public function completeTextGeneration(StoryBook $storyBook): void
    {
        $storyBook->status                       = StoryBookHelper::STATUS_COMPLETE_TEXT;
        $storyBook->current_step                 = $this->totalTextStages();
        $storyBook->completed_steps_count        = $this->totalTextStages();
        $storyBook->error_message                = null;
        $storyBook->stopped_at                   = null;
        $storyBook->text_generation_completed_at = now();

        $storyBook->save();
    }

    public function stopTextGeneration(StoryBook $storyBook): void
    {
        $storyBook->status        = StoryBookHelper::STATUS_STOP_TEXT;
        $storyBook->stopped_at    = now();
        $storyBook->error_message = null;

        $storyBook->save();
    }

    public function isTextGenerationStopped(StoryBook $storyBook): bool
    {
        return $storyBook->stopped_at !== null
            || $storyBook->status === StoryBookHelper::STATUS_STOP_TEXT;
    }

    public function isTextGenerationComplete(StoryBook $storyBook): bool
    {
        return $storyBook->completed_steps_count >= $this->totalTextStages();
    }

    public function startFinalGeneration(StoryBook $storyBook): void
    {
        $storyBook->current_step                            = StoryBookHelper::FINAL_GENERATION_STAGE;
        $storyBook->completed_steps_count                   = max(
            $storyBook->completed_steps_count,
            $this->totalTextStages()
        );
        $storyBook->error_message                           = null;
        $storyBook->stopped_at                              = null;
        $storyBook->illustration_generation_started_at      = now();
        $storyBook->illustration_generation_completed_at    = null;

        $storyBook->save();
    }

    public function completeFinalGeneration(StoryBook $storyBook): void
    {
        $storyBook->status                               = StoryBookHelper::STATUS_COMPLETE;
        $storyBook->current_step                         = StoryBookHelper::FINAL_GENERATION_STAGE;
        $storyBook->completed_steps_count                = $this->totalStages();
        $storyBook->current_illustration_page             = null;
        $storyBook->error_message                        = null;
        $storyBook->stopped_at                           = null;
        $storyBook->illustration_generation_completed_at = now();

        $storyBook->save();
    }

    public function failFinalGeneration(StoryBook $storyBook, string $message): void
    {
        $storyBook->status        = StoryBookHelper::STATUS_STOP_ILLUSTRATION;
        $storyBook->error_message = $message;

        $storyBook->save();
    }

    public function stopFinalGeneration(StoryBook $storyBook): void
    {
        $storyBook->status        = StoryBookHelper::STATUS_STOP_ILLUSTRATION;
        $storyBook->stopped_at    = now();
        $storyBook->error_message = null;

        $storyBook->save();
    }

    public function progress(StoryBook $storyBook): array
    {
        $totalStages = $this->totalStages();

        $completedStages = min($storyBook->completed_steps_count, $totalStages);

        $currentStage = $storyBook->current_step ?: StoryBookHelper::FIRST_GENERATION_STAGE;

        return [
            'status'                  => $storyBook->status,
            'current_stage'           => $currentStage,
            'current_stage_name'      => $this->stages()->get($currentStage - 1)?->name,
            'completed_stages_count'  => $completedStages,
            'total_stages'            => $totalStages,
            'percentage'              => (int) round($completedStages / max($totalStages, 1) * 100),
            'error_message'           => $storyBook->error_message,
            'stopped_at'              => $storyBook->stopped_at?->toDateTimeString(),
            'text_generation_completed' => $this->isTextGenerationComplete($storyBook),
            'stages'                  => $this->stages()->map(fn(StoryBookGeneratorStep $stage, int $index) => [
                'number' => $index + 1,
                'name'   => $stage->name,
                'state'  => $this->stageState($storyBook, $index + 1),
            ])->values()->all(),
            'illustration'            => $this->illustrationProgress($storyBook),
        ];
    }

    private function orderedStages(): Collection
    {
        return StoryBookGeneratorStep::query()
            ->with('aiPrompt')
            ->orderBy('id')
            ->get();
    }

    private function stageState(StoryBook $storyBook, int $number): string
    {
        if ($number <= $storyBook->completed_steps_count) {
            return 'completed';
        }

        if ($number !== $storyBook->current_step) {
            return 'pending';
        }

        if ($storyBook->error_message) {
            return 'failed';
        }

        if (in_array($storyBook->status, [
            StoryBookHelper::STATUS_PROCESSING_TEXT,
            StoryBookHelper::STATUS_PROCESSING_ILLUSTRATION,
        ], true)) {
            return 'processing';
        }

        return 'stopped';
    }

    private function illustrationProgress(StoryBook $storyBook): array
    {
        $totalPages = $storyBook->storyBookPages()->count();

        $completedPages = min($storyBook->completed_illustration_pages, $totalPages);

        return [
            'total_pages'     => $totalPages,
            'completed_pages' => $completedPages,
            'current_page'    => $storyBook->current_illustration_page,
            'percentage'      => (int) round($completedPages / max($totalPages, 1) * 100),
        ];
    }
}
