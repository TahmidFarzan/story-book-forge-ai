<?php
namespace App\Jobs;

use App\Models\AiPrompt;
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

class DeleteAiPromptRelationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $aiPromptId;

    public function __construct(int $aiPromptId)
    {
        $this->aiPromptId = $aiPromptId;
    }

    public function uniqueId(): string
    {
        return "delete-ai-brain-{$this->aiPromptId}-relations";
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
        $aiPrompt = AiPrompt::find($this->aiPromptId);

        if ($aiPrompt && ($aiPrompt->activityLogs()->exists())) {

            try {

                DB::transaction(function () use ($aiPrompt) {
                    if ($aiPrompt->activityLogs()->exists()) {
                        $aiPrompt->activityLogs()->delete();
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
