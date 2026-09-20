<?php
namespace App\Observers;


use App\Jobs\DeleteStoryBookPageRelationsJob;
use App\Models\StoryBookPage;

class StoryBookPageObserver
{
    public function deleting(StoryBookPage $storyBookPage): void
    {
        DeleteStoryBookPageRelationsJob::dispatchSync($storyBookPage->id);
    }
}