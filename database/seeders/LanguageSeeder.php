<?php

namespace Database\Seeders;

use App\Helpers\SeederHelper;
use App\Models\Language;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSeeder extends Seeder
{
    public function run(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            Language::query()->delete();
            DB::statement("DELETE FROM sqlite_sequence WHERE name='languages'");
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            Language::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            Language::truncate();
        }

        foreach (SeederHelper::languages() as $language) {

            Language::factory()->state([
                'name' => $language->name,
                'brief' => $language->brief ?? null,
            ])->create();

        }
    }
}