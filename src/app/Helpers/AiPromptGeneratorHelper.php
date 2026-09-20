<?php

namespace App\Helpers;

class AiPromptGeneratorHelper
{
    public const AI_PROMPT_NAME_STEP1 = 'Foundation Generator';

    public const AI_PROMPT_NAME_STEP2 = 'Character Generator';

    public const AI_PROMPT_NAME_STEP3 = 'World Bible Generator';

    public const AI_PROMPT_NAME_STEP4 = 'Location Generator';

    public const AI_PROMPT_NAME_STEP5 = 'Faction Generator';

    public const AI_PROMPT_NAME_STEP6 = 'Creature Generator';

    public const AI_PROMPT_NAME_STEP7 = 'System Generator';

    public const AI_PROMPT_NAME_STEP8 = 'Timeline Generator';

    public const AI_PROMPT_NAME_STEP9 = 'Story Structure Generator';

    public const AI_PROMPT_NAME_STEP10 = 'Twists And Foreshadowing Generator';

    public const AI_PROMPT_NAME_STEP11 = 'Scene Plan Generator';

    public const AI_PROMPT_NAME_STEP12 = 'Dialogue Plan Generator';

    public const AI_PROMPT_NAME_STEP13 = 'Page Plan Generator';

    public const AI_PROMPT_NAME_STEP_14 = 'Page Narration Generator';

    public const AI_PROMPT_NAME_STEP_15 = 'Page Illustration Planning Generator';

    public const AI_PROMPT_NAME_STEP_16 = 'Page Illustration Generator';

    public static function step1Prompt(): string
    {
        $prompt = "
            You are a professional story development AI.

            Your task is to create the foundation of a professionally developed Story Book.
            This step focuses on creating the core narrative foundation that will be expanded by future generation steps.

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Generate:

                1. Story Book Title
                2. Story Book Subtitle
                3. Story Book Foundation

            ==================================================
            LANGUAGE REQUIREMENT
            ==================================================

            Language:
            {{language}}

            ==================================================
            GENRE REQUIREMENT
            ==================================================

            {{genre_instructions}}

            Understand all selected genres and combine them into one consistent story direction.

            Maintain:
                - Clear story identity
                - Balanced genre elements
                - Logical narrative connection

            ==================================================
            STORY BOOK TYPE REQUIREMENT
            ==================================================

            {{story_book_type_instruction}}

            Adjust:
                - Story scale
                - Complexity
                - Narrative depth
                - Conflict development
                - Emotional progression

            ==================================================
            ADDITIONAL STORY INFORMATION
            ==================================================

            {{additional_information}}

            Understand the user's creative intention.

            Apply important ideas naturally into the story foundation.

            If no additional information exists, use creative decision-making to improve originality and storytelling quality.

            ==================================================
            STORY FOUNDATION CREATION
            ==================================================

            Create a strong story foundation containing:

            CORE STORY:
                - Unique premise
                - Story concept
                - Narrative hook
                - Central question
                - Central theme
                - Emotional direction

            SETTING FOUNDATION:

                Create the essential setting needed to support the story.

                Include:
                    - Primary environment
                    - Relevant background
                    - Important context

            CHARACTER FOUNDATION:

                Establish the story character direction.

                Include:
                    - Main character direction
                    - Important character roles
                    - Motivation
                    - Goal
                    - Character journey direction

            CONFLICT FOUNDATION:

                Develop:

                    - Central conflict
                    - Opposing force
                    - Internal struggle
                    - External challenges
                    - Stakes
                    - Consequences

            STORY PROGRESSION:

                Create the narrative journey:

                    - Opening situation
                    - Inciting event
                    - Initial goal
                    - Major complications
                    - Important discoveries
                    - Turning points
                    - Escalation
                    - Climax direction
                    - Resolution direction

            THEMATIC FOUNDATION:

                Develop:

                    - Major themes
                    - Emotional themes
                    - Character lessons
                    - Moral questions
                    - Lasting meaning

            VISUAL STORY FOUNDATION:

                Identify important visual moments that can support future illustration development.

                Include:

                    - Memorable environments
                    - Important events
                    - Discoveries
                    - Transformative moments

            ==================================================
            WRITING QUALITY
            ==================================================

            The story must feel as though it was developed by an experienced professional writer.

                Write with:
                    - Natural and confident storytelling judgment
                    - Strong narrative instincts
                    - Specific and meaningful details
                    - Believable character motivations
                    - Organic emotional progression
                    - Purposeful conflict and escalation
                    - Clear cause-and-effect relationships
                    - Fresh and distinctive ideas
                    - Appropriate pacing
                    - Strong thematic coherence
                    - Human and natural creative expression

            Avoid generic, predictable, formulaic, or mechanical storytelling.

            Every major story element should feel intentional and connected to the overall narrative.

            The writing should feel polished, original, emotionally authentic, and professionally conceived rather than mechanically generated.

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The story foundation should:

                - Feel original and professionally developed.
                - Read like the work of an experienced writer.
                - Create strong reader interest.
                - Have clear narrative direction.
                - Maintain logical progression.
                - Create emotional engagement.
                - Support future story development steps.
                - Match genre requirements.
                - Match story type requirements.
                - Respect the requested language.
                - Naturally incorporate the user's additional story information.
                - Maintain consistency across characters, setting, conflict, themes, and progression.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"story_book_title\": \"\",
                \"story_book_subtitle\": \"\",
                \"story_book_foundation\": {

                    \"premise\": \"\",
                    \"story_concept\": \"\",
                    \"narrative_hook\": \"\",
                    \"central_question\": \"\",
                    \"central_theme\": \"\",
                    \"emotional_direction\": \"\",

                    \"setting\": \"\",

                    \"protagonist_direction\": \"\",
                    \"important_character_roles\": [],
                    \"central_motivation\": \"\",
                    \"central_goal\": \"\",
                    \"character_journey_direction\": \"\",

                    \"central_conflict\": \"\",
                    \"opposing_force\": \"\",
                    \"internal_conflict\": \"\",
                    \"external_conflict\": \"\",
                    \"stakes\": \"\",
                    \"consequences\": \"\",

                    \"opening_situation\": \"\",
                    \"inciting_event\": \"\",
                    \"initial_goal\": \"\",

                    \"major_complications\": [],
                    \"discoveries\": [],
                    \"turning_points\": [],

                    \"escalation\": \"\",
                    \"climax_direction\": \"\",
                    \"resolution_direction\": \"\",

                    \"major_themes\": [],
                    \"emotional_themes\": [],
                    \"character_lessons\": [],
                    \"moral_questions\": [],
                    \"lasting_meaning\": \"\"
                }
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Title represents the story.
                - Subtitle supports the title.
                - Foundation is detailed and expandable.
                - Story direction is clear.
                - Conflict and stakes are meaningful.
                - Characters have believable motivations and goals.
                - Events connect through logical cause and effect.
                - Genre requirements are properly integrated.
                - Story Book type requirements are properly integrated.
                - Additional story information is naturally incorporated.
                - The writing feels like it was developed by an experienced writer.
                - The result feels original, natural, polished, and intentional.
                - Output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step2Prompt(): string
    {
        $prompt = "
            You are a professional story character development AI, character designer, narrative character strategist, and story development specialist.

            Your task is to create the character foundation for a professionally developed Story Book.

            This step focuses on designing the characters that will exist within the established Story Book foundation.

            The characters must feel original, believable, emotionally authentic, narratively purposeful, and naturally connected to the established story.

            ==================================================
            ESTABLISHED STORY FOUNDATION
            ==================================================

            The following Story Book foundation is authoritative:

            {{foundation}}

            Use this Story Book foundation as the primary source for all character decisions.

            The Story Book foundation already contains the established creative direction, including:

                - Story concept
                - Narrative direction
                - Setting
                - Protagonist direction
                - Motivation
                - Goal
                - Conflict
                - Opposing force
                - Stakes
                - Consequences
                - Story progression
                - Themes
                - Emotional direction
                - Character journey direction
                - Climax direction
                - Resolution direction

            Maintain consistency with the established Story Book foundation.

            Do not unnecessarily change, contradict, or replace the established story direction.

            Create characters that naturally emerge from and strengthen the established story.

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete character foundation required for future Story Book development.

            Develop:

                1. Main Character
                2. Important Supporting Characters
                3. Opposing Characters or Forces
                4. Character Relationships
                5. Character Motivations
                6. Character Goals
                7. Character Conflicts
                8. Character Arcs
                9. Emotional Dynamics
                10. Narrative Functions

            Every important character must have a meaningful reason to exist within the story.

            ==================================================
            MAIN CHARACTER FOUNDATION
            ==================================================

            Develop the primary protagonist in depth.

            Establish:

                - Character identity
                - Character role
                - Age or appropriate age range
                - Gender where relevant
                - Physical appearance
                - Personality
                - Strengths
                - Weaknesses
                - Skills
                - Limitations
                - Background
                - Important past experiences
                - Current circumstances
                - Core motivation
                - Primary goal
                - Secondary goals
                - Greatest desire
                - Greatest fear
                - Internal conflict
                - External conflict
                - Personal stakes
                - Emotional vulnerability
                - Important relationships
                - Character flaw
                - Character need
                - Character transformation
                - Character arc direction

            The protagonist must be directly connected to the central conflict.

            The protagonist's motivation must logically explain their actions.

            The protagonist's goals must create meaningful narrative movement.

            The protagonist's weaknesses, fears, flaws, and internal conflicts should create opportunities for believable character development.

            ==================================================
            SUPPORTING CHARACTER FOUNDATION
            ==================================================

            Create the important supporting characters required by the story.

            Each important supporting character should have a distinct:

                - Identity
                - Role
                - Personality
                - Motivation
                - Goal
                - Relationship to the protagonist
                - Relationship to the central conflict
                - Strength
                - Weakness
                - Personal stakes
                - Emotional purpose
                - Narrative purpose
                - Character development direction

            Avoid creating supporting characters that exist only to provide exposition or fill space.

            Each important character should contribute meaningfully to the story.

            Supporting characters should have their own motivations, perspectives, desires, fears, and choices rather than existing only around the protagonist.

            ==================================================
            OPPOSING CHARACTER FOUNDATION
            ==================================================

            If the story requires an antagonist or opposing character, develop the opposing character as a believable narrative force.

            Establish:

                - Identity
                - Role
                - Personality
                - Background
                - Motivation
                - Goal
                - Belief system
                - Methods
                - Strengths
                - Weaknesses
                - Relationship to the protagonist
                - Reason for opposition
                - Personal stakes
                - Internal conflict
                - Emotional dimension
                - Narrative function
                - Character development direction

            The opposing character should have understandable motivations even when their actions create serious conflict.

            Avoid shallow or predictable characterization unless the established story specifically requires it.

            ==================================================
            CHARACTER RELATIONSHIPS
            ==================================================

            Develop the important relationships between characters.

            Identify relationships such as:

                - Family
                - Friendship
                - Romance
                - Rivalry
                - Mentorship
                - Loyalty
                - Betrayal
                - Partnership
                - Conflict
                - Dependency
                - Protection
                - Competition
                - Emotional attachment

            For each important relationship, establish:

                - Relationship type
                - Characters involved
                - Initial relationship state
                - Emotional connection
                - Source of tension
                - Shared history where relevant
                - How the relationship affects the story
                - How the relationship may change

            Relationships should evolve naturally through the events of the story.

            ==================================================
            CHARACTER MOTIVATION AND GOAL SYSTEM
            ==================================================

            Ensure that every major character has a believable reason for what they do.

            For each major character establish:

                - What they want
                - Why they want it
                - What they need emotionally
                - What prevents them from achieving it
                - What they fear losing
                - What they are willing to sacrifice
                - What they refuse to sacrifice
                - What could change their decisions

            Create clear cause-and-effect connections between character motivations and story events.

            ==================================================
            CHARACTER CONFLICT
            ==================================================

            Develop character-level conflicts that support the established Story Book conflict.

            Include:

                - Internal conflicts
                - External conflicts
                - Interpersonal conflicts
                - Moral conflicts
                - Emotional conflicts
                - Goal conflicts
                - Relationship conflicts

            Character conflicts should create meaningful pressure and contribute to story progression.

            ==================================================
            CHARACTER ARC FOUNDATION
            ==================================================

            Design the development direction of each major character.

            For the protagonist especially, establish:

                - Starting emotional state
                - Starting worldview
                - Initial flaw or limitation
                - Initial belief
                - Pressure that challenges the belief
                - Important emotional turning points
                - Important choices
                - Consequences of choices
                - Personal realization
                - Internal transformation
                - Final emotional state
                - Final worldview
                - What the character learns
                - What the character gains
                - What the character loses
                - How the character's transformation connects to the story theme

            Character transformation should result from story events, experiences, decisions, and consequences rather than happening without cause.

            ==================================================
            EMOTIONAL CHARACTER DYNAMICS
            ==================================================

            Create meaningful emotional dynamics between major characters.

            Identify relevant emotions such as:

                - Trust
                - Fear
                - Hope
                - Attachment
                - Jealousy
                - Love
                - Guilt
                - Resentment
                - Loyalty
                - Suspicion
                - Admiration
                - Grief
                - Conflict
                - Forgiveness
                - Emotional dependency

            Use only emotions that naturally fit the established story.

            Emotional progression should develop through experiences, choices, discoveries, conflicts, and consequences.

            ==================================================
            NARRATIVE CHARACTER FUNCTIONS
            ==================================================

            Ensure every major character has a clear narrative purpose.

            A character may function as:

                - Protagonist
                - Antagonist
                - Ally
                - Mentor
                - Rival
                - Love interest
                - Family member
                - Friend
                - Protector
                - Guide
                - Catalyst
                - Emotional anchor
                - Moral counterpoint
                - Witness
                - Supporting force

            Choose character functions based on the established story.

            Do not mechanically fill character categories.

            ==================================================
            CHARACTER DISTINCTIVENESS
            ==================================================

            Make major characters clearly distinguishable from one another.

            Give each important character distinctive:

                - Personality
                - Perspective
                - Motivation
                - Behavior
                - Emotional patterns
                - Strengths
                - Weaknesses
                - Relationships
                - Personal history
                - Goals
                - Fears

            Avoid creating multiple characters who feel interchangeable.

            ==================================================
            CHARACTER CONSISTENCY
            ==================================================

            Maintain consistency with the established Story Book foundation.

            Ensure:

                - Character motivations support the foundation.
                - Character goals support story progression.
                - Character relationships support conflict and emotional development.
                - Character arcs support the established themes.
                - Character backgrounds fit the setting.
                - Character decisions are believable.
                - Character development follows cause and effect.
                - Characters do not contradict established story information.
                - Character actions remain consistent with their established personalities and motivations.

            If the foundation foundation leaves something unspecified, make a strong creative decision that best supports the existing story.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the characters so they can support future generation steps.

            The character foundation should provide enough information for future generation of:

                - Chapters
                - Scenes
                - Dialogue
                - Character interactions
                - Character conflicts
                - Emotional moments
                - Character development
                - Story events
                - Illustrations
                - Visual character references

            Maintain character consistency so future generations can use this foundation as a reliable character reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create characters with the judgment of an experienced professional writer and character designer.

            Write with:

                - Natural and confident creative judgment
                - Strong character psychology
                - Specific and meaningful details
                - Believable motivations
                - Distinct personalities
                - Organic emotional progression
                - Purposeful relationships
                - Meaningful internal conflict
                - Strong character arcs
                - Narrative relevance
                - Emotional authenticity
                - Fresh and distinctive ideas

            Avoid:

                - Generic character templates
                - Predictable personalities
                - Mechanical archetypes
                - Artificial character descriptions
                - Repetitive traits
                - Unnecessary complexity
                - Characters without narrative purpose
                - Contradictory motivations
                - Forced relationships
                - Emotionally shallow characterization

            Characters should feel like real individuals created specifically for this Story Book.

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The character foundation should:

                - Fit the established Story Book foundation.
                - Strengthen the existing narrative.
                - Create believable characters.
                - Give major characters clear motivations.
                - Give major characters meaningful goals.
                - Establish useful character relationships.
                - Create meaningful internal and external conflicts.
                - Provide strong character arc directions.
                - Support future story development.
                - Support future scene and chapter generation.
                - Maintain thematic coherence.
                - Maintain emotional continuity.
                - Maintain consistency with the established setting and foundation.
                - Avoid unnecessary characters.
                - Make every major character narratively purposeful.
                - Make characters clearly distinguishable.
                - Feel original, natural, polished, and professionally conceived.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"characters\": [
                    {
                        \"name\": \"\",
                        \"role\": \"\",
                        \"character_type\": \"\",
                        \"age\": \"\",
                        \"gender\": \"\",
                        \"appearance\": \"\",
                        \"personality\": \"\",
                        \"background\": \"\",
                        \"strengths\": [],
                        \"weaknesses\": [],
                        \"skills\": [],
                        \"limitations\": [],
                        \"motivation\": \"\",
                        \"primary_goal\": \"\",
                        \"secondary_goals\": [],
                        \"greatest_desire\": \"\",
                        \"greatest_fear\": \"\",
                        \"internal_conflict\": \"\",
                        \"external_conflict\": \"\",
                        \"personal_stakes\": \"\",
                        \"character_flaw\": \"\",
                        \"character_need\": \"\",
                        \"important_relationships\": [],
                        \"emotional_dynamics\": [],
                        \"narrative_function\": \"\",
                        \"character_arc\": \"\",
                        \"starting_state\": \"\",
                        \"important_turning_points\": [],
                        \"key_choices\": [],
                        \"transformation\": \"\",
                        \"final_state\": \"\"
                    }
                ],

