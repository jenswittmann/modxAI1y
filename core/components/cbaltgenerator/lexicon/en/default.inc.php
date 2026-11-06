<?php
/**
 * ContentBlocks-Image-ALT-Text-Generator lexicon (en)
 */
$_lang['cbaltgenerator'] = 'ContentBlocks-Image-ALT-Text-Generator';
$_lang['cbaltgenerator.button'] = 'Create alt text';
$_lang['cbaltgenerator.button_title'] = 'Let Claude describe the image as an alt text';
$_lang['cbaltgenerator.busy'] = 'Creating alt text …';
$_lang['cbaltgenerator.done'] = 'Alt text created. Please check it.';
$_lang['cbaltgenerator.overwrite'] = 'The field already has a text. Replace it?';
$_lang['cbaltgenerator.default_prompt'] = 'You write alt texts for images on a website. Describe factually and briefly what the image shows and what matters for understanding the page. Rules: at most {max_length} characters, one sentence, no introduction like "Image of" or "Photo of", no judgements, no line breaks, no quotation marks. Language: {language}. Answer with the alt text only.';

$_lang['cbaltgenerator.error.generic'] = 'The alt text could not be created.';
$_lang['cbaltgenerator.error.no_image'] = 'Please choose an image first.';
$_lang['cbaltgenerator.error.api_key_missing'] = 'No API key. Please enter it in the system setting [[+setting]].';
$_lang['cbaltgenerator.error.api_key_invalid'] = 'The API key was rejected. Please check the system setting [[+setting]].';
$_lang['cbaltgenerator.error.busy'] = 'The service is busy right now. Please try again in a moment.';
$_lang['cbaltgenerator.error.connection'] = 'The connection to the service failed. Please try again.';
$_lang['cbaltgenerator.error.api'] = 'The service answered with an error ([[+status]]). Details are in the MODX error log.';
$_lang['cbaltgenerator.error.credits'] = 'The credits of the service are used up.';
$_lang['cbaltgenerator.error.provider'] = 'Unknown service "[[+provider]]". Please check the system setting cbaltgenerator.provider (claude or inkludocs).';
$_lang['cbaltgenerator.error.refusal'] = 'The model did not describe this image.';
$_lang['cbaltgenerator.error.empty'] = 'The service returned no text.';
$_lang['cbaltgenerator.error.vendor'] = 'The Composer packages are missing (core/components/cbaltgenerator/vendor).';
$_lang['cbaltgenerator.error.image_invalid'] = 'The image path is invalid.';
$_lang['cbaltgenerator.error.image_external'] = 'Only images of this website can be described.';
$_lang['cbaltgenerator.error.image_missing'] = 'The image file was not found.';
$_lang['cbaltgenerator.error.image_not_allowed'] = 'The image is not in an allowed folder (system setting cbaltgenerator.allowed_paths).';
$_lang['cbaltgenerator.error.image_type'] = 'Only JPEG, PNG, GIF and WebP images are supported.';
$_lang['cbaltgenerator.error.image_big'] = 'The image is too big and could not be scaled down.';
