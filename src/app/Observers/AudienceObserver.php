<?php
namespace App\Observers;

use App\Jobs\DeleteAudienceRelationsJob;
use App\Models\Audience;

class AudienceObserver
{
    public function deleting(Audience $audience): void
    {
        DeleteAudienceRelationsJob::dispatchSync($audience->id);
    }
}
