<?php

declare(strict_types=1);

namespace Modxai1y\Providers;

use Modxai1y\ServiceException;

/**
 * A service that describes an image. Providers throw ServiceException with a message that is safe to
 * show in the manager and log the details to the MODX error log.
 */
interface ProviderInterface
{
    /**
     * @param string $data      the image, base64 encoded
     * @param string $mediaType image/jpeg, image/png, image/gif or image/webp
     * @param string $context   optional hint (page title)
     * @param int    $maxLength wished maximum length of the text (providers that cannot limit it ignore it)
     *
     * @throws ServiceException
     */
    public function generate(string $data, string $mediaType, string $context, int $maxLength): string;
}
