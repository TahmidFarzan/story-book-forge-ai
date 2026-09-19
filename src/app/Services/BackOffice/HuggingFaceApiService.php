<?php
namespace App\Services\BackOffice;

use Exception;
use Illuminate\Support\Facades\Http;

class HuggingFaceApiService
{
    protected int $defaultTimeout = 120;

    public function sendPostRequest(string $url, string $apiKey, string $model, mixed $data = null, ?int $maxOutputTokens = null, ?int $timeout = null): array
    {
        $requestTimeout = $timeout ?? $this->defaultTimeout;

        set_time_limit($requestTimeout);

        $payload = $this->buildPayload(
            $model,
            $data,
            $maxOutputTokens
        );

        $endpoint = rtrim($url, '/');

        $response = Http::timeout(
            $requestTimeout
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

    public function sendGetRequest(string $url, string $apiKey, array $params = [], ?int $timeout = null): array
    {
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

        $response = Http::timeout(
            $requestTimeout
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

        return $this->extractImageResponse($response);
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
                'type' => 'base64',
                'extension' => $this->extensionFromMime($dataUriMatch[1]),
                'encoded' => $encoded,
            ];
        }

        if (preg_match('/^https?:\/\/\S+$/i', $candidate)) {
            return [
                'type' => 'url',
                'url' => $candidate,
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
                'type' => 'base64',
                'extension' => 'png',
                'encoded' => $candidate,
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
                    'type' => 'base64',
                    'extension' => $this->extensionFromMime($contentType),
                    'encoded' => $trimmed,
                ];
            }

            return null;
        }

        return [
            'type' => 'base64',
            'extension' => $this->extensionFromMime($contentType),
            'encoded' => base64_encode($body),
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
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'image/avif' => 'avif',
            'image/svg+xml' => 'svg',
            'image/bmp' => 'bmp',
            default => 'png',
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
