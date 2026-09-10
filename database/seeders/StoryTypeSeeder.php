<?php

namespace Database\Seeders;

use App\Helpers\SeederHelper;
use App\Models\StoryType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StoryTypeSeeder extends Seeder
{
    public function run(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            StoryType::query()->delete();
            DB::statement("DELETE FROM sqlite_sequence WHERE name='story_types'");
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            StoryType::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            StoryType::truncate();
        }

        foreach (SeederHelper::storyTypes() as $storyType) {

            StoryType::factory()->state([
                'name' => $storyType->name,
                'brief' => $storyType->brief ?? null,
                'prompt_instruction' => $storyType->prompt_instruction,
            ])->create();

        }
    }
}