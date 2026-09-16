<?php
namespace App\Services\BackOffice;

use App\Http\Requests\IllustrationTypeRequest;
use App\Models\IllustrationType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IllustrationTypeService
{
    public function new (): IllustrationType
    {
        return new IllustrationType;
    }

    public function find(string $slug): IllustrationType
    {
        return IllustrationType::with([
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

        $query = IllustrationType::query();

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
            ], 'like', $likeSearch);
        }

        return $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());
    }

    public function save(IllustrationTypeRequest $request, IllustrationType $illustrationType): array
    {
        $isNew       = empty($illustrationType->id);
        $statusEvent = $isNew ? 'save' : 'update';

        try {

            DB::transaction(function () use ($request, $illustrationType, $isNew) {
                $illustrationType->name               = $request->input('name');
                $illustrationType->brief              = $request->input('brief');
                $illustrationType->prompt_instruction = $request->input('prompt_instruction');
                $illustrationType->created_by_id      = $isNew ? Auth::id() : $illustrationType->created_by_id;

                $illustrationType->save();
            });

            return [
                'status'  => 'success',
                'message' => $isNew ? 'Illustration Type created successfully.' : 'Illustration Type updated successfully.',
            ];
        } catch (Exception $exception) {
            Log::error("Failed to {$statusEvent} illustration type.", [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to save illustration type. Please try again.',
            ];
        }
    }

    public function delete(IllustrationType $illustrationType): array
    {

        try {

            DB::transaction(function () use ($illustrationType) {
                $illustrationType->delete();
            });

            return [
                'status'  => 'success',
                'message' => 'Illustration Type deleted successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Illustration type delete failed.', [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to delete illustration type. Please try again.',
            ];
        }
    }
}