<?php

namespace Tests\Feature;

use App\Helpers\UserPermissionHelper;
use App\Models\AiPrompt;
use App\Models\StoryBookGeneratorStep;
use App\Models\User;
use App\Models\UserPermission;
use Database\Seeders\AiPromptSeeder;
use Database\Seeders\StoryBookGeneratorStepSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Tests\TestCase;

class StoryBookGeneratorStepTest extends TestCase
{
    use RefreshDatabase;

    private User $superAdmin;

    private User $normalUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'is_super_admin' => true,
        ]);

        $this->normalUser = User::factory()->create([
            'is_super_admin' => false,
        ]);
    }

    private function seedPrompt(string $code = 'FoundationGenerator'): AiPrompt
    {
        return AiPrompt::factory()->create([
            'code' => $code,
            'created_by_id' => $this->superAdmin->id,
        ]);
    }

    public function test_model_can_be_created_with_relationships(): void
    {
        $prompt = $this->seedPrompt();

        $this->actingAs($this->superAdmin);

        $first = StoryBookGeneratorStep::factory()->create([
            'name' => 'Foundation Generator',
            'ai_prompt_id' => $prompt->id,
        ]);

        $second = StoryBookGeneratorStep::factory()->create([
            'name' => 'Character Generator',
            'ai_prompt_id' => $prompt->id,
            'previous_step_id' => $first->id,
            'depend_on_step_ids' => [$first->id],
        ]);

        $this->assertDatabaseHas('story_book_generator_steps', ['name' => 'Foundation Generator']);
        $this->assertDatabaseHas('story_book_generator_steps', ['name' => 'Character Generator']);

        $this->assertNotNull($first->slug);
        $this->assertSame('Foundation Generator', $second->previousStep->name);

        $first->update(['next_step_id' => $second->id]);
        $this->assertSame('Character Generator', $first->nextStep->name);

        $this->assertSame(['id' => $prompt->id], $second->aiPrompt->only('id'));
        $this->assertSame([$first->id], $second->depend_on_step_ids);
    }

    public function test_depend_on_step_ids_is_cast_to_array(): void
    {
        $prompt = $this->seedPrompt();

        $step = StoryBookGeneratorStep::factory()->create([
            'name' => 'World Bible Generator',
            'ai_prompt_id' => $prompt->id,
            'depend_on_step_ids' => [1, 2],
        ]);

        $fresh = $step->fresh();

        $this->assertIsArray($fresh->depend_on_step_ids);
        $this->assertSame([1, 2], $fresh->depend_on_step_ids);
    }

    public function test_policy_denies_create_update_delete_even_for_super_admin(): void
    {
        $this->actingAs($this->superAdmin);

        $prompt = $this->seedPrompt();
        $step = StoryBookGeneratorStep::factory()->create([
            'name' => 'Character Generator',
            'ai_prompt_id' => $prompt->id,
        ]);

        $this->assertFalse(Gate::allows('create', StoryBookGeneratorStep::class));
        $this->assertFalse(Gate::allows('update', $step));
        $this->assertFalse(Gate::allows('delete', $step));
    }

    public function test_policy_allows_super_admin_to_view(): void
    {
        $this->actingAs($this->superAdmin);

        $this->assertTrue(Gate::allows('viewAny', StoryBookGeneratorStep::class));

        $prompt = $this->seedPrompt();
        $step = StoryBookGeneratorStep::factory()->create([
            'name' => 'Foundation Generator',
            'ai_prompt_id' => $prompt->id,
        ]);

        $this->assertTrue(Gate::allows('view', $step));
    }

    public function test_policy_requires_permission_for_normal_user(): void
    {
        $this->actingAs($this->normalUser);

        $prompt = $this->seedPrompt();
        $step = StoryBookGeneratorStep::factory()->create([
            'name' => 'Foundation Generator',
            'ai_prompt_id' => $prompt->id,
        ]);

        $this->assertFalse(Gate::allows('viewAny', StoryBookGeneratorStep::class));
        $this->assertFalse(Gate::allows('view', $step));

        $permission = UserPermission::factory()->create([
            'module' => UserPermissionHelper::MODULE_STORY_BOOK_GENERATOR_STEP,
            'access' => UserPermissionHelper::ACCESS_VIEW_ANY,
        ]);

        $this->normalUser->userPermissions()->attach($permission);

        $this->assertTrue(Gate::allows('viewAny', StoryBookGeneratorStep::class));
    }

    public function test_seeder_creates_three_linked_stages(): void
    {
        $this->seed([
            AiPromptSeeder::class,
            StoryBookGeneratorStepSeeder::class,
        ]);

        $this->assertDatabaseCount('story_book_generator_steps', 3);

        $stages = StoryBookGeneratorStep::orderBy('id')->get()->values();

        $this->assertSame([
            'Story Foundation Generator',
            'Story Detail Generator',
            'Page Illustration Generator',
        ], $stages->pluck('name')->all());

        $this->assertNull($stages->first()->previousStep);
        $this->assertNull($stages->last()->nextStep);

        foreach ($stages as $index => $stage) {
            if ($index > 0) {
                $this->assertSame($stages[$index - 1]->id, $stage->previousStep->id);
            }

            if ($index < $stages->count() - 1) {
                $this->assertSame($stages[$index + 1]->id, $stage->nextStep->id);
            }

            $this->assertNotNull($stage->aiPrompt);
            $this->assertNotNull($stage->dependOnSteps());

            foreach ($stage->depend_on_step_ids ?? [] as $dependencyId) {
                $this->assertNotSame($stage->id, $dependencyId);
                $this->assertLessThan($stage->id, $dependencyId);
            }
        }
    }

    public function test_seeder_dependencies_match_prompt_consumption(): void
    {
        $this->seed([
            AiPromptSeeder::class,
            StoryBookGeneratorStepSeeder::class,
        ]);

        $stages = StoryBookGeneratorStep::orderBy('id')->get()->values();

        $this->assertNull($stages[0]->depend_on_step_ids);

        $this->assertSame([$stages[0]->id], $stages[1]->depend_on_step_ids);

        $this->assertSame([
            $stages[0]->id,
            $stages[1]->id,
        ], $stages[2]->depend_on_step_ids);
    }

    public function test_seeder_dependency_codes_all_resolve(): void
    {
        $this->seed([
            AiPromptSeeder::class,
            StoryBookGeneratorStepSeeder::class,
        ]);

        $stages = StoryBookGeneratorStep::orderBy('id')->get()->values();

        $stageCodes = collect($stages)
            ->keyBy(fn (StoryBookGeneratorStep $stage) => Str::studly($stage->name));

        foreach ($stages as $stage) {
            foreach ($stage->depend_on_step_ids ?? [] as $dependencyId) {
                $this->assertTrue(
                    $stages->contains(fn (StoryBookGeneratorStep $candidate) => $candidate->id === $dependencyId)
                );
            }

            $this->assertTrue($stageCodes->has(Str::studly($stage->name)));
        }
    }

    public function test_seeder_is_idempotent(): void
    {
        $this->seed([
            AiPromptSeeder::class,
            StoryBookGeneratorStepSeeder::class,
        ]);

        $firstIds = StoryBookGeneratorStep::orderBy('id')->pluck('id')->all();

        $this->seed([
            StoryBookGeneratorStepSeeder::class,
        ]);

        $secondIds = StoryBookGeneratorStep::orderBy('id')->pluck('id')->all();

        $this->assertSame($firstIds, $secondIds);
        $this->assertDatabaseCount('story_book_generator_steps', 3);
    }

    public function test_seeder_creates_three_prompts_with_matching_codes(): void
    {
        $this->seed([
            AiPromptSeeder::class,
            StoryBookGeneratorStepSeeder::class,
        ]);

        $this->assertDatabaseCount('ai_prompts', 3);

        $prompts = AiPrompt::orderBy('id')->get()->values();

        $this->assertSame([
            'StoryFoundationGenerator',
            'StoryDetailGenerator',
            'PageIllustrationGenerator',
        ], $prompts->pluck('code')->all());

        $this->assertSame([1, 2, 3], $prompts->pluck('step_number')->all());

        $stages = StoryBookGeneratorStep::orderBy('id')->get()->values();

        foreach ($stages as $index => $stage) {
            $this->assertSame($prompts[$index]->id, $stage->aiPrompt->id);
        }
    }

    public function test_seeded_prompt_placeholders_match_generation_stages(): void
    {
        $this->seed([
            AiPromptSeeder::class,
        ]);

        $placeholders = static function (string $prompt): array {
            preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', $prompt, $matches);

            return array_values(array_unique($matches[1]));
        };

        $first = $placeholders(AiPrompt::where('code', 'StoryFoundationGenerator')->value('prompt'));
        $second = $placeholders(AiPrompt::where('code', 'StoryDetailGenerator')->value('prompt'));
        $final = $placeholders(AiPrompt::where('code', 'PageIllustrationGenerator')->value('prompt'));

        $this->assertEqualsCanonicalizing([
            'language',
            'genre_instructions',
            'audience_instruction',
            'story_book_type_instruction',
            'additional_information',
        ], $first);

        $this->assertEqualsCanonicalizing([
            'language',
            'genre_instructions',
            'audience_instruction',
            'story_book_type_instruction',
            'foundation',
            'characters',
            'world_bible',
            'locations',
            'factions',
            'creatures',
            'systems',
            'timeline',
        ], $second);

        $this->assertEqualsCanonicalizing([
            'illustration_type_prompt_instruction',
            'page',
            'foundation',
            'characters',
            'world_bible',
            'locations',
        ], $final);
    }
}
