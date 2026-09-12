<?php
namespace App\Services\BackOffice;

use App\Models\AiBrain;
use Exception;
use App\Http\Requests\AiBrainRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AiBrainService
{
    public function new(): AiBrain
    {
        return new AiBrain();
    }

    public function find(string $slug): AiBrain
    {
        return AiBrain::with([
            'createdBy',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('slug', $slug)->firstOrFail();
    }

    public function findById(string|int $id): AiBrain
    {
        return AiBrain::with([
            'createdBy',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('id', $id)->firstOrFail();
    }

    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = AiBrain::query();

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
                'brief',
                'focus',
                'api_url',
            ], 'like', $likeSearch);
        }
        return $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());
    }


    public function save(AiBrainRequest $request, AiBrain $aiBrain): array
    {
        $isNew       = empty($aiBrain->id);
        $statusEvent = $isNew ? "save" : "update";

        try {

            DB::transaction(function () use ($request, $aiBrain, $isNew) {
                $aiBrain->name              = $request->input('name');
                $aiBrain->api_url           = $request->input('api_url');
                $aiBrain->api_key           = $request->input('api_key');
                $aiBrain->brief             = $request->input('brief');
                $aiBrain->focus             = $request->input('focus');
                $aiBrain->context_window    = $request->input('context_window');
                $aiBrain->average_latency   = $request->input('average_latency');
                $aiBrain->minimum_wait_time = $request->input('minimum_wait_time');
                $aiBrain->timeout_seconds   = $request->input('timeout_seconds');
                $aiBrain->max_output_tokens = $request->input('max_output_tokens');
                $aiBrain->created_by_id     = $isNew ? Auth::id() : $aiBrain->created_by_id;

                $aiBrain->save();
            });
            return [
                'status'  => 'success',
                'message' => $isNew ? 'Ai brain created successfully.' : 'Ai brain updated successfully.',
            ];
        } catch (Exception $exception) {
            Log::error("Failed to {$statusEvent} ai brain.", [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to save ai brain. Please try again.',
            ];
        }
    }

    public function delete(AiBrain $aiBrain): array
    {
        try {

            DB::transaction(function () use ($aiBrain) {
                $aiBrain->delete();
            });

            return [
                'status'  => 'success',
                'message' => 'Ai brain deleted successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Ai brain delete failed.', [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to delete ai brain. Please try again.',
            ];
        }
    }

}
