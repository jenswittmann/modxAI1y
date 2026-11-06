<?php
/**
 * Settings Lexicon Entries (en)
 *
 * @package cbaltgenerator
 * @subpackage lexicon
 */
$_lang['setting_cbaltgenerator.apikey'] = 'Anthropic API key';
$_lang['setting_cbaltgenerator.apikey_desc'] = 'The key stays on the server and is never sent to the browser. Create one at console.anthropic.com. Empty: the environment variable ANTHROPIC_API_KEY is used.';
$_lang['setting_cbaltgenerator.model'] = 'Model';
$_lang['setting_cbaltgenerator.model_desc'] = 'Claude model that describes the images. Default: claude-haiku-5-5 (fast and cheap, enough for short alt texts).';
$_lang['setting_cbaltgenerator.prompt'] = 'Prompt';
$_lang['setting_cbaltgenerator.prompt_desc'] = 'Instruction for the alt texts. Placeholders: {max_length}, {language}. Empty: the default prompt of the extra.';
$_lang['setting_cbaltgenerator.language'] = 'Language of the alt texts';
$_lang['setting_cbaltgenerator.language_desc'] = 'Written as it should appear in the prompt, e.g. Deutsch or English.';
$_lang['setting_cbaltgenerator.max_length'] = 'Maximum length';
$_lang['setting_cbaltgenerator.max_length_desc'] = 'Maximum number of characters of an alt text. Claude is asked for it, texts of other services are shortened to whole sentences.';
$_lang['setting_cbaltgenerator.max_image_edge'] = 'Maximum image edge';
$_lang['setting_cbaltgenerator.max_image_edge_desc'] = 'Bigger images are scaled down to this edge length (pixels) before they are sent. Saves time and costs.';
$_lang['setting_cbaltgenerator.allowed_paths'] = 'Allowed image folders';
$_lang['setting_cbaltgenerator.allowed_paths_desc'] = 'Comma separated, relative to the web root. Only images below these folders are read.';
$_lang['setting_cbaltgenerator.field_queries'] = 'Field query JSON';
$_lang['setting_cbaltgenerator.field_queries_desc'] = 'Selectors of the ContentBlocks fields where the button is added: [{"field": "…", "image": "…", "target": "…"}]. Empty: the image fields with title and the gallery.';
$_lang['setting_cbaltgenerator.api_base_url'] = 'API base URL';
$_lang['setting_cbaltgenerator.api_base_url_desc'] = 'Only for proxies or tests. Empty: https://api.anthropic.com.';
$_lang['setting_cbaltgenerator.provider'] = 'Service';
$_lang['setting_cbaltgenerator.provider_desc'] = 'Which service creates the alt texts: claude (default) or inkludocs.';
$_lang['setting_cbaltgenerator.inkludocs_apikey'] = 'InkluDocs API key';
$_lang['setting_cbaltgenerator.inkludocs_apikey_desc'] = 'Only for the service inkludocs. The key stays on the server and is never sent to the browser. Create it in the InkluDocs app under Account, Settings, API keys.';
$_lang['setting_cbaltgenerator.inkludocs_language'] = 'InkluDocs language';
$_lang['setting_cbaltgenerator.inkludocs_language_desc'] = 'Language code of the alt texts: de, en, fr, es, da or sv.';
$_lang['setting_cbaltgenerator.inkludocs_base_url'] = 'InkluDocs base URL';
$_lang['setting_cbaltgenerator.inkludocs_base_url_desc'] = 'Empty: https://inkludocs.de.';
$_lang['setting_cbaltgenerator.use_context'] = 'Send page title';
$_lang['setting_cbaltgenerator.use_context_desc'] = 'Claude gets the title of the page as a hint (not to be repeated in the text). Default: yes.';
$_lang['setting_cbaltgenerator.inkludocs_use_context'] = 'InkluDocs: send page title';
$_lang['setting_cbaltgenerator.inkludocs_use_context_desc'] = 'InkluDocs lets the context shape the text a lot (the title ends up in the alt text), so it is off by default. Set to yes to send the page title as context.';
