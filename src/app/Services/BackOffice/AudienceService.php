<?php

namespace App\Services\BackOffice;

use App\Http\Requests\AudienceRequest;
use App\Models\Audience;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AudienceService
{
    public function new(): Audience
    {
        return new Audience;
    }

    public function find(string $slug): Audience
    {
        return Audience::with([
            'genres',

            'createdBy',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('slug', $slug)->firstOrFail();
    }

    public function findById(string|int $id): Audience
    {
        return Audience::where('id', $id)->firstOrFail();
    }

    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = Audience::query();

        if ($request->filled('created_by_id')) {
            $query->where('created_by_id', $request->input('created_by_id'));
        }

        if ($request->filled('date')) {
            $date = $request->input('date');
            $date = is_string($date) ? new \DateTime($date) : $date;
            $query->whereDate('created_at', '<=', $date);
        }

        if ($request->filled('genre_id')) {
            $query->whereHas(
                'genres',
                fn($query) => $query->where('genres.id', $request->input('genre_id'))
            );
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

    public function save(AudienceRequest $request, Audience $audience): array
    {
        $isNew       = empty($audience->id);
        $statusEvent = $isNew ? 'save' : 'update';

        try {

            DB::transaction(function () use ($request, $audience, $isNew) {
                $audience->name               = $request->input('name');
                $audience->brief              = $request->input('brief');
                $audience->prompt_instruction = $request->input('prompt_instruction');
                $audience->created_by_id      = $isNew ? Auth::id() : $audience->created_by_id;

                $audience->save();

                if ($request->has('genre_ids')) {
                    $audience->genres()->sync((array) $request->input('genre_ids', []));
                }
            });

            return [
                'status'  => 'success',
                'message' => $isNew ? 'Audience created successfully.' : 'Audience updated successfully.',
            ];
        } catch (Exception $exception) {
            Log::error("Failed to {$statusEvent} audience.", [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to save audience. Please try again.',
            ];
        }
    }

    public function delete(Audience $audience): array
    {

        try {

            DB::transaction(function () use ($audience) {
                $audience->delete();
            });

            return [
                'status'  => 'success',
                'message' => 'Audience deleted successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Audience delete failed.', [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to delete audience. Please try again.',
            ];
        }
    }
}
