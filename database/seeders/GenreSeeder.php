<?php

namespace Database\Seeders;

use App\Helpers\SeederHelper;
use App\Models\Genre;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GenreSeeder extends Seeder
{
    public function run(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            Genre::query()->delete();
            DB::statement("DELETE FROM sqlite_sequence WHERE name='genres'");
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Genre::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            Genre::truncate();
        }

        foreach (SeederHelper::genres() as $genre) {

            Genre::factory()->state([
                'name' => $genre->name,
                'brief' => $genre->brief ?? null,
                'prompt_instruction' => $genre->prompt_instruction ?? null,
            ])->create();

        }
    }
}
