<?php
/**
 * Systemeinstellungen Lexikon (de)
 *
 * @package modxai1y
 * @subpackage lexicon
 */
$_lang['setting_modxai1y.apikey'] = 'Anthropic API-Key';
$_lang['setting_modxai1y.apikey_desc'] = 'Der Key bleibt auf dem Server und wird nie an den Browser gesendet. Einen Key gibt es unter console.anthropic.com. Leer: Umgebungsvariable ANTHROPIC_API_KEY.';
$_lang['setting_modxai1y.model'] = 'Modell';
$_lang['setting_modxai1y.model_desc'] = 'Claude-Modell, das die Bilder beschreibt. Standard: claude-haiku-5-5 (schnell und günstig, für kurze Alt-Texte ausreichend).';
$_lang['setting_modxai1y.prompt'] = 'Prompt';
$_lang['setting_modxai1y.prompt_desc'] = 'Anweisung für die Alt-Texte. Platzhalter: {max_length}, {language}. Leer: Standard-Prompt des Extras.';
$_lang['setting_modxai1y.language'] = 'Sprache der Alt-Texte';
$_lang['setting_modxai1y.language_desc'] = 'So geschrieben, wie sie im Prompt stehen soll, z. B. Deutsch oder English.';
$_lang['setting_modxai1y.max_length'] = 'Maximale Länge';
$_lang['setting_modxai1y.max_length_desc'] = 'Maximale Zeichenzahl eines Alt-Textes.';
$_lang['setting_modxai1y.max_image_edge'] = 'Maximale Bildkante';
$_lang['setting_modxai1y.max_image_edge_desc'] = 'Größere Bilder werden vor dem Senden auf diese Kantenlänge (Pixel) verkleinert. Spart Zeit und Kosten.';
$_lang['setting_modxai1y.allowed_paths'] = 'Erlaubte Bildordner';
$_lang['setting_modxai1y.allowed_paths_desc'] = 'Kommagetrennt, relativ zum Web-Root. Nur Bilder unterhalb dieser Ordner werden gelesen.';
$_lang['setting_modxai1y.field_queries'] = 'Feld-Selektoren (JSON)';
$_lang['setting_modxai1y.field_queries_desc'] = 'Selektoren der ContentBlocks-Felder, an denen der Button erscheint: [{"field": "…", "image": "…", "target": "…"}]. Leer: Bildfelder mit Titel und Galerie.';
$_lang['setting_modxai1y.api_base_url'] = 'API-Basis-URL';
$_lang['setting_modxai1y.api_base_url_desc'] = 'Nur für Proxys oder Tests. Leer: https://api.anthropic.com.';
