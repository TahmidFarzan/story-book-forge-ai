<?php
namespace App\Observers;


use App\Models\AiBrain;
use Illuminate\Support\Str;
use App\Jobs\DeleteAiBrainRelationsJob;

class AiBrainObserver
{
    public function deleting(AiBrain $aiBrain): void
    {
        DeleteAiBrainRelationsJob::dispatchSync($aiBrain->id);
    }
}