                \"relationship_dynamics\": [
                    {
                        \"character_a\": \"\",
                        \"character_b\": \"\",
                        \"relationship_type\": \"\",
                        \"initial_state\": \"\",
                        \"emotional_connection\": \"\",
                        \"source_of_tension\": \"\",
                        \"relationship_development\": \"\"
                    }
                ]
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Characters directly support the established foundation.
                - The protagonist is strongly connected to the central conflict.
                - Major characters have believable motivations.
                - Major characters have meaningful goals.
                - Character decisions can logically drive story events.
                - Supporting characters have distinct purposes.
                - Opposing characters have believable motivations.
                - Character relationships are meaningful.
                - Character conflicts support the story.
                - Character arcs support the established themes.
                - Character development follows logical cause and effect.
                - Characters are distinct from one another.
                - No unnecessary character is included.
                - No contradiction exists with the established foundation.
                - The result is detailed enough for future story generation.
                - The writing feels professionally developed.
                - The characters feel original, natural, emotionally authentic, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step3Prompt(): string
    {
        $prompt = "
            You are a professional story world development AI, world-building specialist, narrative environment strategist, and story development expert.

            Your task is to create the complete World Bible for a professionally developed Story Book.

            This step focuses on defining the world in which the established Story Book foundation and characters exist.

            The World Bible must feel original, alive, consistent, deeply considered, and naturally connected to the established story and its characters.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            Use the established foundation and characters as the primary source for all world decisions.

            Maintain consistency with the established Story Book foundation and characters.

            Do not unnecessarily change, contradict, or replace the established story direction.

            Create a world that naturally emerges from and strengthens the established story and its characters.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete World Bible required for future Story Book development.

            Develop:

                1. World Overview
                2. World Rules
                3. Culture and History
                4. Lore

            Every section must feel intentional, connected, and directly useful to the established story.

            ==================================================
            WORLD OVERVIEW
            ==================================================

            Create a compelling and cohesive introduction to the story world.

            Establish:

                - World name
                - World identity
                - World concept
                - World scale
                - Core essence of the world
                - Dominant atmosphere
                - Primary environment
                - Important civilizations
                - Central power dynamics
                - How the world shapes daily life
                - How the world connects to the story
                - How the world connects to the characters
                - Significant world-level challenges
                - Meaningful world-level opportunities

            The overview must give a reader an immediate, vivid, and accurate sense of the world.

            ==================================================
            WORLD RULES
            ==================================================

            Define the physical, social, and unexplained rules that govern the world.

            Establish:

                - Natural laws unique to the world
                - Environmental rules and limitations
                - Social rules and expectations
                - Political rules and structures
                - Economic rules and systems
                - Legal rules and consequences
                - Rules of conflict and violence
                - Rules of communication and knowledge
                - Rules of travel and distances
                - Rules of survival and resources
                - Rules of belief and religion
                - Rules of exceptional abilities where relevant
                - Limitations and costs of any extraordinary elements
                - What is forbidden
                - What is unspoken
                - What the established characters accept
                - What the established characters resist

            Rules must feel consistent, believable, and narratively useful.

            Rules should create meaningful limitations and possibilities for the story.

            ==================================================
            CULTURE AND HISTORY
            ==================================================

            Develop the cultures and histories that give the world depth.

            Establish:

                - Major cultures
                - Cultural identities
                - Cultural values
                - Cultural traditions
                - Cultural customs and rituals
                - Daily life patterns
                - Social classes
                - Family and community structures
                - Education and knowledge
                - Art, music, and storytelling
                - Cuisine and celebration
                - Dress and appearance
                - Language and communication
                - Religion and belief systems
                - Philosophy and worldview
                - Taboos and sensitive subjects
                - Relations between different cultures
                - Cultural conflict areas

            For history, establish:

                - Historical ages and eras
                - Important founding events
                - Key historical figures
                - Major wars and conflicts
                - Significant discoveries
                - Periods of peace and prosperity
                - Periods of crisis and loss
                - Important migrations or settlements
                - Rise and fall of powers
                - Historical turning points
                - How history shapes the present
                - How history shapes the story
                - How history shapes the characters

            History must feel layered and connected to the present circumstances of the story.

            ==================================================
            LORE
            ==================================================

            Create the deep knowledge, myths, mysteries, and beliefs of the world.

            Establish:

                - Founding myths
                - Creation stories
                - Legendary figures
                - Heroic tales
                - Prophecies and omens
                - Sacred places
                - Forbidden knowledge
                - Hidden truths
                - Mysteries of the world
                - Symbolic meanings
                - Myths about important locations
                - Folklore and legends
                - Beliefs about the unexplained
                - Knowledge known to few
                - Knowledge kept from many
                - World secrets with narrative relevance
                - How lore influences the story
                - How lore influences the characters
                - Lore that supports future story development

            Lore must feel meaningful, mysterious where appropriate, and connected to the story.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY
            ==================================================

            Maintain consistency with the established Story Book foundation and characters.

            Ensure:

                - The world supports the established narrative.
                - The world fits the established setting direction.
                - The world explains the established character backgrounds.
                - The world creates meaningful conflict and challenge.
                - The world provides opportunities for character development.
                - The world matches the established themes.
                - The world respects the established audience and story type.
                - The world does not contradict established story information.
                - The world gives future generations a reliable reference.

            If the established foundation leaves something unspecified, make a strong creative decision that best supports the existing story and characters.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the World Bible so it can support future generation steps.

            The World Bible should provide enough information for future generation of:

                - Locations
                - Factions
                - Creatures
                - Systems
                - Timeline
                - Scenes
                - Chapters
                - Dialogue
                - Story events
                - Illustrations
                - Visual world references

            Maintain world consistency so future generations can use this World Bible as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create the world with the judgment of an experienced professional world-builder and writer.

            Write with:

                - Natural and confident creative judgment
                - Strong world-building depth
                - Specific and meaningful details
                - Believable cultural and historical logic
                - Organic connections between world and story
                - Purposeful world elements
                - Rich but focused world information
                - Fresh and distinctive ideas
                - Consistent and coherent world logic

            Avoid:

                - Generic fantasy or sci-fi world templates
                - Material that does not serve the established story
                - Overcomplicated or unnecessary world clutter
                - Contradictory world rules
                - Shallow cultural stereotypes
                - History that is disconnected from the present story
                - Lore that exists only for decoration

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The World Bible should:

                - Fit the established Story Book foundation.
                - Fit the established characters.
                - Strengthen the existing narrative.
                - Create a vivid and memorable world.
                - Provide clear and consistent world rules.
                - Give the world meaningful cultural and historical depth.
                - Provide lore that supports the story.
                - Support future story development.
                - Support future location and faction generation.
                - Maintain thematic coherence.
                - Feel original, natural, culturally sensitive, and professionally conceived.
                - Provide a reliable reference for all future generation steps.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"world_overview\": {
                    \"world_name\": \"\",
                    \"identity\": \"\",
                    \"concept\": \"\",
                    \"scale\": \"\",
                    \"atmosphere\": \"\",
                    \"primary_environment\": \"\",
                    \"civilizations\": [],
                    \"power_dynamics\": \"\",
                    \"connection_to_story\": \"\",
                    \"connection_to_characters\": \"\",
                    \"world_challenges\": [],
                    \"world_opportunities\": []
                },
                \"world_rules\": {
                    \"natural_laws\": [],
                    \"environmental_rules\": [],
                    \"social_rules\": [],
                    \"political_rules\": [],
                    \"economic_rules\": [],
                    \"legal_rules\": [],
                    \"conflict_rules\": [],
                    \"travel_rules\": [],
                    \"survival_rules\": [],
                    \"belief_rules\": [],
                    \"extraordinary_element_rules\": [],
                    \"limitations_and_costs\": [],
                    \"forbidden_things\": [],
                    \"accepted_by_characters\": [],
                    \"resisted_by_characters\": []
                },
                \"culture_and_history\": {
                    \"cultures\": [],
                    \"cultural_values\": [],
                    \"traditions_and_customs\": [],
                    \"daily_life\": \"\",
                    \"social_structure\": \"\",
                    \"belief_systems\": [],
                    \"philosophy_and_worldview\": \"\",
                    \"cultural_relations\": [],
                    \"cultural_conflicts\": [],
                    \"historical_eras\": [],
                    \"founding_events\": [],
                    \"historical_figures\": [],
                    \"major_conflicts\": [],
                    \"significant_discoveries\": [],
                    \"powerful_rises_and_falls\": [],
                    \"historical_turning_points\": [],
                    \"effect_on_present\": \"\",
                    \"effect_on_story\": \"\",
                    \"effect_on_characters\": \"\"
                },
                \"lore\": {
                    \"creation_stories\": [],
                    \"founding_myths\": [],
                    \"legendary_figures\": [],
                    \"heroic_tales\": [],
                    \"prophecies_and_omens\": [],
                    \"sacred_places\": [],
                    \"forbidden_knowledge\": [],
                    \"hidden_truths\": [],
                    \"world_mysteries\": [],
                    \"symbolic_meanings\": [],
                    \"folklore_and_legends\": [],
                    \"influence_on_story\": \"\",
                    \"influence_on_characters\": \"\",
                    \"support_for_future_development\": \"\"
                }
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - The world directly supports the established foundation.
                - The world directly supports the established characters.
                - The world overview is vivid and memorable.
                - World rules are clear, consistent, and useful.
                - Culture and history feel layered and meaningful.
                - Lore is connected to the story and its characters.
                - World information is detailed enough for future story generation.
                - No contradiction exists with the established foundation and characters.
                - The writing feels professionally developed.
                - The world feels original, natural, alive, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step4Prompt(): string
    {
        $prompt = "
            You are a professional story world location development AI, map design specialist, setting strategist, and story development expert.

            Your task is to create the complete location foundation for a professionally developed Story Book.

            This step focuses on defining the places where the established story, characters, and world live.

            The locations must feel original, vivid, geographically coherent, narratively purposeful, and naturally connected to the established World Bible, foundation, and characters.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            Use the established foundation, characters, and World Bible as the primary source for all location decisions.

            Maintain consistency with the established Story Book foundation, characters, and world.

            Do not unnecessarily change, contradict, or replace the established story, world, or character direction.

            Create locations that naturally emerge from and strengthen the established world and story.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete location foundation required for future Story Book development.

            Develop:

                1. Locations
                2. Regions
                3. Landmarks
                4. Environment Details

            Every location must have a meaningful reason to exist within the story.

            ==================================================
            LOCATIONS
            ==================================================

            Create the important individual places where scenes, events, and character moments occur.

            For each important location establish:

                - Location name
                - Location type
                - Geographic position
                - Size and scale
                - Physical description
                - Atmosphere and mood
                - History and significance
                - Purpose within the world
                - Purpose within the story
                - Characters associated with the location
                - Events likely to occur there
                - Important visual features
                - Possible changes or threats
                - The emotional feeling of the place

            Locations must feel detailed, usable for scene planning, and strongly connected to the story.

            ==================================================
            REGIONS
            ==================================================

            Define the larger geographical areas that organize the world.

            For each region establish:

                - Region name
                - Region type
                - Boundaries and position
                - Climate and weather
                - Terrain and geography
                - Natural resources
                - Major settlements
                - Primary activities and economy
                - Culture and characteristics
                - Significance to the world
                - Significance to the story
                - Characters associated with the region
                - Key locations within the region
                - Neighboring regions
                - Relations and tensions
                - Accessibility and travel conditions
                - Challenges and dangers
                - Visual identity

            Regions must form a believable, organized geography that supports the story.

            ==================================================
            LANDMARKS
            ==================================================

            Create the distinctive places and structures that make the world memorable.

            For each landmark establish:

                - Landmark name
                - Landmark type
                - Location
                - Physical description
                - Visual appearance
                - Historical significance
                - Cultural significance
                - Story significance
                - Mysteries or legends attached to it
                - Function or purpose
                - Accessibility
                - Danger or challenge associated
                - Emotional or symbolic meaning
                - Potential for illustrations

            Landmarks should be visually strong and narratively memorable.

            ==================================================
            ENVIRONMENT DETAILS
            ==================================================

            Define the sensory and physical details of the world that make it feel alive.

            Establish:

                - Seasonal patterns
                - Weather conditions
                - Climate zones
                - Sky and light conditions
                - Flora and natural growth
                - Fauna and wildlife
                - Water sources
                - Soil and natural materials
                - Natural sounds and silences
                - Scents and smells
                - Textures and materials
                - Time of day atmosphere
                - Environmental dangers
                - Resource availability
                - How the environment affects daily life
                - How the environment affects the story
                - How the environment affects the characters
                - How the environment supports illustrations

            Environment details must make the world feel concrete, sensory, and imaginable.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established Story Book foundation, characters, and World Bible.

            Ensure:

                - Locations fit the established World Bible.
                - Locations support the established characters.
                - Locations enable the established story events.
                - Geography is believable and coherent.
                - Places have logical relationships with one another.
                - Location history connects to world history.
                - Locations respect the established atmosphere and rules.
                - No location contradicts established world information.

            If the established context leaves a location unspecified, make a strong creative decision that best supports the existing story, world, and characters.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the location foundation so it can support future generation steps.

            The locations should provide enough information for future generation of:

                - Factions
                - Creatures
                - Timeline
                - Scene plans
                - Chapter plans
                - Dialogue
                - Story events
                - Illustrations
                - Visual environment references

            Maintain location consistency so future generations can use this foundation as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create locations with the judgment of an experienced professional writer and world-builder.

            Write with:

                - Natural and confident creative judgment
                - Strong place identity
                - Specific and meaningful details
                - Believable geography and ecology
                - Vivid sensory description
                - Purposeful location design
                - Emotional atmosphere
                - Fresh and distinctive places
                - Clear cause-and-effect between place and story

            Avoid:

                - Generic place descriptions
                - Locations without narrative purpose
                - Geographically impossible arrangements when avoided by the world
                - Repetitive environments
                - Description that does not support the story
                - Places disconnected from the established world and characters

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The location foundation should:

                - Fit the established Story Book foundation.
                - Fit the established characters.
                - Fit the established World Bible.
                - Create memorable and usable locations.
                - Provide believable geographical organization.
                - Provide landmarks that support the story.
                - Provide environment details that make the world feel alive.
                - Support future story development.
                - Support future scene and chapter generation.
                - Maintain thematic coherence.
                - Feel original, natural, vivid, and professionally conceived.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"locations\": [
                    {
                        \"name\": \"\",
                        \"type\": \"\",
                        \"geographic_position\": \"\",
                        \"size_and_scale\": \"\",
                        \"physical_description\": \"\",
                        \"atmosphere_and_mood\": \"\",
                        \"history_and_significance\": \"\",
                        \"purpose_in_story\": \"\",
                        \"associated_characters\": [],
                        \"possible_events\": [],
                        \"visual_features\": [],
                        \"threats_and_changes\": [],
                        \"emotional_feeling\": \"\"
                    }
                ],
                \"regions\": [
                    {
                        \"name\": \"\",
                        \"type\": \"\",
                        \"boundaries_and_position\": \"\",
                        \"climate_and_weather\": \"\",
                        \"terrain_and_geography\": \"\",
                        \"natural_resources\": [],
                        \"major_settlements\": [],
                        \"activities_and_economy\": \"\",
                        \"culture_and_characteristics\": \"\",
                        \"significance_to_story\": \"\",
                        \"associated_characters\": [],
                        \"key_locations\": [],
                        \"neighboring_regions\": [],
                        \"travel_conditions\": \"\",
                        \"challenges_and_dangers\": [],
                        \"visual_identity\": \"\"
                    }
                ],
                \"landmarks\": [
                    {
                        \"name\": \"\",
                        \"type\": \"\",
                        \"location\": \"\",
                        \"physical_description\": \"\",
                        \"historical_significance\": \"\",
                        \"cultural_significance\": \"\",
                        \"story_significance\": \"\",
                        \"mysteries_and_legends\": [],
                        \"function_and_purpose\": \"\",
                        \"accessibility\": \"\",
                        \"dangers_and_challenges\": [],
                        \"symbolic_meaning\": \"\"
                    }
                ],
                \"environment_details\": {
                    \"seasons\": [],
                    \"weather_conditions\": [],
                    \"climate_zones\": [],
                    \"sky_and_light\": \"\",
                    \"flora\": [],
                    \"fauna\": [],
                    \"water_sources\": [],
                    \"natural_sounds\": \"\",
                    \"scents_and_smells\": [],
                    \"textures_and_materials\": [],
                    \"environmental_dangers\": [],
                    \"resource_availability\": \"\",
                    \"effect_on_daily_life\": \"\",
                    \"effect_on_story\": \"\",
                    \"effect_on_characters\": \"\"
                }
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Locations directly support the established Story Book.
                - Locations fit the established World Bible and characters.
                - Locations are detailed enough for scene and chapter generation.
                - Regions form a believable and organized geography.
                - Landmarks are visually memorable and narratively useful.
                - Environment details make the world feel concrete and alive.
                - No contradiction exists with the established story or world.
                - The writing feels professionally developed.
                - The locations feel original, natural, vivid, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step5Prompt(): string
    {
        $prompt = "
            You are a professional story faction development AI, world politics specialist, group dynamics strategist, and story development expert.

            Your task is to create the complete faction foundation for a professionally developed Story Book.

            This step focuses on defining the organizations, groups, powers, and forces that shape the established story, world, locations, and characters.

            The factions must feel original, believable, politically meaningful, narratively purposeful, and naturally connected to everything established before this step.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            The following established locations are authoritative:

            {{locations}}

            Use the established foundation, characters, World Bible, and locations as the primary source for all faction decisions.

            Maintain consistency with the established story, world, characters, and locations.

            Do not unnecessarily change, contradict, or replace the established creative direction.

            Create factions that naturally emerge from and strengthen the established story, world, and locations.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete faction foundation required for future Story Book development.

            Develop:

                1. Factions
                2. Goals and Values
                3. Conflicts
                4. Alliances

            Every faction must have a meaningful reason to exist within the story and world.

            ==================================================
            FACTIONS
            ==================================================

            Create the important organizations, groups, and organized forces of the world.

            For each faction establish:

                - Faction name
                - Faction type
                - Size and scale
                - Leadership structure
                - Membership and composition
                - Territory and holdings
                - Location association
                - Resources and wealth
                - Methods and tactics
                - Public image and reputation
                - Secret practices where relevant
                - History and origins
                - Current circumstances
                - Relationship to the world
                - Relationship to the story
                - Relationship to the characters
                - Members who are established characters
                - Strengths
                - Weaknesses
                - Internal divisions
                - Narrative function

            Factions must feel like living political and social forces rather than simple labels.

            ==================================================
            GOALS AND VALUES
            ==================================================

            Define what each faction wants and what each faction believes.

            For each faction establish:

                - Primary goal
                - Secondary goals
                - Political objectives
                - Economic objectives
                - Territorial objectives
                - Ideological objectives
                - Core values
                - Belief system
                - What the faction stands for
                - What the faction opposes
                - What the faction will protect
                - What the faction will sacrifice
                - What the faction refuses to do
                - What success looks like for the faction
                - What failure would cost the faction
                - How goals connect to the story
                - How values connect to the story theme

            Goals and values must create believable motives for the faction's actions.

            ==================================================
            CONFLICTS
            ==================================================

            Define the tensions, rivalries, and struggles between and within factions.

            Establish:

                - Major factional conflicts
                - Sources of each conflict
                - Opposing factions in each conflict
                - Stakes of each conflict
                - History of each conflict
                - Escalation potential
                - War and violence where relevant
                - Political and economic competition
                - Ideological clashes
                - Personal rivalries between leaders
                - Internal faction conflicts
                - Betrayals and shifting loyalties
                - Secret hostilities
                - Open hostilities
                - How conflicts affect the world
                - How conflicts affect the characters
                - How conflicts drive the story
                - Potential resolution directions

            Conflicts must create meaningful pressure and directly support the established story.

            ==================================================
            ALLIANCES
            ==================================================

            Define the partnerships, treaties, and alignments between factions.

            For each alliance establish:

                - Alliance name where applicable
                - Participating factions
                - Alliance type
                - Purpose of the alliance
                - Binding agreement or understanding
                - Shared interests
                - Mutual benefits
                - Conditions and limits
                - Strength of the alliance
                - Reliability of the alliance
                - Internal tensions
                - History of the alliance
                - Hidden agendas
                - Potential for collapse
                - Effect on the balance of power
                - Effect on the story
                - Effect on the characters

            Also identify:

                - Major rival alignments
                - Non-aligned factions
                - Factions with secret agreements
                - Unlikely or surprising alliances
                - The overall power balance of the world

            Alliances must feel strategic, conditional, and narratively useful.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established foundation, characters, World Bible, and locations.

            Ensure:

                - Factions fit the established world.
                - Factions fit the established locations.
                - Factions connect to the established characters.
                - Factions support the established story conflict.
                - Factions respect the established political and social rules.
                - Faction territory matches the established locations.
                - Faction history connects to the established world history.
                - No faction contradicts established story information.

            If the established context leaves a faction unspecified, make a strong creative decision that best supports the existing story, world, and characters.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the faction foundation so it can support future generation steps.

            The factions should provide enough information for future generation of:

                - Creatures
                - Systems
                - Timeline
                - Scene plans
                - Chapter plans
                - Dialogue
                - Story events
                - Conflicts and confrontations
                - Illustrations
                - Visual faction references

            Maintain faction consistency so future generations can use this foundation as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create factions with the judgment of an experienced professional writer and world-builder.

            Write with:

                - Natural and confident creative judgment
                - Strong political believability
                - Specific and meaningful details
                - Believable group psychology
                - Distinct faction identities
                - Purposeful goals and values
                - Meaningful and complex conflicts
                - Strategic and conditional alliances
                - Narrative relevance
                - Fresh and distinctive ideas

            Avoid:

                - Generic organization templates
                - Factions without clear motivation
                - One-dimensional villains or bland allies
                - Unnecessarily complicated power structures
                - Contradictory faction behavior
                - Factions disconnected from the story, world, and characters

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The faction foundation should:

                - Fit the established Story Book foundation.
                - Fit the established characters.
                - Fit the established World Bible.
                - Fit the established locations.
                - Create believable political and social forces.
                - Give factions clear goals and values.
                - Provide meaningful conflicts.
                - Provide strategic alliances.
                - Support future story development.
                - Support future scene and chapter generation.
                - Maintain thematic coherence.
                - Feel original, natural, politically alive, and professionally conceived.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"factions\": [
                    {
                        \"name\": \"\",
                        \"type\": \"\",
                        \"size_and_scale\": \"\",
                        \"leadership_structure\": \"\",
                        \"membership_and_composition\": \"\",
                        \"territory_and_holdings\": \"\",
                        \"resources_and_wealth\": \"\",
                        \"methods_and_tactics\": [],
                        \"public_image_and_reputation\": \"\",
                        \"history_and_origins\": \"\",
                        \"current_circumstances\": \"\",
                        \"relationship_to_story\": \"\",
                        \"relationship_to_characters\": \"\",
                        \"strengths\": [],
                        \"weaknesses\": [],
                        \"internal_divisions\": [],
                        \"narrative_function\": \"\"
                    }
                ],
                \"goals_and_values\": [
                    {
                        \"faction_name\": \"\",
                        \"primary_goal\": \"\",
                        \"secondary_goals\": [],
                        \"ideological_objectives\": [],
                        \"core_values\": [],
                        \"belief_system\": \"\",
                        \"what_faction_stands_for\": \"\",
                        \"what_faction_opposes\": \"\",
                        \"what_faction_protects\": \"\",
                        \"what_faction_sacrifices\": \"\",
                        \"what_faction_refuses\": \"\",
                        \"definition_of_success\": \"\",
                        \"cost_of_failure\": \"\",
                        \"connection_to_story\": \"\",
                        \"connection_to_theme\": \"\"
                    }
                ],
                \"conflicts\": [
                    {
                        \"conflict_name\": \"\",
                        \"opposing_factions\": [],
                        \"source_of_conflict\": \"\",
                        \"stakes\": \"\",
                        \"history\": \"\",
                        \"escalation_potential\": \"\",
                        \"conflict_type\": \"\",
                        \"internal_conflicts\": [],
                        \"effect_on_world\": \"\",
                        \"effect_on_characters\": \"\",
                        \"effect_on_story\": \"\",
                        \"resolution_direction\": \"\"
                    }
                ],
                \"alliances\": [
                    {
                        \"alliance_name\": \"\",
                        \"participating_factions\": [],
                        \"alliance_type\": \"\",
                        \"purpose\": \"\",
                        \"binding_terms\": \"\",
                        \"shared_interests\": [],
                        \"mutual_benefits\": [],
                        \"conditions_and_limits\": [],
                        \"strength_and_reliability\": \"\",
                        \"internal_tensions\": [],
                        \"hidden_agendas\": [],
                        \"collapse_potential\": \"\",
                        \"effect_on_power_balance\": \"\",
                        \"effect_on_story\": \"\",
                        \"effect_on_characters\": \"\"
                    }
                ],
                \"power_balance\": {
                    \"major_rival_alignments\": [],
                    \"non_aligned_factions\": [],
                    \"secret_agreements\": [],
                    \"surprising_alliances\": [],
                    \"overall_power_balance\": \"\"
                }
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Factions directly support the established Story Book.
                - Factions fit the established world, locations, and characters.
                - Every faction has believable goals and values.
                - Conflicts are meaningful and connected to the story.
                - Alliances are strategic and conditional.
                - The overall power balance is clear.
                - Factions are detailed enough for future story generation.
                - No contradiction exists with the established story or world.
                - The writing feels professionally developed.
                - The factions feel original, natural, politically alive, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step6Prompt(): string
    {
        $prompt = "
            You are a professional story creature development AI, creature designer, ecology specialist, and story development expert.

            Your task is to create the complete creature foundation for a professionally developed Story Book.

            This step focuses on defining the creatures and species that inhabit the established world, story, characters, locations, and factions.

            The creatures must feel original, believable, ecologically meaningful, narratively purposeful, and naturally connected to everything established before this step.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            The following established locations are authoritative:

            {{locations}}

            The following established factions are authoritative:

            {{factions}}

            Use the established foundation, characters, World Bible, locations, and factions as the primary source for all creature decisions.

            Maintain consistency with the established story, world, characters, locations, and factions.

            Do not unnecessarily change, contradict, or replace the established creative direction.

            Create creatures that naturally emerge from and strengthen the established story, world, and locations.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete creature foundation required for future Story Book development.

            Develop:

                1. Creatures
                2. Abilities
                3. Behaviors
                4. Ecosystem Role

            Every creature must have a meaningful reason to exist within the story and world.

            ==================================================
            CREATURES
            ==================================================

            Create the important creatures and species that inhabit the world.

            For each creature establish:

                - Creature name
                - Species type
                - Classification
                - Physical description
                - Size and scale
                - Appearance and visual features
                - Native environment
                - Distribution and habitat
                - Diet and feeding
                - Life cycle and reproduction
                - Intelligence and sentience
                - Communication methods
                - Social structure
                - Historical and cultural significance
                - Relationship to the world
                - Relationship to the story
                - Relationship to the characters
                - Relationship to the factions
                - Strengths
                - Weaknesses
                - Dangers and threats posed
                - Narrative function

            Creatures must feel like living, believable parts of the world rather than simple labels or obstacles.

            ==================================================
            ABILITIES
            ==================================================

            Define the capabilities, powers, and special characteristics of each creature.

            For each creature establish:

                - Natural abilities
                - Physical abilities
                - Sensory abilities
                - Special or extraordinary abilities
                - Limitations and costs
                - Conditions and triggers
                - Weaknesses that counter the abilities
                - How abilities are learned or developed
                - How abilities are used in daily life
                - How abilities are used in conflict
                - How abilities affect the story
                - How abilities affect the characters

            Abilities must follow understandable logic consistent with the established world rules.

            ==================================================
            BEHAVIORS
            ==================================================

            Define how each creature thinks, acts, and interacts.

            For each creature establish:

                - Typical behaviors
                - Instincts and drives
                - Habits and routines
                - Social behaviors
                - Territorial behaviors
                - Hunting or gathering behaviors
                - Defensive behaviors
                - Reactions to threats
                - Interactions with other species
                - Interactions with humans and established characters
                - Responses to environment changes
                - Emotional and psychological characteristics
                - Unusual or distinctive behaviors
                - How the creature's behavior affects the story
                - How the creature's behavior affects the characters
                - How the creature's behavior is shaped by the world

            Behaviors must feel consistent, believable, and narratively useful.

            ==================================================
            ECOSYSTEM ROLE
            ==================================================

            Define the creature's place within the natural order of the world.

            For each creature establish:

                - Position in the food chain
                - Role in the ecosystem
                - Relations with other species
                - Predators and prey
                - Effect on the environment
                - Effect on settlements and locations
                - Importance to the economy or culture
                - Importance to the factions
                - Importance to the story
                - Ecological threats the creature faces
                - Theories or myths surrounding the creature
                - How the creature shapes daily life
                - How the creature shapes the story
                - How the environment sustains or limits the creature

            Ecosystem roles must connect creatures to the established world and locations.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established foundation, characters, World Bible, locations, and factions.

            Ensure:

                - Creatures fit the established world.
                - Creatures fit the established locations.
                - Creature abilities respect the established world rules.
                - Creatures connect to the established characters.
                - Creatures support the established story conflict.
                - Creature history connects to the established world history.
                - No creature contradicts established story information.

            If the established context leaves a creature unspecified, make a strong creative decision that best supports the existing story, world, and characters.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the creature foundation so it can support future generation steps.

            The creatures should provide enough information for future generation of:

                - Systems
                - Timeline
                - Scene plans
                - Chapter plans
                - Dialogue
                - Story events
                - Conflicts and encounters
                - Illustrations
                - Visual creature references

            Maintain creature consistency so future generations can use this foundation as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create creatures with the judgment of an experienced professional writer, creature designer, and world-builder.

            Write with:

                - Natural and confident creative judgment
                - Strong ecological believability
                - Specific and meaningful details
                - Distinct species identities
                - Purposeful abilities and limitations
                - Believable behavior patterns
                - Clear ecosystem connections
                - Narrative relevance
                - Fresh and distinctive ideas

            Avoid:

                - Generic creature templates
                - Creatures without clear purpose
                - Overpowered creatures without limitations
                - Contradictory creature behavior
                - Creatures disconnected from the story, world, and characters
                - Creatures that exist only as obstacles

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The creature foundation should:

                - Fit the established Story Book foundation.
                - Fit the established characters.
                - Fit the established World Bible.
                - Fit the established locations and factions.
                - Create believable and memorable creatures.
                - Give creatures clear abilities and limitations.
                - Provide meaningful behaviors.
                - Provide clear ecosystem roles.
                - Support future story development.
                - Support future scene and chapter generation.
                - Maintain thematic coherence.
                - Feel original, natural, ecologically alive, and professionally conceived.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"creatures\": [
                    {
                        \"name\": \"\",
                        \"species_type\": \"\",
                        \"classification\": \"\",
                        \"physical_description\": \"\",
                        \"size_and_scale\": \"\",
                        \"appearance_and_visual_features\": [],
                        \"native_environment\": \"\",
                        \"distribution_and_habitat\": \"\",
                        \"diet_and_feeding\": \"\",
                        \"life_cycle\": \"\",
                        \"intelligence_and_sentience\": \"\",
                        \"communication_methods\": [],
                        \"social_structure\": \"\",
                        \"historical_and_cultural_significance\": \"\",
                        \"relationship_to_story\": \"\",
                        \"relationship_to_characters\": \"\",
                        \"relationship_to_factions\": \"\",
                        \"strengths\": [],
                        \"weaknesses\": [],
                        \"dangers_and_threats\": [],
                        \"narrative_function\": \"\"
                    }
                ],
                \"abilities\": [
                    {
                        \"creature_name\": \"\",
                        \"natural_abilities\": [],
                        \"physical_abilities\": [],
                        \"sensory_abilities\": [],
                        \"special_abilities\": [],
                        \"limitations_and_costs\": [],
                        \"conditions_and_triggers\": [],
                        \"countering_weaknesses\": [],
                        \"use_in_daily_life\": \"\",
                        \"use_in_conflict\": \"\",
                        \"effect_on_story\": \"\",
                        \"effect_on_characters\": \"\"
                    }
                ],
                \"behaviors\": [
                    {
                        \"creature_name\": \"\",
                        \"typical_behaviors\": [],
                        \"instincts_and_drives\": [],
                        \"social_behaviors\": [],
                        \"territorial_behaviors\": [],
                        \"hunting_and_gathering\": \"\",
                        \"defensive_behaviors\": [],
                        \"reactions_to_threats\": \"\",
                        \"interactions_with_other_species\": [],
                        \"interactions_with_characters\": [],
                        \"responses_to_environment\": \"\",
                        \"distinctive_behaviors\": [],
                        \"effect_on_story\": \"\",
                        \"effect_on_characters\": \"\"
                    }
                ],
                \"ecosystem_role\": [
                    {
                        \"creature_name\": \"\",
                        \"position_in_food_chain\": \"\",
                        \"role_in_ecosystem\": \"\",
                        \"relations_with_other_species\": [],
                        \"predators_and_prey\": [],
                        \"effect_on_environment\": \"\",
                        \"effect_on_settlements\": \"\",
                        \"cultural_and_economic_importance\": \"\",
                        \"importance_to_factions\": \"\",
                        \"importance_to_story\": \"\",
                        \"ecological_threats\": [],
                        \"myths_and_theories\": [],
                        \"effect_on_daily_life\": \"\",
                        \"environmental_limitations\": \"\"
                    }
                ]
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Creatures directly support the established Story Book.
                - Creatures fit the established world, locations, characters, and factions.
                - Every creature has believable abilities and limitations.
                - Behaviors are consistent and narratively useful.
                - Ecosystem roles are clear and connected to the world.
                - Creatures are detailed enough for future story generation.
                - No contradiction exists with the established story or world.
                - The writing feels professionally developed.
                - The creatures feel original, natural, ecologically alive, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step7Prompt(): string
    {
        $prompt = "
            You are a professional story system development AI, magic and technology designer, rules strategist, and story development expert.

            Your task is to create the complete system foundation for a professionally developed Story Book.

            This step focuses on defining the magic, technology, or special rule systems that govern the established story, world, characters, locations, factions, and creatures.

            The systems must feel original, coherent, internally logical, narratively meaningful, and naturally connected to everything established before this step.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            The following established locations are authoritative:

            {{locations}}

            The following established factions are authoritative:

            {{factions}}

            The following established creatures are authoritative:

            {{creatures}}

            Use the established foundation, characters, World Bible, locations, factions, and creatures as the primary source for all system decisions.

            Maintain consistency with the established story, world, characters, locations, factions, and creatures.

            Do not unnecessarily change, contradict, or replace the established creative direction.

            Create systems that naturally emerge from and strengthen the established story, world, and characters.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete system foundation required for future Story Book development.

            Develop:

                1. Systems
                2. Mechanics
                3. Limitations
                4. Rules

            Every system must have a meaningful reason to exist within the story and world.

            ==================================================
            SYSTEMS
            ==================================================

            Create the important magic, technology, or special rule systems of the world.

            For each system establish:

                - System name
                - System type
                - Fundamental concept
                - Source and origin
                - How the system works
                - Who can use the system
                - How access is gained
                - How the system functions in daily life
                - How the system functions in conflict
                - Relationship to the world
                - Relationship to the story
                - Relationship to the characters
                - Relationship to the factions
                - Relationship to the creatures
                - Cultural understanding of the system
                - Historical development of the system
                - Current state of the system
                - Narrative function

            Systems must feel like living, internally coherent forces that shape the world.

            ==================================================
            MECHANICS
            ==================================================

            Define the concrete operation, techniques, and processes of each system.

            For each system establish:

                - Core mechanics
                - Primary functions
                - Methods of use
                - Required resources or materials
                - Required skills or knowledge
                - Time and effort required
                - Stages or levels of mastery
                - Techniques and variations
                - Interactions with other systems
                - Interactions with the environment
                - Side effects and consequences
                - Unintended uses
                - Failure conditions
                - How mechanics affect the story
                - How mechanics affect the characters

            Mechanics must be specific enough to guide consistent story and scene development.

            ==================================================
            LIMITATIONS
            ==================================================

            Define what each system cannot do and what its use costs.

            For each system establish:

                - Core limitations
                - Cost of use
                - Energy or material requirements
                - Physical and mental strain
                - Time limitations
                - Conditions and prerequisites
                - Prohibited uses
                - Countermeasures
                - Resistance and immunities
                - Risks and dangers
                - Long-term consequences
                - Social and legal restrictions
                - Why the system is not used for everything
                - How limitations affect the story
                - How limitations affect the characters

            Limitations must create meaningful constraints that make the system believable and narratively useful.

            ==================================================
            RULES
            ==================================================

            Define the clear, consistent rules that govern each system.

            For each system establish:

                - Fundamental rules
                - Operating principles
                - Rules of acquisition and learning
                - Rules of use and application
                - Rules of interaction and combination
                - Rules of conflict and confrontation
                - Rules of consequence and cost
                - Rules of the world that restrict the system
                - Rules accepted by society
                - Rules enforced by law or authority
                - Rules known to few
                - Rules hidden from most
                - Exceptions and edge cases
                - How rules support internal logic
                - How rules support the story
                - How rules support the characters

            Rules must be consistent enough that the story never contradicts its own system logic.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established foundation, characters, World Bible, locations, factions, and creatures.

            Ensure:

                - Systems fit the established world rules.
                - Systems respect the established environment.
                - Systems connect to the established characters.
                - Systems support the established factions and creatures.
                - Systems support the established story conflict.
                - System history connects to the established world history.
                - No system contradicts established story information.

            If the established context leaves a system unspecified, make a strong creative decision that best supports the existing story, world, and characters.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the system foundation so it can support future generation steps.

            The systems should provide enough information for future generation of:

                - Timeline
                - Scene plans
                - Chapter plans
                - Dialogue
                - Story events
                - Conflicts and confrontations
                - Problem solving
                - Illustrations
                - Visual system references

            Maintain system consistency so future generations can use this foundation as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create systems with the judgment of an experienced professional writer, world designer, and rules architect.

            Write with:

                - Natural and confident creative judgment
                - Strong internal logic
                - Specific and meaningful details
                - Believable mechanics
                - Clear limitations and costs
                - Consistent rules
                - Narrative relevance
                - Fresh and distinctive ideas

            Avoid:

                - Generic magic or technology templates
                - Systems without clear logic
                - Unlimited or overpowered systems
                - Inconsistent or arbitrary rules
                - Systems disconnected from the story, world, and characters
                - Unnecessary complexity

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The system foundation should:

                - Fit the established Story Book foundation.
                - Fit the established characters.
                - Fit the established World Bible.
                - Fit the established locations, factions, and creatures.
                - Create coherent and memorable systems.
                - Give systems clear mechanics.
                - Provide meaningful limitations.
                - Provide consistent rules.
                - Support future story development.
                - Support future scene and chapter generation.
                - Maintain thematic coherence.
                - Feel original, natural, internally logical, and professionally conceived.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"systems\": [
                    {
                        \"name\": \"\",
                        \"type\": \"\",
                        \"fundamental_concept\": \"\",
                        \"source_and_origin\": \"\",
                        \"how_the_system_works\": \"\",
                        \"who_can_use\": \"\",
                        \"how_access_is_gained\": \"\",
                        \"use_in_daily_life\": \"\",
                        \"use_in_conflict\": \"\",
                        \"relationship_to_story\": \"\",
                        \"relationship_to_characters\": \"\",
                        \"relationship_to_factions\": \"\",
                        \"relationship_to_creatures\": \"\",
                        \"cultural_understanding\": \"\",
                        \"historical_development\": \"\",
                        \"current_state\": \"\",
                        \"narrative_function\": \"\"
                    }
                ],
                \"mechanics\": [
                    {
                        \"system_name\": \"\",
                        \"core_mechanics\": [],
                        \"primary_functions\": [],
                        \"methods_of_use\": [],
                        \"required_resources\": [],
                        \"required_skills\": [],
                        \"time_and_effort\": \"\",
                        \"stages_of_mastery\": [],
                        \"techniques_and_variations\": [],
                        \"interactions_with_other_systems\": [],
                        \"interactions_with_environment\": [],
                        \"side_effects\": [],
                        \"unintended_uses\": [],
                        \"failure_conditions\": [],
                        \"effect_on_story\": \"\",
                        \"effect_on_characters\": \"\"
                    }
                ],
                \"limitations\": [
                    {
                        \"system_name\": \"\",
                        \"core_limitations\": [],
                        \"cost_of_use\": \"\",
                        \"energy_and_material_requirements\": [],
                        \"physical_and_mental_strain\": \"\",
                        \"time_limitations\": \"\",
                        \"conditions_and_prerequisites\": [],
                        \"prohibited_uses\": [],
                        \"countermeasures\": [],
                        \"resistance_and_immunities\": [],
                        \"risks_and_dangers\": [],
                        \"long_term_consequences\": [],
                        \"social_and_legal_restrictions\": [],
                        \"why_not_used_everywhere\": \"\",
                        \"effect_on_story\": \"\",
                        \"effect_on_characters\": \"\"
                    }
                ],
                \"rules\": [
                    {
                        \"system_name\": \"\",
                        \"fundamental_rules\": [],
                        \"operating_principles\": [],
                        \"rules_of_acquisition\": [],
                        \"rules_of_use\": [],
                        \"rules_of_interaction\": [],
                        \"rules_of_conflict\": [],
                        \"rules_of_consequence\": [],
                        \"world_restrictions\": [],
                        \"societal_rules\": [],
                        \"enforced_rules\": [],
                        \"secret_rules\": [],
                        \"exceptions_and_edge_cases\": [],
                        \"support_for_internal_logic\": \"\",
                        \"support_for_story\": \"\",
                        \"support_for_characters\": \"\"
                    }
                ]
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Systems directly support the established Story Book.
                - Systems fit the established world, characters, factions, and creatures.
                - Every system has clear and coherent mechanics.
                - Limitations are meaningful and create constraint.
                - Rules are consistent and internally logical.
                - Systems are detailed enough for future story generation.
                - No contradiction exists with the established story or world.
                - The writing feels professionally developed.
                - The systems feel original, natural, internally logical, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step8Prompt(): string
    {
        $prompt = "
            You are a professional story timeline development AI, chronology specialist, history architect, and story development expert.

            Your task is to create the complete timeline foundation for a professionally developed Story Book.

            This step focuses on defining the chronological history and important events that connect the established story, world, characters, locations, factions, creatures, and systems.

            The timeline must feel original, logically coherent, historically meaningful, narratively purposeful, and naturally connected to everything established before this step.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            The following established locations are authoritative:

            {{locations}}

            The following established factions are authoritative:

            {{factions}}

            The following established creatures are authoritative:

            {{creatures}}

            The following established systems are authoritative:

            {{systems}}

            Use the established foundation, characters, World Bible, locations, factions, creatures, and systems as the primary source for all timeline decisions.

            Maintain consistency with the established story, world, characters, locations, factions, creatures, and systems.

            Do not unnecessarily change, contradict, or replace the established creative direction.

            Create a timeline that naturally emerges from and connects the established story, world, and characters.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete timeline foundation required for future Story Book development.

            Develop:

                1. Timeline
                2. Major Events
                3. Milestones
                4. Historical Flow

            Every event must have a meaningful connection to the established story and world.

            ==================================================
            TIMELINE
            ==================================================

            Create the chronological sequence of important events in the world and story.

            For each event establish:

                - Event name
                - Time period
                - Chronological position
                - Event type
                - Location
                - Characters involved
                - Factions involved
                - Creatures involved
                - Systems involved
                - Event description
                - Short-term consequences
                - Long-term consequences
                - Importance to the world
                - Importance to the story
                - Emotional significance
                - Narrative function

            Events must connect through logical cause and effect.

            ==================================================
            MAJOR EVENTS
            ==================================================

            Identify and develop the most significant events that shaped the world and drive the story.

            For each major event establish:

                - Event name
                - Time period
                - Location
                - Participants
                - Background and causes
                - What happened
                - Immediate effects
                - Long-term effects
                - Turning point consequences
                - Importance to the world
                - Importance to the story
                - Importance to the characters
                - Connection to the present story
                - Connection to the central conflict
                - How it shapes future events

            Major events must explain why the story world and characters are in their current state.

            ==================================================
            MILESTONES
            ==================================================

            Define the important markers of change, progress, and development.

            For each milestone establish:

                - Milestone name
                - Time period
                - Milestone type
                - What changed
                - Who was affected
                - Significance to the world
                - Significance to the story
                - Significance to the characters
                - Character development connection
                - Relationship development connection
                - Symbolic or thematic meaning
                - Connection to future events

            Milestones must represent meaningful points of change within the story and world.

            ==================================================
            HISTORICAL FLOW
            ==================================================

            Describe how the world and story developed over time as a connected sequence.

            Establish:

                - Major historical eras
                - The progression from each era to the next
                - Key transitions between eras
                - How the established world history developed
                - How the established culture developed
                - How the established factions developed
                - How the established creatures developed
                - How the established systems developed
                - How the established characters are shaped by history
                - The chain of cause and effect across history
                - How the past leads to the present story
                - How the present story is positioned within history
                - The overall direction of history
                - What the future may hold

            Historical flow must make the world and story feel connected across time.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established foundation, characters, World Bible, locations, factions, creatures, and systems.

            Ensure:

                - Events fit the established world history.
                - Events connect to the established characters.
                - Events respect the established factions and creatures.
                - Events respect the established systems.
                - Events support the established story conflict.
                - The chronology is logically consistent.
                - Character histories align with world history.
                - No event contradicts established story information.

            If the established context leaves an event unspecified, make a strong creative decision that best supports the existing story, world, and characters.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the timeline foundation so it can support future generation steps.

            The timeline should provide enough information for future generation of:

                - Scene plans
                - Chapter plans
                - Dialogue
                - Story events
                - Character development
                - Conflicts and confrontations
                - Flashbacks and history
                - Future story direction
                - Illustrations
                - Visual historical references

            Maintain timeline consistency so future generations can use this foundation as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create the timeline with the judgment of an experienced professional writer and history architect.

            Write with:

                - Natural and confident creative judgment
                - Strong chronological logic
                - Specific and meaningful details
                - Believable cause and effect
                - Purposeful historical development
                - Meaningful emotional milestones
                - Narrative relevance
                - Fresh and distinctive ideas

            Avoid:

                - Generic history templates
                - Events without meaningful consequence
                - Contradictory chronology
                - Disconnected historical details
                - Events that do not serve the established story
                - Unnecessary complexity

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The timeline foundation should:

                - Fit the established Story Book foundation.
                - Fit the established characters.
                - Fit the established World Bible.
                - Fit the established locations, factions, creatures, and systems.
                - Create a logically coherent chronology.
                - Identify meaningful major events.
                - Provide important milestones.
                - Establish a clear historical flow.
                - Align character and world history.
                - Support future story development.
                - Support future scene and chapter generation.
                - Maintain thematic coherence.
                - Feel original, natural, historically alive, and professionally conceived.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"timeline\": [
                    {
                        \"event_name\": \"\",
                        \"time_period\": \"\",
                        \"chronological_position\": \"\",
                        \"event_type\": \"\",
                        \"location\": \"\",
                        \"characters_involved\": [],
                        \"factions_involved\": [],
                        \"creatures_involved\": [],
                        \"systems_involved\": [],
                        \"event_description\": \"\",
                        \"short_term_consequences\": [],
                        \"long_term_consequences\": [],
                        \"importance_to_world\": \"\",
                        \"importance_to_story\": \"\",
                        \"emotional_significance\": \"\",
                        \"narrative_function\": \"\"
                    }
                ],
                \"major_events\": [
                    {
                        \"event_name\": \"\",
                        \"time_period\": \"\",
                        \"location\": \"\",
                        \"participants\": [],
                        \"background_and_causes\": \"\",
                        \"what_happened\": \"\",
                        \"immediate_effects\": [],
                        \"long_term_effects\": [],
                        \"turning_point_consequences\": [],
                        \"importance_to_world\": \"\",
                        \"importance_to_story\": \"\",
                        \"importance_to_characters\": \"\",
                        \"connection_to_present_story\": \"\",
                        \"connection_to_central_conflict\": \"\",
                        \"shapes_future_events\": \"\"
                    }
                ],
                \"milestones\": [
                    {
                        \"milestone_name\": \"\",
                        \"time_period\": \"\",
                        \"milestone_type\": \"\",
                        \"what_changed\": \"\",
                        \"who_was_affected\": [],
                        \"significance_to_world\": \"\",
                        \"significance_to_story\": \"\",
                        \"significance_to_characters\": \"\",
                        \"character_development_connection\": \"\",
                        \"relationship_development_connection\": \"\",
                        \"symbolic_meaning\": \"\",
                        \"connection_to_future_events\": \"\"
                    }
                ],
                \"historical_flow\": {
                    \"major_historical_eras\": [],
                    \"era_progression\": [],
                    \"key_transitions\": [],
                    \"development_of_world_history\": \"\",
                    \"development_of_culture\": \"\",
                    \"development_of_factions\": \"\",
                    \"development_of_creatures\": \"\",
                    \"development_of_systems\": \"\",
                    \"character_shaping_by_history\": \"\",
                    \"chain_of_cause_and_effect\": [],
                    \"how_past_leads_to_present\": \"\",
                    \"present_story_position\": \"\",
                    \"overall_direction_of_history\": \"\",
                    \"future_direction\": \"\"
                }
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - The timeline directly supports the established Story Book.
                - Events fit the established world, characters, factions, creatures, and systems.
                - The chronology is logical and consistent.
                - Major events meaningfully shape the present story.
                - Milestones represent real points of change.
                - Historical flow connects all story elements across time.
                - Character and world history are aligned.
                - The timeline is detailed enough for future story generation.
                - No contradiction exists with the established story or world.
                - The writing feels professionally developed.
                - The timeline feels original, natural, historically coherent, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step9Prompt(): string
    {
        $prompt = "
            You are a professional story structure development AI, narrative architect, plot designer, and story development expert.

            Your task is to create the complete story structure framework for a professionally developed Story Book.

            This step focuses on assembling the established story, world, characters, locations, factions, creatures, systems, and timeline into a complete narrative framework.

            The story structure must feel original, logically paced, narratively coherent, and naturally connected to everything established before this step.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            The following established locations are authoritative:

            {{locations}}

            The following established factions are authoritative:

            {{factions}}

            The following established creatures are authoritative:

            {{creatures}}

            The following established systems are authoritative:

            {{systems}}

            The following established timeline is authoritative:

            {{timeline}}

            Use the established foundation, characters, World Bible, locations, factions, creatures, systems, and timeline as the primary source for all story structure decisions.

            Maintain consistency with the established story, world, characters, and timeline.

            Do not unnecessarily change, contradict, or replace the established creative direction.

            Create a story structure that organizes and strengthens the story the reader will experience.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete story structure framework required for future Story Book development.

            Develop:

                1. Story Outline
                2. Acts and Chapters
                3. Plot Progression
                4. Pacing Guide

            Every structural element must serve the established story and its characters.

            ==================================================
            STORY OUTLINE
            ==================================================

            Create a complete narrative outline for the story.

            Establish:

                - Overall story overview
                - Core narrative spine
                - Main plot throughline
                - Important subplots
                - How subplots connect to the main plot
                - Major story beats
                - Beginning, middle, and end direction
                - Inciting incident placement
                - Rising action progression
                - Climax design
                - Falling action and resolution
                - How the outline connects to the foundation
                - How the outline connects to the characters
                - How the outline connects to the world and timeline
                - How the outline supports the story type and audience

            The outline must read as a complete, coherent narrative roadmap.

            ==================================================
            ACTS AND CHAPTERS
            ==================================================

            Divide the story into acts and chapters with clear purpose and progression.

            For each act establish:

                - Act number
                - Act title
                - Act purpose
                - Story events covered
                - Character development covered
                - Major conflicts covered
                - Emotional progression
                - How the act connects to the previous act
                - How the act connects to the next act
                - Estimated length

            For each chapter establish:

                - Chapter number
                - Chapter title
                - Act association
                - Chapter purpose
                - Events and scenes intended
                - Characters featured
                - Locations featured
                - Conflict and tension
                - Character development
                - Emotional beat
                - Reveals and discoveries
                - How the chapter advances the plot
                - How the chapter advances the characters
                - Estimated length

            Acts and chapters must follow the established story type length requirements and create natural narrative momentum.

            ==================================================
            PLOT PROGRESSION
            ==================================================

            Define how the plot progresses across the entire story.

            Establish:

                - Opening state of the story
                - Inciting incident
                - Escalating complications
                - Key turning points
                - Midpoint stakes change
                - Rising tension sequence
                - All is lost moment
                - Climactic confrontation
                - Resolution and fallout
                - How each progression step builds on the previous
                - How causes create effects
                - How character choices drive progression
                - How conflicts escalate logically
                - How the established twists connect
                - How the story reaches its established climax direction
                - How the story reaches its established resolution direction

            Plot progression must follow the established foundation's story progression and climax direction.

            ==================================================
            PACING GUIDE
            ==================================================

            Create guidance for controlling the rhythm and speed of the narrative.

            Establish:

                - Pacing objectives for the opening
                - Pacing objectives for the middle
                - Pacing objectives for the climax
                - Pacing objectives for the resolution
                - Moments requiring slower pacing
                - Moments requiring faster pacing
                - How to balance action, dialogue, and description
                - How to build and release tension
                - How to vary scene length
                - How to vary chapter length
                - Emotional pacing guidance
                - Suspense pacing guidance
                - How to manage information reveals
                - Common pacing risks to avoid
                - How the pacing supports the story type and audience

            Pacing guidance must be specific and practically usable during chapter and scene generation.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established foundation, characters, World Bible, locations, factions, creatures, systems, and timeline.

            Ensure:

                - The structure follows the established story progression.
                - The structure respects the established climax and resolution.
                - The structure honors the established character arcs.
                - The structure uses the established world and timeline correctly.
                - Chapters and acts fit the established story type length.
                - The structure supports the established themes.
                - No structural element contradicts established story information.

            If the established context leaves a structural decision unspecified, make a strong creative decision that best serves the existing story.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the story structure so it can support future generation steps.

            The structure should provide enough information for future generation of:

                - Twists and foreshadowing
                - Scene plans
                - Chapter plans
                - Dialogue plans
                - Page plans
                - Story events
                - Character development
                - Illustrations
                - The final Story Book assembly

            Maintain structure consistency so future generations can use this framework as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create the story structure with the judgment of an experienced professional writer and narrative architect.

            Write with:

                - Natural and confident creative judgment
                - Strong narrative logic
                - Specific and meaningful details
                - Believable plot cause and effect
                - Purposeful act and chapter design
                - Clear emotional progression
                - Consistent pacing judgment
                - Narrative relevance
                - Fresh and distinctive ideas

            Avoid:

                - Generic plot templates
                - Structural padding
                - Contradictory progression
                - Disconnected chapters
                - Pacing that does not suit the story type
                - Structure that does not serve the established story

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The story structure should:

                - Fit the established Story Book foundation and characters.
                - Fit the established World Bible and timeline.
                - Create a coherent and complete story outline.
                - Provide clear acts and chapters.
                - Establish compelling plot progression.
                - Provide practical pacing guidance.
                - Support future story development.
                - Support future twist, scene, and chapter generation.
                - Maintain thematic coherence.
                - Feel original, natural, professionally designed, and intentional.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"story_outline\": {
                    \"overall_overview\": \"\",
                    \"core_narrative_spine\": \"\",
                    \"main_plot_throughline\": \"\",
                    \"important_subplots\": [],
                    \"subplot_connections\": [],
                    \"major_story_beats\": [],
                    \"inciting_incident\": \"\",
                    \"rising_action_progression\": [],
                    \"climax_design\": \"\",
                    \"falling_action_and_resolution\": \"\",
                    \"connection_to_foundation\": \"\",
                    \"connection_to_characters\": \"\",
                    \"connection_to_world_and_timeline\": \"\"
                },
                \"acts_and_chapters\": {
                    \"acts\": [
                        {
                            \"act_number\": 1,
                            \"act_title\": \"\",
                            \"act_purpose\": \"\",
                            \"story_events_covered\": [],
                            \"character_development_covered\": [],
                            \"major_conflicts_covered\": [],
                            \"emotional_progression\": \"\",
                            \"connection_to_previous_act\": \"\",
                            \"connection_to_next_act\": \"\",
                            \"estimated_length\": \"\"
                        }
                    ],
                    \"chapters\": [
                        {
                            \"chapter_number\": 1,
                            \"chapter_title\": \"\",
                            \"act_association\": \"\",
                            \"chapter_purpose\": \"\",
                            \"events_and_scenes_intended\": [],
                            \"characters_featured\": [],
                            \"locations_featured\": [],
                            \"conflict_and_tension\": \"\",
                            \"character_development\": \"\",
                            \"emotional_beat\": \"\",
                            \"reveals_and_discoveries\": [],
                            \"advances_plot\": \"\",
                            \"advances_characters\": \"\",
                            \"estimated_length\": \"\"
                        }
                    ]
                },
                \"plot_progression\": {
                    \"opening_state\": \"\",
                    \"inciting_incident\": \"\",
                    \"escalating_complications\": [],
                    \"key_turning_points\": [],
                    \"midpoint_stakes_change\": \"\",
                    \"rising_tension_sequence\": [],
                    \"all_is_lost_moment\": \"\",
                    \"climactic_confrontation\": \"\",
                    \"resolution_and_fallout\": \"\",
                    \"cause_and_effect_chain\": [],
                    \"character_choice_drivers\": [],
                    \"conflict_escalation\": [],
                    \"twist_connections\": [],
                    \"climax_direction\": \"\",
                    \"resolution_direction\": \"\"
                },
                \"pacing_guide\": {
                    \"opening_pacing\": \"\",
                    \"middle_pacing\": \"\",
                    \"climax_pacing\": \"\",
                    \"resolution_pacing\": \"\",
                    \"slower_pacing_moments\": [],
                    \"faster_pacing_moments\": [],
                    \"action_dialogue_description_balance\": \"\",
                    \"tension_build_and_release\": \"\",
                    \"scene_length_variation\": \"\",
                    \"chapter_length_variation\": \"\",
                    \"emotional_pacing\": \"\",
                    \"suspense_pacing\": \"\",
                    \"information_reveal_management\": \"\",
                    \"pacing_risks_to_avoid\": [],
                    \"pacing_support_for_story_type\": \"\"
                }
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - The structure directly supports the established Story Book.
                - The story outline is complete and coherent.
                - Acts and chapters are clear and purposeful.
                - Plot progression follows the established foundation.
                - Pacing guidance is practical and specific.
                - The structure is detailed enough for future story generation.
                - No contradiction exists with the established story or world.
                - The writing feels professionally developed.
                - The structure feels original, natural, professionally designed, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step10Prompt(): string
    {
        $prompt = "
            You are a professional story twist development AI, narrative surprise designer, foreshadowing specialist, and story development expert.

            Your task is to create the complete twists and foreshadowing foundation for a professionally developed Story Book.

            This step focuses on defining the hidden narrative elements, surprises, reveals, and clues that will enrich the established story structure.

            The twists must feel original, earned, internally consistent, narratively meaningful, and naturally connected to the established story structure and all prior context.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            The following established locations are authoritative:

            {{locations}}

            The following established factions are authoritative:

            {{factions}}

            The following established creatures are authoritative:

            {{creatures}}

            The following established systems are authoritative:

            {{systems}}

            The following established timeline is authoritative:

            {{timeline}}

            The following established story structure is authoritative:

            {{story_structure}}

            Use the established foundation, characters, World Bible, locations, factions, creatures, systems, timeline, and story structure as the primary source for all twist decisions.

            Maintain consistency with the established story, world, characters, and structure.

            Do not unnecessarily change, contradict, or replace the established creative direction.

            Create twists that naturally emerge from and strengthen the established story structure.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete twists and foreshadowing foundation required for future Story Book development.

            Develop:

                1. Twists
                2. Foreshadowing
                3. Hidden Clues
                4. Reveal Points

            Every twist must be earned, consistent, and connected to the established story.

            ==================================================
            TWISTS
            ==================================================

            Create the narrative surprises that reshape the reader's understanding of the story.

            For each twist establish:

                - Twist name
                - Twist type
                - Story location
                - What the reader believes beforehand
                - What is actually true
                - What is revealed
                - Characters affected
                - Why the twist matters
                - How the twist changes the story
                - How the twist changes character understanding
                - How the twist was set up
                - Emotional impact
                - Connection to the story structure
                - Connection to the plot progression
                - Connection to the timeline
                - Risks of the twist
                - How the twist will feel earned

            Twists must feel surprising yet inevitable in hindsight.

            ==================================================
            FORESHADOWING
            ==================================================

            Design the preparation and hinting that makes future moments land.

            For each foreshadowing element establish:

                - Foreshadowing name
                - What it foreshadows
                - Where it appears
                - How it is presented
                - How subtle it should be
                - What the reader notices initially
                - What the reader understands on rereading
                - Connection to an established twist or reveal
                - Connection to the story structure
                - How it remains consistent with the world
                - How it supports the story theme
                - Balancing subtlety with recognition

            Foreshadowing must reward attentive readers without revealing the twist prematurely.

            ==================================================
            HIDDEN CLUES
            ==================================================

            Define the specific clues and details that prepare the reveals.

            For each hidden clue establish:

                - Clue name
                - Associated twist or reveal
                - Where the clue appears
                - How the clue is presented
                - What the clue suggests
                - How the clue misleads the reader
                - How the clue is connected to other clues
                - When the clue becomes meaningful
                - Who notices the clue
                - How the clue affects the characters
                - How the clue affects the reader
                - How the clue remains consistent with the story

            Hidden clues must be discoverable on rereading and consistent with the story logic.

            ==================================================
            REVEAL POINTS
            ==================================================

            Define when and how each major revelation is delivered.

            For each reveal establish:

                - Reveal name
                - Associated twist
                - Story location
                - Chapter or act placement
                - Build-up before the reveal
                - The moment of reveal
                - How the reveal is delivered
                - Who delivers the reveal
                - Immediate reaction
                - Reader impact
                - Character impact
                - Consequences after the reveal
                - How the story changes after the reveal
                - Connection to the story structure
                - How the reveal was foreshadowed

            Reveals must create meaningful emotional and narrative payoff.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established foundation, characters, World Bible, locations, factions, creatures, systems, timeline, and story structure.

            Ensure:

                - Twists honor the established plot progression.
                - Twists do not break established world rules.
                - Twists remain consistent with established character motivations.
                - Foreshadowing fits the established structure.
                - Hidden clues respect the established timeline.
                - Reveal points match the established acts and chapters.
                - No twist contradicts established story information.
                - Each twist is checked for internal consistency.

            If the established context leaves a twist decision unspecified, make a strong creative decision that best serves the existing story.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the twists foundation so it can support future generation steps.

            The twists should provide enough information for future generation of:

                - Scene plans
                - Chapter plans
                - Dialogue plans
                - Page plans
                - Story events
                - Character reactions
                - Emotional moments
                - Illustrations
                - The final Story Book assembly

            Maintain twist consistency so future generations can use this foundation as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create twists with the judgment of an experienced professional writer and narrative designer.

            Write with:

                - Natural and confident creative judgment
                - Strong narrative surprise
                - Logical consistency
                - Specific and meaningful details
                - Believable foreshadowing
                - Purposeful reveals
                - Emotional impact
                - Narrative relevance
                - Fresh and distinctive ideas

            Avoid:

                - Unearned or random twists
                - Twists that break established logic
                - Obvious foreshadowing
                - Misleading without fairness
                - Twists disconnected from the story
                - Overloaded or excessive reveals

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The twists foundation should:

                - Fit the established Story Book foundation and characters.
                - Fit the established World Bible and timeline.
                - Connect with the established story structure.
                - Create surprising yet earned twists.
                - Provide subtle and effective foreshadowing.
                - Provide hidden clues discoverable on rereading.
                - Provide well-timed reveal points.
                - Support future story development.
                - Support future scene and chapter generation.
                - Maintain thematic coherence.
                - Feel original, natural, surprising, and professionally conceived.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"twists\": [
                    {
                        \"twist_name\": \"\",
                        \"twist_type\": \"\",
                        \"story_location\": \"\",
                        \"belief_beforehand\": \"\",
                        \"actual_truth\": \"\",
                        \"what_is_revealed\": \"\",
                        \"characters_affected\": [],
                        \"why_it_matters\": \"\",
                        \"how_it_changes_story\": \"\",
                        \"how_it_changes_characters\": \"\",
                        \"how_it_was_set_up\": \"\",
                        \"emotional_impact\": \"\",
                        \"connection_to_story_structure\": \"\",
                        \"connection_to_plot_progression\": \"\",
                        \"connection_to_timeline\": \"\",
                        \"risks\": [],
                        \"why_it_feels_earned\": \"\"
                    }
                ],
                \"foreshadowing\": [
                    {
                        \"foreshadowing_name\": \"\",
                        \"foreshadows\": \"\",
                        \"where_it_appears\": \"\",
                        \"how_it_is_presented\": \"\",
                        \"subtlety_level\": \"\",
                        \"initial_reader_notice\": \"\",
                        \"rereading_understanding\": \"\",
                        \"connection_to_twist\": \"\",
                        \"connection_to_story_structure\": \"\",
                        \"world_consistency\": \"\",
                        \"theme_support\": \"\",
                        \"subtlety_balance\": \"\"
                    }
                ],
                \"hidden_clues\": [
                    {
                        \"clue_name\": \"\",
                        \"associated_twist\": \"\",
                        \"where_clue_appears\": \"\",
                        \"how_clue_is_presented\": \"\",
                        \"what_clue_suggests\": \"\",
                        \"how_clue_misleads\": \"\",
                        \"clue_connections\": [],
                        \"when_clue_becomes_meaningful\": \"\",
                        \"who_notices_clue\": \"\",
                        \"effect_on_characters\": \"\",
                        \"effect_on_reader\": \"\",
                        \"story_logic_consistency\": \"\"
                    }
                ],
                \"reveal_points\": [
                    {
                        \"reveal_name\": \"\",
                        \"associated_twist\": \"\",
                        \"story_location\": \"\",
                        \"chapter_or_act_placement\": \"\",
                        \"build_up\": \"\",
                        \"moment_of_reveal\": \"\",
                        \"how_reveal_is_delivered\": \"\",
                        \"who_delivers_reveal\": \"\",
                        \"immediate_reaction\": \"\",
                        \"reader_impact\": \"\",
                        \"character_impact\": \"\",
                        \"consequences_after_reveal\": [],
                        \"how_story_changes\": \"\",
                        \"connection_to_story_structure\": \"\",
                        \"foreshadowing_connection\": \"\"
                    }
                ]
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Twists directly connect with the established Story Structure.
                - Every twist is earned and internally consistent.
                - Foreshadowing is subtle but effective.
                - Hidden clues are discoverable on rereading.
                - Reveal points are well-timed and meaningful.
                - No twist contradicts the established story or world.
                - The twists are detailed enough for future story generation.
                - The writing feels professionally developed.
                - The twists feel original, natural, surprising, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step11Prompt(): string
    {
        $prompt = "
            You are a professional scene planning AI, scene designer, story sequencing specialist, and story development expert.

            Your task is to create the complete scene plan foundation for a professionally developed Story Book.

            This step focuses on breaking the established story structure into detailed, ordered scenes that can be directly used for chapter, dialogue, and page planning.

            The scenes must feel original, vividly staged, narratively purposeful, and naturally connected to the established story structure and all prior context.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            The following established locations are authoritative:

            {{locations}}

            The following established factions are authoritative:

            {{factions}}

            The following established creatures are authoritative:

            {{creatures}}

            The following established systems are authoritative:

            {{systems}}

            The following established timeline is authoritative:

            {{timeline}}

            The following established story structure is authoritative:

            {{story_structure}}

            The following established twists and foreshadowing are authoritative:

            {{twists_and_foreshadowing}}

            Use the established foundation, characters, World Bible, locations, factions, creatures, systems, timeline, story structure, and twists as the primary source for all scene decisions.

            Maintain consistency with the established story, world, characters, structure, and twists.

            Do not unnecessarily change, contradict, or replace the established creative direction.

            Create scenes that naturally emerge from and fulfill the established story structure.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete scene plan foundation required for future Story Book development.

            Develop:

                1. Scene List
                2. Scene Objectives
                3. Locations
                4. Point of View and Tone

            Every scene must advance the story meaningfully and serve the established structure.

            ==================================================
            SCENE LIST
            ==================================================

            Break the complete story into an ordered list of scenes.

            For each scene establish:

                - Scene number
                - Scene title
                - Parent chapter
                - Scene type
                - Characters present
                - Location
                - Time and timing
                - What happens in the scene
                - What is accomplished
                - Conflict present
                - Emotional beat
                - Reveals or discoveries
                - How the scene connects to the previous scene
                - How the scene connects to the next scene
                - Scene length estimate

            Scenes must follow the story structure acts and chapters and create continuous narrative flow.

            ==================================================
            SCENE OBJECTIVES
            ==================================================

            Define the purpose of every scene within the story.

            For each scene establish:

                - Scene objective
                - What the scene must accomplish
                - Plot information delivered
                - Character development delivered
                - World information delivered
                - Conflict advanced
                - Stakes reinforced
                - Emotional purpose
                - Thematic purpose
                - Relationship development
                - Escape or transition function
                - How the objective advances the story
                - How the objective serves the chapter
                - How the objective serves the act
                - Consequences the scene sets up

            Every scene must have a clear reason to exist.

            ==================================================
            LOCATIONS
            ==================================================

            Define where each scene takes place.

            For each scene location establish:

                - Location name
                - Location type
                - Scene association
                - Atmosphere and mood
                - Sensory details
                - Visual features
                - Time of day and lighting
                - Weather conditions
                - How the location affects the scene
                - How the location affects the characters
                - How the location supports the tone
                - Alternative locations if useful
                - Illustration potential
                - Connection to established locations

            Locations must match the established locations and world information.

            ==================================================
            POINT OF VIEW AND TONE
            ==================================================

            Define the narrative perspective and emotional tone for every scene.

            For each scene establish:

                - Point of view character
                - Perspective type
                - Narrative distance
                - What the POV character knows
                - What the POV character feels
                - How the POV shapes the scene
                - Scene tone
                - Emotional arc within the scene
                - Humor, seriousness, or tension level
                - Atmosphere and mood
                - Dialogue style suited to the tone
                - Description style suited to the tone
                - How the tone supports the story
                - How the tone supports the chapter
                - How the tone supports the audience

            Point of view and tone must remain consistent with the established characters and story.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established foundation, characters, World Bible, locations, factions, creatures, systems, timeline, story structure, and twists.

            Ensure:

                - Scenes follow the established acts and chapters.
                - Scenes honor the established plot progression.
                - Scenes include the established twists and reveals correctly.
                - Scene locations match the established locations.
                - Scene characters match the established characters.
                - Scene timing matches the established timeline.
                - No scene contradicts established story information.

            If the established context leaves a scene decision unspecified, make a strong creative decision that best serves the existing story.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the scene plan so it can support future generation steps.

            The scenes should provide enough information for future generation of:

                - Chapter plans
                - Dialogue plans
                - Page plans
                - Story events
                - Character interactions
                - Emotional moments
                - Illustrations
                - Visual scene references
                - The final Story Book assembly

            Maintain scene consistency so future generations can use this plan as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create scenes with the judgment of an experienced professional writer and scene designer.

            Write with:

                - Natural and confident creative judgment
                - Strong visual staging
                - Specific and meaningful details
                - Clear narrative purpose
                - Believable character staging
                - Effective tone control
                - Consistent pacing
                - Narrative relevance
                - Fresh and distinctive ideas

            Avoid:

                - Generic scene descriptions
                - Scenes without clear purpose
                - Repetitive or redundant scenes
                - Disconnected scene sequences
                - Scenes that contradict the established story
                - Unnecessary scenes

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The scene plan should:

                - Fit the established Story Book foundation and characters.
                - Fit the established World Bible and locations.
                - Connect with the established story structure and twists.
                - Break the complete story into detailed scenes.
                - Give every scene a clear objective.
                - Define locations for every scene.
                - Provide clear point of view and tone.
                - Support future story development.
                - Support future chapter, dialogue, and page generation.
                - Maintain thematic coherence.
                - Feel original, natural, vivid, and professionally conceived.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"scene_list\": [
                    {
                        \"scene_number\": 1,
                        \"scene_title\": \"\",
                        \"parent_chapter\": \"\",
                        \"scene_type\": \"\",
                        \"characters_present\": [],
                        \"location\": \"\",
                        \"time_and_timing\": \"\",
                        \"what_happens\": \"\",
                        \"what_is_accomplished\": \"\",
                        \"conflict_present\": \"\",
                        \"emotional_beat\": \"\",
                        \"reveals_and_discoveries\": [],
                        \"connection_to_previous_scene\": \"\",
                        \"connection_to_next_scene\": \"\",
                        \"scene_length_estimate\": \"\"
                    }
                ],
                \"scene_objectives\": [
                    {
                        \"scene_number\": 1,
                        \"scene_objective\": \"\",
                        \"must_accomplish\": [],
                        \"plot_information_delivered\": [],
                        \"character_development_delivered\": [],
                        \"world_information_delivered\": [],
                        \"conflict_advanced\": \"\",
                        \"stakes_reinforced\": \"\",
                        \"emotional_purpose\": \"\",
                        \"thematic_purpose\": \"\",
                        \"relationship_development\": \"\",
                        \"escape_or_transition_function\": \"\",
                        \"advances_story\": \"\",
                        \"serves_chapter\": \"\",
                        \"serves_act\": \"\",
                        \"consequences_set_up\": []
                    }
                ],
                \"locations\": [
                    {
                        \"scene_number\": 1,
                        \"location_name\": \"\",
                        \"location_type\": \"\",
                        \"atmosphere_and_mood\": \"\",
                        \"sensory_details\": [],
                        \"visual_features\": [],
                        \"time_of_day_and_lighting\": \"\",
                        \"weather_conditions\": \"\",
                        \"effect_on_scene\": \"\",
                        \"effect_on_characters\": \"\",
                        \"tone_support\": \"\",
                        \"alternative_locations\": [],
                        \"illustration_potential\": \"\",
                        \"connection_to_established_locations\": \"\"
                    }
                ],
                \"pov_and_tone\": [
                    {
                        \"scene_number\": 1,
                        \"point_of_view_character\": \"\",
                        \"perspective_type\": \"\",
                        \"narrative_distance\": \"\",
                        \"pov_knowledge\": \"\",
                        \"pov_emotions\": \"\",
                        \"how_pov_shapes_scene\": \"\",
                        \"scene_tone\": \"\",
                        \"emotional_arc\": \"\",
                        \"tension_level\": \"\",
                        \"atmosphere_and_mood\": \"\",
                        \"dialogue_style\": \"\",
                        \"description_style\": \"\",
                        \"tone_support_for_story\": \"\",
                        \"tone_support_for_chapter\": \"\",
                        \"tone_support_for_audience\": \"\"
                    }
                ]
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Scenes directly fulfill the established Story Structure.
                - The complete story is broken into detailed scenes.
                - Every scene has a clear objective.
                - Locations are defined and consistent.
                - Point of view and tone are clear and consistent.
                - Scenes are detailed enough for chapter, dialogue, and page planning.
                - No scene contradicts the established story or world.
                - The writing feels professionally developed.
                - The scenes feel original, natural, vivid, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step12Prompt(): string
    {
        $prompt = "
            You are a professional dialogue development AI, character voice specialist, conversation designer, and story development expert.

            Your task is to create the complete dialogue plan foundation for a professionally developed Story Book.

            This step focuses on defining how characters speak, what conversations must happen, and how dialogue carries the story forward.

            The dialogue must feel original, natural, distinctly voiced, narratively purposeful, and naturally connected to the established characters and scenes.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            The following established locations are authoritative:

            {{locations}}

            The following established factions are authoritative:

            {{factions}}

            The following established creatures are authoritative:

            {{creatures}}

            The following established systems are authoritative:

            {{systems}}

            The following established timeline is authoritative:

            {{timeline}}

            The following established story structure is authoritative:

            {{story_structure}}

            The following established twists and foreshadowing are authoritative:

            {{twists_and_foreshadowing}}

            The following established scene plans are authoritative:

            {{scene_plans}}

            Use the established characters and scene plans as the primary source for all dialogue decisions.

            Maintain consistency with the established story, world, characters, structure, and scenes.

            Do not unnecessarily change, contradict, or replace the established creative direction.

            Create dialogue that sounds like the established characters and serves the established scenes.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete dialogue plan foundation required for future Story Book development.

            Develop:

                1. Dialogue Bank
                2. Character Voice
                3. Conversation Flow
                4. Key Dialogues

            Every dialogue element must serve the established characters and scenes.

            ==================================================
            DIALOGUE BANK
            ==================================================

            Create a reusable bank of dialogue lines, exchanges, and exchanges grouped by purpose.

            For each dialogue entry establish:

                - Dialogue identifier
                - Scene association
                - Characters involved
                - Dialogue type
                - The dialogue lines
                - Emotional tone
                - Subtext
                - Purpose in the scene
                - Information conveyed
                - Character revealed
                - How the dialogue connects to the scene objective
                - How the dialogue connects to the story
                - Usage notes

            The dialogue bank must be specific enough to be reused directly during page and book generation.

            ==================================================
            CHARACTER VOICE
            ==================================================

            Define how each speaking character sounds.

            For each character establish:

                - Character name
                - Voice summary
                - Vocabulary level
                - Sentence patterns
                - Speech rhythm
                - Favorite expressions
                - Verbal habits
                - Humor style
                - Emotional expression
                - Confidence and hesitation patterns
                - How the character speaks to different people
                - How the character speaks under pressure
                - How the voice reflects backstory
                - How the voice reflects personality
                - Voice consistency notes
                - Voice weaknesses to avoid

            Character voices must remain distinct, recognizable, and consistent throughout the story.

            ==================================================
            CONVERSATION FLOW
            ==================================================

            Define how conversations develop within the established scenes.

            For each conversation establish:

                - Conversation identifier
                - Scene association
                - Participants
                - Conversation purpose
                - Opening beat
                - Escalation and turns
                - Emotional shifts
                - Conflict and tension
                - Information exchange
                - Subtext progression
                - Key decisions or realizations
                - Closing beat
                - How the conversation advances the scene
                - How the conversation advances the story
                - Length and pacing guidance

            Conversation flow must match the established scene plans and stay true to the characters.

            ==================================================
            KEY DIALOGUES
            ==================================================

            Identify and design the most important dialogue moments of the story.

            For each key dialogue establish:

                - Key dialogue name
                - Story location
                - Scene association
                - Characters involved
                - Story significance
                - What must be communicated
                - What must NOT be revealed
                - Emotional stakes
                - Subtext
                - The turning point of the dialogue
                - The climactic line or exchange
                - Immediate reactions
                - Consequences after the dialogue
                - How the dialogue connects to the twists and reveals
                - How the dialogue connects to the story structure
                - Writing guidance

            Key dialogues must carry the emotional and narrative weight of the story.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established foundation, characters, World Bible, locations, factions, creatures, systems, timeline, story structure, twists, and scene plans.

            Ensure:

                - Character voices match established personalities.
                - Dialogue fits the established scenes.
                - Conversations respect the established timeline.
                - Key dialogues align with reveals and twists.
                - Dialogue tone matches the established story type and audience.
                - No dialogue contradicts established story information.

            If the established context leaves a dialogue decision unspecified, make a strong creative decision that best serves the existing story.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the dialogue plan so it can support future generation steps.

            The dialogue plan should provide enough information for future generation of:

                - Page plans
                - Final narrative passages
                - Character interactions
                - Emotional moments
                - Illustrations
                - The final Story Book compilation

            Maintain dialogue consistency so future generations can use this plan as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create dialogue with the judgment of an experienced professional writer and dialogue specialist.

            Write with:

                - Natural and confident creative judgment
                - Distinct character voices
                - Believable conversation rhythm
                - Specific and meaningful lines
                - Effective subtext
                - Emotional authenticity
                - Narrative relevance
                - Fresh and distinctive ideas

            Avoid:

                - Generic or interchangeable voices
                - On-the-nose exposition
                - Dialogue without purpose
                - Repetitive exchanges
                - Dialogue that contradicts the characters
                - Unnecessary conversations

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The dialogue plan should:

                - Fit the established Story Book foundation and characters.
                - Fit the established scene plans.
                - Give every speaking character a distinct voice.
                - Provide a reusable dialogue bank.
                - Establish clear conversation flow.
                - Deliver powerful key dialogues.
                - Support future page and book generation.
                - Maintain thematic coherence.
                - Feel original, natural, authentic, and professionally written.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"dialogue_bank\": [
                    {
                        \"dialogue_identifier\": \"\",
                        \"scene_association\": \"\",
                        \"characters_involved\": [],
                        \"dialogue_type\": \"\",
                        \"dialogue_lines\": [],
                        \"emotional_tone\": \"\",
                        \"subtext\": \"\",
                        \"purpose_in_scene\": \"\",
                        \"information_conveyed\": [],
                        \"character_revealed\": \"\",
                        \"connection_to_scene_objective\": \"\",
                        \"connection_to_story\": \"\",
                        \"usage_notes\": \"\"
                    }
                ],
                \"character_voice\": [
                    {
                        \"character_name\": \"\",
                        \"voice_summary\": \"\",
                        \"vocabulary_level\": \"\",
                        \"sentence_patterns\": \"\",
                        \"speech_rhythm\": \"\",
                        \"favorite_expressions\": [],
                        \"verbal_habits\": [],
                        \"humor_style\": \"\",
                        \"emotional_expression\": \"\",
                        \"confidence_and_hesitation\": \"\",
                        \"speech_to_different_people\": \"\",
                        \"speech_under_pressure\": \"\",
                        \"voice_reflects_backstory\": \"\",
                        \"voice_reflects_personality\": \"\",
                        \"voice_consistency_notes\": \"\",
                        \"voice_weaknesses_to_avoid\": []
                    }
                ],
                \"conversation_flow\": [
                    {
                        \"conversation_identifier\": \"\",
                        \"scene_association\": \"\",
                        \"participants\": [],
                        \"conversation_purpose\": \"\",
                        \"opening_beat\": \"\",
                        \"escalation_and_turns\": [],
                        \"emotional_shifts\": [],
                        \"conflict_and_tension\": \"\",
                        \"information_exchange\": [],
                        \"subtext_progression\": \"\",
                        \"key_decisions_or_realizations\": [],
                        \"closing_beat\": \"\",
                        \"advances_scene\": \"\",
                        \"advances_story\": \"\",
                        \"length_and_pacing\": \"\"
                    }
                ],
                \"key_dialogues\": [
                    {
                        \"key_dialogue_name\": \"\",
                        \"story_location\": \"\",
                        \"scene_association\": \"\",
                        \"characters_involved\": [],
                        \"story_significance\": \"\",
                        \"must_communicate\": [],
                        \"must_not_reveal\": [],
                        \"emotional_stakes\": \"\",
                        \"subtext\": \"\",
                        \"turning_point\": \"\",
                        \"climactic_line_or_exchange\": \"\",
                        \"immediate_reactions\": \"\",
                        \"consequences\": [],
                        \"connection_to_twists\": \"\",
                        \"connection_to_story_structure\": \"\",
                        \"writing_guidance\": \"\"
                    }
                ]
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Dialogue directly serves the established characters and scenes.
                - Character voices are distinct and consistent.
                - Conversation flow follows the established scene plans.
                - Key dialogues carry real narrative weight.
                - No dialogue contradicts the established story or world.
                - The dialogue is detailed enough for page and book generation.
                - The writing feels professionally developed.
                - The dialogue feels original, natural, authentic, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step13Prompt(): string
    {
        $prompt = "
            You are a professional page layout AI, illustrated book designer, page pacing specialist, and story development expert.

            Your task is to create the complete page plan foundation for a professionally developed Story Book.

            This step focuses on translating the established scenes and dialogue into a page-by-page layout ready for final narrative and illustration production.

            The page plan must feel original, visually considered, narratively paced, and naturally connected to the established story.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            The following established locations are authoritative:

            {{locations}}

            The following established factions are authoritative:

            {{factions}}

            The following established creatures are authoritative:

            {{creatures}}

            The following established systems are authoritative:

            {{systems}}

            The following established timeline is authoritative:

            {{timeline}}

            The following established story structure is authoritative:

            {{story_structure}}

            The following established twists and foreshadowing are authoritative:

            {{twists_and_foreshadowing}}

            The following established scene plans are authoritative:

            {{scene_plans}}

            The following established dialogue plans are authoritative:

            {{dialogue_plans}}

            Use the established scene plans and dialogue plans as the primary source for all page decisions.

            Maintain consistency with the established story, world, characters, structure, scenes, and dialogue.

            Do not unnecessarily change, contradict, or replace the established creative direction.

            Create a page plan that presents the established story clearly and beautifully.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Create the complete page plan foundation required for final Story Book production.

            Develop:

                1. Page Layout
                2. Page Descriptions
                3. Illustration Notes
                4. Key Points

            Every page must serve the established story and its intended reader experience.

            ==================================================
            PAGE LAYOUT
            ==================================================

            Define the page-by-page structure of the complete book.

            For each page establish:

                - Page number
                - Parent scene
                - Parent chapter
                - Page type
                - Narrative content summary
                - Illustration placement
                - Text placement
                - Layout composition
                - Amount of text
                - Transition into the page
                - Transition out of the page
                - Emotional beat
                - How the page connects to the previous page
                - How the page connects to the next page
                - Pacing role of the page

            Page layout must follow the established scenes and maintain smooth reading flow.

            ==================================================
            PAGE DESCRIPTIONS
            ==================================================

            Describe what appears on every page.

            For each page establish:

                - Page number
                - Page summary
                - Narrative description
                - Visual description
                - Characters present
                - Location
                - Action depicted
                - Mood and atmosphere
                - Text content direction
                - Dialogue used
                - Story information delivered
                - Emotional tone
                - Reader experience
                - Continuity notes

            Page descriptions must be detailed enough to guide final narrative and illustration production.

            ==================================================
            ILLUSTRATION NOTES
            ==================================================

            Provide illustration guidance for every illustrated page.

            For each illustration establish:

                - Page number
                - Illustration summary
                - Main subject
                - Characters depicted
                - Character poses and expressions
                - Setting and background
                - Action captured
                - Composition and framing
                - Perspective and camera angle
                - Lighting and mood
                - Color direction
                - Important visual details
                - Details to avoid
                - Continuity with previous illustrations
                - Illustration type

            Illustration notes must be specific, consistent, and production ready.

            ==================================================
            KEY POINTS
            ==================================================

            Capture the essential content that must appear across the book.

            For each key point establish:

                - Key point identifier
                - Associated page or pages
                - Associated scene
                - What must be communicated
                - Why it matters
                - Story information involved
                - Character information involved
                - Emotional purpose
                - Visual requirement
                - Text requirement
                - Continuity requirement
                - Risk if omitted

            Key points must protect the most important story, character, and visual information.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established foundation, characters, World Bible, locations, factions, creatures, systems, timeline, story structure, twists, scene plans, and dialogue plans.

            Ensure:

                - Pages follow the established scene order.
                - Illustrations match established characters and locations.
                - Dialogue placed on pages matches the dialogue plan.
                - Twists and reveals are placed at the correct story moments.
                - Page pacing matches the established pacing guidance.
                - No page contradicts established story information.

            If the established context leaves a page decision unspecified, make a strong creative decision that best serves the existing story.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the page plan so it can support the final compilation step.

            The page plan should provide enough information for future generation of:

                - Final narrative passages
                - Illustration generation
                - Page text and typography
                - The complete publication-ready Story Book
                - eBook and PDF assembly

            Maintain page consistency so future generations can use this plan as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Create the page plan with the judgment of an experienced professional book designer and story developer.

            Write with:

                - Natural and confident creative judgment
                - Strong visual thinking
                - Specific and meaningful details
                - Believable page pacing
                - Purposeful illustration placement
                - Emotional sensitivity
                - Audience awareness
                - Fresh and distinctive ideas

            Avoid:

                - Generic page descriptions
                - Illustration ideas without story purpose
                - Overcrowded or empty pages
                - Inconsistent visual continuity
                - Pages that contradict the established story
                - Unnecessary pages

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The page plan should:

                - Fit the established Story Book foundation and characters.
                - Fit the established scene and dialogue plans.
                - Cover the complete story page by page.
                - Balance text and illustrations.
                - Provide clear, production-ready illustration notes.
                - Protect essential story and character information.
                - Support the final Story Book compilation.
                - Maintain thematic coherence.
                - Feel original, natural, visually compelling, and professional.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            {
                \"page_layout\": [
                    {
                        \"page_number\": 1,
                        \"parent_scene\": \"\",
                        \"parent_chapter\": \"\",
                        \"page_type\": \"\",
                        \"narrative_content_summary\": \"\",
                        \"illustration_placement\": \"\",
                        \"text_placement\": \"\",
                        \"layout_composition\": \"\",
                        \"amount_of_text\": \"\",
                        \"transition_in\": \"\",
                        \"transition_out\": \"\",
                        \"emotional_beat\": \"\",
                        \"connection_to_previous_page\": \"\",
                        \"connection_to_next_page\": \"\",
                        \"pacing_role\": \"\"
                    }
                ],
                \"page_descriptions\": [
                    {
                        \"page_number\": 1,
                        \"page_summary\": \"\",
                        \"narrative_description\": \"\",
                        \"visual_description\": \"\",
                        \"characters_present\": [],
                        \"location\": \"\",
                        \"action_depicted\": \"\",
                        \"mood_and_atmosphere\": \"\",
                        \"text_content_direction\": \"\",
                        \"dialogue_used\": [],
                        \"story_information_delivered\": [],
                        \"emotional_tone\": \"\",
                        \"reader_experience\": \"\",
                        \"continuity_notes\": \"\"
                    }
                ],
                \"illustration_notes\": [
                    {
                        \"page_number\": 1,
                        \"illustration_summary\": \"\",
                        \"main_subject\": \"\",
                        \"characters_depicted\": [],
                        \"poses_and_expressions\": [],
                        \"setting_and_background\": \"\",
                        \"action_captured\": \"\",
                        \"composition_and_framing\": \"\",
                        \"perspective_and_angle\": \"\",
                        \"lighting_and_mood\": \"\",
                        \"color_direction\": \"\",
                        \"important_visual_details\": [],
                        \"details_to_avoid\": [],
                        \"continuity_with_previous\": \"\",
                        \"illustration_type\": \"\"
                    }
                ],
                \"key_points\": [
                    {
                        \"key_point_identifier\": \"\",
                        \"associated_pages\": [],
                        \"associated_scene\": \"\",
                        \"what_must_be_communicated\": \"\",
                        \"why_it_matters\": \"\",
                        \"story_information\": [],
                        \"character_information\": [],
                        \"emotional_purpose\": \"\",
                        \"visual_requirement\": \"\",
                        \"text_requirement\": \"\",
                        \"continuity_requirement\": \"\",
                        \"risk_if_omitted\": \"\"
                    }
                ]
            }

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Pages directly follow the established scenes.
                - The complete story is covered page by page.
                - Illustrations are production ready and consistent.
                - Dialogue placement matches the dialogue plan.
                - Key points protect essential story information.
                - No page contradicts the established story or world.
                - The page plan is detailed enough for the final compilation.
                - The writing feels professionally developed.
                - The page plan feels original, natural, visually compelling, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }    public static function step14Prompt(): string
    {
        $prompt = "
            You are a professional story narration AI, illustrated book narrator, page writing specialist, and story development expert.

            Your task is to generate the page narration for every page of a professionally developed illustrated Story Book.

            This step focuses on writing the narration text that will appear on each page of the final illustrated Story Book, based on the established page plan and the story context it depends on.

            The narration must feel original, natural, page-paced, and perfectly connected to the established story.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            The following established story structure is authoritative:

            {{story_structure}}

            The following established twists and foreshadowing are authoritative:

            {{twists_and_foreshadowing}}

            The following established scene plans are authoritative:

            {{scene_plans}}

            The following established dialogue plans are authoritative:

            {{dialogue_plans}}

            The following established page plan is authoritative:

            {{page_plan}}

            Use the established page plan as the primary source for every page's narration.

            Maintain consistency with the established story, world, characters, structure, scenes, dialogue, and pages.

            Do not unnecessarily change, contradict, or replace the established creative direction.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Generate the narration for every page of the illustrated Story Book.

            The output must contain exactly one page object for every page in the established page plan layout.

            The final number of page objects must exactly match the number of pages in the established page plan layout.

            Do NOT invent additional pages.

            Do NOT skip any planned page.

            Do NOT return placeholder, empty, or filler narration for any page.

            ==================================================
            PAGE NARRATION
            ==================================================

            Write the narration text for every page of the illustrated Story Book.

            For each page establish:

                - no: the page number, starting at 1 and increasing by exactly 1 for every following page
                - narration: the narration text that appears on the page

            The narration represents what the reader actually experiences on that page.

            It is NOT a screenplay.

            It is NOT a dialogue-only script.

            It is NOT an image prompt.

            It is NOT an explanation of what the AI should draw.

            It is the actual story narration associated with that page.

            The narration must:

                - Match the page plan's narrative content for that page.
                - Follow the established scene, dialogue, and emotional direction.
                - Be written for the page only, not as a long chapter passage.
                - Read naturally and feel like polished storybook prose.
                - Advance the story while also describing the visible moment of the page.
                - Remain short enough that the page still leaves strong space for its illustration.
                - Use language appropriate for the established audience.
                - Stay consistent with the characters, world, timeline, and story continuity.
                - Connect smoothly to the narration of the previous page and the next page.

            If dialogue is appropriate for a page, dialogue may naturally appear inside the narration, but the output remains page narration rather than a dialogue plan.

            ==================================================
            ILLUSTRATION AWARENESS
            ==================================================

            The narration will later be used as input for illustration prompt generation.

            Therefore, every page should describe enough meaningful story action, character activity, setting, or emotional moment that a later AI can derive a coherent illustration from it.

            However, the narration must NOT contain image-generation instructions.

            Do NOT mention camera angles.

            Do NOT mention art styles.

            Do NOT mention rendering.

            Do NOT mention IllustrationType.

            Do NOT mention illustration prompts.

            Do NOT use the narration block to instruct any future drawing or illustration behavior.

            Illustration planning belongs to a later stage, not to this page narration.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established foundation, characters, World Bible, locations, factions, creatures, systems, timeline, story structure, twists, scene plans, dialogue plans, and page plan.

            Ensure:

                - Every page from the page plan receives one narration entry.
                - Narration follows the scene order established by the page plan.
                - Dialogue woven into narration matches the dialogue plan.
                - Twists and reveals are narrated at the correct story moments.
                - Page pacing matches the established pacing guidance.
                - No narration contradicts established story information.

            If the established context leaves a narration decision unspecified, make a strong creative decision that best serves the existing story.

            ==================================================
            FUTURE STORY DEVELOPMENT
            ==================================================

            Design the page narration so it can support the illustration generation steps.

            The page narration should be detailed enough for future generation of:

                - Illustration prompts for each page
                - Final page text and typography
                - The complete publication-ready illustrated Story Book

            Maintain narration consistency so future generations can use it as a reliable reference.

            ==================================================
            WRITING QUALITY
            ==================================================

            Write the narration with the judgment of an experienced professional storybook writer.

            Write with:

                - Natural and confident creative judgment
                - Strong page-level pacing
                - Specific and meaningful details
                - Believable emotional progression
                - Audience awareness
                - Fresh and distinctive language
                - Visual storytelling that invites illustration

            Avoid:

                - Generic or filler narration
                - Page narration that contradicts the established story
                - Excessively long passages that crowd the page
                - Inconsistent character voice
                - Pages that skip important story information

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The page narration should:

                - Fit the established Story Book foundation, characters, and world.
                - Fit the established scene, dialogue, and page plans.
                - Cover the complete story page by page.
                - Read naturally and professionally.
                - Leave clear room for page illustrations.
                - Protect essential story and character information.
                - Support the final illustrated Story Book compilation.
                - Feel original, natural, emotionally engaging, and polished.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            The entire response must be a single JSON object with a pages array:

            {
                \"pages\": [
                    {
                        \"no\": 1,
                        \"narration\": \"\"
                    }
                ]
            }

            Every page object must have exactly two fields:

                - \"no\": the page number. It is an integer, starts at 1, and must be strictly sequential (1, 2, 3, ...) with no gaps, no repeats, and no extra numbering.
                - \"narration\": the page narration. It must be a non-empty string containing the actual story narration for that page.

            The number of page objects in the pages array must exactly equal the number of pages in the established page plan layout.

            Do NOT wrap the JSON in Markdown code fences.

            Do NOT include any explanatory text before or after the JSON.

            Do NOT include any illustration, image, or rendering instructions anywhere in the output.

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Every page from the page plan has exactly one narration entry.
                - Page numbers are unique and complete.
                - Narration directly follows the established page plan and scenes.
                - The narration reads as polished storybook prose.
                - The narration leaves room for page illustrations.
                - No narration contradicts the established story or world.
                - The narration is appropriate for the established audience.
                - The narration is detailed enough for illustration generation.
                - The writing feels professionally developed.
                - The narration feels original, natural, and intentional.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.
        ";

        return $prompt;
    }

    public static function step15Prompt(): string
    {
        $prompt = "
            You are a professional illustration planning AI, visual storytelling specialist, and story development expert.

            Your task is to generate the illustration prompt for every page of a professionally developed illustrated Story Book.

            This step focuses on determining what each page's illustration should depict so that a later image-generation stage can produce the actual artwork.

            The illustration prompt must feel visual, precise, directly useful to an image-generation model, and fully consistent with the established story.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            The following established Story Book foundation is authoritative:

            {{foundation}}

            The following established characters are authoritative:

            {{characters}}

            The following established World Bible is authoritative:

            {{world_bible}}

            The following established locations are authoritative:

            {{locations}}

            The following established factions are authoritative:

            {{factions}}

            The following established creatures are authoritative:

            {{creatures}}

            The following established systems are authoritative:

            {{systems}}

            The following established timeline is authoritative:

            {{timeline}}

            The following established story structure is authoritative:

            {{story_structure}}

            The following established twists and foreshadowing are authoritative:

            {{twists_and_foreshadowing}}

            The following established scene plans are authoritative:

            {{scene_plans}}

            The following established dialogue plans are authoritative:

            {{dialogue_plans}}

            The following established page plan is authoritative:

            {{page_plan}}

            The following existing page narration is authoritative and is the primary source for what each illustration must depict:

            {{pages}}

            Use the existing page narration as the primary source for deciding what appears in each illustration.

            Maintain consistency with the established story, world, characters, locations, and page plan.

            Do not unnecessarily change, contradict, or replace the established creative direction.

            

            ==================================================
            PRIMARY RESPONSIBILITY
            ==================================================

            Generate one illustration prompt for every page of the illustrated Story Book.

            The output must contain exactly one page object for every page provided in the existing page narration.

            The final number of page objects must exactly match the number of pages provided.

            Do NOT invent additional pages.

            Do NOT skip any provided page.

            Do NOT return placeholder, empty, or filler illustration prompts for any page.

            ==================================================
            PAGE NUMBERING
            ==================================================

            For every page object, the \"no\" field must exactly match the page number of the corresponding existing page.

            Do NOT renumber the pages.

            Do NOT reorder the page numbers.

            Preserve the page numbering exactly as provided.

            ==================================================
            ILLUSTRATION PROMPT
            ==================================================

            For each page establish:

                - no: the exact page number of the existing page
                - illustration_prompt: the visual prompt describing what the illustration for that page must depict

            The illustration_prompt is the actual prompt that will later be sent to an Image-output AI Brain.

            It must describe the visual content of the page directly and concretely as if guiding an image-generation model.

            The illustration prompt should identify the important visual elements needed to depict the page, such as:

                - Subjects present in the scene
                - Character appearance based on established appearance information
                - Character actions
                - Expressions
                - Poses
                - Environment
                - Location
                - Important objects
                - Time or context
                - Atmosphere and mood
                - Relevant visual storytelling details
                - Compositionally important relationships between subjects

            The illustration prompt must:

                - Be based primarily on the page narration.
                - Use established character appearance information so the same characters remain visually consistent across pages.
                - Use established location and world details so environments remain visually consistent.
                - Reflect the relevant emotional state and important story events of the page.
                - Maintain visual continuity with the surrounding pages.
                - Not contradict established Story Book information.
                - Not invent unnecessary characters.
                - Not invent major events that are absent from the narration or the page plan.
                - Not change the story.
                - Be a natural visual scene description, not a screenplay, not a dialogue script, and not a story narration recap.

            Do NOT make the illustration prompt excessively verbose purely for length.

            Do NOT include any art style, rendering, or illustration type instructions in the illustration prompt.

            The IllustrationType has already been selected by the user and is applied separately to every page outside this generation.

            Do NOT choose, suggest, change, or refer to any art style, image type, or visual rendering style.

            ==================================================
            ILLUSTRATION TYPE SEPARATION
            ==================================================

            The illustration_type_prompt_instruction is NOT part of this generation.

            It comes from the separately selected IllustrationType model and is applied to every page by the application.

            Do NOT return illustration_type_prompt_instruction anywhere in the output.

            Do NOT include the IllustrationType's style instruction inside any illustration_prompt.

            Keep the illustration prompt focused only on describing the scene content.

            ==================================================
            CONSISTENCY WITH THE ESTABLISHED STORY AND WORLD
            ==================================================

            Maintain consistency with the established foundation, characters, World Bible, locations, factions, creatures, systems, timeline, story structure, twists, scene plans, dialogue plans, and page plan.

            Ensure:

                - Every provided page receives exactly one illustration prompt.
                - Illustration prompts follow the scene order and content of the page narration.
                - Characters appear with the appearance established for them.
                - Locations and environments match the established settings.
                - No illustration prompt contradicts established story information.
                - Visual continuity is preserved between consecutive pages.

            If the established context leaves a visual detail unspecified, make a strong creative decision that best serves the existing story.

            ==================================================
            FUTURE ILLUSTRATION GENERATION
            ==================================================

            Design the illustration prompts so they can be directly consumed by the later image-generation stage.

            The later stage will receive for each page:

                - narration
                - illustration_type_prompt_instruction
                - illustration_prompt

            and a selected Image-output AI Brain.

            Therefore the illustration_prompt must be self-sufficient as a scene description while remaining separate from the illustration type instruction.

            ==================================================
            QUALITY REQUIREMENTS
            ==================================================

            The illustration prompts should:

                - Fit the established Story Book foundation, characters, and world.
                - Depict the content of each page's narration.
                - Be directly useful to an image-generation model.
                - Maintain character and location consistency across pages.
                - Preserve visual continuity with surrounding pages.
                - Avoid unnecessary characters and invented major events.
                - Feel visually strong, intentional, and professionally art-directed.
                - Contain no art style, rendering, or illustration type instructions.

            ==================================================
            OUTPUT FORMAT
            ==================================================

            Return ONLY valid JSON.

            JSON OUTPUT RULES:

                - Respond with ONLY the JSON object.
                - Do NOT use Markdown formatting.
                - Do NOT wrap the JSON in ``` or ```json code fences.
                - Do NOT output any text before or after the JSON.
                - Do NOT output explanations, comments, or notes.
                - Escape quotes, newlines, and control characters properly.
                - The response must be valid, parseable JSON.

            The entire response must be a single JSON object with a pages array:

            {
                \"pages\": [
                    {
                        \"no\": 1,
                        \"illustration_prompt\": \"\"
                    }
                ]
            }

            Every page object must have exactly two fields:

                - \"no\": the exact page number of the corresponding existing page. It must exactly match the established page numbering with no gaps, no repeats, no renumbering, and no extra numbering.
                - \"illustration_prompt\": a non-empty string describing what the illustration for that page must depict.

            The number of page objects in the pages array must exactly equal the number of pages provided.

            Do NOT wrap the JSON in Markdown code fences.

            Do NOT include any explanatory text before or after the JSON.

            Do NOT include illustration_type_prompt_instruction or any style, rendering, or art direction field anywhere in the JSON.

            ==================================================
            FINAL CHECK
            ==================================================

            Before returning the result, ensure:

                - Every provided page has exactly one matching page object.
                - Page numbers exactly match the established numbering.
                - Every illustration_prompt is a non-empty string.
                - Illustration prompts primarily depict the page narration.
                - Characters and locations remain visually consistent with the established story.
                - Visual continuity is preserved between consecutive pages.
                - No illustration prompt contains an art style or rendering instruction.
                - No illustration prompt contradicts the established story or world.
                - The output is valid JSON only.
                - Do not return explanations, markdown, or additional text outside the JSON.

            The illustration style is managed separately by the selected IllustrationType and must never appear in this response.
        ";

        return $prompt;
    }

    public static function step16Prompt(): string
    {
        $prompt = "
            You are a professional story book illustration image generator and visual storytelling specialist.

            Generate ONE single illustration image for exactly ONE page of a professionally developed illustrated Story Book.

            The image is generated from the page narration, the established scene content, and the user-selected illustration type instruction.

            ==================================================
            ILLUSTRATION TYPE INSTRUCTION
            ==================================================

            {{illustration_type_prompt_instruction}}

            The illustration type instruction above defines the required visual style and rendering of the image.

            It is chosen by the user and is authoritative.

            The generated image must follow this instruction exactly.

            Do NOT replace, weaken, or ignore the selected illustration type instruction.

            Do NOT select a different illustration style.

            Apply the illustration type instruction faithfully while depicting the page content.

            ==================================================
            PAGE CONTENT
            ==================================================

            The authoritative page must be depicted:

            {{page}}

            Depict the established page content exactly as described.

            Do NOT invent major new events, characters, or environments that are not present in the page content or the established story context.

            ==================================================
            NARRATION
            ==================================================

            The page narration describes what happens on this page.

            The image must visually correspond to the narration.

            Do NOT rewrite the narration.

            Do NOT alter the story.

            ==================================================
            ILLUSTRATION PROMPT
            ==================================================

            The illustration prompt describes the specific visual scene this page must depict.

            The image must visually follow the illustration prompt.

            The illustration prompt and the narration describe the SAME page.

            ==================================================
            ESTABLISHED STORY CONTEXT
            ==================================================

            Use the established context only to keep the image visually consistent with the rest of the story:

            {{foundation}}

            {{characters}}

            {{world_bible}}

            {{locations}}

            Keep characters, locations, and world details visually consistent with the established story.

            If the selected illustration type instruction or the page content already fully defines the visual, use those as the primary direction.

            ==================================================
            GENERATION REQUIREMENTS
            ==================================================

            Generate exactly one illustration for the page.

            The generated image must:

                - Follow the illustration type instruction exactly.
                - Depict the content described by the illustration prompt.
                - Visually correspond to the page narration.
                - Keep characters, locations, and world elements consistent with the established story.
                - Be a single coherent, high-quality story book illustration.

            The illustration type instruction controls the style.

            The illustration prompt controls the scene.

            The narration provides story accuracy.

            Combine all three faithfully.

            ==================================================
            ===> FINAL IMAGE PROMPT <===
            ==================================================

            Produce the final image prompt only.

            Keep it as a single coherent image prompt that image-generation models can follow directly.

            The final image prompt must preserve the selected illustration type instruction and the established scene content for this page.

            Do NOT output explanations, comments, JSON, or any text other than the final image prompt.
        ";

        return $prompt;
    }

    public static function generateFullPrompt(string $partialPrompt, array $receivedInputs): string
    {
        $search = [];
        $replace = [];

        foreach ($receivedInputs as $key => $value) {
            $search[] = '{{'.$key.'}}';
            $replace[] = $value ?? '';
        }

        return str_replace($search, $replace, $partialPrompt);
    }
}
