<?php

namespace Database\Seeders;

use App\Models\AiBrain;
use App\Models\AiBrainOutputType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AiBrainOutputTypeRelationshipSeeder extends Seeder
{
    public function run(): void
    {
        $this->resetPivot();

        $outputTypeIdsBySlug = AiBrainOutputType::pluck('id', 'slug');

        $brainOutputTypeSlugs = [
            'google-gemma-4-26b-a4b'              => ['text'],
            'qwen-qwen3-8b'                       => ['text'],
            'mistral-ai-mistral-small-32-24b-instruct' => ['text'],
            'krea-krea-2-turbo'                   => ['image'],
            'black-forest-labs-flux1-schnell'     => ['image'],
            'stability-ai-stable-diffusion-xl-base-10' => ['image'],
        ];

        foreach ($brainOutputTypeSlugs as $brainSlug => $outputTypeSlugs) {
            $brain = AiBrain::where('slug', $brainSlug)->first();

            if (! $brain) {
                throw new RuntimeException("AiBrain '{$brainSlug}' not found.");
            }

            $outputTypeIds = [];

            foreach ($outputTypeSlugs as $outputTypeSlug) {
                if (! isset($outputTypeIdsBySlug[$outputTypeSlug])) {
                    throw new RuntimeException("AiBrainOutputType '{$outputTypeSlug}' not found.");
                }

                $outputTypeIds[] = $outputTypeIdsBySlug[$outputTypeSlug];
            }

            $brain->aiBrainOutputTypes()->sync(array_unique($outputTypeIds));
        }
    }

    private function resetPivot(): void
    {
        if (config('database.default') === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
            DB::table('ai_brain_ai_brain_output_type')->delete();
            DB::statement('PRAGMA foreign_keys = ON;');
        }

        if (config('database.default') === 'mysql') {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('ai_brain_ai_brain_output_type')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        if (in_array(config('database.default'), ['pgsql', 'sqlsrv'])) {
            DB::table('ai_brain_ai_brain_output_type')->truncate();
        }
    }
}
