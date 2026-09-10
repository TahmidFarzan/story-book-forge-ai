<?php
namespace App\Jobs;

use App\Models\IllustrationType;
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

class DeleteIllustrationTypeRelationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $illustrationTypeId;

    public function __construct(int $illustrationTypeId)
    {
        $this->illustrationTypeId = $illustrationTypeId;
    }

    public function uniqueId(): string
    {
        return "delete-illustration-type-{$this->illustrationTypeId}-relations";
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
        $illustrationType = IllustrationType::find($this->illustrationTypeId);

        if ($illustrationType && ($illustrationType->activityLogs()->exists())) {

            try {

                DB::transaction(function () use ($illustrationType) {
                    if ($illustrationType->activityLogs()->exists()) {
                        $illustrationType->activityLogs()->delete();
                    }
                });

            } catch (Exception $ex) {
                Log::error("Fail to delete illustration type relations.", [
                    'exception' => $ex,
                ]);

                throw $ex;
            }
        }
    }
}