<?php

declare(strict_types=1);

namespace Modxai1y;

use Anthropic\Client;
use Anthropic\Core\Exceptions\APIConnectionException;
use Anthropic\Core\Exceptions\APIStatusException;
use Anthropic\Core\Exceptions\AuthenticationException;
use Anthropic\Core\Exceptions\OverloadedException;
use Anthropic\Core\Exceptions\RateLimitException;
use MODX\Revolution\modX;

/**
 * Creates alt texts for images with Claude.
 *
 * The API key never leaves the server: the manager only talks to the processor
 * (Processors\Mgr\AltText\Generate), which reads the image from the web root, calls the
 * Anthropic Messages API and returns plain text.
 */
class Service
{
    public const DEFAULT_MODEL = 'claude-haiku-5-5';

    /** the API takes images up to 5 MB, bigger ones are scaled down */
    private const MAX_IMAGE_BYTES = 4_500_000;

    private const MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

    private ?Client $client = null;

    public function __construct(private readonly modX $modx)
    {
        $this->modx->lexicon->load('modxai1y:default');
    }

    /**
     * @param string $image   image URL or path as shown in the manager (inside the allowed folders)
     * @param string $context optional hint, e.g. the page title
     *
     * @throws ServiceException
     */
    public function generate(string $image, string $context = ''): string
    {
        $path = $this->resolveFile($image);
        ['data' => $data, 'mediaType' => $mediaType] = $this->encodeImage($path);

        $maxLength = max(20, (int) $this->setting('max_length', 125));
        $text = 'Schreibe den Alt-Text für dieses Bild.';
        $context = trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $context) ?? '');
        if ($context !== '') {
            $text .= "\n\nKontext (nur zur Orientierung, nicht wiederholen): " . mb_substr($context, 0, 200);
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
            $this->logError($e);
            throw new ServiceException($this->lex('modxai1y.error.api_key_invalid'));
        } catch (RateLimitException | OverloadedException $e) {
            $this->logError($e);
            throw new ServiceException($this->lex('modxai1y.error.busy'));
        } catch (APIConnectionException $e) {
            $this->logError($e);
            throw new ServiceException($this->lex('modxai1y.error.connection'));
        } catch (APIStatusException $e) {
            $this->logError($e);
            throw new ServiceException($this->lex('modxai1y.error.api', ['status' => (string) $e->status]));
        }

        if ($message->stopReason === 'refusal') {
            throw new ServiceException($this->lex('modxai1y.error.refusal'));
        }

        foreach ($message->content as $block) {
            if ($block->type === 'text') {
                $alt = $this->clean($block->text, $maxLength);
                if ($alt !== '') {
                    return $alt;
                }
            }
        }
        throw new ServiceException($this->lex('modxai1y.error.empty'));
    }

    /**
     * Maps what the manager shows (relative path, absolute path, full URL of this site) to a file
     * below the allowed folders. Anything else (other hosts, "..", other folders) is refused.
     *
     * @throws ServiceException
     */
    public function resolveFile(string $image): string
    {
        $parts = parse_url(trim($image));
        if ($parts === false || ($parts['path'] ?? '') === '') {
            throw new ServiceException($this->lex('modxai1y.error.image_invalid'));
        }

        $siteHost = parse_url((string) $this->modx->getOption('site_url'), PHP_URL_HOST);
        if (isset($parts['host']) && $siteHost && strcasecmp($parts['host'], $siteHost) !== 0) {
            throw new ServiceException($this->lex('modxai1y.error.image_external'));
        }

        $path = rawurldecode($parts['path']);
        $baseUrl = '/' . trim((string) $this->modx->getOption('base_url'), '/');
        $baseUrl = $baseUrl === '/' ? '/' : $baseUrl . '/';
        if ($baseUrl !== '/' && str_starts_with($path, $baseUrl)) {
            $path = substr($path, strlen($baseUrl));
        }
        $path = ltrim($path, '/');

        $basePath = realpath((string) $this->modx->getOption('base_path'));
        $file = $basePath ? realpath($basePath . '/' . $path) : false;
        if ($file === false || !is_file($file)) {
            throw new ServiceException($this->lex('modxai1y.error.image_missing'));
        }

        foreach (array_filter(array_map('trim', explode(',', (string) $this->setting('allowed_paths', 'upload/')))) as $allowed) {
            $dir = realpath($basePath . '/' . ltrim($allowed, '/'));
            if ($dir !== false && str_starts_with($file, $dir . DIRECTORY_SEPARATOR)) {
                return $file;
            }
        }
        throw new ServiceException($this->lex('modxai1y.error.image_not_allowed'));
    }

    /**
     * Reads the image and scales it down when it is too big for the API or needlessly large
     * (cheaper and faster, the description does not get better with more pixels).
     *
     * @return array{data: string, mediaType: string}
     *
     * @throws ServiceException
     */
    public function encodeImage(string $file): array
    {
        $mediaType = (string) (new \finfo(FILEINFO_MIME_TYPE))->file($file);
        if (!in_array($mediaType, self::MIME_TYPES, true)) {
            throw new ServiceException($this->lex('modxai1y.error.image_type'));
        }

        $bytes = (string) file_get_contents($file);
        $maxEdge = max(256, (int) $this->setting('max_image_edge', 1568));
        $size = @getimagesizefromstring($bytes);
        $tooBig = strlen($bytes) > self::MAX_IMAGE_BYTES || ($size && max($size[0], $size[1]) > $maxEdge);

        if ($tooBig) {
            $scaled = $this->scale($bytes, $maxEdge);
            if ($scaled === null && strlen($bytes) > self::MAX_IMAGE_BYTES) {
                throw new ServiceException($this->lex('modxai1y.error.image_big'));
            }
            if ($scaled !== null) {
                return ['data' => base64_encode($scaled), 'mediaType' => 'image/jpeg'];
            }
        }
        return ['data' => base64_encode($bytes), 'mediaType' => $mediaType];
    }

    /**
     * Scales an image down to a JPEG with a longest edge of $maxEdge. Null without GD or on failure.
     */
    private function scale(string $bytes, int $maxEdge): ?string
    {
        if (!function_exists('imagecreatefromstring')) {
            return null;
        }
        $source = @imagecreatefromstring($bytes);
        if ($source === false) {
            return null;
        }
        $width = imagesx($source);
        $height = imagesy($source);
        $ratio = min(1.0, $maxEdge / max($width, $height));
        $newWidth = max(1, (int) round($width * $ratio));
        $newHeight = max(1, (int) round($height * $ratio));

        $target = imagecreatetruecolor($newWidth, $newHeight);
        imagefill($target, 0, 0, imagecolorallocate($target, 255, 255, 255)); // transparency becomes white
        imagecopyresampled($target, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagejpeg($target, null, 82);
        return (string) ob_get_clean();
    }

    /**
     * One line, no quotes around, no "Bild von", at most $maxLength characters (cut at a word).
     */
    public function clean(string $text, int $maxLength): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? '');
        $text = trim($text, " \t\"'„“”‚‘’»«");
        $text = preg_replace('/^(ein\s+)?(bild|foto|abbildung|grafik)\s+(von|mit|zeigt)\s+/iu', '', $text) ?? $text;
        if (mb_strlen($text) > $maxLength) {
            $cut = mb_substr($text, 0, $maxLength);
            $space = mb_strrpos($cut, ' ');
            $text = rtrim($space !== false && $space > $maxLength * 0.6 ? mb_substr($cut, 0, $space) : $cut, " ,;:-");
        }
        return $text;
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
            $autoload = dirname(__DIR__) . '/vendor/autoload.php';
            if (!is_file($autoload)) {
                throw new ServiceException($this->lex('modxai1y.error.vendor'));
            }
            require_once $autoload;

            $key = trim((string) $this->setting('apikey', ''));
            $baseUrl = trim((string) $this->setting('api_base_url', ''));
            if ($key === '' && getenv('ANTHROPIC_API_KEY') === false) {
                throw new ServiceException($this->lex('modxai1y.error.api_key_missing'));
            }
            $this->client = new Client(
                apiKey: $key !== '' ? $key : null,
                baseUrl: $baseUrl !== '' ? $baseUrl : null,
                requestOptions: ['timeout' => 30.0, 'maxRetries' => 1],
            );
        }
        return $this->client;
    }

    private function setting(string $key, mixed $default = null): mixed
    {
        return $this->modx->getOption('modxai1y.' . $key, null, $default, true);
    }

    private function lex(string $key, array $params = []): string
    {
        return $this->modx->lexicon($key, $params);
    }

    /** details go to the MODX error log, never to the browser (and never the key) */
    private function logError(\Throwable $e): void
    {
        $this->modx->log(modX::LOG_LEVEL_ERROR, '[modxAI1y] ' . $e::class . ': ' . $e->getMessage());
    }
}
