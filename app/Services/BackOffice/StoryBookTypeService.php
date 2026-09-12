<?php
namespace App\Services\BackOffice;

use App\Http\Requests\StoryBookTypeRequest;
use App\Models\StoryBookType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoryBookTypeService
{
    public function new (): StoryBookType
    {
        return new StoryBookType;
    }

    public function find(string $slug): StoryBookType
    {
        return StoryBookType::with([
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

        $query = StoryBookType::query();

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

    public function save(StoryBookTypeRequest $request, StoryBookType $storyBookType): array
    {
        $isNew       = empty($storyBookType->id);
        $statusEvent = $isNew ? 'save' : 'update';

        try {

            DB::transaction(function () use ($request, $storyBookType, $isNew) {
                $storyBookType->name               = $request->input('name');
                $storyBookType->brief              = $request->input('brief');
                $storyBookType->prompt_instruction = $request->input('prompt_instruction');
                $storyBookType->created_by_id      = $isNew ? Auth::id() : $storyBookType->created_by_id;

                $storyBookType->save();
            });

            return [
                'status'  => 'success',
                'message' => $isNew ? 'Story Type created successfully.' : 'Story Type updated successfully.',
            ];
        } catch (Exception $exception) {
            Log::error("Failed to {$statusEvent} story type.", [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to save story type. Please try again.',
            ];
        }
    }

    public function delete(StoryBookType $storyBookType): array
    {

        try {

            DB::transaction(function () use ($storyBookType) {
                $storyBookType->delete();
            });

            return [
                'status'  => 'success',
                'message' => 'Story Type deleted successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Story type delete failed.', [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to delete story type. Please try again.',
            ];
        }
    }
}