<?php

namespace App\Services\BackOffice;

use App\Models\StoryBookGeneratorStep;
use Illuminate\Http\Request;

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

            'activityLogs' => fn ($query) => $query->latest()->limit(10),
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

            'activityLogs' => fn ($query) => $query->latest()->limit(10),
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
}
