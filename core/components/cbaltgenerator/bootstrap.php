<?php

/**
 * ContentBlocks-Image-ALT-Text-Generator bootstrap (MODX 3): registers the autoloader for the extra's own classes.
 * The Composer packages (Anthropic SDK) are loaded lazily by the service, not on every request.
 *
 * @var \MODX\Revolution\modX $modx
 * @var array $namespace
 */

spl_autoload_register(static function (string $class): void {
    $prefix = 'CbAltGenerator\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }
    $file = __DIR__ . '/src/' . str_replace('\\', '/', substr($class, strlen($prefix))) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});
