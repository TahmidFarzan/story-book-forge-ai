<?php
namespace App\Services\BackOffice;

use App\Http\Requests\StoryBookGenerate;
use App\Http\Requests\StoryBookIllustrationStart;
use App\Models\StoryBook;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoryBookService
{
    public function new (): StoryBook
    {
        return new StoryBook;
    }

    public function find(string $slug): StoryBook
    {
        return StoryBook::with([
            'language',
            'storyBookType',
            'audience',
            'genres',
            'illustrationType',
            'aiBrainText',
            'aiBrainIllustration',

            'storyBookPages' => fn($query) => $query->orderBy('no', 'asc'),
            'storyBookPages.illustrationImage',

            'createdBy',

            'activityLogs'   => fn($query)   => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('slug', $slug)->firstOrFail();
    }

    public function search(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = StoryBook::query();

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
                'title',
                'sub_title',
            ], 'like', $likeSearch);
        }

        if ($request->filled('genre_id')) {
            $query->whereHas(
                'genres',
                fn($query) => $query->where('genres.id', $request->input('genre_id'))
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        return $query->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->all());
    }

    public function save(StoryBookGenerate $request, StoryBook $storyBook): array
    {
        if ($storyBook->isLocked()) {
            return [
                'status'  => 'error',
                'message' => 'Story book is complete and can no longer be updated.',
            ];
        }

        try {
            DB::transaction(function () use ($request, $storyBook) {
                $storyBook->audience_id               = $request->input('audience_id');
                $storyBook->language_id               = $request->input('language_id');
                $storyBook->story_book_type_id        = $request->input('story_book_type_id');
                $storyBook->illustration_type_id      = $request->input('illustration_type_id');
                $storyBook->ai_brain_text_id          = $request->input('ai_brain_text_id');
                $storyBook->additional_information    = $request->input('additional_information');

                $storyBook->save();

                $storyBook->genres()->sync((array) $request->input('genre_ids', []));
            });

            return [
                'story_book' => $storyBook,
                'status'     => 'success',
                'message'    => 'Story book updated successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Story book update failed.', [
                'exception' => $exception->getMessage(),
            ]);

            return [
                'story_book' => null,
                'status'     => 'error',
                'message'    => $exception->getMessage(),
            ];
        }
    }

    public function delete(StoryBook $storyBook): array
    {

        try {

            DB::transaction(function () use ($storyBook) {
                $storyBook->delete();
            });

            return [
                'status'  => 'success',
                'message' => 'Story deleted successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Story delete failed.', [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to delete story. Please try again.',
            ];
        }
    }

    public function startTextGeneration(StoryBookGenerate $request): array
    {
        return $this->storyBookGeneratorService()->startTextGeneration($request);
    }

    public function resumeTextGeneration(StoryBook $storyBook): array
    {
        return $this->storyBookGeneratorService()->resumeTextGeneration($storyBook);
    }

    public function stopTextGeneration(StoryBook $storyBook): array
    {
        return $this->storyBookGeneratorService()->stopTextGeneration($storyBook);
    }

    public function startIllustrationGeneration(StoryBookIllustrationStart $request, StoryBook $storyBook): array
    {
        return $this->storyBookGeneratorService()->startIllustrationGeneration($request, $storyBook);
    }

    public function stopIllustrationGeneration(StoryBook $storyBook): array
    {
        return $this->storyBookGeneratorService()->stopIllustrationGeneration($storyBook);
    }

    public function progress(StoryBook $storyBook): array
    {
        return $this->storyBookGeneratorService()->progress($storyBook);
    }

    private function storyBookGeneratorService(): StoryBookGeneratorService
    {
        return app(StoryBookGeneratorService::class);
    }
}
