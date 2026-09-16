<?php

namespace App\Services\BackOffice;

use Exception;
use Illuminate\Support\Facades\Http;

class HuggingFaceApiService
{
    protected int $defaultTimeout = 120;

    public function sendPostRequest(
        string $url,
        string $apiKey,
        string $model,
        mixed $data = null,
        ?int $maxOutputTokens = null,
        ?int $timeout = null
    ): array {
        $payload = $this->buildPayload(
            $model,
            $data,
            $maxOutputTokens
        );

        $endpoint = rtrim($url, '/') . '/chat/completions';

        $response = Http::timeout(
            $timeout ?? $this->defaultTimeout
        )
            ->withToken($apiKey)
            ->acceptJson()
            ->post(
                $endpoint,
                $payload
            );

        if (! $response->successful()) {
            throw new Exception(
                $response->body()
            );
        }

        return $response->json();
    }

    public function sendGetRequest(
        string $url,
        string $apiKey,
        array $params = [],
        ?int $timeout = null
    ): array {
        $response = Http::timeout(
            $timeout ?? $this->defaultTimeout
        )
            ->withToken($apiKey)
            ->acceptJson()
            ->get(
                rtrim($url, '/'),
                $params
            );

        if (! $response->successful()) {
            throw new Exception(
                $response->body()
            );
        }

        return $response->json();
    }

    private function buildPayload(
        string $model,
        mixed $data = null,
        ?int $maxOutputTokens = null
    ): array {
        $content = $this->buildContent($data);

        $payload = [
            'model' => $model,
            'messages' => [
                [
                    'role' => 'user',
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
