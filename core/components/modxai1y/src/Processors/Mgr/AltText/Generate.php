<?php

declare(strict_types=1);

namespace Modxai1y\Processors\Mgr\AltText;

use Modxai1y\Service;
use Modxai1y\ServiceException;
use MODX\Revolution\Processors\Processor;

/**
 * Manager connector processor: creates an alt text for one image.
 *
 *   action    Modxai1y\Processors\Mgr\AltText\Generate
 *   image     URL or path of the image, as shown in the ContentBlocks field
 *   context   optional hint (page title)
 *
 * Response: { success: true, object: { alt: "…" } } or { success: false, message: "…" }
 */
class Generate extends Processor
{
    public function checkPermissions(): bool
    {
        return $this->modx->hasPermission('save_document') && $this->modx->user->hasSessionContext('mgr');
    }

    public function initialize()
    {
        $this->modx->lexicon->load('modxai1y:default');
        return parent::initialize();
    }

    public function process()
    {
        $image = trim((string) $this->getProperty('image'));
        if ($image === '') {
            return $this->failure($this->modx->lexicon('modxai1y.error.image_invalid'));
        }

        try {
            $alt = (new Service($this->modx))->generate($image, (string) $this->getProperty('context'));
        } catch (ServiceException $e) {
            return $this->failure($e->getMessage());
        }

        return $this->success('', ['alt' => $alt]);
    }
}
