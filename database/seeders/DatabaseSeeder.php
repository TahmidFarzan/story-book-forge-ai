<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\AiPrompt;
use App\Models\Genre;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserPermissionSeeder::class);

        $this->call(UserSeeder::class);

        $this->call(GenreSeeder::class);
        $this->call(AudienceSeeder::class);
        $this->call(GenreAudienceSeeder::class);
        $this->call(LanguageSeeder::class);
        $this->call(StoryTypeSeeder::class);
        $this->call(IllustrationTypeSeeder::class);

        $this->call(AiBrainSeeder::class);

        $this->call(AiPromptSeeder::class);
    }
}
