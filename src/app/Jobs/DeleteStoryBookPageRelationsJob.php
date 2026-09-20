<?php

namespace App\Jobs;

use App\Models\StoryBookPage;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DeleteStoryBookPageRelationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $storyBookPageId;

    public function __construct(int $storyBookPageId)
    {
        $this->storyBookPageId = $storyBookPageId;
    }

    public function uniqueId(): string
    {
        return "delete-story-book-page-{$this->storyBookPageId}-relations";
    }

    public function retryAfter()
    {
        return 60;
    }

    public function backoff()
    {
        return [61, 123, 185];
    }

    public function handle(): void
    {
        $storyBookPage = StoryBookPage::find($this->storyBookPageId);

        if (! $storyBookPage) {
            return;
        }

        try {
            DB::transaction(function () use ($storyBookPage) {

                if ($storyBookPage->activityLogs()->exists()) {
                    $storyBookPage->activityLogs()->delete();
                }

                if ($storyBookPage->getMedia($storyBookPage->media_collection_name)->count() > 0) {
                    $storyBookPage->clearMediaCollection($storyBookPage->media_collection_name);
                }
            });
        } catch (Exception $ex) {
            Log::error("Fail to delete story book page relations.", [
                'exception' => $ex,
            ]);

            throw $ex;
        }
    }
}
