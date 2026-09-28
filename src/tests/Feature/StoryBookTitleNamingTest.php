<?php

namespace Tests\Feature;

use App\Helpers\AiPromptGeneratorHelper;
use App\Models\AiPrompt;
use App\Models\User;
use App\Services\BackOffice\HuggingFaceApiService;
use Database\Seeders\AiPromptSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StoryBookTitleNamingTest extends TestCase
{
    use RefreshDatabase;

    private const LEGACY_NAMES = [
        'story_book_title',
        'story_book_subtitle',
        'story_book_sub_title',
        'storyBookTitle',
        'storyBookSubtitle',
        'storyBookSubTitle',
        'story-book-title',
        'story-book-subtitle',
        'story-book-sub-title',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        User::factory()->create([
            'is_super_admin' => true,
        ]);

        $this->seed(AiPromptSeeder::class);
    }

    public function test_foundation_prompt_has_no_legacy_title_naming(): void
    {
        foreach (AiPrompt::all() as $prompt) {
            foreach (self::LEGACY_NAMES as $legacyName) {
                $this->assertStringNotContainsStringIgnoringCase(
                    $legacyName,
                    $prompt->prompt,
                    "AI prompt \"{$prompt->code}\" still uses the legacy name \"{$legacyName}\"."
                );
            }
        }
    }

    public function test_foundation_prompt_declares_title_and_sub_title_schema_keys(): void
    {
        $prompt = AiPrompt::where('code', 'Foundation')->value('prompt');

        $this->assertStringContainsString('"title": ""', $prompt);
        $this->assertStringContainsString('"sub_title": ""', $prompt);
    }

    public function test_foundation_prompt_placeholder_coverage_is_unchanged(): void
    {
        preg_match_all(
            '/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/',
            AiPrompt::where('code', 'Foundation')->value('prompt'),
            $matches
        );

        $this->assertEqualsCanonicalizing([
            'language',
            'genre_instructions',
            'audience_instruction',
            'story_book_type_instruction',
            'additional_information',
        ], array_values(array_unique($matches[1])));
    }

    public function test_first_generation_response_maps_title_and_sub_title(): void
    {
        Http::fake([
            '*/text' => Http::response([
                'id' => 'fake-id',
                'model' => 'fake-model',
                'choices' => [[
                    'index' => 0,
                    'finish_reason' => 'stop',
                    'message' => [
                        'role' => 'assistant',
                        'content' => (string) json_encode([
                            'title' => 'The Still Tide',
                            'sub_title' => 'A keeper unravels a tide that has stopped',
                            'story_book_foundation' => ['premise' => 'The sea stopped moving.'],
                            'characters' => ['characters' => []],
                            'world_overview' => 'A cold northern coast.',
                            'world_rules' => [],
                            'culture_and_history' => [],
                            'locations' => ['locations' => []],
                            'regions' => [],
                            'landmarks' => [],
                            'environment_details' => [],
                            'factions' => ['factions' => []],
                            'goals_and_values' => [],
                            'conflicts' => [],
                            'alliances' => [],
                            'power_balance' => [],
                            'creatures' => ['creatures' => []],
                            'abilities' => [],
                            'behaviors' => [],
                            'ecosystem_role' => [],
                            'systems' => ['systems' => []],
                            'mechanics' => [],
                            'limitations' => [],
                            'rules' => [],
                            'timeline' => ['major_events' => []],
                            'historical_flow' => [],
                        ]),
                    ],
                ]],
                'usage' => ['prompt_tokens' => 1, 'completion_tokens' => 1, 'total_tokens' => 2],
            ]),
        ]);

        $response = app(HuggingFaceApiService::class)->sendPostRequest(
            'https://fake.test/text',
            'fake-key',
            'fake-model',
            AiPromptGeneratorHelper::AI_PROMPT_NAME_FIRST_GENERATION,
            'prompt body',
            null,
            30
        );

        $this->assertTrue($response['success'], $response['message']);

        $data = (array) $response['data'];

        $this->assertSame('The Still Tide', $data['title']);
        $this->assertSame('A keeper unravels a tide that has stopped', $data['sub_title']);

        $this->assertArrayNotHasKey('subtitle', $data);
        $this->assertArrayNotHasKey('story_book_title', $data);
        $this->assertArrayNotHasKey('story_book_subtitle', $data);
    }
}
