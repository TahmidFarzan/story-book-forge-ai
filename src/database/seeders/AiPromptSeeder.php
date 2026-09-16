<?php
namespace Database\Seeders;

use App\Helpers\SeederHelper;
use App\Models\AiPrompt;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AiPromptSeeder extends Seeder
{
    public function run(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            AiPrompt::query()->delete();
            DB::statement("DELETE FROM sqlite_sequence WHERE name='ai_prompts'");
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            AiPrompt::truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            AiPrompt::truncate();
        }

        foreach (SeederHelper::aiPrompts() as $aiPrompt) {

            AiPrompt::factory()->state([
                'name'             => $aiPrompt->name,
                'code'             => $aiPrompt->code,
                'prompt'           => $aiPrompt->prompt,
                'step_number'      => $aiPrompt->step_number,
                'depend_on_prompt_ids' => $aiPrompt->depend_on_prompt_ids,
            ])->create();

        }
    }
}
