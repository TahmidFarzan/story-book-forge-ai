<?php

namespace Database\Seeders;

use App\Helpers\SeederHelper;
use App\Models\Audience;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AudienceSeeder extends Seeder
{
    public function run(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            Audience::query()->delete();
            DB::statement("DELETE FROM sqlite_sequence WHERE name='audiences'");
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Audience::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            Audience::truncate();
        }

        foreach (SeederHelper::audiences() as $audience) {

            Audience::factory()->state([
                'name' => $audience->name,
                'brief' => $audience->brief ?? null,
                'prompt_instruction' => $audience->prompt_instruction ?? null,
            ])->create();

        }
    }
}
