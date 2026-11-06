<?php

/**
 * Builds the MODX transport package of ContentBlocks-Image-ALT-Text-Generator.
 *
 *   php _build/build.transport.php --core=/path/to/modx/core [--out=_packages]
 *
 * The script only needs the classes and the workspace of a MODX 3 installation (it reads, it does not install
 * anything there). Run `composer install --no-dev` in core/components/cbaltgenerator first: the Anthropic SDK
 * (vendor/) is part of the package. Tests and docs of the dependencies are left out.
 *
 * Result: <out>/cbaltgenerator-<version>-<release>.transport.zip
 */

use MODX\Revolution\modNamespace;
use MODX\Revolution\modPlugin;
use MODX\Revolution\modPluginEvent;
use MODX\Revolution\modSystemSetting;
use MODX\Revolution\modX;
use MODX\Revolution\Transport\modPackageBuilder;
use xPDO\Transport\xPDOFileVehicle;
use xPDO\Transport\xPDOTransport;

$options = getopt('', ['core:', 'out::']);
$root = dirname(__DIR__);
$corePath = rtrim((string) ($options['core'] ?? getenv('MODX_CORE_PATH') ?: ''), '/') . '/';
if ($corePath === '/' || !is_file($corePath . 'vendor/autoload.php')) {
    fwrite(STDERR, "Usage: php _build/build.transport.php --core=/path/to/modx/core\n");
    exit(1);
}
$out = rtrim((string) ($options['out'] ?? $root . '/_packages'), '/') . '/';
$config = json_decode((string) file_get_contents(__DIR__ . '/config.json'), true);
[$version, $release] = array_pad(explode('-', (string) $config['version'], 2), 2, '');
$name = (string) $config['lowCaseName'];

if (!is_file($root . '/core/components/' . $name . '/vendor/autoload.php')) {
    fwrite(STDERR, "vendor/ is missing: run `composer install --no-dev` in core/components/{$name} first.\n");
    exit(1);
}

require_once $corePath . 'vendor/autoload.php';
define('MODX_CORE_PATH', $corePath);
define('MODX_CONFIG_KEY', 'config');
$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

// staging copy of core/ without the tests and docs of the dependencies
$staging = sys_get_temp_dir() . '/' . $name . '-build-' . getmypid() . '/';
$copy = static function (string $from, string $to) use (&$copy): void {
    @mkdir($to, 0755, true);
    foreach (new DirectoryIterator($from) as $item) {
        if ($item->isDot() || $item->getFilename() === '.DS_Store') {
            continue;
        }
        $target = $to . $item->getFilename();
        $item->isDir() ? $copy($item->getPathname(), $target . '/') : copy($item->getPathname(), $target);
    }
};
$copy($root . '/core/components/' . $name, $staging . 'core/components/' . $name . '/');
$copy($root . '/assets/components/' . $name, $staging . 'assets/components/' . $name . '/');
$remove = static function (string $dir) use (&$remove): void {
    foreach (new DirectoryIterator($dir) as $item) {
        if (!$item->isDot()) {
            $item->isDir() && !$item->isLink() ? $remove($item->getPathname()) : unlink($item->getPathname());
        }
    }
    rmdir($dir);
};
$prune = static function (string $dir) use (&$prune, $remove): void {
    foreach (new DirectoryIterator($dir) as $item) {
        if ($item->isDot()) {
            continue;
        }
        $path = $item->getPathname();
        if ($item->isDir()) {
            preg_match('/^(tests?|docs?|examples?|\.github|\.git)$/i', $item->getFilename()) ? $remove($path) : $prune($path);
        } elseif (preg_match('/\.(md|dist|xml|neon)$|^(CHANGELOG|UPGRADE|phpunit|phpstan|psalm|composer\.lock)/i', $item->getFilename())) {
            unlink($path);
        }
    }
};
$prune($staging . 'core/components/' . $name . '/vendor/');

$builder = new modPackageBuilder($modx);
$builder->directory = $out;
@mkdir($out, 0755, true);
$builder->createPackage($name, $version, $release);
$builder->registerNamespace($name, false, true, '{core_path}components/' . $name . '/', '{assets_path}components/' . $name . '/');

// system settings
foreach ($config['package']['systemSettings'] as $def) {
    $setting = $modx->newObject(modSystemSetting::class);
    $setting->fromArray([
        'key' => $name . '.' . $def['key'],
        'xtype' => $def['type'],
        'namespace' => $name,
        'area' => $def['area'],
        'value' => $def['value'],
    ], '', true, true);
    $builder->putVehicle($builder->createVehicle($setting, [
        xPDOTransport::UNIQUE_KEY => 'key',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => false, // an update keeps what the admin entered (API keys)
    ]));
}

// plugin with its events, the code comes from the plugin file
foreach ($config['package']['elements']['plugins'] as $def) {
    $code = (string) file_get_contents($root . '/core/components/' . $name . '/elements/plugins/' . $def['name'] . '.plugin.php');
    $plugin = $modx->newObject(modPlugin::class);
    $plugin->fromArray([
        'name' => $def['name'],
        'description' => $config['description'],
        'plugincode' => trim(preg_replace('/^<\?php\s*/', '', $code)),
        'static' => 0,
    ], '', true, true);
    $events = [];
    foreach ($def['events'] as $eventName) {
        $event = $modx->newObject(modPluginEvent::class);
        $event->fromArray(['event' => $eventName, 'priority' => 0, 'propertyset' => 0], '', true, true);
        $events[] = $event;
    }
    $plugin->addMany($events, 'PluginEvents');
    $builder->putVehicle($builder->createVehicle($plugin, [
        xPDOTransport::UNIQUE_KEY => 'name',
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::RELATED_OBJECTS => true,
        xPDOTransport::RELATED_OBJECT_ATTRIBUTES => [
            'PluginEvents' => [
                xPDOTransport::PRESERVE_KEYS => true,
                xPDOTransport::UPDATE_OBJECT => true,
                xPDOTransport::UNIQUE_KEY => ['pluginid', 'event'],
            ],
        ],
    ]));
}

// files: core/components/<name> and assets/components/<name> (file vehicles, no database object)
foreach ([
    ['core/components/' . $name, "return MODX_CORE_PATH . 'components/';"],
    ['assets/components/' . $name, "return MODX_ASSETS_PATH . 'components/';"],
] as [$source, $target]) {
    $builder->putVehicle($builder->createVehicle(
        ['source' => $staging . $source, 'target' => $target],
        ['vehicle_class' => xPDOFileVehicle::class]
    ));
}

$builder->setPackageAttributes([
    'license' => file_get_contents($root . '/core/components/' . $name . '/docs/license.md'),
    'readme' => file_get_contents($root . '/core/components/' . $name . '/docs/readme.md'),
    'changelog' => file_get_contents($root . '/core/components/' . $name . '/docs/changelog.md'),
]);
$builder->pack();

// clean up
$remove(rtrim($staging, '/'));
$zip = $out . $builder->getSignature() . '.transport.zip';
printf("\nBuilt %s (%.1f MB)\n", $zip, filesize($zip) / 1048576);
