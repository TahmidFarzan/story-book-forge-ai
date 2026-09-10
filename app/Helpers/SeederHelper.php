<?php

namespace App\Helpers;

class SeederHelper
{
    public static function genres()
    {
        return collect([
            (object) [
                'name'               => 'Fantasy',
                'brief'              => 'Stories featuring magic, imaginary worlds, mythical elements, and fantastical adventures',
                'prompt_instruction' => 'Focus on creating a compelling fantasy story where magical or fantastical elements are essential to the narrative. Establish clear internal rules for the world and its extraordinary elements, including magic, creatures, cultures, mythology, important locations, and supernatural forces. Character planning should consider how the fantasy world affects identities, abilities, relationships, goals, fears, and choices. Plot development should make fantastical elements directly influence the conflict, discoveries, obstacles, and character development. Introduce extraordinary elements naturally, escalate the conflict logically, and ensure the climax and resolution remain consistent with the established rules of the world. When combined with other genres, integrate the fantasy elements naturally without duplicating the requirements of the other genres.',
            ],

            (object) [
                'name'               => 'Dark Fantasy',
                'brief'              => 'Fantasy stories involving dark worlds, dangerous magic, mysterious forces, and difficult choices',
                'prompt_instruction' => 'Focus on creating a dark fantasy story where supernatural forces, magic, danger, corruption, mystery, and difficult choices shape the narrative. Establish a world with a strong sense of danger, mystery, decay, or oppression and define the consequences and limitations of extraordinary powers. Character planning should consider flawed characters, morally complicated choices, sacrifice, temptation, personal weaknesses, and the consequences of interacting with dangerous forces. Plot development should gradually reveal darker truths, increase the cost of important choices, and create escalating conflicts between personal goals and larger threats. The climax should force meaningful decisions and the resolution should reflect the emotional or moral consequences of the characters’ experiences.',
            ],

            (object) [
                'name'               => 'Magical',
                'brief'              => 'Stories centered on magical experiences, enchanted places, magical objects, and wondrous events',
                'prompt_instruction' => 'Focus on creating a sense of wonder through magical experiences, enchanted places, magical objects, unusual abilities, mysterious creatures, or extraordinary events. Establish the magical elements clearly enough that they feel consistent within the story world while preserving a sense of discovery and imagination. Character planning should consider curiosity, wonder, courage, friendship, kindness, imagination, personal goals, and how characters respond to magical experiences. Plot development should introduce magical elements naturally, build discovery and excitement, create meaningful challenges, and allow the magical elements to influence the characters and central conflict. The climax should provide a satisfying magical payoff and the resolution should show the consequences of the characters’ experiences.',
            ],

            (object) [
                'name'               => 'Fairy Tale',
                'brief'              => 'Imaginative stories featuring magic, wonder, memorable characters, challenges, and timeless themes',
                'prompt_instruction' => 'Focus on creating a fairy tale with a clear imaginative premise, memorable characters, magical elements, meaningful challenges, and a strong emotional or thematic message. Establish a simple but engaging story world containing distinctive locations, magical objects, unusual creatures, helpful characters, opposing characters, and a central challenge. Character planning should emphasize recognizable motivations, courage, kindness, cleverness, personal qualities, growth, and meaningful choices. Plot development should follow a clear progression from introduction to challenge, discovery, rising difficulty, climax, and resolution. Use symbolic and imaginative elements naturally and maintain a strong sense of wonder throughout the story. The ending should provide a satisfying emotional or thematic conclusion.',
            ],

            (object) [
                'name'               => 'Bedtime Story',
                'brief'              => 'Gentle and comforting stories designed for calm reading before sleep',
                'prompt_instruction' => 'Focus on creating a gentle, comforting, imaginative, and emotionally reassuring story suitable for bedtime reading. Establish a calm atmosphere, simple but engaging characters, a safe sense of adventure, and a clear emotional focus. Character planning should emphasize warmth, friendship, kindness, curiosity, family relationships, imagination, and emotional reassurance. Plot development should gradually move toward comfort, understanding, resolution, or peaceful discovery. Use soothing settings, familiar emotional themes, gentle discoveries, and positive relationships where appropriate. Avoid unnecessary intensity and ensure the ending feels calm, satisfying, and emotionally reassuring.',
            ],

            (object) [
                'name'               => 'Fable',
                'brief'              => 'Imaginative stories that communicate meaningful lessons through characters and their choices',
                'prompt_instruction' => 'Focus on creating a story built around a clear moral, lesson, or meaningful observation about life and behaviour. Characters may include people, animals, or imaginative figures whose actions naturally demonstrate the central theme. Character planning should give each important character a clear motivation, personality, strength, weakness, and relationship to the lesson. Plot development should create a simple but meaningful conflict where decisions produce understandable consequences. The moral should emerge naturally from the characters’ experiences rather than being forced into the ending. The resolution should provide a memorable conclusion that reinforces the central lesson.',
            ],

            (object) [
                'name'               => 'Moral Story',
                'brief'              => 'Stories that communicate a meaningful moral, life lesson, value, or positive principle',
                'prompt_instruction' => 'Focus on creating an engaging story built around a clear moral, life lesson, value, or meaningful principle. Establish characters and situations that naturally demonstrate the consequences of choices, behaviour, honesty, kindness, courage, responsibility, friendship, patience, generosity, respect, or other meaningful values. Character planning should give each important character distinct motivations, strengths, weaknesses, and choices that connect naturally to the central lesson. Plot development should present a meaningful situation or conflict where characters make decisions and experience understandable consequences. The moral should emerge naturally from the characters’ experiences rather than feeling forced or disconnected from the story. The climax should reinforce the central lesson through an important choice or realization, and the resolution should provide a memorable and satisfying conclusion.',
            ],

            (object) [
                'name'               => 'Animal Story',
                'brief'              => 'Stories where animals are central characters with personalities, relationships, challenges, and adventures',
                'prompt_instruction' => 'Focus on creating an engaging story where animals play central roles in the narrative. Give each important animal distinct personalities, motivations, strengths, weaknesses, relationships, and goals. The animals may behave realistically or communicate and act like characters depending on the story world. Plot development should use the animals’ abilities, environments, relationships, and challenges as important parts of the conflict. Create meaningful interactions, discoveries, adventures, emotional moments, or humorous situations while keeping the animals central to the story. The resolution should provide satisfying consequences for the characters and their relationships.',
            ],

            (object) [
                'name'               => 'Friendship',
                'brief'              => 'Stories centered on friendship, trust, teamwork, loyalty, and shared experiences',
                'prompt_instruction' => 'Focus on developing meaningful friendships through trust, cooperation, misunderstandings, challenges, loyalty, support, and personal growth. Establish why the characters become friends, what connects them, what differences create tension, and what challenges test their relationship. Character planning should give each friend distinct personalities, interests, goals, fears, strengths, weaknesses, and perspectives. Plot development should use shared experiences, disagreements, discoveries, challenges, and important choices to strengthen or test the friendship. The climax should require the characters to make meaningful choices about trust or loyalty, and the resolution should show how their relationship has changed.',
            ],

            (object) [
                'name'               => 'Love Story',
                'brief'              => 'Stories centered on love, affection, emotional connection, relationships, and meaningful bonds',
                'prompt_instruction' => 'Focus on creating a heartfelt story centered on love, emotional connection, affection, trust, care, sacrifice, family bonds, friendship, or other meaningful forms of love. Establish the important relationships, why the characters care about one another, what connects them, and what challenges test those bonds. Character planning should give each important character distinct personalities, emotional needs, values, goals, fears, and reasons for caring about others. Plot development should use meaningful interactions, shared experiences, misunderstandings, challenges, choices, discoveries, and emotional turning points to develop the central relationships. The climax should bring an important emotional or relational decision to the foreground, and the resolution should provide a satisfying and meaningful outcome for the relationships.',
            ],

            (object) [
                'name'               => 'Romance',
                'brief'              => 'Stories centered on romantic relationships, emotional connection, trust, and personal growth',
                'prompt_instruction' => 'Focus on developing a believable central romantic relationship through emotional connection, trust, communication, vulnerability, conflict, and personal growth. Establish why the characters are drawn to one another, what separates them, what they misunderstand, and what obstacles affect their relationship. Character planning should create distinct personalities, emotional needs, personal goals, flaws, fears, communication patterns, and believable reasons for attraction and conflict. Plot development should gradually develop the relationship through meaningful interactions, shared experiences, emotional turning points, setbacks, misunderstandings, and important choices. The climax should require meaningful relationship decisions, and the resolution should reflect earned growth and choices rather than coincidence.',
            ],

            (object) [
                'name'               => 'Family',
                'brief'              => 'Stories centered on family relationships, love, support, everyday experiences, and shared challenges',
                'prompt_instruction' => 'Focus on creating a story where family relationships are central to the characters and narrative. Establish the family structure, relationships, shared experiences, responsibilities, expectations, and emotional connections that influence the story. Character planning should give family members distinct personalities, motivations, needs, strengths, weaknesses, and perspectives. Plot development should use family events, misunderstandings, discoveries, challenges, cooperation, emotional moments, or important decisions to create meaningful progression. The resolution should demonstrate growth, understanding, connection, reconciliation, or another meaningful change within the family.',
            ],

            (object) [
                'name'               => 'School Story',
                'brief'              => 'Stories set around school life, classmates, teachers, learning, friendship, and everyday challenges',
                'prompt_instruction' => 'Focus on creating a story where school life is an important part of the characters’ experiences. Establish the school environment, classmates, teachers, activities, routines, expectations, friendships, and challenges that shape the narrative. Character planning should give students distinct personalities, interests, goals, strengths, weaknesses, friendships, and concerns. Plot development should use school events, relationships, discoveries, competitions, projects, misunderstandings, challenges, or personal growth as meaningful sources of conflict. The resolution should show how the characters have learned, changed, or strengthened their relationships through the experience.',
            ],

            (object) [
                'name'               => 'Educational',
                'brief'              => 'Stories that naturally teach knowledge, concepts, values, skills, or important ideas through storytelling',
                'prompt_instruction' => 'Focus on teaching a meaningful concept, fact, skill, value, or idea through an engaging story rather than through a purely instructional explanation. Establish characters and situations that naturally create opportunities to discover and understand the educational topic. Character planning should make the learning process part of the characters’ goals, challenges, discoveries, or decisions. Plot development should allow the characters to encounter questions, problems, experiments, mistakes, discoveries, and consequences that naturally reveal the intended lesson. Keep the story entertaining and understandable while ensuring the educational information remains accurate and appropriately integrated into the narrative.',
            ],

            (object) [
                'name'               => 'Folklore',
                'brief'              => 'Stories inspired by traditional beliefs, cultural stories, customs, and oral storytelling',
                'prompt_instruction' => 'Focus on creating a story inspired by folklore, traditional beliefs, cultural storytelling, customs, symbolic characters, supernatural traditions, or regional storytelling patterns. Establish the cultural or traditional setting and identify the beliefs, customs, symbols, creatures, or stories that influence the narrative. Character planning should reflect the values, relationships, responsibilities, and worldview of the chosen cultural setting. Plot development should integrate traditional elements into the characters’ challenges and decisions rather than using them only as decoration. Preserve the spirit of the selected folklore while creating an engaging and coherent narrative.',
            ],

            (object) [
                'name'               => 'Myth & Legend',
                'brief'              => 'Stories inspired by legendary heroes, mythical beings, ancient traditions, and extraordinary events',
                'prompt_instruction' => 'Focus on creating a story inspired by myths, legends, legendary heroes, mythical beings, ancient traditions, or extraordinary events. Establish the mythological or legendary world, important figures, supernatural elements, cultural beliefs, symbolic locations, and central conflict. Character planning should give heroes and other important figures meaningful motivations, abilities, weaknesses, relationships, responsibilities, and choices. Plot development should involve discovery, trials, quests, conflicts, revelations, or extraordinary challenges connected to the mythological setting. Maintain a strong sense of wonder and significance while keeping the narrative coherent and engaging.',
            ],

            (object) [
                'name'               => 'Historical Fiction',
                'brief'              => 'Fictional stories set within real historical periods and circumstances',
                'prompt_instruction' => 'Focus on telling a fictional story within a believable and historically grounded period. Establish the time period, location, society, customs, technology, economy, social structures, political conditions, and important historical circumstances that influence the story. Character planning should create people whose behaviour, opportunities, beliefs, relationships, and limitations are appropriate to the period. Plot development should connect personal conflicts with the historical environment and allow historical circumstances to naturally affect decisions and experiences. Historical details should support the story without overwhelming it.',
            ],

            (object) [
                'name'               => 'Drama',
                'brief'              => 'Character-driven stories focused on serious conflicts, relationships, emotions, and important life events',
                'prompt_instruction' => 'Focus on creating a character-driven story built around meaningful personal, emotional, social, or relational conflicts. Establish the characters’ goals, relationships, responsibilities, fears, unresolved problems, and circumstances that create pressure. Character planning should give each major character distinct motivations, values, weaknesses, emotional needs, and conflicting perspectives. Plot development should progress through meaningful choices, consequences, revelations, relationship changes, and escalating personal conflicts. The climax should force important decisions or emotional confrontations, followed by a resolution showing meaningful consequences and character development.',
            ],

            (object) [
                'name'               => 'Comedy',
                'brief'              => 'Stories built around humour, amusing situations, entertaining characters, and unexpected consequences',
                'prompt_instruction' => 'Focus on creating an entertaining story driven by humour, amusing situations, character personalities, misunderstandings, unexpected consequences, witty interactions, or escalating absurdity. Character planning should create distinct personalities whose goals, flaws, habits, relationships, and reactions naturally generate humorous situations. Plot development should establish a clear central problem and progressively create complications that become increasingly entertaining while remaining believable within the story world. Humour should arise naturally from character behaviour, situations, dialogue, timing, contrast, and consequences. The climax should bring the major complications together in an entertaining payoff.',
            ],

            (object) [
                'name'               => 'Mystery',
                'brief'              => 'Stories built around secrets, clues, investigation, discovery, and unanswered questions',
                'prompt_instruction' => 'Focus on creating a compelling central mystery with a clear unanswered question, hidden information, logical clues, believable suspects, meaningful motives, and a satisfying explanation. Establish what is unknown, what happened, the important timeline, evidence, possible explanations, misleading information, and different levels of character knowledge. Plot development should reveal information gradually, provide fair clues, create investigative obstacles, introduce believable red herrings, and progressively narrow the possible explanations. The climax should reveal the truth through evidence, character actions, or logical discovery, followed by a resolution that addresses the major unanswered questions.',
            ],

            (object) [
                'name'               => 'Thriller',
                'brief'              => 'Stories driven by suspense, danger, uncertainty, pressure, and escalating stakes',
                'prompt_instruction' => 'Focus on creating a story with strong suspense, uncertainty, danger, pressure, and escalating consequences. Establish a central threat, important stakes, opposing forces, hidden information, time pressure, and reasons the characters cannot easily avoid the conflict. Character planning should create protagonists with strong motivations, vulnerabilities, fears, strengths, weaknesses, and personal stakes. Plot development should maintain forward momentum through discoveries, obstacles, reversals, unexpected developments, difficult decisions, and increasing consequences. The climax should bring the central threat to a decisive confrontation and the resolution should address the consequences.',
            ],

            (object) [
                'name'               => 'Horror',
                'brief'              => 'Stories built around fear, dread, mystery, vulnerability, and threatening supernatural or human forces',
                'prompt_instruction' => 'Focus on creating fear through atmosphere, uncertainty, vulnerability, psychological pressure, isolation, disturbing situations, and an increasingly threatening presence. Establish the source of fear, why the characters are exposed to it, what they stand to lose, and why the threat becomes difficult to understand or escape. Character planning should consider personal fears, emotional weaknesses, relationships, vulnerabilities, and reactions under pressure. Plot development should build dread gradually, reveal information carefully, create uncertainty, and escalate the threat. The climax should force the characters to confront the central source of fear, while the resolution remains consistent with the established premise.',
            ],

            (object) [
                'name'               => 'Science Fiction',
                'brief'              => 'Speculative stories involving science, technology, space, future societies, or extraordinary scientific possibilities',
                'prompt_instruction' => 'Focus on creating a story built around a meaningful speculative concept involving science, technology, future societies, space, artificial intelligence, scientific discoveries, alternate realities, or other possibilities. Establish the central speculative idea, its capabilities, limitations, consequences, and effect on the world and characters. World planning should consider technology, institutions, society, economy, environment, and how people adapt to changed circumstances. Character planning should explore how individuals benefit from, depend on, resist, misuse, or are affected by the speculative concept. Plot development should make the central idea an active cause of conflict and major events.',
            ],

            (object) [
                'name'               => 'Adventure',
                'brief'              => 'Stories about journeys, exploration, discovery, challenges, and exciting experiences',
                'prompt_instruction' => 'Focus on creating an engaging journey involving exploration, discovery, obstacles, challenges, relationships, and personal transformation. Establish why the characters begin the journey, important destinations, environments, obstacles, resources, discoveries, companions, rivals, and forces working against them. Character planning should give protagonists meaningful goals, motivations, strengths, weaknesses, fears, and reasons for continuing despite difficulties. Plot development should move through meaningful stages where each challenge or discovery changes the situation and develops the characters. The journey should progressively increase in importance and lead toward a significant discovery, achievement, confrontation, or transformation.',
            ],

            (object) [
                'name'               => 'Psychological',
                'brief'              => 'Stories centered on thoughts, emotions, perception, identity, memory, and internal conflict',
                'prompt_instruction' => 'Focus on exploring characters’ thoughts, emotions, memories, fears, desires, beliefs, perceptions, contradictions, and internal conflicts. Establish the psychological forces influencing the characters and connect their internal struggles to external events. Character planning should create psychologically complex individuals with believable motivations, emotional vulnerabilities, personal history, contradictions, and patterns of behaviour. Plot development should gradually reveal hidden motivations, conflicting perceptions, emotional pressure, uncertainty, and changes in how characters understand themselves or others. Internal conflict should actively influence decisions and relationships so that character psychology drives the plot.',
            ],

            (object) [
                'name'               => 'Supernatural',
                'brief'              => 'Stories involving mysterious supernatural forces, beings, abilities, events, or unexplained phenomena',
                'prompt_instruction' => 'Focus on introducing supernatural forces, beings, abilities, events, or mysteries into the story and exploring their effect on characters and the world. Establish the nature of the supernatural element, its rules or mysteries, its consequences, and how people understand or respond to it. Character planning should consider belief, disbelief, curiosity, fear, personal stakes, relationships, and how supernatural experiences change the characters. Plot development should introduce extraordinary events gradually, maintain mystery or uncertainty, reveal important information appropriately, and connect supernatural events directly to the central conflict.',
            ],

            (object) [
                'name'               => 'Crime',
                'brief'              => 'Stories involving crimes, criminals, investigations, motives, consequences, and justice',
                'prompt_instruction' => 'Focus on creating a compelling crime story involving a central crime, its motives, consequences, investigation, suspects, victims, witnesses, and people affected by the events. Establish what happened, why it happened, the relationships between the people involved, important evidence, the timeline, conflicting explanations, and investigative challenges. Character planning should create believable criminals, investigators, victims, witnesses, allies, and other relevant characters with distinct motivations and personal stakes. Plot development should reveal the crime, develop the investigation, introduce obstacles and discoveries, increase pressure, and gradually reveal the truth.',
            ],

            (object) [
                'name'               => 'Political Fiction',
                'brief'              => 'Stories involving political power, institutions, leadership, influence, competing interests, and social conflict',
                'prompt_instruction' => 'Focus on creating a fictional story involving political systems, leadership, institutions, competing interests, influence, public pressure, and struggles for power. Establish the political environment, important factions, institutions, laws, social pressures, public opinion, and sources of influence that shape the conflict. Character planning should include leaders, officials, advisers, journalists, citizens, activists, or other relevant characters with different goals, beliefs, loyalties, fears, and ambitions. Plot development should progress through negotiations, alliances, rivalries, secrets, strategic decisions, public pressure, and political consequences.',
            ],

            (object) [
                'name'               => 'War',
                'brief'              => 'Stories centered on conflict, survival, courage, relationships, and the human consequences of war',
                'prompt_instruction' => 'Focus on telling a human-centered story shaped by war or armed conflict. Establish the circumstances of the conflict, opposing forces, political conditions, locations, civilians, social changes, uncertainty, and consequences affecting the characters. Character planning should create individuals with personal goals, relationships, fears, hopes, moral struggles, responsibilities, and reasons for becoming involved or being affected by the conflict. Plot development should balance larger events with personal experiences and show how conflict changes characters and relationships. The climax should resolve the central personal or narrative conflict while the ending acknowledges meaningful consequences.',
            ],

            (object) [
                'name'               => 'Dystopian',
                'brief'              => 'Stories set in oppressive or deeply flawed societies shaped by restrictive systems and social conflict',
                'prompt_instruction' => 'Focus on creating a story set within a society where political, technological, social, economic, or ideological systems restrict freedom or create serious inequality and conflict. Establish how the society operates, who holds power, what rules control people, how information is managed, and what consequences characters face when challenging the system. Character planning should consider personal fears, relationships, beliefs, goals, compromises, and reasons for resisting or accepting the system. Plot development should reveal the true nature of the society gradually, create increasing conflict between individuals and the system, and force difficult choices.',
            ],

            (object) [
                'name'               => 'Coming of Age',
                'brief'              => 'Stories about growing up, identity, independence, self-discovery, and personal transformation',
                'prompt_instruction' => 'Focus on a character’s transition through an important period of personal growth, self-discovery, independence, or changing understanding of life. Establish the protagonist’s starting beliefs, relationships, ambitions, insecurities, responsibilities, and challenges. Character planning should show how experiences, mistakes, relationships, and difficult choices gradually change the protagonist’s understanding of themselves and the world. Plot development should present meaningful challenges and turning points that require increasingly mature decisions. The climax should represent an important moment of realization, responsibility, or choice, while the resolution demonstrates how the character has changed.',
            ],
        ]);
    }

