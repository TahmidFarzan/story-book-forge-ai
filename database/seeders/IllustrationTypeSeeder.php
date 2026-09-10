<?php

namespace Database\Seeders;

use App\Helpers\SeederHelper;
use App\Models\IllustrationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IllustrationTypeSeeder extends Seeder
{
    public function run(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            IllustrationType::query()->delete();
            DB::statement("DELETE FROM sqlite_sequence WHERE name='illustration_types'");
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            IllustrationType::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            IllustrationType::truncate();
        }

        foreach (SeederHelper::illustrationTypes() as $illustrationType) {

            IllustrationType::factory()->state([
                'name' => $illustrationType->name,
                'brief' => $illustrationType->brief ?? null,
                'prompt_instruction' => $illustrationType->prompt_instruction,
            ])->create();

        }
    }
}