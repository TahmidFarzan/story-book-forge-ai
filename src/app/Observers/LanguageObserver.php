<?php
namespace App\Observers;

use App\Jobs\DeleteLanguageRelationsJob;
use App\Models\Language;

class LanguageObserver
{
    public function deleting(Language $language): void
    {
        DeleteLanguageRelationsJob::dispatchSync($language->id);
    }
}