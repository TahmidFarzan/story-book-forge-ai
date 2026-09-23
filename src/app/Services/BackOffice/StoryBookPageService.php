<?php
namespace App\Services\BackOffice;

use App\Helpers\MediaHelper;
use App\Models\StoryBook;
use App\Models\StoryBookPage;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class StoryBookPageService
{
    public function new (): StoryBookPage
    {
        return new StoryBookPage();
    }

    public function find(StoryBook $storyBook, string $slug): StoryBookPage
    {
        return StoryBookPage::with([
            'createdBy',

            'storyBook',

            'illustrationImage',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('story_book_id', $storyBook->id)->where('slug', $slug)->firstOrFail();
    }

    public function findByNo(StoryBook $storyBook, string | int $no): StoryBookPage
    {
        return StoryBookPage::with([
            'createdBy',

            'storyBook',

            'illustrationImage',

            'activityLogs' => fn($query) => $query->latest()->limit(10),
            'activityLogs.causer',

            'latestActivityLog',
            'latestActivityLog.causer',
        ])->where('story_book_id', $storyBook->id)->where('no', $no)->firstOrFail();
    }

    public function syncStoryBookPages(StoryBook $storyBook, array $apiResponce): void
    {
        $incomingPages = [];

        foreach ($apiResponce as $perRecordInResponce) {
            $no = (int) ($perRecordInResponce['no'] ?? null);

            $incomingPages[$no] = [
                'no'        => $no,
                'narration' => $perRecordInResponce['narration'] ?? null,
            ];
        }

        $existingPages = $storyBook->storyBookPages()->get()->keyBy('no');

        foreach ($incomingPages as $no => $incomingPage) {

            $storyBookPage = $storyBook->storyBookPages()->where('no', $no)->first();

            if (! $storyBookPage) {
                $storyBookPage                = $this->new();
                $storyBookPage->story_book_id = $storyBook->id;
                $storyBookPage->created_by_id = Auth::id();
            }

            $storyBookPage->no                                   = $no;
            $storyBookPage->narration                            = $incomingPage['narration'];
            $storyBookPage->illustration_prompt                  = null;
            $storyBookPage->illustration_type_prompt_instruction = null;
            $storyBookPage->save();

        }

        $missingPageNos = $existingPages->keys()->diff(array_keys($incomingPages));

        if ($missingPageNos->isNotEmpty()) {
            $storyBook->storyBookPages()->whereIn('no', $missingPageNos)->get()
                ->each(fn(StoryBookPage $storyBookPage) => $storyBookPage->delete());
        }
    }

    public function applyIllustrationPlanning(StoryBook $storyBook, array $plannedPages, string $illustrationTypePromptInstruction): void
    {
        $illustrationPrompts = [];

        foreach ($plannedPages as $plannedPage) {
            $no = (int) ($plannedPage['no'] ?? null);

            $illustrationPrompts[$no] = $plannedPage['illustration_prompt'] ?? null;
        }

        DB::transaction(function () use ($storyBook, $illustrationPrompts, $illustrationTypePromptInstruction) {
            $storyBook->storyBookPages()->whereIn('no', array_keys($illustrationPrompts))->get()
                ->each(function (StoryBookPage $storyBookPage) use ($illustrationPrompts, $illustrationTypePromptInstruction) {
                    $storyBookPage->illustration_prompt                  = $illustrationPrompts[$storyBookPage->no] ?? null;
                    $storyBookPage->illustration_type_prompt_instruction = $illustrationTypePromptInstruction;
                    $storyBookPage->save();
                });
        });
    }

    public function replaceIllustrationImage(StoryBookPage $storyBookPage, array $image): Media
    {
        $this->deleteExistingIllustrationImage($storyBookPage);

        return $this->storeStoryBookPageIllustration($storyBookPage, $image);
    }

    public function search(StoryBook $storyBook, Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $query = StoryBookPage::query();

        $query->where('story_book_id', $storyBook->id);

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
                'no',
                'narration',
            ], 'like', $likeSearch);
        }

        return $query->orderBy('no', 'asc')
            ->orderBy('id', 'asc')
            ->paginate($perPage)
            ->appends($request->all());
    }

    public function delete(StoryBook $storyBook, StoryBookPage $storyBookPage): array
    {
        try {
            DB::transaction(function () use ($storyBookPage) {
                $storyBookPage->delete();
            });

            return [
                'status'  => 'success',
                'message' => 'Story book page deleted successfully.',
            ];
        } catch (Exception $exception) {

            Log::error('Story book page delete failed.', [
                'exception' => $exception,
            ]);

            return [
                'status'  => 'error',
                'message' => 'Failed to delete story book page. Please try again.',
            ];
        }
    }

    private function deleteExistingIllustrationImage(StoryBookPage $storyBookPage): void
    {
        $storyBookPage->illustrationImage()->delete();
    }

    private function storeStoryBookPageIllustration(StoryBookPage $storyBookPage, array $image): Media
    {
        $mediaBaseName = "{$storyBookPage->storyBook->title} Page {$storyBookPage->no}";

        $mediaFileName = MediaHelper::generateMediaName($mediaBaseName, $image['extension'], 200);

        $alt = "{$storyBookPage->storyBook->title} Page {$storyBookPage->no}";

        $narration = Str::limit((string) ($storyBookPage->narration ?? ''), 200);

        $caption = $narration !== '' ? $narration : $alt;

        $customProperties = [
            'caption' => $caption,
            'alt'     => $alt,
            'role'    => MediaHelper::ROLE_STORY_BOOK_PAGE_ILLUSTRATION,
            'page_no' => $storyBookPage->no,
        ];

        if ($image['type'] === 'url') {
            $media = $storyBookPage
                ->addMediaFromUrl($image['url'])
                ->usingName($mediaBaseName)
                ->usingFileName($mediaFileName)
                ->withCustomProperties($customProperties)
                ->toMediaCollection($storyBookPage->media_collection_name);
        } else {
            $tempPath = tempnam(sys_get_temp_dir(), 'illustration_image_');

            file_put_contents(
                $tempPath,
                (string) (base64_decode($image['encoded'], true) ?: '')
            );

            try {
                $media = $storyBookPage
                    ->addMedia($tempPath)
                    ->usingName($mediaBaseName)
                    ->usingFileName($mediaFileName)
                    ->withCustomProperties($customProperties)
                    ->toMediaCollection($storyBookPage->media_collection_name);
            } finally {
                if (file_exists($tempPath)) {
                    unlink($tempPath);
                }
            }
        }

        $media->update([
            'order_column' => $storyBookPage->no,
        ]);

        return $media;
    }
}
