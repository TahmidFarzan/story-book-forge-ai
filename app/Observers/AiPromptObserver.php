<?php
namespace App\Observers;

use App\Models\AiPrompt;
use Illuminate\Support\Str;
use App\Jobs\DeleteAiPromptRelationsJob;

class AiPromptObserver
{
    public function creating(AiPrompt $aiPrompt): void
    {
        $aiPrompt->code = Str::studly($aiPrompt->name);
    }

        public function deleting(AiPrompt $aiPrompt): void
    {
        DeleteAiPromptRelationsJob::dispatchSync($aiPrompt->id);
    }
}
