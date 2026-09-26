<?php

namespace App\Jobs;

use App\Services\BackOffice\StoryBookGeneratorService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateStoryBookTextJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public string $slug)
    {
    }

    public function handle(StoryBookGeneratorService $storyBookGeneratorService): void
    {
        $storyBookGeneratorService->runTextGeneration($this->slug);
    }
}
