<?php
namespace App\Observers;

use App\Jobs\DeleteStoryTypeRelationsJob;
use App\Models\StoryType;

class StoryTypeObserver
{
    public function deleting(StoryType $storyType): void
    {
        DeleteStoryTypeRelationsJob::dispatchSync($storyType->id);
    }
}