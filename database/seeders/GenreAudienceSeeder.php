<?php

namespace Database\Seeders;

use App\Models\Audience;
use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class GenreAudienceSeeder extends Seeder
{
    public function run(): void
    {
        $this->resetPivot();

        $audienceIdsBySlug = Audience::pluck('id', 'slug');

        $genreAudienceSlugs = [
            'fantasy'              => ['children', 'young-adult', 'adult'],
            'dark-fantasy'         => ['young-adult', 'adult'],
            'magical'              => ['children', 'young-adult', 'adult'],
            'fairy-tale'           => ['children', 'young-adult'],
            'bedtime-story'        => ['children'],
            'fable'                => ['children', 'young-adult', 'adult'],
            'moral-story'          => ['children', 'young-adult', 'adult'],
            'animal-story'         => ['children', 'young-adult', 'adult'],
            'friendship'           => ['children', 'young-adult', 'adult'],
            'love-story'           => ['young-adult', 'adult'],
            'romance'              => ['young-adult', 'adult'],
            'family'               => ['children', 'young-adult', 'adult'],
            'school-story'         => ['children', 'young-adult'],
            'educational'          => ['children', 'young-adult'],
            'folklore'             => ['children', 'young-adult', 'adult'],
            'myth-legend'          => ['children', 'young-adult', 'adult'],
            'historical-fiction'   => ['young-adult', 'adult'],
            'drama'                => ['young-adult', 'adult'],
            'comedy'               => ['children', 'young-adult', 'adult'],
            'mystery'              => ['children', 'young-adult', 'adult'],
            'thriller'             => ['adult'],
            'horror'               => ['adult'],
            'science-fiction'      => ['children', 'young-adult', 'adult'],
            'adventure'            => ['children', 'young-adult', 'adult'],
            'psychological'        => ['adult'],
            'supernatural'         => ['young-adult', 'adult'],
            'crime'                => ['adult'],
            'political-fiction'    => ['adult'],
            'war'                  => ['adult'],
            'dystopian'            => ['young-adult', 'adult'],
            'coming-of-age'        => ['young-adult'],
        ];

        foreach ($genreAudienceSlugs as $genreSlug => $audienceSlugs) {
            $genre = Genre::where('slug', $genreSlug)->first();

            if (! $genre) {
                throw new RuntimeException("Genre '{$genreSlug}' not found.");
            }

            $audienceIds = [];

            foreach ($audienceSlugs as $audienceSlug) {
                if (! isset($audienceIdsBySlug[$audienceSlug])) {
                    throw new RuntimeException("Audience '{$audienceSlug}' not found.");
                }

                $audienceIds[] = $audienceIdsBySlug[$audienceSlug];
            }

            $genre->audiences()->sync(array_unique($audienceIds));
        }
    }

    private function resetPivot(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            DB::table('genre_audience')->delete();
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('genre_audience')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            DB::table('genre_audience')->truncate();
        }
    }
}