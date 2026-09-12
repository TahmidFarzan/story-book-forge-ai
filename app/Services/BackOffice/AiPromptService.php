<?php
namespace App\Services\BackOffice;

use App\Http\Requests\AiPromptRequest;
use App\Models\AiPrompt;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AiPromptService
{
    public function new(): AiPrompt
    {
        return new AiPrompt();
    }

    public function find(string $slug): AiPrompt
    {
        return AiPrompt::with([
            'createdBy',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('slug', $slug)->firstOrFail();
    }

    public function findById(string|int $id): AiPrompt
    {
        return AiPrompt::with([
            'createdBy',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('id', $id)->firstOrFail();
    }

    public function findByStepNumber(string|int $stepNumber): AiPrompt
    {
        return AiPrompt::with([
            'createdBy',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('step_number', $stepNumber)->firstOrFail();
    }

    public function findByCode(string $code): AiPrompt
    {
        return AiPrompt::with([
            'createdBy',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('id', $code)->firstOrFail();
    }

    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = AiPrompt::query();

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
                'name',
                'prompt',
            ], 'like', $likeSearch);
        }
        return $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());
    }

    public function save(AiPromptRequest $request, AiPrompt $aiPrompt): array
    {
        $isNew       = empty($aiPrompt->id);
        $statusEvent = $isNew ? "save" : "update";

        try {

            DB::transaction(function () use ($request, $aiPrompt, $isNew) {
                $aiPrompt->prompt = $request->input('prompt');

                $aiPrompt->save();
            });
            return [
                'status'  => 'success',
                'message' => $isNew ? 'Ai prompt created successfully.' : 'Ai prompt updated successfully.',
            ];
        } catch (Exception $exception) {
            Log::error("Failed to {$statusEvent} ai prompt.", [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to save ai prompt. Please try again.',
            ];
        }
    }
}
