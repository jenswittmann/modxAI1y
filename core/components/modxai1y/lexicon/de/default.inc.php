<?php
/**
 * modxAI1y Lexikon (de)
 */
$_lang['modxai1y'] = 'modxAI1y';
$_lang['modxai1y.button'] = 'Alt-Text erzeugen';
$_lang['modxai1y.button_title'] = 'Claude beschreibt das Bild als Alt-Text';
$_lang['modxai1y.busy'] = 'Alt-Text wird erzeugt …';
$_lang['modxai1y.done'] = 'Alt-Text erzeugt. Bitte prüfen.';
$_lang['modxai1y.overwrite'] = 'Das Feld enthält schon einen Text. Ersetzen?';
$_lang['modxai1y.default_prompt'] = 'Du schreibst Alt-Texte für Bilder auf einer Website. Beschreibe sachlich und knapp, was auf dem Bild zu sehen ist und was für das Verständnis der Seite wichtig ist. Regeln: höchstens {max_length} Zeichen, ein Satz, keine Einleitung wie „Bild von“ oder „Foto von“, keine Wertungen, keine Zeilenumbrüche, keine Anführungszeichen. Sprache: {language}. Antworte ausschließlich mit dem Alt-Text.';

$_lang['modxai1y.error.generic'] = 'Der Alt-Text konnte nicht erzeugt werden.';
$_lang['modxai1y.error.no_image'] = 'Bitte zuerst ein Bild auswählen.';
$_lang['modxai1y.error.api_key_missing'] = 'Kein API-Key. Bitte in der Systemeinstellung modxai1y.apikey eintragen.';
$_lang['modxai1y.error.api_key_invalid'] = 'Der API-Key wurde abgelehnt. Bitte die Systemeinstellung modxai1y.apikey prüfen.';
$_lang['modxai1y.error.busy'] = 'Der Dienst ist gerade ausgelastet. Bitte gleich noch einmal versuchen.';
$_lang['modxai1y.error.connection'] = 'Die Verbindung zum Dienst ist fehlgeschlagen. Bitte noch einmal versuchen.';
$_lang['modxai1y.error.api'] = 'Der Dienst hat mit einem Fehler geantwortet ([[+status]]). Details stehen im MODX-Fehlerprotokoll.';
$_lang['modxai1y.error.refusal'] = 'Das Modell hat dieses Bild nicht beschrieben.';
$_lang['modxai1y.error.empty'] = 'Der Dienst hat keinen Text geliefert.';
$_lang['modxai1y.error.vendor'] = 'Die Composer-Pakete fehlen (core/components/modxai1y/vendor).';
$_lang['modxai1y.error.image_invalid'] = 'Der Bildpfad ist ungültig.';
$_lang['modxai1y.error.image_external'] = 'Es können nur Bilder dieser Website beschrieben werden.';
$_lang['modxai1y.error.image_missing'] = 'Die Bilddatei wurde nicht gefunden.';
$_lang['modxai1y.error.image_not_allowed'] = 'Das Bild liegt nicht in einem erlaubten Ordner (Systemeinstellung modxai1y.allowed_paths).';
$_lang['modxai1y.error.image_type'] = 'Es werden nur JPEG-, PNG-, GIF- und WebP-Bilder unterstützt.';
$_lang['modxai1y.error.image_big'] = 'Das Bild ist zu groß und konnte nicht verkleinert werden.';
