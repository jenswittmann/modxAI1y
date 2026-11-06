<?php
/**
 * ContentBlocks-Image-ALT-Text-Generator Lexikon (de)
 */
$_lang['cbaltgenerator'] = 'ContentBlocks-Image-ALT-Text-Generator';
$_lang['cbaltgenerator.button'] = 'Alt-Text erzeugen';
$_lang['cbaltgenerator.button_title'] = 'Claude beschreibt das Bild als Alt-Text';
$_lang['cbaltgenerator.busy'] = 'Alt-Text wird erzeugt …';
$_lang['cbaltgenerator.done'] = 'Alt-Text erzeugt. Bitte prüfen.';
$_lang['cbaltgenerator.overwrite'] = 'Das Feld enthält schon einen Text. Ersetzen?';
$_lang['cbaltgenerator.default_prompt'] = 'Du schreibst Alt-Texte für Bilder auf einer Website. Beschreibe sachlich und knapp, was auf dem Bild zu sehen ist und was für das Verständnis der Seite wichtig ist. Regeln: höchstens {max_length} Zeichen, ein Satz, keine Einleitung wie „Bild von“ oder „Foto von“, keine Wertungen, keine Zeilenumbrüche, keine Anführungszeichen. Sprache: {language}. Antworte ausschließlich mit dem Alt-Text.';

$_lang['cbaltgenerator.error.generic'] = 'Der Alt-Text konnte nicht erzeugt werden.';
$_lang['cbaltgenerator.error.no_image'] = 'Bitte zuerst ein Bild auswählen.';
$_lang['cbaltgenerator.error.api_key_missing'] = 'Kein API-Key. Bitte in der Systemeinstellung [[+setting]] eintragen.';
$_lang['cbaltgenerator.error.api_key_invalid'] = 'Der API-Key wurde abgelehnt. Bitte die Systemeinstellung [[+setting]] prüfen.';
$_lang['cbaltgenerator.error.busy'] = 'Der Dienst ist gerade ausgelastet. Bitte gleich noch einmal versuchen.';
$_lang['cbaltgenerator.error.connection'] = 'Die Verbindung zum Dienst ist fehlgeschlagen. Bitte noch einmal versuchen.';
$_lang['cbaltgenerator.error.api'] = 'Der Dienst hat mit einem Fehler geantwortet ([[+status]]). Details stehen im MODX-Fehlerprotokoll.';
$_lang['cbaltgenerator.error.credits'] = 'Das Guthaben des Dienstes ist aufgebraucht.';
$_lang['cbaltgenerator.error.provider'] = 'Unbekannter Dienst „[[+provider]]“. Bitte die Systemeinstellung cbaltgenerator.provider prüfen (claude oder inkludocs).';
$_lang['cbaltgenerator.error.refusal'] = 'Das Modell hat dieses Bild nicht beschrieben.';
$_lang['cbaltgenerator.error.empty'] = 'Der Dienst hat keinen Text geliefert.';
$_lang['cbaltgenerator.error.vendor'] = 'Die Composer-Pakete fehlen (core/components/cbaltgenerator/vendor).';
$_lang['cbaltgenerator.error.image_invalid'] = 'Der Bildpfad ist ungültig.';
$_lang['cbaltgenerator.error.image_external'] = 'Es können nur Bilder dieser Website beschrieben werden.';
$_lang['cbaltgenerator.error.image_missing'] = 'Die Bilddatei wurde nicht gefunden.';
$_lang['cbaltgenerator.error.image_not_allowed'] = 'Das Bild liegt nicht in einem erlaubten Ordner (Systemeinstellung cbaltgenerator.allowed_paths).';
$_lang['cbaltgenerator.error.image_type'] = 'Es werden nur JPEG-, PNG-, GIF- und WebP-Bilder unterstützt.';
$_lang['cbaltgenerator.error.image_big'] = 'Das Bild ist zu groß und konnte nicht verkleinert werden.';
