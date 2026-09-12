<?php
namespace App\Jobs;

use App\Models\StoryBookType;
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

class DeleteStoryBookTypeRelationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $storyBookTypeId;

    public function __construct(int $storyBookTypeId)
    {
        $this->storyBookTypeId = $storyBookTypeId;
    }

    public function uniqueId(): string
    {
        return "delete-story-book-type-{$this->storyBookTypeId}-relations";
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
        $storyBookType = StoryBookType::find($this->storyBookTypeId);

        if ($storyBookType && ($storyBookType->activityLogs()->exists())) {

            try {

                DB::transaction(function () use ($storyBookType) {
                    if ($storyBookType->activityLogs()->exists()) {
                        $storyBookType->activityLogs()->delete();
                    }
                });

            } catch (Exception $ex) {
                Log::error("Fail to delete story book type relations.", [
                    'exception' => $ex,
                ]);

                throw $ex;
            }
        }
    }
}