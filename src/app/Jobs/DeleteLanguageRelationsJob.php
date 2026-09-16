<?php
namespace App\Jobs;

use App\Models\Language;
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

class DeleteLanguageRelationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $languageId;

    public function __construct(int $languageId)
    {
        $this->languageId = $languageId;
    }

    public function uniqueId(): string
    {
        return "delete-language-{$this->languageId}-relations";
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
        $language = Language::find($this->languageId);

        if ($language && ($language->activityLogs()->exists())) {

            try {

                DB::transaction(function () use ($language) {
                    if ($language->activityLogs()->exists()) {
                        $language->activityLogs()->delete();
                    }
                });

            } catch (Exception $ex) {
                Log::error("Fail to delete language relations.", [
                    'exception' => $ex,
                ]);

                throw $ex;
            }
        }
    }
}