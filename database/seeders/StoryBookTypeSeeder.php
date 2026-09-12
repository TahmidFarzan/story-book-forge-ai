<?php

namespace Database\Seeders;

use App\Helpers\SeederHelper;
use App\Models\StoryBookType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoryBookTypeSeeder extends Seeder
{
    public function run(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            StoryBookType::query()->delete();
            DB::statement("DELETE FROM sqlite_sequence WHERE name='story_book_types'");
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            StoryBookType::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            StoryBookType::truncate();
        }

        foreach (SeederHelper::storyBookTypes() as $storyBookType) {

            StoryBookType::factory()->state([
                'name' => $storyBookType->name,
                'brief' => $storyBookType->brief ?? null,
                'prompt_instruction' => $storyBookType->prompt_instruction,
            ])->create();

        }
    }
}
