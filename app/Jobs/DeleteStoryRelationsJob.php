<?php
namespace App\Jobs;

use App\Models\Story;
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

class DeleteStoryRelationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $storyId;

    public function __construct(int $storyId)
    {
        $this->storyId = $storyId;
    }

    public function uniqueId(): string
    {
        return "delete-story-generator-{$this->storyId}-relations";
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
        $story = Story::find($this->storyId);

        if ($story && ($story->activityLogs()->exists() )) {

            try {
                DB::transaction(function () use ($story) {
                    if ($story->activityLogs()->exists()) {
                        $story->activityLogs()->delete();
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
