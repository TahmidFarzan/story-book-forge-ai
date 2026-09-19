<?php
namespace App\Services\BackOffice;

use App\Helpers\AiPromptGeneratorHelper;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class HuggingFaceApiService
{
    protected int $defaultTimeout = 120;

    public function sendPostRequest(string $url, string $apiKey, string $model, mixed $data = null, ?int $maxOutputTokens = null, ?int $timeout = null, string $context = ''): array
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

        return $this->formatAIResponse($response, $context);
    }

    private function formatAIResponse($response, string $context = ''): array
    {
        if (! $response->successful()) {
            return $this->formatErrorResponse(
                $this->formatApiErrorResponse($response)
            );
        }

        try {
            $apiResponse = $this->parseJsonResponse($response);

            $data = $this->decodeAiResponseContent($apiResponse, $context);
        } catch (Exception $exception) {
            return $this->formatErrorResponse(
                $exception->getMessage()
            );
        }

        return $this->formatSuccessResponse($data);
    }

    private function formatSuccessResponse(array $data): array
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

    public function processAIResponse(string $promptName, array $apiResponse): object
    {
        $fields = $this->aiResponseFormats()[$promptName] ?? [];

        if ($fields === []) {
            return (object) $apiResponse;
        }

        $result = [];

        foreach ($fields as $source => $definition) {
            if (is_int($source)) {
                $result[$definition] = $apiResponse[$definition] ?? [];
                continue;
            }

            $default = array_key_exists('default', $definition) ? $definition['default'] : [];

            $result[$definition['key'] ?? $source] = $apiResponse[$source] ?? $default;
        }

        return (object) $result;
    }

    private function aiResponseFormats(): array
    {
        return [
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP1_FOUNDATION_GENERATOR                => [
                'story_book_title'      => ['key' => 'title', 'default' => null],
                'story_book_subtitle'   => ['key' => 'subtitle', 'default' => null],
                'story_book_foundation' => ['key' => 'foundation', 'default' => null],
            ],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP2_CHARACTERS_GENERATOR                => ['characters', 'relationship_dynamics'],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP3_WORLD_VIBE_GENERATOR                => ['world_overview', 'world_rules', 'culture_and_history', 'lore'],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP4_LOCATIONS_GENERATOR                 => ['locations', 'regions', 'landmarks', 'environment_details'],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP5_FACTIONS_GENERATOR                  => ['factions', 'goals_and_values', 'conflicts', 'alliances'],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP6_CREATURE_GENERATOR                  => ['creatures', 'abilities', 'behaviors', 'ecosystem_role'],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP7_SYSTEM_GENERATOR                    => ['systems', 'mechanics', 'limitations', 'rules'],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP8_TIMELINE_GENERATOR                  => ['timeline', 'major_events', 'milestones', 'historical_flow'],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP9_STORY_STRUCTURE_GENERATOR           => ['story_outline', 'acts_and_chapters', 'plot_progression', 'pacing_guide'],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP10_TWISTS_AND_FORESHADOWING_GENERATOR => ['twists', 'foreshadowing', 'hidden_clues', 'reveal_points'],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP11_SCENE_PLAN_GENERATOR               => ['scene_list', 'scene_objectives', 'locations', 'pov_and_tone'],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP12_DIALOGUE_PLAN_GENERATOR            => ['dialogue_bank', 'character_voice', 'conversation_flow', 'key_dialogues'],
            AiPromptGeneratorHelper::AI_PROMPT_NAME_STEP13_PAGE_PLAN_GENERATOR                => ['page_layout', 'page_descriptions', 'illustration_notes', 'key_points'],
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
