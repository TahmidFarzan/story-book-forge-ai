<?php
namespace App\Jobs;

use App\Models\AiBrain;
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

class DeleteAiBrainRelationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $aiBrainId;

    public function __construct(int $aiBrainId)
    {
        $this->aiBrainId = $aiBrainId;
    }

    public function uniqueId(): string
    {
        return "delete-ai-brain-{$this->aiBrainId}-relations";
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
        $aiBrain = AiBrain::find($this->aiBrainId);

        if ($aiBrain && ($aiBrain->activityLogs()->exists())) {

            try {

                DB::transaction(function () use ($aiBrain) {
                    if ($aiBrain->activityLogs()->exists()) {
                        $aiBrain->activityLogs()->delete();
                    }
                });

            } catch (Exception $ex) {
                Log::error("Fail to delete ai brain relations.", [
                    'exception' => $ex,
                ]);

                throw $ex;
            }
        }
    }
}
