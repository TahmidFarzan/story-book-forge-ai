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

    public function test_seeder_creates_sixteen_linked_steps(): void
    {
        $this->seed([
            AiPromptSeeder::class,
            StoryBookGeneratorStepSeeder::class,
        ]);

        $this->assertDatabaseCount('story_book_generator_steps', 16);

        $steps = StoryBookGeneratorStep::orderByDesc('id')->get()->reverse()->values();

        $this->assertNull($steps->first()->previousStep);
        $this->assertNull($steps->last()->nextStep);

        foreach ($steps as $index => $step) {
            if ($index > 0) {
                $this->assertSame($steps[$index - 1]->id, $step->previousStep->id);
            }

            if ($index < $steps->count() - 1) {
                $this->assertSame($steps[$index + 1]->id, $step->nextStep->id);
            }

            $this->assertNotNull($step->aiPrompt);
            $this->assertNotNull($step->dependOnSteps());

            foreach ($step->depend_on_step_ids ?? [] as $dependencyId) {
                $this->assertNotSame($step->id, $dependencyId);
                $this->assertLessThan($step->id, $dependencyId);
            }
        }
    }

    public function test_seeder_dependencies_match_prompt_consumption(): void
    {
        $this->seed([
            AiPromptSeeder::class,
            StoryBookGeneratorStepSeeder::class,
        ]);

        $steps = StoryBookGeneratorStep::orderByDesc('id')->get()->reverse()->values();

        $this->assertNull($steps[0]->depend_on_step_ids);

        $this->assertSame([$steps[0]->id], $steps[1]->depend_on_step_ids);

        $this->assertSame([
            $steps[0]->id,
            $steps[1]->id,
            $steps[2]->id,
        ], $steps[3]->depend_on_step_ids);

        $this->assertSame([
            $steps[0]->id,
            $steps[1]->id,
            $steps[2]->id,
            $steps[8]->id,
            $steps[9]->id,
            $steps[10]->id,
            $steps[11]->id,
            $steps[12]->id,
        ], $steps[13]->depend_on_step_ids);

        $this->assertSame(
            collect($steps)->take(14)->pluck('id')->values()->all(),
            $steps[14]->depend_on_step_ids
        );

        $this->assertSame([
            $steps[0]->id,
            $steps[1]->id,
            $steps[2]->id,
            $steps[3]->id,
            $steps[13]->id,
            $steps[14]->id,
        ], $steps[15]->depend_on_step_ids);
    }

    public function test_seeder_dependency_codes_all_resolve(): void
    {
        $this->seed([
            AiPromptSeeder::class,
            StoryBookGeneratorStepSeeder::class,
        ]);

        $steps = StoryBookGeneratorStep::orderByDesc('id')->get()->reverse()->values();

        $stepCodes = collect($steps)
            ->keyBy(fn (StoryBookGeneratorStep $step) => Str::studly($step->name));

        foreach ($steps as $step) {
            foreach ($step->depend_on_step_ids ?? [] as $dependencyId) {
                $this->assertTrue(
                    $steps->contains(fn (StoryBookGeneratorStep $candidate) => $candidate->id === $dependencyId)
                );
            }

            $this->assertTrue($stepCodes->has(Str::studly($step->name)));
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
        $this->assertDatabaseCount('story_book_generator_steps', 16);
    }
}
