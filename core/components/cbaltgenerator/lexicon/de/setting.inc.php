<?php
/**
 * Systemeinstellungen Lexikon (de)
 *
 * @package cbaltgenerator
 * @subpackage lexicon
 */
$_lang['setting_cbaltgenerator.apikey'] = 'Anthropic API-Key';
$_lang['setting_cbaltgenerator.apikey_desc'] = 'Der Key bleibt auf dem Server und wird nie an den Browser gesendet. Einen Key gibt es unter console.anthropic.com. Leer: Umgebungsvariable ANTHROPIC_API_KEY.';
$_lang['setting_cbaltgenerator.model'] = 'Modell';
$_lang['setting_cbaltgenerator.model_desc'] = 'Claude-Modell, das die Bilder beschreibt. Standard: claude-haiku-5-5 (schnell und günstig, für kurze Alt-Texte ausreichend).';
$_lang['setting_cbaltgenerator.prompt'] = 'Prompt';
$_lang['setting_cbaltgenerator.prompt_desc'] = 'Anweisung für die Alt-Texte. Platzhalter: {max_length}, {language}. Leer: Standard-Prompt des Extras.';
$_lang['setting_cbaltgenerator.language'] = 'Sprache der Alt-Texte';
$_lang['setting_cbaltgenerator.language_desc'] = 'So geschrieben, wie sie im Prompt stehen soll, z. B. Deutsch oder English.';
$_lang['setting_cbaltgenerator.max_length'] = 'Maximale Länge';
$_lang['setting_cbaltgenerator.max_length_desc'] = 'Maximale Zeichenzahl eines Alt-Textes.';
$_lang['setting_cbaltgenerator.max_image_edge'] = 'Maximale Bildkante';
$_lang['setting_cbaltgenerator.max_image_edge_desc'] = 'Größere Bilder werden vor dem Senden auf diese Kantenlänge (Pixel) verkleinert. Spart Zeit und Kosten.';
$_lang['setting_cbaltgenerator.allowed_paths'] = 'Erlaubte Bildordner';
$_lang['setting_cbaltgenerator.allowed_paths_desc'] = 'Kommagetrennt, relativ zum Web-Root. Nur Bilder unterhalb dieser Ordner werden gelesen.';
$_lang['setting_cbaltgenerator.field_queries'] = 'Feld-Selektoren (JSON)';
$_lang['setting_cbaltgenerator.field_queries_desc'] = 'Selektoren der ContentBlocks-Felder, an denen der Button erscheint: [{"field": "…", "image": "…", "target": "…"}]. Leer: Bildfelder mit Titel und Galerie.';
$_lang['setting_cbaltgenerator.api_base_url'] = 'API-Basis-URL';
$_lang['setting_cbaltgenerator.api_base_url_desc'] = 'Nur für Proxys oder Tests. Leer: https://api.anthropic.com.';
$_lang['setting_cbaltgenerator.provider'] = 'Dienst';
$_lang['setting_cbaltgenerator.provider_desc'] = 'Welcher Dienst die Alt-Texte erzeugt: claude (Standard) oder inkludocs.';
$_lang['setting_cbaltgenerator.inkludocs_apikey'] = 'InkluDocs API-Key';
$_lang['setting_cbaltgenerator.inkludocs_apikey_desc'] = 'Nur für den Dienst inkludocs. Der Key bleibt auf dem Server und wird nie an den Browser gesendet. Anlegen in der InkluDocs-App unter Konto, Einstellungen, API-Schlüssel.';
$_lang['setting_cbaltgenerator.inkludocs_language'] = 'InkluDocs-Sprache';
$_lang['setting_cbaltgenerator.inkludocs_language_desc'] = 'Sprachcode der Alt-Texte: de, en, fr, es, da oder sv.';
$_lang['setting_cbaltgenerator.inkludocs_base_url'] = 'InkluDocs-Basis-URL';
$_lang['setting_cbaltgenerator.inkludocs_base_url_desc'] = 'Leer: https://inkludocs.de.';