    public static function audiences()
    {
        return collect([
            (object) [
                'name'               => 'Children',
                'brief'              => 'Story books primarily intended for children and young readers',
                'prompt_instruction' => 'The audience is Children. All story planning, language, themes, characters, conflicts, pacing, descriptions, dialogue, and resolution must remain appropriate for children. Use clear, accessible, age-appropriate language and concepts while preserving imagination, emotional depth, curiosity, and entertainment. Characters should be understandable and relatable to young readers, with motivations and relationships that can be followed easily. Conflicts should remain suitable for children and should avoid unnecessarily mature, disturbing, graphic, or adult-oriented subject matter. The story may explore meaningful emotions, challenges, mystery, adventure, fear, loss, friendship, family, learning, or personal growth when handled in an age-appropriate way. The selected genres define what kind of story is being created, while the audience determines the appropriate maturity, presentation, complexity, and boundaries of the story. When multiple genres are selected, combine them into one coherent child-appropriate story rather than treating them as separate stories or unrelated sections.',
            ],

            (object) [
                'name'               => 'Young Adult',
                'brief'              => 'Story books primarily intended for teenagers and young adult readers',
                'prompt_instruction' => 'The audience is Young Adult. All story planning, language, themes, characters, conflicts, pacing, descriptions, dialogue, and resolution should be appropriate for young adult readers. Stories may explore identity, independence, friendship, family relationships, belonging, personal growth, emotional challenges, social pressure, difficult choices, ambition, relationships, and other themes relevant to young adult readers. Characters should have distinct personalities, believable motivations, emotional complexity, strengths, weaknesses, goals, fears, and evolving perspectives. Conflicts may be more complex than children’s stories while remaining appropriate for the intended audience. The selected genres define what kind of story is being created, while the audience determines the appropriate maturity, presentation, complexity, and boundaries of the story. When multiple genres are selected, combine them into one coherent narrative rather than treating them as separate stories or unrelated sections.',
            ],

            (object) [
                'name'               => 'Adult',
                'brief'              => 'Story books intended for adult readers with mature themes and complex narratives',
                'prompt_instruction' => 'The audience is Adult. All story planning, language, themes, characters, conflicts, pacing, descriptions, dialogue, and resolution should be appropriate for adult readers. The narrative may explore complex relationships, sophisticated ideas, difficult decisions, social issues, psychological conflicts, moral ambiguity, professional responsibilities, family dynamics, personal consequences, and other themes associated with adult life. Characters should have layered motivations, believable histories, contradictions, emotional complexity, meaningful relationships, and realistic consequences for their decisions. The story may use sophisticated language, structure, symbolism, subplots, and thematic depth appropriate for adult readers. The selected genres define what kind of story is being created, while the audience determines the appropriate maturity, presentation, complexity, and boundaries of the story. When multiple genres are selected, combine them into one cohesive and sophisticated narrative rather than treating them as independent genre sections.',
            ],
        ]);
    }

