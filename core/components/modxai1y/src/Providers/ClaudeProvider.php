<?php

declare(strict_types=1);

namespace Modxai1y\Providers;

use Anthropic\Client;
use Anthropic\Core\Exceptions\APIConnectionException;
use Anthropic\Core\Exceptions\APIStatusException;
use Anthropic\Core\Exceptions\AuthenticationException;
use Anthropic\Core\Exceptions\OverloadedException;
use Anthropic\Core\Exceptions\RateLimitException;
use Modxai1y\ServiceException;

/**
 * Claude (Anthropic Messages API) with the official PHP SDK.
 */
class ClaudeProvider extends AbstractProvider
{
    public const DEFAULT_MODEL = 'claude-haiku-5-5';

    private ?Client $client = null;

    public function generate(string $data, string $mediaType, string $context, int $maxLength): string
    {
        $text = 'Schreibe den Alt-Text für dieses Bild.';
        if ($context !== '') {
            $text .= "\n\nKontext (nur zur Orientierung, nicht wiederholen): " . $context;
        }

        $params = [
            'model' => (string) $this->setting('model', self::DEFAULT_MODEL),
            'maxTokens' => 1024,
            'system' => $this->systemPrompt($maxLength),
            'messages' => [[
                'role' => 'user',
                'content' => [
                    ['type' => 'image', 'source' => ['type' => 'base64', 'mediaType' => $mediaType, 'data' => $data]],
                    ['type' => 'text', 'text' => $text],
                ],
            ]],
        ];
        // a short answer needs no deep thinking (effort exists from the 5.x models on)
        if (preg_match('/^claude-(haiku|sonnet|opus|fable)-5/', $params['model'])) {
            $params['outputConfig'] = ['effort' => 'low'];
        }

        try {
            $message = $this->client()->messages->create(...$params);
        } catch (AuthenticationException $e) {
            $this->logError($e::class . ': ' . $e->getMessage());
            throw new ServiceException($this->lex('modxai1y.error.api_key_invalid', ['setting' => 'modxai1y.apikey']));
        } catch (RateLimitException | OverloadedException $e) {
            $this->logError($e::class . ': ' . $e->getMessage());
            throw new ServiceException($this->lex('modxai1y.error.busy'));
        } catch (APIConnectionException $e) {
            $this->logError($e::class . ': ' . $e->getMessage());
            throw new ServiceException($this->lex('modxai1y.error.connection'));
        } catch (APIStatusException $e) {
            $this->logError($e::class . ': ' . $e->getMessage());
            throw new ServiceException($this->lex('modxai1y.error.api', ['status' => (string) $e->status]));
        }

        if ($message->stopReason === 'refusal') {
            throw new ServiceException($this->lex('modxai1y.error.refusal'));
        }

        foreach ($message->content as $block) {
            if ($block->type === 'text' && trim($block->text) !== '') {
                return $block->text;
            }
        }
        throw new ServiceException($this->lex('modxai1y.error.empty'));
    }

    private function systemPrompt(int $maxLength): string
    {
        $prompt = trim((string) $this->setting('prompt', ''));
        if ($prompt === '') {
            $prompt = $this->lex('modxai1y.default_prompt');
        }
        return strtr($prompt, [
            '{max_length}' => (string) $maxLength,
            '{language}' => (string) $this->setting('language', 'Deutsch'),
        ]);
    }

    private function client(): Client
    {
        if ($this->client === null) {
            $key = trim((string) $this->setting('apikey', ''));
            $baseUrl = trim((string) $this->setting('api_base_url', ''));
            if ($key === '' && getenv('ANTHROPIC_API_KEY') === false) {
                throw new ServiceException($this->lex('modxai1y.error.api_key_missing', ['setting' => 'modxai1y.apikey']));
            }
            $this->client = new Client(
                apiKey: $key !== '' ? $key : null,
                baseUrl: $baseUrl !== '' ? $baseUrl : null,
                requestOptions: ['timeout' => 30.0, 'maxRetries' => 1],
            );
        }
        return $this->client;
    }
}
