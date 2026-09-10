<?php
namespace App\Jobs;

use App\Models\Genre;
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

class DeleteGenreRelationsJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, IsMonitored;

    public int $genreId;

    public function __construct(int $genreId)
    {
        $this->genreId = $genreId;
    }

    public function uniqueId(): string
    {
        return "delete-genre-{$this->genreId}-relations";
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
        $genre = Genre::find($this->genreId);

        if ($genre && ($genre->activityLogs()->exists() || $genre->audiences()->exists())) {

            try {

                DB::transaction(function () use ($genre) {
                    if ($genre->activityLogs()->exists()) {
                        $genre->activityLogs()->delete();
                    }

                    if ($genre->audiences()->exists()) {
                        $genre->audiences()->detach();
                    }
                });

            } catch (Exception $ex) {
                Log::error("Fail to delete genre relations.", [
                    'exception' => $ex,
                ]);

                throw $ex;
            }
        }
    }
}
