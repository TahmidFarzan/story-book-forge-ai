<?php
namespace App\Observers;

use App\Jobs\DeleteIllustrationTypeRelationsJob;
use App\Models\IllustrationType;

class IllustrationTypeObserver
{
    public function deleting(IllustrationType $illustrationType): void
    {
        DeleteIllustrationTypeRelationsJob::dispatchSync($illustrationType->id);
    }
}