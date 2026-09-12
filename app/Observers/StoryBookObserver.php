<?php
namespace App\Observers;


use App\Models\StoryBook;
use Illuminate\Support\Str;
use App\Jobs\DeleteStoryBookRelationsJob;

class StoryBookObserver
{
    public function deleting(StoryBook $storyBook): void
    {
        DeleteStoryBookRelationsJob::dispatchSync($storyBook->id);
    }
}