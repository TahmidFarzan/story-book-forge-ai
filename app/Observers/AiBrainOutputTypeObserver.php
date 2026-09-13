<?php
namespace App\Observers;

use App\Models\AiBrainOutputType;
use Illuminate\Support\Str;

class AiBrainOutputTypeObserver
{
    public function creating(AiBrainOutputType $aiBrainOutputType): void
    {
        $aiBrainOutputType->code = Str::studly($aiBrainOutputType->name);
    }
}