    public static function languages()
    {
        return collect([

            (object) [
                'name'  => 'English',
                'brief' => 'Global language widely used for original and translated story books',
            ],

            (object) [
                'name'  => 'Spanish',
                'brief' => 'Major world language with a rich tradition of storytelling and children\'s literature',
            ],

            (object) [
                'name'  => 'French',
                'brief' => 'Widely spoken language known for classic fairy tales and illustrated story books',
            ],

            (object) [
                'name'  => 'German',
                'brief' => 'Language with a strong heritage of folk tales, fables, and story collections',
            ],

            (object) [
                'name'  => 'Italian',
                'brief' => 'Language celebrated for classic fables, fairy tales, and picture books',
            ],

            (object) [
                'name'  => 'Portuguese',
                'brief' => 'Language spoken across continents with a vibrant tradition of oral storytelling',
            ],

            (object) [
                'name'  => 'Dutch',
                'brief' => 'Language with a beloved tradition of children\'s story books and illustrated tales',
            ],

            (object) [
                'name'  => 'Russian',
                'brief' => 'Language known for rich folk tales, fables, and classic story traditions',
            ],

            (object) [
                'name'  => 'Arabic',
                'brief' => 'Language with a deep heritage of storytelling, folk tales, and fables',
            ],

            (object) [
                'name'  => 'Hindi',
                'brief' => 'Language with a vibrant storytelling tradition spanning folk tales and modern stories',
            ],

            (object) [
                'name'  => 'Bengali',
                'brief' => 'Language with a celebrated children\'s literature and folk story tradition',
            ],

            (object) [
                'name'  => 'Chinese (Simplified)',
                'brief' => 'Language read by millions of readers across contemporary and traditional stories',
            ],

            (object) [
                'name'  => 'Japanese',
                'brief' => 'Language with a rich culture of illustrated story books and imaginative tales',
            ],

            (object) [
                'name'  => 'Korean',
                'brief' => 'Language with a growing tradition of illustrated children\'s story books',
            ],
        ]);
    }

