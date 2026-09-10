<?php
namespace App\Jobs;

use App\Models\StoryType;
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

class DeleteStoryTypeRelationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $storyTypeId;

    public function __construct(int $storyTypeId)
    {
        $this->storyTypeId = $storyTypeId;
    }

    public function uniqueId(): string
    {
        return "delete-story-type-{$this->storyTypeId}-relations";
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
        $storyType = StoryType::find($this->storyTypeId);

        if ($storyType && ($storyType->activityLogs()->exists())) {

            try {

                DB::transaction(function () use ($storyType) {
                    if ($storyType->activityLogs()->exists()) {
                        $storyType->activityLogs()->delete();
                    }
                });

            } catch (Exception $ex) {
                Log::error("Fail to delete story type relations.", [
                    'exception' => $ex,
                ]);

                throw $ex;
            }
        }
    }
}