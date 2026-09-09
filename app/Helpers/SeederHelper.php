<?php

namespace App\Helpers;

class SeederHelper
{
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
