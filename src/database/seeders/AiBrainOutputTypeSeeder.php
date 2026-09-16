<?php
namespace Database\Seeders;

use App\Helpers\SeederHelper;
use App\Models\AiBrainOutputType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AiBrainOutputTypeSeeder extends Seeder
{
    public function run(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            AiBrainOutputType::query()->delete();
            DB::statement("DELETE FROM sqlite_sequence WHERE name='ai_brain_output_types'");
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            AiBrainOutputType::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            AiBrainOutputType::truncate();
        }

        foreach (SeederHelper::aiBrainOutputTypes() as $aiBrainOutputType) {
            AiBrainOutputType::factory()->state([
                'name'  => $aiBrainOutputType->name,
                'brief' => $aiBrainOutputType->brief,
            ])->create();
        }
    }
}