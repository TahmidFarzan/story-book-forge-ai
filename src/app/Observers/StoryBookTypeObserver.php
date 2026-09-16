<?php
namespace App\Observers;

use App\Jobs\DeleteStoryBookTypeRelationsJob;
use App\Models\StoryBookType;

class StoryBookTypeObserver
{
    public function deleting(StoryBookType $storyBookType): void
    {
        DeleteStoryBookTypeRelationsJob::dispatchSync($storyBookType->id);
    }
}