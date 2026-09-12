<?php
namespace App\Observers;


use App\Models\Story;
use Illuminate\Support\Str;
use App\Jobs\DeleteStoryRelationsJob;

class StoryObserver
{
    public function deleting(Story $story): void
    {
        DeleteStoryRelationsJob::dispatchSync($story->id);
    }
}