    public static function storyTypes()
    {
        return collect([

            (object) [
                'name'               => 'Short',
                'brief'              => 'Short-length stories with a compact and focused narrative structure',
                'prompt_instruction' => 'Develop the story with a compact but sufficiently complete narrative scope to produce at least 32 pages. The story must have enough meaningful plot development, character development, events, dialogue, descriptions, conflict progression, discoveries, and resolution to naturally support a minimum of 32 pages. Maintain a focused narrative with a clear beginning, developed middle, climax, and resolution. Do not make the story artificially short or compress important narrative development merely to keep it concise. At the same time, do not use repetition, filler, artificial padding, or meaningless content solely to increase the page count. The minimum 32-page requirement applies to the completed story regardless of the selected audience, genre, or other story instructions.',
            ],

            (object) [
                'name'               => 'Medium',
                'brief'              => 'Medium-length stories with a balanced structure and deeper character development',
                'prompt_instruction' => 'Develop the story with a sufficiently broad and balanced narrative scope to produce at least 64 pages. The story must have enough meaningful plot development, character arcs, relationships, events, dialogue, descriptions, discoveries, complications, turning points, escalating conflict, climax, and resolution to naturally support a minimum of 64 pages. Allow important characters, relationships, conflicts, and story events enough room to develop rather than compressing the narrative. Use meaningful subplots and additional story development when they strengthen the main narrative. Do not use repetition, filler, artificial padding, or meaningless content solely to increase the page count. The minimum 64-page requirement applies to the completed story regardless of the selected audience, genre, or other story instructions.',
            ],

            (object) [
                'name'               => 'Long',
                'brief'              => 'Long-length stories with expansive plots and extensive character arcs',
                'prompt_instruction' => 'Develop the story with an expansive narrative scope sufficient to produce at least 128 pages. The story must have enough meaningful plot development, extensive character arcs, complex relationships, interconnected conflicts, major discoveries, world-building, meaningful subplots, escalating challenges, turning points, climax, and resolution to naturally support a minimum of 128 pages. Give the major characters, relationships, conflicts, settings, subplots, and narrative events sufficient development throughout the story. Structure the narrative across multiple meaningful stages or major sections so the story can develop progressively from its opening through its conclusion. Do not use repetition, filler, artificial padding, or meaningless content solely to increase the page count. The minimum 128-page requirement applies to the completed story regardless of the selected audience, genre, or other story instructions.',
            ],
        ]);
    }

