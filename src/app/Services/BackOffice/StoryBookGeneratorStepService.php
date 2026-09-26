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

    public function textSteps(): Collection
    {
        return $this->orderedSteps()
            ->take(StoryBookHelper::TEXT_GENERATION_STEP_COUNT)
            ->values();
    }

    public function illustrationStep(): ?StoryBookGeneratorStep
    {
        return $this->orderedSteps()->get(StoryBookHelper::TEXT_GENERATION_STEP_COUNT);
    }

    public function totalTextSteps(): int
    {
        return $this->textSteps()->count();
    }

    public function stepByNumber(int $number): ?StoryBookGeneratorStep
    {
        return $this->textSteps()->get($number - 1);
    }

    public function startTextGeneration(StoryBook $storyBook): void
    {
        $storyBook->status                        = StoryBookHelper::STATUS_PROCESSING_TEXT;
        $storyBook->current_step                  = $storyBook->completed_steps_count + 1;
        $storyBook->error_message                 = null;
        $storyBook->stopped_at                    = null;
        $storyBook->text_generation_started_at    = $storyBook->text_generation_started_at ?? now();
        $storyBook->text_generation_completed_at  = null;

        $storyBook->save();
    }

    public function startStep(StoryBook $storyBook, int $number): void
    {
        $storyBook->current_step = $number;

        $storyBook->save();
    }

    public function completeStep(StoryBook $storyBook, int $number): void
    {
        $storyBook->current_step           = $number;
        $storyBook->completed_steps_count = max($storyBook->completed_steps_count, $number);
        $storyBook->error_message          = null;

        $storyBook->save();
    }

    public function failStep(StoryBook $storyBook, int $number, string $message): void
    {
        $storyBook->current_step  = $number;
        $storyBook->error_message = $message;

        $storyBook->save();
    }

    public function completeTextGeneration(StoryBook $storyBook): void
    {
        $storyBook->status                       = StoryBookHelper::STATUS_COMPLETE_TEXT;
        $storyBook->current_step                 = StoryBookHelper::TEXT_GENERATION_STEP_COUNT;
        $storyBook->completed_steps_count        = StoryBookHelper::TEXT_GENERATION_STEP_COUNT;
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

    public function resumeStep(StoryBook $storyBook): ?StoryBookGeneratorStep
    {
        $number = $storyBook->current_step ?: 1;

        return $this->stepByNumber(min($number, $this->totalTextSteps()));
    }

    public function isTextGenerationComplete(StoryBook $storyBook): bool
    {
        return $storyBook->completed_steps_count >= $this->totalTextSteps();
    }

    public function progress(StoryBook $storyBook): array
    {
        $totalSteps = $this->totalTextSteps();

        $completedSteps = min($storyBook->completed_steps_count, $totalSteps);

        return [
            'status'                => $storyBook->status,
            'current_step'          => $storyBook->current_step,
            'current_step_name'     => $this->stepByNumber($storyBook->current_step ?: 1)?->name,
            'completed_steps_count' => $completedSteps,
            'total_steps'           => $totalSteps,
            'percentage'            => (int) round($completedSteps / max($totalSteps, 1) * 100),
            'error_message'         => $storyBook->error_message,
            'stopped_at'            => $storyBook->stopped_at?->toDateTimeString(),
            'text_generation_completed' => $this->isTextGenerationComplete($storyBook),
            'steps'                 => $this->textSteps()->map(fn(StoryBookGeneratorStep $step, int $index) => [
                'number' => $index + 1,
                'name'   => $step->name,
                'state'  => $this->stepState($storyBook, $index + 1),
            ])->values()->all(),
            'illustration'          => $this->illustrationProgress($storyBook),
        ];
    }

    private function orderedSteps(): Collection
    {
        return StoryBookGeneratorStep::query()
            ->with('aiPrompt')
            ->orderBy('id')
            ->get();
    }

    private function stepState(StoryBook $storyBook, int $number): string
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

        if ($storyBook->status === StoryBookHelper::STATUS_PROCESSING_TEXT) {
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
