<?php
namespace App\Services\BackOffice;

use App\Http\Requests\GenreRequest;
use App\Models\Genre;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenreService
{
    public function new (): Genre
    {
        return new Genre;
    }

    public function find(string $slug): Genre
    {
        return Genre::with([
            'createdBy',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('slug', $slug)->firstOrFail();
    }

    public function findByIdsOrRandom($ids = null)
    {
        if (empty($ids)) {
            return Genre::inRandomOrder()
                ->limit(rand(2, 3))
                ->get();
        }

        if (! is_array($ids)) {
            $ids = [$ids];
        }

        return Genre::whereIn('id', $ids)->get();
    }
    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = Genre::query();

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

    public function save(GenreRequest $request, Genre $genre): array
    {
        $isNew       = empty($genre->id);
        $statusEvent = $isNew ? 'save' : 'update';

        try {

            DB::transaction(function () use ($request, $genre, $isNew) {
                $genre->name               = $request->input('name');
                $genre->brief              = $request->input('brief');
                $genre->prompt_instruction = $request->input('prompt_instruction');
                $genre->created_by_id      = $isNew ? Auth::id() : $genre->created_by_id;

                $genre->save();
            });

            return [
                'status'  => 'success',
                'message' => $isNew ? 'Genre created successfully.' : 'Genre updated successfully.',
            ];
        } catch (Exception $exception) {
            Log::error("Failed to {$statusEvent} genre.", [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to save genre. Please try again.',
            ];
        }
    }

    public function delete(Genre $genre): array
    {

        try {

            DB::transaction(function () use ($genre) {
                $genre->delete();
            });

            return [
                'status'  => 'success',
                'message' => 'Genre deleted successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Genre delete failed.', [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to delete genre. Please try again.',
            ];
        }
    }
}
