<?php

declare(strict_types=1);

namespace Modxai1y\Providers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Modxai1y\ServiceException;

/**
 * InkluDocs (https://inkludocs.de/api/v1/docs): POST /api/v1/alt-text with the image as base64 in JSON,
 * the key in the header X-API-Key. The service has its own rules for the text (and its own language
 * parameter: de, en, fr, es, da, sv), so prompt, model and maximum length of the extra do not apply.
 */
class InkluDocsProvider extends AbstractProvider
{
    public const DEFAULT_BASE_URL = 'https://inkludocs.de';

    public function generate(string $data, string $mediaType, string $context, int $maxLength): string
    {
        $key = trim((string) $this->setting('inkludocs_apikey', ''));
        if ($key === '') {
            throw new ServiceException($this->lex('modxai1y.error.api_key_missing', ['setting' => 'modxai1y.inkludocs_apikey']));
        }

        $body = [
            'image_base64' => $data,
            'language' => (string) $this->setting('inkludocs_language', 'de'),
        ];
        if ($context !== '') {
            $body['context'] = $context;
        }

        try {
            $response = (new Client([
                'base_uri' => rtrim((string) $this->setting('inkludocs_base_url', self::DEFAULT_BASE_URL), '/') . '/',
                'timeout' => 45,
                'http_errors' => false,
            ]))->post('api/v1/alt-text', ['headers' => ['X-API-Key' => $key, 'Accept' => 'application/json'], 'json' => $body]);
        } catch (GuzzleException $e) {
            $this->logError('InkluDocs: ' . $e::class . ': ' . $e->getMessage());
            throw new ServiceException($this->lex('modxai1y.error.connection'));
        }

        $status = $response->getStatusCode();
        $json = json_decode((string) $response->getBody(), true);
        $json = is_array($json) ? $json : [];

        if ($status >= 400) {
            $code = (string) ($json['error']['code'] ?? '');
            $this->logError(sprintf('InkluDocs: %d %s %s', $status, $code, (string) ($json['error']['message'] ?? '')));
            throw new ServiceException(match (true) {
                $status === 401 => $this->lex('modxai1y.error.api_key_invalid', ['setting' => 'modxai1y.inkludocs_apikey']),
                $code === 'credits_fehlen' || $status === 402 => $this->lex('modxai1y.error.credits'),
                $status === 429 => $this->lex('modxai1y.error.busy'),
                default => $this->lex('modxai1y.error.api', ['status' => (string) $status]),
            });
        }

        $alt = trim((string) ($json['alt_text'] ?? ''));
        if ($alt === '') {
            throw new ServiceException($this->lex('modxai1y.error.empty'));
        }
        return $alt;
    }
}
