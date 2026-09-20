<?php
namespace App\Services\BackOffice;

use App\Models\StoryBook;
use App\Helpers\AiPromptGeneratorHelper;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HuggingFaceApiService
{
    protected AudienceService $audienceService;

    protected GenreService $genreService;

    protected LanguageService $languageService;

    protected StoryBookTypeService $storyBookTypeService;

    protected int $defaultTimeout = 120;

    public function __construct(AudienceService $audienceService, GenreService $genreService, LanguageService $languageService, StoryBookTypeService $storyBookTypeService)
    {
        $this->audienceService = $audienceService;
        $this->genreService = $genreService;
        $this->languageService = $languageService;
        $this->storyBookTypeService = $storyBookTypeService;
    }

    public function sendPostRequest(string $url, string $apiKey, string $model, mixed $data = null, ?int $maxOutputTokens = null, ?int $timeout = null, string $stepName = '', array $stepData = []): array
    {
        $requestTimeout = $timeout ?? $this->defaultTimeout;

        set_time_limit($requestTimeout);

        $payload = $this->buildPayload($model,$data,$maxOutputTokens);

        $endpoint = rtrim($url, '/');

        try {
            $response = Http::timeout($requestTimeout)
            ->withToken($apiKey)
            ->acceptJson()
            ->post($endpoint, $payload);
        } catch (Exception $exception) {
            return $this->formatErrorResponse(
                'Hugging Face API request failed: ' . $exception->getMessage()
            );
        }

        return $this->formatAIResponse($response, $stepName, $stepData);
    }

    private function formatAIResponse($response, string $stepName = '', array $stepData = []): array
    {
        if (! $response->successful()) {
            return $this->formatErrorResponse(
                $this->formatApiErrorResponse($response)
            );
        }

        try {
            $apiResponse = $this->parseJsonResponse($response);

            $data = $this->decodeAiResponseContent($apiResponse, $stepName);

            $data = $this->formatAIResponseByStep($stepName, $data, $stepData);
        } catch (Exception $exception) {
            return $this->formatErrorResponse(
                $exception->getMessage()
            );
        }

        return $this->formatSuccessResponse($data);
    }

    private function formatSuccessResponse(mixed $data): array
    {
        return [
            'success' => true,
            'message' => 'AI response generated successfully',
            'data'    => $data,
        ];
    }

    private function formatErrorResponse(string $message): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data'    => null,
        ];
    }

    private function formatAIResponseByStep(string $stepName, array $response, array $stepData = []): object
    {
        switch ($stepName) {
            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP1:
                return $this->formatStep1FoundationResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP2:
                return $this->formatStep2CharactersResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP3:
                return $this->formatStep3WorldVibeResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP4:
                return $this->formatStep4LocationsResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP5:
                return $this->formatStep5FactionsResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP6:
                return $this->formatStep6CreaturesResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP7:
                return $this->formatStep7SystemResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP8:
                return $this->formatStep8TimelineResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP9:
                return $this->formatStep9StoryStructureResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP10:
                return $this->formatStep10TwistsAndForeshadowingResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP11:
                return $this->formatStep11ScenePlanResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP12:
                return $this->formatStep12DialoguePlanResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP13:
                return $this->formatStep13PagePlanResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_14:
                return $this->formatStep14PagesResponse($response);

            case AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_15:
                return $this->formatStep15IllustrationPlanningResponse($response, $stepData);

            default:
                return (object) $response;
        }
    }

    private function formatStep1FoundationResponse(array $response): object
    {
        return (object) [
            'title' => $response['story_book_title'] ?? null,
            'subtitle' => $response['story_book_subtitle'] ?? null,
            'foundation' => $response['story_book_foundation'] ?? null,
        ];
    }

    private function formatStep2CharactersResponse(array $response): object
    {
        return (object) [
            'characters' => $response['characters'] ?? [],
            'relationship_dynamics' => $response['relationship_dynamics'] ?? [],
        ];
    }

    private function formatStep3WorldVibeResponse(array $response): object
    {
        return (object) [
            'world_overview' => $response['world_overview'] ?? [],
            'world_rules' => $response['world_rules'] ?? [],
            'culture_and_history' => $response['culture_and_history'] ?? [],
            'lore' => $response['lore'] ?? [],
        ];
    }

    private function formatStep4LocationsResponse(array $response): object
    {
        return (object) [
            'locations' => $response['locations'] ?? [],
            'regions' => $response['regions'] ?? [],
            'landmarks' => $response['landmarks'] ?? [],
            'environment_details' => $response['environment_details'] ?? [],
        ];
    }

    private function formatStep5FactionsResponse(array $response): object
    {
        return (object) [
            'factions' => $response['factions'] ?? [],
            'goals_and_values' => $response['goals_and_values'] ?? [],
            'conflicts' => $response['conflicts'] ?? [],
            'alliances' => $response['alliances'] ?? [],
        ];
    }

    private function formatStep6CreaturesResponse(array $response): object
    {
        return (object) [
            'creatures' => $response['creatures'] ?? [],
            'abilities' => $response['abilities'] ?? [],
            'behaviors' => $response['behaviors'] ?? [],
            'ecosystem_role' => $response['ecosystem_role'] ?? [],
        ];
    }

    private function formatStep7SystemResponse(array $response): object
    {
        return (object) [
            'systems' => $response['systems'] ?? [],
            'mechanics' => $response['mechanics'] ?? [],
            'limitations' => $response['limitations'] ?? [],
            'rules' => $response['rules'] ?? [],
        ];
    }

    private function formatStep8TimelineResponse(array $response): object
    {
        return (object) [
            'timeline' => $response['timeline'] ?? [],
            'major_events' => $response['major_events'] ?? [],
            'milestones' => $response['milestones'] ?? [],
            'historical_flow' => $response['historical_flow'] ?? [],
        ];
    }

    private function formatStep9StoryStructureResponse(array $response): object
    {
        return (object) [
            'story_outline' => $response['story_outline'] ?? [],
            'acts_and_chapters' => $response['acts_and_chapters'] ?? [],
            'plot_progression' => $response['plot_progression'] ?? [],
            'pacing_guide' => $response['pacing_guide'] ?? [],
        ];
    }

    private function formatStep10TwistsAndForeshadowingResponse(array $response): object
    {
        return (object) [
            'twists' => $response['twists'] ?? [],
            'foreshadowing' => $response['foreshadowing'] ?? [],
            'hidden_clues' => $response['hidden_clues'] ?? [],
            'reveal_points' => $response['reveal_points'] ?? [],
        ];
    }

    private function formatStep11ScenePlanResponse(array $response): object
    {
        return (object) [
            'scene_list' => $response['scene_list'] ?? [],
            'scene_objectives' => $response['scene_objectives'] ?? [],
            'locations' => $response['locations'] ?? [],
            'pov_and_tone' => $response['pov_and_tone'] ?? [],
        ];
    }

    private function formatStep12DialoguePlanResponse(array $response): object
    {
        return (object) [
            'dialogue_bank' => $response['dialogue_bank'] ?? [],
            'character_voice' => $response['character_voice'] ?? [],
            'conversation_flow' => $response['conversation_flow'] ?? [],
            'key_dialogues' => $response['key_dialogues'] ?? [],
        ];
    }

    private function formatStep13PagePlanResponse(array $response): object
    {
        return (object) [
            'page_layout' => $response['page_layout'] ?? [],
            'page_descriptions' => $response['page_descriptions'] ?? [],
            'illustration_notes' => $response['illustration_notes'] ?? [],
            'key_points' => $response['key_points'] ?? [],
        ];
    }

    private function formatStep14PagesResponse(array $response): object
    {
        $pages = $response['pages'] ?? [];

        if (! is_array($pages)) {
            $pages = [];
        }

        $normalizedPages = [];

        foreach ($pages as $index => $page) {
            if (! is_array($page)) {
                continue;
            }

            $normalizedPages[] = [
                'no' => (int) ($page['no'] ?? $index + 1),
                'narration' => $page['narration'] ?? null,
                'illustration_type_prompt_instruction' => null,
                'illustration_prompt' => null,
            ];
        }

        usort(
            $normalizedPages,
            fn (array $a, array $b) => $a['no'] <=> $b['no']
        );

        return (object) ['pages' => $normalizedPages];
    }

    private function formatStep15IllustrationPlanningResponse(array $response, array $stepData = []): object
    {
        $pages = $response['pages'] ?? null;

        if (! is_array($pages)) {
            throw new Exception('AI response does not contain a valid pages array.');
        }

        $existingByNo = [];

        foreach ($stepData['existing_pages'] ?? [] as $existingPage) {
            if (! is_array($existingPage)) {
                continue;
            }

            $existingByNo[(int) ($existingPage['no'] ?? null)] = true;
        }

        $expectedCount = count($existingByNo);

        if ($expectedCount === 0) {
            throw new Exception('Story book has no pages to plan illustrations for.');
        }

        $result = [];
        $seenNos = [];

        foreach ($pages as $page) {
            if (! is_array($page)) {
                throw new Exception('AI response contains an invalid page object.');
            }

            $no = $page['no'] ?? null;

            if (! is_numeric($no)) {
                throw new Exception('AI response contains an invalid page number.');
            }

            $no = (int) $no;

            if (! isset($existingByNo[$no])) {
                throw new Exception("AI response contains a page number that does not exist: {$no}.");
            }

            if (isset($seenNos[$no])) {
                throw new Exception("AI response contains a duplicate page number: {$no}.");
            }

            $seenNos[$no] = true;

            $illustrationPrompt = $page['illustration_prompt'] ?? null;

            if (! is_string($illustrationPrompt) || trim($illustrationPrompt) === '') {
                throw new Exception("AI response contains an empty illustration prompt for page {$no}.");
            }

            $result[$no] = [
                'no' => $no,
                'illustration_prompt' => $illustrationPrompt,
            ];
        }

        if (count($result) !== $expectedCount) {
            throw new Exception('AI response does not return every expected page.');
        }

        ksort($result);

        return (object) ['pages' => array_values($result)];
    }

    public function formatRequestInputs(StoryBook $storyBook, string $stepName, array $inputs): array
    {
        return match ($stepName) {
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP1 => $this->step1FoundationRequestInputsFormatter($storyBook,$inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP2 => $this->step2CharactersRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP3 => $this->step3WorldVibeRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP4 => $this->step4LocationsRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP5 => $this->step5FactionsRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP6 => $this->step6CreatureRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP7 => $this->step7SystemRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP8 => $this->step8TimelineRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP9 => $this->step9StoryStructureRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP10 => $this->step10TwistsAndForeshadowingRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP11 => $this->step11ScenePlanRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP12 => $this->step12DialoguePlanRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP13 => $this->step13PagePlanRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_14 => $this->step14PageNarrationRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_15 => $this->step15IllustrationPlanningRequestInputsFormatter($storyBook, $inputs),

            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP_16 => $this->step16PageIllustrationRequestInputsFormatter($storyBook, $inputs),
        };
    }

    private function step1FoundationRequestInputsFormatter(StoryBook $storyBook, array $inputs): array
    {
        $requestInputs = [];

        $language = $this->languageService->findByIdsOrEnglish($inputs['language_id'] ?? null);
        $audience = $this->audienceService->findById($inputs['audience_id'] ?? null);
        $storyBookType = $this->storyBookTypeService->findById($inputs['story_book_type_id'] ?? null);
        $genres = $this->genreService->findByIdsOrRandom($inputs['genre_ids'] ?? []);

        $genrePromptInstruction = '';
        foreach ($genres as $genre) {

            $gInstruction = trim($genre->prompt_instruction);

            if (! str_ends_with($gInstruction, '.')) {
                $gInstruction .= '.';
            }

            if ($genrePromptInstruction !== '') {
                $genrePromptInstruction .= ' ';
            }

            $genrePromptInstruction .= $gInstruction;
        }

        $requestInputs = [
            'language' => $language?->name,
            'additional_information' => $inputs['additional_information'] ?? null,
            'genre_prompt_instruction' => $genrePromptInstruction,
            'audience_instruction' => $audience->prompt_instruction,
            'story_book_type_instruction' => $storyBookType->prompt_instruction,
        ];

        return $requestInputs;
    }

    private function step2CharactersRequestInputsFormatter(StoryBook $storyBook, array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $requestInputs = [
            'foundation' => $formatedFoundation,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step3WorldVibeRequestInputsFormatter(StoryBook $storyBook,array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step4LocationsRequestInputsFormatter(StoryBook $storyBook,array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step5FactionsRequestInputsFormatter(StoryBook $storyBook, array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step6CreatureRequestInputsFormatter(StoryBook $storyBook,array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step7SystemRequestInputsFormatter(StoryBook $storyBook,array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step8TimelineRequestInputsFormatter(StoryBook $storyBook,array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step9StoryStructureRequestInputsFormatter(StoryBook $storyBook, array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step10TwistsAndForeshadowingRequestInputsFormatter(StoryBook $storyBook,array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step11ScenePlanRequestInputsFormatter(StoryBook $storyBook, array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);
        $formatedTwistsAndForeshadowing = json_encode($storyBook->twists_and_foreshadowing, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'twists_and_foreshadowing' => $formatedTwistsAndForeshadowing,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step12DialoguePlanRequestInputsFormatter(StoryBook $storyBook,array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);
        $formatedTwistsAndForeshadowing = json_encode($storyBook->twists_and_foreshadowing, JSON_PRETTY_PRINT);
        $formatedScenePlans = json_encode($storyBook->scene_plans, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'twists_and_foreshadowing' => $formatedTwistsAndForeshadowing,
            'scene_plans' => $formatedScenePlans,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step13PagePlanRequestInputsFormatter(StoryBook $storyBook,array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);
        $formatedTwistsAndForeshadowing = json_encode($storyBook->twists_and_foreshadowing, JSON_PRETTY_PRINT);
        $formatedScenePlans = json_encode($storyBook->scene_plans, JSON_PRETTY_PRINT);
        $formatedDialoguePlans = json_encode($storyBook->dialogue_plans, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'twists_and_foreshadowing' => $formatedTwistsAndForeshadowing,
            'scene_plans' => $formatedScenePlans,
            'dialogue_plans' => $formatedDialoguePlans,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step14PageNarrationRequestInputsFormatter(StoryBook $storyBook, array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);
        $formatedTwistsAndForeshadowing = json_encode($storyBook->twists_and_foreshadowing, JSON_PRETTY_PRINT);
        $formatedScenePlans = json_encode($storyBook->scene_plans, JSON_PRETTY_PRINT);
        $formatedDialoguePlans = json_encode($storyBook->dialogue_plans, JSON_PRETTY_PRINT);
        $formatedPagePlan = json_encode($storyBook->page_plan, JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'twists_and_foreshadowing' => $formatedTwistsAndForeshadowing,
            'scene_plans' => $formatedScenePlans,
            'dialogue_plans' => $formatedDialoguePlans,
            'page_plan' => $formatedPagePlan,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step15IllustrationPlanningRequestInputsFormatter(StoryBook $storyBook,array $inputs): array
    {
        $requestInputs = [];

        $additionalInformation = $inputs['additional_information'] ?? null;

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);
        $formatedFactions = json_encode($storyBook->factions, JSON_PRETTY_PRINT);
        $formatedCreatures = json_encode($storyBook->creatures, JSON_PRETTY_PRINT);
        $formatedSystems = json_encode($storyBook->systems, JSON_PRETTY_PRINT);
        $formatedTimeline = json_encode($storyBook->timeline, JSON_PRETTY_PRINT);
        $formatedStoryStructure = json_encode($storyBook->story_structure, JSON_PRETTY_PRINT);
        $formatedTwistsAndForeshadowing = json_encode($storyBook->twists_and_foreshadowing, JSON_PRETTY_PRINT);
        $formatedScenePlans = json_encode($storyBook->scene_plans, JSON_PRETTY_PRINT);
        $formatedDialoguePlans = json_encode($storyBook->dialogue_plans, JSON_PRETTY_PRINT);
        $formatedPagePlan = json_encode($storyBook->page_plan, JSON_PRETTY_PRINT);

        $storyBookPages = $storyBook->storyBookPages;
        $formatedPages = json_encode($storyBookPages->map(fn($storyBookPage) => [
            'no' => $storyBookPage->no,
            'narration' => $storyBookPage->narration,
        ])->values()->all(), JSON_PRETTY_PRINT);

        $requestInputs = [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'factions' => $formatedFactions,
            'creatures' => $formatedCreatures,
            'systems' => $formatedSystems,
            'timeline' => $formatedTimeline,
            'story_structure' => $formatedStoryStructure,
            'twists_and_foreshadowing' => $formatedTwistsAndForeshadowing,
            'scene_plans' => $formatedScenePlans,
            'dialogue_plans' => $formatedDialoguePlans,
            'page_plan' => $formatedPagePlan,
            'pages' => $formatedPages,
            'additional_information' => $additionalInformation,
        ];

        return $requestInputs;
    }

    private function step16PageIllustrationRequestInputsFormatter(StoryBook $storyBook, array $inputs): array
    {
        $page = $inputs['page'];

        $formatedFoundation = json_encode($storyBook->foundation, JSON_PRETTY_PRINT);
        $formatedCharacters = json_encode($storyBook->characters, JSON_PRETTY_PRINT);
        $formatedWorldBible = json_encode($storyBook->world_bible, JSON_PRETTY_PRINT);
        $formatedLocations = json_encode($storyBook->locations, JSON_PRETTY_PRINT);

        $formatedPage = json_encode([
            'no' => $page['no'] ?? null,
            'narration' => $page['narration'] ?? null,
            'illustration_prompt' => $page['illustration_prompt'] ?? null,
        ], JSON_PRETTY_PRINT);

        return [
            'foundation' => $formatedFoundation,
            'characters' => $formatedCharacters,
            'world_bible' => $formatedWorldBible,
            'locations' => $formatedLocations,
            'illustration_type_prompt_instruction' => $page['illustration_type_prompt_instruction'] ?? 'No additional visual style instruction.',
            'page' => $formatedPage,
        ];
    }

    public function sendGetRequest(string $url, string $apiKey, array $params = [], ?int $timeout = null): array
    {
        try {
            $response = Http::timeout(
                $timeout ?? $this->defaultTimeout
            )
                ->withToken($apiKey)
                ->acceptJson()
                ->get(
                    rtrim($url, '/'),
                    $params
                );
        } catch (Exception $exception) {
            throw new Exception(
                'Hugging Face API request failed: ' . $exception->getMessage(),
                0,
                $exception
            );
        }

        if (! $response->successful()) {
            throw new Exception(
                $this->formatApiErrorResponse($response)
            );
        }

        return $this->parseJsonResponse($response);
    }

    public function sendImageRequest(string $url, string $apiKey, string $model, mixed $data = null, ?int $timeout = null): array
    {
        $requestTimeout = $timeout ?? $this->defaultTimeout;

        set_time_limit($requestTimeout);

        $payload = $this->buildPayload(
            $model,
            $data,
            null
        );

        $endpoint = rtrim($url, '/');

        try {
            $response = Http::timeout(
                $requestTimeout
            )
                ->withToken($apiKey)
                ->acceptJson()
                ->post(
                    $endpoint,
                    $payload
                );
        } catch (Exception $exception) {
            throw new Exception(
                'Hugging Face API request failed: ' . $exception->getMessage(),
                0,
                $exception
            );
        }

        if (! $response->successful()) {
            throw new Exception(
                $this->formatApiErrorResponse($response)
            );
        }

        return $this->extractImageResponse($response);
    }

    public function decodeAiResponseContent($apiResponse, string $context = ''): array
    {
        $content = data_get(
            $apiResponse,
            'choices.0.message.content'
        );

        if (! is_string($content) || trim($content) === '') {
            throw new Exception(
                $this->buildDecodeErrorMessage(
                    $context,
                    'AI response is empty or invalid structure.'
                )
            );
        }

        $content = $this->sanitizeAiJsonResponseContent($content);

        $decoded = json_decode(
            $content,
            true
        );

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            ! is_array($decoded)
        ) {
            throw new Exception(
                $this->buildDecodeErrorMessage(
                    $context,
                    json_last_error_msg(),
                    $content
                )
            );
        }

        return $decoded;
    }

    private function sanitizeAiJsonResponseContent(string $content): string
    {
        $content = trim($content);

        if (strncmp($content, "\xEF\xBB\xBF", 3) === 0) {
            $content = substr($content, 3);

            $content = trim($content);
        }

        $content = preg_replace(
            '/^```(?:json)?\s*/i',
            '',
            $content
        );

        $content = preg_replace(
            '/\s*```$/i',
            '',
            $content
        );

        $content = trim($content);

        $firstBrace = strpos($content, '{');
        $lastBrace  = strrpos($content, '}');

        if ($firstBrace !== false && $lastBrace !== false && $lastBrace > $firstBrace) {
            $content = substr($content, $firstBrace, $lastBrace - $firstBrace + 1);
        }

        $content = preg_replace('/[\x00-\x1F\x7F]/', '', $content);

        return trim($content);
    }

    private function parseJsonResponse($response): array
    {
        $body = $response->body();

        if (trim($body) === '') {
            throw new Exception(
                'Hugging Face API returned an empty response.'
            );
        }

        $decoded = json_decode(
            $body,
            true
        );

        if (! is_array($decoded)) {
            throw new Exception(
                'Hugging Face API response is not valid JSON: ' . json_last_error_msg() . ' (HTTP ' . $response->status() . ')'
            );
        }

        return $decoded;
    }

    private function formatApiErrorResponse($response): string
    {
        $status = $response->status();

        $body = trim($response->body());

        if ($body === '') {
            return 'Hugging Face API error (' . $status . '): Request failed with an empty response.';
        }

        return 'Hugging Face API error (' . $status . '): ' . $this->formatApiErrorMessage($body);
    }

    private function formatApiErrorMessage(string $body): string
    {
        $decoded = json_decode(
            $body,
            true
        );

        if (! is_array($decoded)) {
            return $body;
        }

        $error = $decoded['error'] ?? null;

        $message = (is_string($error) && trim($error) !== '')
            ? trim($error)
            : $body;

        $estimatedTime = $decoded['estimated_time'] ?? null;

        if (! is_null($estimatedTime) && trim((string) $estimatedTime) !== '') {
            $message .= '. Estimated time: ' . trim((string) $estimatedTime) . ' seconds';
        }

        return $message;
    }

    private function buildDecodeErrorMessage(string $context, string $jsonError, string $content = ''): string
    {
        $stepLabel = $context !== '' ? $context : 'AI generation';

        $message = $stepLabel . " generation failed.\n\nJSON Error:\n" . $jsonError;

        if ($content !== '') {
            $message .= "\n\nAPI Response:\n" . Str::limit($content, 600);
        }

        return $message;
    }

    private function extractImageResponse($response): array
    {
        $contentType = $response->header('Content-Type') ?? '';

        if (str_contains($contentType, 'application/json')) {
            $image = $this->extractImageFromJson(
                $response->json()
            );

            if ($image !== null) {
                return $image;
            }
        }

        if (str_starts_with($contentType, 'image/')) {
            $image = $this->extractImageFromBinary(
                $response->body(),
                $contentType
            );

            if ($image !== null) {
                return $image;
            }
        }

        $body = $response->body();

        $decoded = json_decode(
            $body,
            true
        );

        if (is_array($decoded)) {
            $image = $this->extractImageFromJson($decoded);

            if ($image !== null) {
                return $image;
            }
        }

        $image = $this->parseImageCandidate($body);

        if ($image !== null) {
            return $image;
        }

        $image = $this->extractImageFromBinary(
            $body,
            $contentType
        );

        if ($image !== null) {
            return $image;
        }

        throw new Exception(
            'AI image response could not be parsed.'
        );
    }

    private function extractImageFromJson(mixed $data): ?array
    {
        if (! is_array($data)) {
            return null;
        }

        $candidates = [];

        $this->collectImageCandidates(
            $data,
            $candidates
        );

        foreach ($candidates as $candidate) {
            $image = $this->parseImageCandidate($candidate);

            if ($image !== null) {
                return $image;
            }
        }

        return null;
    }

    private function collectImageCandidates(mixed $value, array &$candidates): void
    {
        if (is_string($value)) {
            $value = trim($value);

            if ($value !== '') {
                $candidates[] = $value;
            }

            return;
        }

        if (is_array($value)) {
            foreach ($value as $item) {
                $this->collectImageCandidates(
                    $item,
                    $candidates
                );
            }
        }
    }

    private function parseImageCandidate(string $candidate): ?array
    {
        if ($candidate === '') {
            return null;
        }

        $dataUriMatch = [];

        if (preg_match('/^data:(image\/[a-z0-9.+-]+);base64,/', $candidate, $dataUriMatch)) {
            $offset = strpos($candidate, ',');

            $encoded = $offset !== false
                ? substr($candidate, $offset + 1)
                : $candidate;

            return [
                'type'      => 'base64',
                'extension' => $this->extensionFromMime($dataUriMatch[1]),
                'encoded'   => $encoded,
            ];
        }

        if (preg_match('/^https?:\/\/\S+$/i', $candidate)) {
            return [
                'type'      => 'url',
                'url'       => $candidate,
                'extension' => $this->extensionFromUrl($candidate),
            ];
        }

        if (
            strlen($candidate) > 100 &&
            preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $candidate) &&
            base64_decode($candidate, true) !== false &&
            $this->isLikelyImageBase64($candidate)
        ) {
            return [
                'type'      => 'base64',
                'extension' => 'png',
                'encoded'   => $candidate,
            ];
        }

        return null;
    }

    private function extractImageFromBinary(string $body, string $contentType): ?array
    {
        if ($body === '') {
            return null;
        }

        $trimmed = trim($body);

        if (
            strlen($trimmed) > 100 &&
            preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $trimmed)
        ) {
            if ($this->isLikelyImageBase64($trimmed)) {
                return [
                    'type'      => 'base64',
                    'extension' => $this->extensionFromMime($contentType),
                    'encoded'   => $trimmed,
                ];
            }

            return null;
        }

        return [
            'type'      => 'base64',
            'extension' => $this->extensionFromMime($contentType),
            'encoded'   => base64_encode($body),
        ];
    }

    private function isLikelyImageBase64(string $candidate): bool
    {
        $decoded = base64_decode($candidate, true);

        if ($decoded === false || $decoded === '') {
            return false;
        }

        $signature = substr($decoded, 0, 12);

        return str_starts_with($signature, "\x89PNG\r\n\x1a\n")
        || str_starts_with($signature, "\xFF\xD8\xFF")
        || str_starts_with($signature, 'GIF87a')
        || str_starts_with($signature, 'GIF89a')
        || str_starts_with($signature, 'RIFF');
    }

    private function extensionFromMime(string $mime): string
    {
        $mime = strtolower(
            trim(
                explode(';', $mime)[0]
            )
        );

        return match ($mime) {
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/gif'     => 'gif',
            'image/webp'    => 'webp',
            'image/avif'    => 'avif',
            'image/svg+xml' => 'svg',
            'image/bmp'     => 'bmp',
            default         => 'png',
        };
    }

    private function extensionFromUrl(string $url): string
    {
        $path = parse_url($url, PHP_URL_PATH);

        $extension = strtolower(
            pathinfo($path ?? '', PATHINFO_EXTENSION)
        );

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'bmp'])
            ? ($extension === 'jpeg' ? 'jpg' : $extension)
            : 'png';
    }

    private function buildPayload(string $model, mixed $data = null, ?int $maxOutputTokens = null): array
    {
        $content = $this->buildContent($data);

        $payload = [
            'model'    => $model,
            'messages' => [
                [
                    'role'    => 'user',
                    'content' => $content,
                ],
            ],
        ];

        if ($maxOutputTokens !== null && $maxOutputTokens > 0) {
            $payload['max_tokens'] = $maxOutputTokens;
        }

        return $payload;
    }

    private function buildContent(mixed $data): string
    {
        if ($data === null) {
            return '';
        }

        if (is_string($data)) {
            return $data;
        }

        if (is_array($data)) {
            if (isset($data['content'])) {
                return (string) $data['content'];
            }

            return json_encode(
                $data,
                JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            );
        }

        return (string) $data;
    }
}
