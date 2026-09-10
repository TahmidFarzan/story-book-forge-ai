<?php

namespace App\Helpers;

class SeederHelper
{
    public static function genres()
    {
        return collect([
            (object) [
                'name'               => 'Fantasy',
                'brief'              => 'Fantasy fiction with imaginary worlds and magic',
                'prompt_instruction' => 'Focus on building an invented world with consistent internal rules, a defined magic system with limitations and costs, memorable creatures and cultures, rich mythology, and conflicts that naturally emerge from the setting. World planning should consider geography, societies, power structures, laws of the world, magic sources and their consequences, technology level, mythology, and historical events that shape present tensions. Character planning should consider how characters are influenced by this world, how abilities and limitations create moral and practical challenges, and how personal goals connect with larger world conflicts. Story structure should ensure that fantastical elements escalate logically, stakes grow naturally, and the resolution remains consistent with the established world rules. When combined with other genres, integrate these fantasy world-building elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Dark Fantasy',
                'brief'              => 'Dark and mysterious fantasy stories',
                'prompt_instruction' => 'Focus on creating a grim and morally complex world where supernatural forces, magic, corruption, and horror exist together. Planning should consider the cost of power, the consequences of using forbidden forces, oppressive environments, decaying societies, supernatural threats, and how the world itself creates conflict. Character planning should consider morally complex protagonists, flawed heroes, understandable but dangerous antagonists, internal struggles, corruption, sacrifice, and the personal cost of survival. Story structure should consider escalating darkness, meaningful suffering, difficult choices, fragile hope, and resolutions where victory carries emotional or moral consequences. When combined with other genres, integrate these dark fantasy and moral-conflict elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Historical Fiction',
                'brief'              => 'Stories based on historical events and periods',
                'prompt_instruction' => 'Focus on creating an authentic historical setting where real social conditions, events, and cultural realities influence the characters and conflict. Setting planning should consider the time period, geography, politics, class structures, customs, technology, economy, and historical events that shape the world. Character planning should consider believable behaviour within the chosen era, limitations created by society, personal motivations, and how historical circumstances influence individual choices. Story structure should consider how larger historical events intersect with personal experiences, how historical changes create tension, and how the conclusion remains consistent with the period while delivering emotional impact. When combined with other genres, integrate these historical authenticity elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Family Drama',
                'brief'              => 'Stories about family relationships and emotions',
                'prompt_instruction' => 'Focus on developing realistic family relationships, emotional conflicts, hidden tensions, and long-term bonds between family members. Planning should consider family structure, shared history, unresolved conflicts, generational differences, secrets, loyalty, resentment, and emotional expectations. Character planning should consider individual personalities, personal wounds, motivations, family roles, and how love and conflict exist together within relationships. Story structure should consider gradual emotional escalation, realistic conversations, meaningful turning points, and resolutions that address deeper family issues rather than only surface conflicts. When combined with other genres, integrate these family and emotional relationship elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Romance',
                'brief'              => 'Love and relationship based stories',
                'prompt_instruction' => 'Focus on creating a believable central relationship built through emotional connection, attraction, vulnerability, and personal growth. Planning should consider what draws the characters together, what separates them, the obstacles preventing the relationship from developing, and how their connection changes over time. Character planning should consider distinct personalities, genuine chemistry, personal flaws, emotional needs, communication patterns, and individual growth required before commitment feels earned. Story structure should consider relationship development, emotional turning points, conflicts, intimate moments, and a satisfying resolution based on mutual understanding and growth rather than coincidence. When combined with other genres, integrate these relationship-development elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Mystery',
                'brief'              => 'Mystery and investigation stories',
                'prompt_instruction' => 'Focus on creating a central mystery with a clear question, logical investigation, meaningful clues, believable suspects, and a solution supported by evidence. Planning should consider the hidden information, timeline of events, evidence chain, possible explanations, red herrings, and how readers can follow the investigation fairly. Character planning should consider investigators with personal reasons to solve the mystery, suspects with believable motives, witnesses with limited knowledge, and characters whose actions connect logically to the mystery. Story structure should carefully control the reveal of information, escalation of discoveries, investigative setbacks, final revelation, and complete explanation of the mystery. When combined with other genres, integrate these investigation and evidence-based elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Thriller',
                'brief'              => 'Suspenseful and exciting stories',
                'prompt_instruction' => 'Focus on creating escalating tension, danger, uncertainty, psychological pressure, and conflicts that keep characters and readers under constant pressure. Planning should consider the central threat, hidden motives, increasing risks, unexpected developments, and how each event raises the stakes. Character planning should consider strong motivations, personal weaknesses, emotional pressure points, and opposing forces that create meaningful challenges. Story structure should consider pacing, suspense control, major reveals, reversals, rising danger, and a climax that resolves the central conflict in a satisfying way. When combined with other genres, integrate these suspense and tension-building elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Horror',
                'brief'              => 'Scary and horror fiction stories',
                'prompt_instruction' => 'Focus on creating fear through atmosphere, psychological tension, disturbing concepts, vulnerability, and escalating dread. Planning should consider the source of fear, how the threat appears, how fear develops, what characters risk losing, and how uncertainty increases tension. Character planning should consider emotional weaknesses, fears, reactions under pressure, personal stakes, and how confronting horror transforms them. Story structure should consider gradual tension building, controlled reveals, moments of terror, confrontation with the threat, and an ending that matches the established horror tone. When combined with other genres, integrate these horror and fear-building elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],
            (object) [
                'name'               => 'Science Fiction',
                'brief'              => 'Science based futuristic fiction',
                'prompt_instruction' => 'Focus on developing a speculative concept based on science, technology, future societies, or alternative possibilities and explore how it changes human life. Planning should consider the rules and limitations of the technology or scientific idea, its impact on society, ethics, politics, economy, and everyday experiences. World planning should consider technological development, social structures, institutions, scientific explanations, and how people adapt to the changed world. Character planning should consider how characters respond to, benefit from, resist, or suffer because of the speculative concept and how it shapes their personal conflicts. Story structure should ensure that the central scientific or technological idea actively drives the conflict, creates meaningful consequences, and contributes to the resolution. When combined with other genres, integrate these speculative and science-based elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Adventure',
                'brief'              => 'Journey and exploration based stories',
                'prompt_instruction' => 'Focus on creating a meaningful journey involving exploration, discovery, danger, challenges, and personal transformation. Planning should consider the purpose of the journey, destinations, environments, obstacles, resources, cultures encountered, and how each stage challenges the characters. Character planning should consider resourceful protagonists, companion relationships, rivals, enemies, personal goals, and how experiences during the journey change their beliefs and abilities. Story structure should consider escalating challenges, important discoveries, unexpected obstacles, and a climax where the journey leads to a significant personal or external achievement. When combined with other genres, integrate these exploration and journey elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Biography',
                'brief'              => 'Life stories of real people',
                'prompt_instruction' => 'Focus on presenting the life of a real person through important experiences, achievements, struggles, relationships, and lasting influence. Planning should consider the subject’s background, historical context, defining moments, personal qualities, challenges, successes, failures, and impact on others. Character planning should consider the subject’s motivations, decisions, beliefs, relationships, and how external circumstances shaped their life. Story structure should create a meaningful life narrative rather than a simple timeline by highlighting turning points, major transformations, and significant periods. The narrative should prioritize factual accuracy, context, depth, and a balanced understanding of the subject. When combined with other genres, integrate these life-story and subject-development elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Autobiography',
                'brief'              => 'Life story written by the person themselves',
                'prompt_instruction' => 'Focus on creating a personal first-person reflection where the individual explores their experiences, choices, growth, struggles, and understanding of their own life. Planning should consider personal voice, memory, important life stages, achievements, regrets, relationships, values, and moments that shaped identity. Character planning should consider how the narrator views themselves, how their perspective changes over time, and how personal experiences influence their decisions. Story structure should create a clear emotional journey showing transformation, self-discovery, challenges faced, and lessons learned. The narrative should prioritize honesty, self-awareness, reflection, and a meaningful understanding of the person behind the events. When combined with other genres, integrate these autobiographical and self-reflection elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'History',
                'brief'              => 'Historical books and events',
                'prompt_instruction' => 'Focus on presenting real events, their causes, consequences, and the people and systems involved in a clear and evidence-based manner. Planning should consider the historical period, key figures, institutions, geography, political conditions, social structures, causes, sequence of events, and long-term impact. Content planning should consider available sources, competing interpretations, important details, and how information is organized into a meaningful explanation rather than a simple list of events. Structure should create understanding of why events happened, how they developed, and why they continue to matter. When combined with other genres, integrate these historical analysis and evidence-based elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Philosophy',
                'brief'              => 'Books about ideas and philosophy',
                'prompt_instruction' => 'Focus on exploring central ideas, questions, arguments, and concepts through logical reasoning and meaningful analysis. Planning should consider the main philosophical question, key concepts, assumptions, arguments, counterarguments, examples, and practical implications of the ideas. Content planning should prioritize clarity, structured reasoning, understandable explanations, thought experiments, and connections between abstract ideas and human experience. Structure should develop ideas progressively, address opposing views, and guide readers toward deeper understanding. When combined with other genres, integrate these philosophical and conceptual elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Self Help',
                'brief'              => 'Personal development and improvement books',
                'prompt_instruction' => 'Focus on providing practical guidance that helps readers understand a problem, develop useful habits, and achieve personal improvement. Planning should consider the target audience, their challenges, goals, obstacles, and realistic methods for progress. Content planning should include clear principles, actionable steps, examples, exercises, ways to measure improvement, and strategies for overcoming difficulties. Structure should move logically from understanding the problem to applying solutions while maintaining clarity and practical value. When combined with other genres, integrate these personal-development and practical-guidance elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Psychological',
                'brief'              => 'Stories focused on human mind and emotions',
                'prompt_instruction' => 'Focus on exploring characters’ inner worlds, emotions, thoughts, perceptions, and psychological struggles. Planning should consider motivations, fears, desires, memories, beliefs, emotional patterns, defence mechanisms, and how mental states influence behaviour. Character planning should create psychologically complex individuals whose internal conflicts shape their choices and relationships. Story structure should reveal hidden motivations gradually, explore the difference between appearance and reality, build emotional tension, and resolve deeper internal conflicts. When combined with other genres, integrate these psychological and emotional-depth elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Supernatural',
                'brief'              => 'Stories involving supernatural elements',
                'prompt_instruction' => 'Focus on developing supernatural forces, beings, or events and exploring how they affect the world and characters. Planning should consider the source of supernatural elements, their rules or mysteries, their consequences, how people perceive them, and how they influence daily life and beliefs. Character planning should consider how characters react to the unknown, their beliefs, fears, curiosity, and how supernatural encounters transform them. Story structure should balance ordinary experiences with extraordinary events, maintain mystery or wonder, and create a resolution consistent with the nature of the supernatural elements. When combined with other genres, integrate these supernatural and otherworldly elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Crime',
                'brief'              => 'Crime related fiction stories',
                'prompt_instruction' => 'Focus on developing the criminal act, its motives, consequences, investigation, and the people affected by it. Planning should consider the nature of the crime, methods used, evidence, timeline, suspects, motives, investigation process, and social consequences. Character planning should consider criminals with believable psychology, investigators with strengths and limitations, victims, witnesses, and the relationships between those involved. Story structure should consider discovery of the crime, investigation progression, obstacles, pursuit, revelations, and a resolution that addresses both the crime and its consequences. When combined with other genres, integrate these crime and investigation elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],
            (object) [
                'name'               => 'Political Fiction',
                'brief'              => 'Stories involving politics and society',
                'prompt_instruction' => 'Focus on exploring power structures, political systems, ideology, institutions, and how social forces influence individuals and communities. Planning should consider the government structure, political factions, laws, economy, media, public opinion, conflicts of interest, and struggles for influence or control. Character planning should consider politicians, leaders, activists, officials, and ordinary citizens with different beliefs, ambitions, compromises, and personal consequences caused by political decisions. Story structure should consider strategic conflicts, alliances, betrayals, public and private struggles, rising political tension, and a resolution that reveals the impact of the power struggle. When combined with other genres, integrate these political and power-dynamics elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'War',
                'brief'              => 'Stories based on wars and conflicts',
                'prompt_instruction' => 'Focus on portraying the causes, experience, consequences, and human impact of war and conflict. Planning should consider the origin of the conflict, opposing sides, military strategies, technology, politics, civilians, social consequences, and the lasting effects of violence. Character planning should consider soldiers, civilians, leaders, and individuals affected by war, including their fears, motivations, moral struggles, losses, and changes caused by conflict. Story structure should balance large-scale events with personal experiences, showing the physical and emotional cost of war while building toward meaningful outcomes. When combined with other genres, integrate these conflict and war-experience elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Young Adult',
                'brief'              => 'Stories for young adult readers',
                'prompt_instruction' => 'Focus on exploring identity, personal growth, relationships, independence, and challenges connected to adolescence or early adulthood. Planning should consider the characters’ stage of life, personal struggles, social pressures, friendships, family relationships, and questions of belonging and self-discovery. Character planning should create relatable protagonists with authentic emotions, weaknesses, dreams, and evolving understanding of themselves and the world. Story structure should consider meaningful challenges, emotional growth, consequences of choices, and a resolution that reflects earned maturity and personal development. When combined with other genres, integrate these coming-of-age and identity-development elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Children',
                'brief'              => 'Books for children',
                'prompt_instruction' => 'Focus on creating age-appropriate stories with clear language, engaging characters, imagination, and meaningful emotional experiences for young readers. Planning should consider the target age group, reading ability, suitable themes, simple conflicts, curiosity, learning opportunities, and emotional understanding. Character planning should consider memorable child-friendly characters, relatable experiences, imagination, friendships, and positive growth. Story structure should maintain an engaging pace, understandable challenges, creative situations, and a satisfying resolution that provides emotional value without becoming overly complex. When combined with other genres, integrate these child-focused and age-appropriate elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Poetry',
                'brief'              => 'Poetry and verses',
                'prompt_instruction' => 'Focus on expressing emotions, ideas, experiences, and imagery through carefully chosen language, rhythm, structure, and poetic techniques. Planning should consider the central theme, emotional purpose, tone, imagery, symbolism, metaphor, sound, rhythm, and the poetic form best suited to the subject. Content planning should prioritize meaningful word choices, emotional depth, sensory experiences, and the relationship between form and meaning. Structure should consider line arrangement, stanza development, pacing, and how each element contributes to the overall poetic experience. When combined with other genres, integrate these poetic and expressive-language elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Short Story',
                'brief'              => 'Short fiction stories',
                'prompt_instruction' => 'Focus on creating a concentrated narrative built around a strong central idea, limited scope, meaningful characters, and an impactful moment or transformation. Planning should consider the core premise, central conflict, essential characters, important details, and how every element contributes to the intended effect. Character planning should focus on creating depth quickly, showing meaningful motivations, and revealing change through limited but significant events. Story structure should consider efficient pacing, entering the story at the most important moment, developing tension within a limited space, and creating an ending that leaves a lasting emotional or intellectual impact. When combined with other genres, integrate these focused storytelling and economical narrative elements with the requirements of the additional genres while avoiding duplicate planning elements.',
            ],
        ]);
    }

    public static function audiences()
    {
        return collect([
            (object) [
                'name'               => 'Children',
                'brief'              => 'Represents story books primarily intended for children.',
                'prompt_instruction' => 'Focus on creating age-appropriate stories for young readers with clear, simple language, engaging characters, imagination, and meaningful emotional experiences. Planning should consider the target age group, reading ability, suitable themes, simple conflicts, curiosity, learning opportunities, and emotional understanding. Character planning should consider memorable child-friendly characters, relatable experiences, imagination, friendships, and positive growth. Story structure should maintain an engaging pace, understandable challenges, creative situations, and a satisfying resolution that provides emotional value without becoming overly complex. When combined with genres, integrate these child-focused and age-appropriate elements with the requirements of the selected genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Young Adult',
                'brief'              => 'Represents story books primarily intended for young adult readers.',
                'prompt_instruction' => 'Focus on exploring identity, personal growth, relationships, independence, and challenges connected to adolescence or early adulthood. Planning should consider the characters’ stage of life, personal struggles, social pressures, friendships, family relationships, and questions of belonging and self-discovery. Character planning should create relatable protagonists with authentic emotions, weaknesses, dreams, and evolving understanding of themselves and the world. Story structure should consider meaningful challenges, emotional growth, consequences of choices, and a resolution that reflects earned maturity and personal development. When combined with genres, integrate these coming-of-age and identity-development elements with the requirements of the selected genres while avoiding duplicate planning elements.',
            ],

            (object) [
                'name'               => 'Adult',
                'brief'              => 'Represents story books intended for adult readers.',
                'prompt_instruction' => 'Focus on creating nuanced narratives with sophisticated language, complex themes, and deeper emotional and intellectual engagement suitable for adult readers. Planning should consider mature subject matter, socially and personally complex conflicts, layered morality, realistic relationships, and themes that assume adult life experience. Character planning should create fully developed protagonists and antagonists with believable motivations, contradictions, personal histories, and complicated relationships. Story structure should support complex pacing, subplots, moral ambiguity, meaningful ambiguity, and resolutions that respect the intelligence of the reader. When combined with genres, integrate these mature narrative and thematic elements with the requirements of the selected genres while avoiding duplicate planning elements.',
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
