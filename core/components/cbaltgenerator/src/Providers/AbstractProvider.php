<?php

declare(strict_types=1);

namespace CbAltGenerator\Providers;

use MODX\Revolution\modX;

abstract class AbstractProvider implements ProviderInterface
{
    public function __construct(protected readonly modX $modx)
    {
    }

    protected function setting(string $key, mixed $default = null): mixed
    {
        return $this->modx->getOption('cbaltgenerator.' . $key, null, $default, true);
    }

    protected function lex(string $key, array $params = []): string
    {
        return $this->modx->lexicon($key, $params);
    }

    /** details go to the MODX error log, never to the browser (and never the key) */
    protected function logError(string $message): void
    {
        $this->modx->log(modX::LOG_LEVEL_ERROR, '[ContentBlocks-Image-ALT-Text-Generator] ' . $message);
    }
}
