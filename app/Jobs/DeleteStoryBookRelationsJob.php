<?php
namespace App\Jobs;

use App\Models\StoryBook;
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

class DeleteStoryBookRelationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $storyBookId;

    public function __construct(int $storyBookId)
    {
        $this->storyBookId = $storyBookId;
    }

    public function uniqueId(): string
    {
        return "delete-story-book-{$this->storyBookId}-relations";
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
        $storyBook = StoryBook::find($this->storyBookId);

        if ($storyBook && ($storyBook->activityLogs()->exists() )) {

            try {
                DB::transaction(function () use ($storyBook) {
                    if ($storyBook->activityLogs()->exists()) {
                        $storyBook->activityLogs()->delete();
                    }
                });

            } catch (Exception $ex) {
                Log::error("Fail to delete story relations.", [
                    'exception' => $ex,
                ]);

                throw $ex;
            }
        }
    }
}