<?php

declare(strict_types=1);

namespace Modxai1y;

use Modxai1y\Providers\ClaudeProvider;
use Modxai1y\Providers\InkluDocsProvider;
use Modxai1y\Providers\ProviderInterface;
use MODX\Revolution\modX;

/**
 * Creates alt texts for images.
 *
 * The API keys never leave the server: the manager only talks to the processor
 * (Processors\Mgr\AltText\Generate), which reads the image from the web root, asks the configured
 * provider (Claude or InkluDocs) and returns plain text.
 */
class Service
{
    /** the API takes images up to 5 MB, bigger ones are scaled down */
    private const MAX_IMAGE_BYTES = 4_500_000;

    private const MIME_TYPES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

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
        $provider = strtolower(trim((string) $this->setting('provider', 'claude')));
        $path = $this->resolveFile($image);
        ['data' => $data, 'mediaType' => $mediaType] = $this->encodeImage($path);

        $context = trim(preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $context) ?? '');
        $context = mb_substr($context, 0, 200);
        $maxLength = max(20, (int) $this->setting('max_length', 125));

        $text = $this->provider($provider)->generate($data, $mediaType, $context, $maxLength);

        // Claude is asked for the length, InkluDocs has its own rules: only tidy up its text
        $alt = $this->clean($text, $provider === 'claude' ? $maxLength : 1000);
        if ($alt === '') {
            throw new ServiceException($this->lex('modxai1y.error.empty'));
        }
        return $alt;
    }

    private function provider(string $name): ProviderInterface
    {
        $autoload = dirname(__DIR__) . '/vendor/autoload.php';
        if (!is_file($autoload)) {
            throw new ServiceException($this->lex('modxai1y.error.vendor'));
        }
        require_once $autoload;

        return match ($name) {
            'claude' => new ClaudeProvider($this->modx),
            'inkludocs' => new InkluDocsProvider($this->modx),
            default => throw new ServiceException($this->lex('modxai1y.error.provider', ['provider' => $name])),
        };
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

    private function setting(string $key, mixed $default = null): mixed
    {
        return $this->modx->getOption('modxai1y.' . $key, null, $default, true);
    }

    private function lex(string $key, array $params = []): string
    {
        return $this->modx->lexicon($key, $params);
    }
}
