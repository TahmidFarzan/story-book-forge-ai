<?php
namespace Database\Seeders;

use App\Helpers\SeederHelper;
use App\Models\AiBrain;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AiBrainSeeder extends Seeder
{
    public function run(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            AiBrain::query()->delete();
            DB::statement("DELETE FROM sqlite_sequence WHERE name='ai_brains'");
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            AiBrain::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            AiBrain::truncate();
        }

        foreach (SeederHelper::aiBrains() as $aiBrain) {
            AiBrain::factory()->state([
                'name'              => $aiBrain->name,
                'model'              => $aiBrain->model,
                'api_url'           => $aiBrain->api_url,
                'api_key'           => $aiBrain->api_key,
                'brief'             => $aiBrain->brief ?? null,
                'focus'             => $aiBrain->focus ?? null,
                'context_window'    => $aiBrain->context_window,
                'average_latency'   => $aiBrain->average_latency,
                'minimum_wait_time' => $aiBrain->minimum_wait_time,
                'timeout_seconds'   => $aiBrain->timeout_seconds,
                'max_output_tokens' => $aiBrain->max_output_tokens ?? null,
            ])->create();
        }
    }
}
