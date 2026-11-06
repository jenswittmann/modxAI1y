<?php
/**
 * modxAI1y
 *
 * Adds an "create alt text" button to the image fields of ContentBlocks in the manager. The button talks to the
 * processor Modxai1y\Processors\Mgr\AltText\Generate – the API key stays on the server and is not part of the page.
 *
 * Event: OnManagerPageBeforeRender
 *
 * @var \MODX\Revolution\modX $modx
 * @var array $scriptProperties
 */

if ($modx->event->name !== 'OnManagerPageBeforeRender' || !$modx->controller) {
    return;
}

// only where ContentBlocks lives: creating and editing resources
if (!in_array((string) ($_GET['a'] ?? ''), ['resource/update', 'resource/create'], true)) {
    return;
}

$modx->lexicon->load('modxai1y:default');

$assetsUrl = $modx->getOption('modxai1y.assets_url', null, $modx->getOption('assets_url') . 'components/modxai1y/');
$version = '0.2.0';

// default field queries: where the button goes (selectors in the ContentBlocks markup)
$defaultQueries = [
    [
        'field' => '.contentblocks-field-image_with_title',
        'image' => 'input.url',
        'target' => '.contentblocks-field-image-title-input',
    ],
    [
        'field' => '.contentblocks-field-gallery-image',
        'image' => '.contentblocks-field-gallery-image-view img',
        'target' => '.title',
    ],
];
$queries = json_decode((string) $modx->getOption('modxai1y.field_queries', null, ''), true);
if (!is_array($queries) || !$queries) {
    $queries = $defaultQueries;
}

$config = [
    'connectorAction' => 'Modxai1y\\Processors\\Mgr\\AltText\\Generate',
    'fieldQueries' => $queries,
    'lexicon' => [
        'button' => $modx->lexicon('modxai1y.button'),
        'buttonTitle' => $modx->lexicon('modxai1y.button_title'),
        'busy' => $modx->lexicon('modxai1y.busy'),
        'done' => $modx->lexicon('modxai1y.done'),
        'noImage' => $modx->lexicon('modxai1y.error.no_image'),
        'overwrite' => $modx->lexicon('modxai1y.overwrite'),
        'error' => $modx->lexicon('modxai1y.error.generic'),
    ],
];

$modx->controller->addCss($assetsUrl . 'css/mgr/modxai1y.css?v=' . $version);
$modx->controller->addHtml(
    '<script>var Modxai1y = ' . json_encode($config, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) . ';</script>'
);
$modx->controller->addLastJavascript($assetsUrl . 'js/mgr/modxai1y.js?v=' . $version);