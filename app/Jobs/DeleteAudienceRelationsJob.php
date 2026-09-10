<?php
namespace App\Jobs;

use App\Models\Audience;
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

class DeleteAudienceRelationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $audienceId;

    public function __construct(int $audienceId)
    {
        $this->audienceId = $audienceId;
    }

    public function uniqueId(): string
    {
        return "delete-audience-{$this->audienceId}-relations";
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
        $audience = Audience::find($this->audienceId);

        if ($audience && ($audience->activityLogs()->exists())) {

            try {

                DB::transaction(function () use ($audience) {
                    if ($audience->activityLogs()->exists()) {
                        $audience->activityLogs()->delete();
                    }
                });

            } catch (Exception $ex) {
                Log::error("Fail to delete audience relations.", [
                    'exception' => $ex,
                ]);

                throw $ex;
            }
        }
    }
}
