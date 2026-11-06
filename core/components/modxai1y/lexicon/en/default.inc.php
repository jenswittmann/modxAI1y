<?php
/**
 * modxAI1y lexicon (en)
 */
$_lang['modxai1y'] = 'modxAI1y';
$_lang['modxai1y.button'] = 'Create alt text';
$_lang['modxai1y.button_title'] = 'Let Claude describe the image as an alt text';
$_lang['modxai1y.busy'] = 'Creating alt text …';
$_lang['modxai1y.done'] = 'Alt text created. Please check it.';
$_lang['modxai1y.overwrite'] = 'The field already has a text. Replace it?';
$_lang['modxai1y.default_prompt'] = 'You write alt texts for images on a website. Describe factually and briefly what the image shows and what matters for understanding the page. Rules: at most {max_length} characters, one sentence, no introduction like "Image of" or "Photo of", no judgements, no line breaks, no quotation marks. Language: {language}. Answer with the alt text only.';

$_lang['modxai1y.error.generic'] = 'The alt text could not be created.';
$_lang['modxai1y.error.no_image'] = 'Please choose an image first.';
$_lang['modxai1y.error.api_key_missing'] = 'No API key. Please enter it in the system setting modxai1y.apikey.';
$_lang['modxai1y.error.api_key_invalid'] = 'The API key was rejected. Please check the system setting modxai1y.apikey.';
$_lang['modxai1y.error.busy'] = 'The service is busy right now. Please try again in a moment.';
$_lang['modxai1y.error.connection'] = 'The connection to the service failed. Please try again.';
$_lang['modxai1y.error.api'] = 'The service answered with an error ([[+status]]). Details are in the MODX error log.';
$_lang['modxai1y.error.refusal'] = 'The model did not describe this image.';
$_lang['modxai1y.error.empty'] = 'The service returned no text.';
$_lang['modxai1y.error.vendor'] = 'The Composer packages are missing (core/components/modxai1y/vendor).';
$_lang['modxai1y.error.image_invalid'] = 'The image path is invalid.';
$_lang['modxai1y.error.image_external'] = 'Only images of this website can be described.';
$_lang['modxai1y.error.image_missing'] = 'The image file was not found.';
$_lang['modxai1y.error.image_not_allowed'] = 'The image is not in an allowed folder (system setting modxai1y.allowed_paths).';
$_lang['modxai1y.error.image_type'] = 'Only JPEG, PNG, GIF and WebP images are supported.';
$_lang['modxai1y.error.image_big'] = 'The image is too big and could not be scaled down.';
