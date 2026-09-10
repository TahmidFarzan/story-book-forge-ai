<?php
namespace App\Observers;


use App\Models\Genre;
use Illuminate\Support\Str;
use App\Jobs\DeleteGenreRelationsJob;

class GenreObserver
{
    public function deleting(Genre $genre): void
    {
        DeleteGenreRelationsJob::dispatchSync($genre->id);
    }
}