    public static function aiBrains()
    {
        return collect([

            (object) [
                'name'              => 'Google: Gemma 4 26B A4B',
                'model'             => 'google/gemma-4-26B-A4B-it',
                'api_url'           => 'https://router.huggingface.co/v1',
                'api_key'           => null,
                'brief'             => 'AI writing model for generating documents, workbooks, ebooks and structured educational content.',
                'focus'             => 'Premium document generation, chapter writing, workbook creation, story generation, educational materials',
                'context_window'    => 262144,
                'average_latency'   => 0.90,
                'minimum_wait_time' => 2,
                'timeout_seconds'   => 60,
                'max_output_tokens' => 5000,
            ],

            (object) [
                'name'              => 'Qwen: Qwen3 8B',
                'model'             => 'Qwen/Qwen3-8B',
                'api_url'           => 'https://router.huggingface.co/v1',
                'api_key'           =>  null,
                'brief'             => 'Multilingual AI writing model for stories, documents, educational content and structured generation.',
                'focus'             => 'Story writing, creative writing, long-form content, educational materials, reasoning and multilingual generation',
                'context_window'    => 131072,
                'average_latency'   => 1.50,
                'minimum_wait_time' => 2,
                'timeout_seconds'   => 60,
                'max_output_tokens' => 5000,
            ],

            (object) [
                'name'              => 'Mistral AI: Mistral Small 3.2 24B Instruct',
                'model'             => 'mistralai/Mistral-Small-3.2-24B-Instruct-2506',
                'api_url'           => 'https://router.huggingface.co/v1',
                'api_key'           =>  null,
                'brief'             => 'Strong instruction-following model for stories, documents, educational content and long-form generation.',
                'focus'             => 'Story writing, creative writing, long-form documents, educational content, structured generation',
                'context_window'    => 131072,
                'average_latency'   => 2.00,
                'minimum_wait_time' => 2,
                'timeout_seconds'   => 90,
                'max_output_tokens' => 5000,
            ],

            (object) [
                'name'              => 'Krea: Krea 2 Turbo',
                'model'             => 'krea/Krea-2-Turbo',
                'api_url'           => 'https://router.huggingface.co',
                'api_key'           =>  null,
                'brief'             => 'High-quality text-to-image model for premium illustrations and visual storytelling.',
                'focus'             => 'Premium Modern 2.5D Anime Digital Art, cinematic story illustrations, characters, environments, polished visual compositions',
                'context_window'    => null,
                'average_latency'   => 30,
                'minimum_wait_time' => 0,
                'timeout_seconds'   => 180,
                'max_output_tokens' => null,
            ],

            (object) [
                'name'              => 'Black Forest Labs: FLUX.1 Schnell',
                'model'             => 'black-forest-labs/FLUX.1-schnell',
                'api_url'           => 'https://router.huggingface.co',
                'api_key'           =>  null,
                'brief'             => 'Fast text-to-image model for generating premium illustrations and visual scenes.',
                'focus'             => 'Premium Modern 2.5D Anime Digital Art, story illustrations, character scenes, cinematic compositions',
                'context_window'    => null,
                'average_latency'   => 30,
                'minimum_wait_time' => 0,
                'timeout_seconds'   => 180,
                'max_output_tokens' => null,
            ],

            (object) [
                'name'              => 'Stability AI: Stable Diffusion XL Base 1.0',
                'model'             => 'stabilityai/stable-diffusion-xl-base-1.0',
                'api_url'           => 'https://router.huggingface.co',
                'api_key'           =>  null,
                'brief'             => 'Versatile text-to-image model for high-quality illustrations and visual content.',
                'focus'             => 'Premium Modern 2.5D Anime Digital Art, character illustrations, environments, cinematic scenes',
                'context_window'    => null,
                'average_latency'   => 30,
                'minimum_wait_time' => 0,
                'timeout_seconds'   => 180,
                'max_output_tokens' => null,
            ],

        ]);
    }

    public static function imageSettings()
    {
        return [
            'width'           => 1024,
            'height'          => 1024,
            'inference_steps' => 4,
        ];
    }
}
