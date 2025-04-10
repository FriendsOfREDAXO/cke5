<?php

namespace Cke5\Handler;

use rex_addon;
use rex_file;
use rex_path;

class Cke5FileRestoreHandler
{
    public static function restoreEditorFiles(rex_addon $addon): void
    {
        $editorDataDir = rex_path::addonData('cke5', 'editor');
        
        // Editor-Dateien wiederherstellen
        if (is_dir($editorDataDir)) {
            $editorFiles = glob($editorDataDir . '/*');
            $editorMainFile = null;
            $additionalEditorFiles = [];

            foreach ($editorFiles as $file) {
                $fileName = basename($file);
                $targetFile = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/' . $fileName);

                // Stelle sicher, dass das Zielverzeichnis existiert
                $targetDir = dirname($targetFile);
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0775, true);
                }

                // Kopiere die Datei in den assets-Ordner
                rex_file::copy($file, $targetFile);

                // Identifiziere die Hauptdatei und zusätzliche Dateien
                if (pathinfo($fileName, PATHINFO_EXTENSION) === 'js' &&
                    !substr($fileName, -3) === '.js.map') {
                    // Erste .js-Datei als Hauptdatei verwenden
                    if ($editorMainFile === null) {
                        $editorMainFile = $fileName;
                    } else {
                        $additionalEditorFiles[] = $fileName;
                    }
                } else {
                    $additionalEditorFiles[] = $fileName;
                }
            }

            // Speichere die Dateien in der Konfiguration
            if ($editorMainFile !== null) {
                $addon->setConfig('license_cke5_js_path', 'assets/addons/cke5/vendor/ckeditor5-classic/' . $editorMainFile);
                $addon->setConfig('editor_file', $editorMainFile);
            }

            if (!empty($additionalEditorFiles)) {
                $addon->setConfig('editor_files', $additionalEditorFiles);
            }
        }
    }

    public static function restoreTranslationFiles(rex_addon $addon)
    {
        // Dateien aus dem data-Ordner wiederherstellen
        $translationsDataDir = rex_path::addonData('cke5', 'translations');

        // Übersetzungsdateien wiederherstellen
        if (is_dir($translationsDataDir)) {
            $translationFiles = glob($translationsDataDir . '/*.js');
            $translations = [];

            foreach ($translationFiles as $file) {
                $fileName = basename($file);
                $targetDir = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/translations/');

                // Stelle sicher, dass das Zielverzeichnis existiert
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0775, true);
                }

                $targetFile = $targetDir . $fileName;

                // Kopiere die Datei in den assets-Ordner
                rex_file::copy($file, $targetFile);

                $translations[] = $fileName;
            }

            // Speichere die Übersetzungsdateien in der Konfiguration
            if (!empty($translations)) {
                $addon->setConfig('license_translations_path', 'assets/addons/cke5/vendor/ckeditor5-classic/translations/');
                $addon->setConfig('translation_files', $translations);
            }
        }
    }
}