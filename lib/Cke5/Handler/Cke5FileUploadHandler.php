<?php

namespace Cke5\Handler;

use rex;
use rex_addon;
use rex_csrf_token;
use rex_file;
use rex_path;
use rex_request;
use rex_response;
use rex_url;

class Cke5FileUploadHandler
{
    /**
     * Handles the file upload process for CKEditor and translation files
     *
     * This function manages the upload process for both the CKEditor file
     * and translation files. Files are uploaded to the public assets directory.
     */
    /**
     * Handles the file upload process for CKEditor and translation files
     *
     * This function manages the upload process for both the CKEditor file
     * and translation files. Files are uploaded to the public assets directory.
     */
    public static function handleFileUpload(): void
    {
        // Verify request type
        if (rex_request::request('cke5_file_upload', 'bool') !== true) {
            return;
        }

        // Prüfen, ob es sich um eine Map-Datei handelt
        $isMapFile = rex_request::get('is_map_file', 'bool', false);

        // Verify CSRF token
        $csrfToken = rex_csrf_token::factory('cke5_upload');
        if (!$csrfToken->isValid()) {
            self::sendResponse(['success' => false, 'message' => 'CSRF token invalid']);
            exit;
        }

        $type = rex_request::post('type', 'string', '');
        $success = false;
        $message = '';
        $addon = rex_addon::get('cke5');

        // Handle editor upload
        if ($type === 'editor') {
            // Prüfen ob es ein einzelner Upload oder ein Multiupload ist
            if (isset($_FILES['file']) && !is_array($_FILES['file']['name'])) {
                // Einzelner Datei-Upload (bisheriges Verhalten)
                $file = rex_request::files('file');
                if (!empty($file) && $file['error'] === UPLOAD_ERR_OK) {
                    // Erlaube .js und .js.map Dateien
                    $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $isMap = false;

                    if ($fileExtension === 'js') {
                        // Standard JS-Datei
                        $isValidType = true;
                    } elseif ($fileExtension === 'map' || substr($file['name'], -7) === '.js.map') {
                        // JS-Map Datei (entweder mit Endung .map oder vollständig als .js.map)
                        $isValidType = true;
                        $isMap = true;
                    } else {
                        $isValidType = false;
                    }

                    if ($isValidType) {
                        // Beide Pfade - Addon-Quellordner und Frontend-Assets-Ordner
                        $assetsDir = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/');

                        // Stelle sicher dass das Verzeichnis existiert
                        if (!is_dir($assetsDir)) {
                            mkdir($assetsDir, 0775, true);
                        }

                        $assetsFilePath = $assetsDir . $file['name'];

                        // Spezielle Behandlung für Map-Dateien
                        if ($isMap || $isMapFile) {
                            // Stellen sicher, dass die Map-Datei erkannt wird
                            // und richtig verarbeitet wird, auch wenn die Endung nur .map ist

                            if (move_uploaded_file($file['tmp_name'], $assetsFilePath)) {
                                // Speichere als Map-Datei in der Konfiguration
                                $editorFiles = $addon->getConfig('editor_files', []);
                                if (!in_array($file['name'], $editorFiles)) {
                                    $editorFiles[] = $file['name'];
                                    $addon->setConfig('editor_files', $editorFiles);
                                }

                                $success = true;
                                $message = 'Map file uploaded successfully';
                            } else {
                                $message = 'Error moving uploaded map file';
                            }
                        } else {
                            // Normale JS-Datei-Verarbeitung
                            if (move_uploaded_file($file['tmp_name'], $assetsFilePath)) {
                                // Speichere Dateiname in Addon-Konfiguration
                                $addon->setConfig('license_cke5_js_path', 'assets/addons/cke5/vendor/ckeditor5-classic/' . $file['name']);
                                $addon->setConfig('editor_file', $file['name']);

                                $success = true;
                                $message = 'File uploaded and replaced successfully';
                            } else {
                                $message = 'Error moving uploaded file';
                            }
                        }
                    } else {
                        $message = 'Only JS and JS.MAP files are allowed for the editor';
                    }
                } else {
                    $message = 'Error uploading file';
                }
            } else {
                // Multiupload von Dateien
                $files = rex_request::files('file');

                if (!empty($files)) {
                    $assetsDir = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/');

                    // Stelle sicher dass das Verzeichnis existiert
                    if (!is_dir($assetsDir)) {
                        mkdir($assetsDir, 0775, true);
                    }

                    $success = true;
                    $uploadedFiles = [];
                    $mainJsFile = null;

                    // Wenn mehrere Dateien hochgeladen werden, verarbeite sie alle
                    for ($i = 0; $i < count($files['name']); $i++) {
                        if ($files['error'][$i] === UPLOAD_ERR_OK) {
                            $fileName = $files['name'][$i];
                            $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                            $isMap = false;

                            if ($fileExtension === 'js') {
                                $isValidType = true;
                                // Merke dir die erste .js-Datei als Hauptdatei
                                if ($mainJsFile === null) {
                                    $mainJsFile = $fileName;
                                }
                            } elseif ($fileExtension === 'map' || substr($fileName, -7) === '.js.map') {
                                $isValidType = true;
                                $isMap = true;
                            } else {
                                $isValidType = false;
                            }

                            if ($isValidType) {
                                $assetsFilePath = $assetsDir . $fileName;

                                if (move_uploaded_file($files['tmp_name'][$i], $assetsFilePath)) {
                                    $uploadedFiles[] = [
                                        'fileName' => $fileName,
                                        'isMap' => $isMap
                                    ];
                                } else {
                                    $success = false;
                                    $message = 'Error moving one or more uploaded files';
                                    break;
                                }
                            } else {
                                $success = false;
                                $message = 'Only JS and JS.MAP files are allowed for the editor';
                                break;
                            }
                        } else {
                            $success = false;
                            $message = 'Error uploading one or more files';
                            break;
                        }
                    }

                    if ($success) {
                        // Verarbeite die hochgeladenen Dateien
                        $editorFiles = $addon->getConfig('editor_files', []);

                        foreach ($uploadedFiles as $file) {
                            $fileName = $file['fileName'];
                            $isMap = $file['isMap'];

                            if (!$isMap && $fileName === $mainJsFile) {
                                // Hauptdatei für den Editor
                                $addon->setConfig('license_cke5_js_path', 'assets/addons/cke5/vendor/ckeditor5-classic/' . $fileName);
                                $addon->setConfig('editor_file', $fileName);
                            } else {
                                // Zusätzliche Dateien oder Map-Dateien
                                if (!in_array($fileName, $editorFiles)) {
                                    $editorFiles[] = $fileName;
                                }
                            }
                        }

                        // Speichere zusätzliche Dateien in der Konfiguration
                        $addon->setConfig('editor_files', $editorFiles);

                        $message = count($uploadedFiles) . ' files uploaded and replaced successfully';
                    }
                } else {
                    $message = 'No files received';
                }
            }
        }
        // Handle translations upload
        elseif ($type === 'translations') {
            $files = rex_request::files('files');

            if (!empty($files)) {
                $assetsDir = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/translations/');

                // Create directory if it doesn't exist
                if (!is_dir($assetsDir)) {
                    mkdir($assetsDir, 0775, true);
                }

                $success = true;
                $uploadedFiles = [];

                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        if (pathinfo($files['name'][$i], PATHINFO_EXTENSION) === 'js') {
                            $assetsFilePath = $assetsDir . $files['name'][$i];

                            // Stellen sicher dass die Datei tatsächlich in den public-Ordner kopiert wird
                            if (move_uploaded_file($files['tmp_name'][$i], $assetsFilePath)) {
                                $uploadedFiles[] = $files['name'][$i];
                            } else {
                                $success = false;
                                $message = 'Error moving one or more uploaded files';
                                break;
                            }
                        } else {
                            $success = false;
                            $message = 'Only JS files are allowed for translations';
                            break;
                        }
                    } else {
                        $success = false;
                        $message = 'Error uploading one or more files';
                        break;
                    }
                }

                if ($success) {
                    // Pfad in Addon-Konfiguration speichern
                    $addon->setConfig('license_translations_path', 'assets/addons/cke5/vendor/ckeditor5-classic/translations/');

                    // Speichere hochgeladene Dateien in Addon-Konfiguration
                    $existingFiles = $addon->getConfig('translation_files', []);
                    $newFiles = array_unique(array_merge($existingFiles, $uploadedFiles));
                    $addon->setConfig('translation_files', $newFiles);

                    $message = count($uploadedFiles) . ' files uploaded and replaced successfully';
                }
            } else {
                $message = 'No files received';
            }
        } else {
            $message = 'Invalid upload type';
        }

        // Nach erfolgreichem Upload in den assets-Ordner, kopiere in den data-Ordner
        if ($success) {
            $dataDir = rex_path::addonData('cke5', $type === 'editor' ? 'editor' : 'translations');

            // Stelle sicher, dass das Verzeichnis existiert
            if (!is_dir($dataDir)) {
                mkdir($dataDir, 0775, true);
            }

            // Kopiere die Datei(en) in den data-Ordner
            if ($type === 'editor') {
                $dataFilePath = $dataDir . '/' . $file['name'];
                rex_file::copy($assetsFilePath, $dataFilePath);
            } else if ($type === 'translations') {
                foreach ($uploadedFiles as $fileName) {
                    $assetFilePath = $assetsDir . $fileName;
                    $dataFilePath = $dataDir . '/' . $fileName;
                    rex_file::copy($assetFilePath, $dataFilePath);
                }
            }
        }

        self::sendResponse([
            'success' => $success,
            'message' => $message
        ]);
        exit;
    }

    /**
     * Handles the deletion of uploaded files
     *
     * This function deletes a file from the assets directory and then copies
     * the original file from the addon directory to restore the default.
     */
    public static function handleFileDelete(): void
    {
        // Verify request type
        if (rex_request::request('cke5_delete_file', 'bool') !== true) {
            return;
        }

        // Verify CSRF token
        $csrfToken = rex_csrf_token::factory('cke5_delete_file');
        if (!$csrfToken->isValid()) {
            self::sendResponse(['success' => false, 'message' => 'CSRF token invalid']);
            exit;
        }

        $type = rex_request::post('type', 'string', '');
        $filename = rex_request::post('filename', 'string', '');
        $success = false;
        $message = '';
        $addon = rex_addon::get('cke5');

        if (empty($filename) && $type !== 'all_translations' && $type !== 'all_editor') {
            self::sendResponse(['success' => false, 'message' => 'No filename provided']);
            exit;
        }

        if ($type === 'editor') {
            // Assets-Pfad für die hochgeladene Datei
            $assetsFilePath = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/' . $filename);
            // Original-Pfad im Addon
            $originalFilePath = $addon->getPath('assets/vendor/ckeditor5-classic/' . $filename);

            if (!file_exists($originalFilePath) && $filename !== 'ckeditor.js') {
                // Für zusätzliche Dateien, die nicht im Original sind
                $originalFilePath = $addon->getPath('assets/vendor/ckeditor5-classic/ckeditor.js');
                if (pathinfo($filename, PATHINFO_EXTENSION) === 'map') {
                    // Für .map Dateien verwenden wir die Originalmap, wenn vorhanden
                    $originalMapPath = $addon->getPath('assets/vendor/ckeditor5-classic/ckeditor.js.map');
                    if (file_exists($originalMapPath)) {
                        $originalFilePath = $originalMapPath;
                    }
                }
            }

            if (file_exists($assetsFilePath)) {
                if (unlink($assetsFilePath)) {
                    // Entferne aus der Konfiguration
                    if ($filename === $addon->getConfig('editor_file', '')) {
                        // Hauptdatei zurücksetzen
                        $addon->setConfig('license_cke5_js_path', '');
                        $addon->removeConfig('editor_file');
                    } else {
                        // Zusätzliche Datei aus der Liste entfernen
                        $editorFiles = $addon->getConfig('editor_files', []);
                        $editorFiles = array_values(array_filter($editorFiles, function($file) use ($filename) {
                            return $file !== $filename;
                        }));
                        $addon->setConfig('editor_files', $editorFiles);
                    }

                    // Kopiere die Original-Datei zurück in den Assets-Ordner
                    if (file_exists($originalFilePath)) {
                        if (rex_file::copy($originalFilePath, $assetsFilePath)) {
                            $success = true;
                            $message = 'File deleted and reset to default successfully';
                        } else {
                            $success = true;
                            $message = 'File deleted, but could not restore default file';
                        }
                    } else {
                        $success = true;
                        $message = 'File deleted, but original file not found';
                    }
                } else {
                    $message = 'Error deleting file from assets directory';
                }
            } else {
                // Datei existiert nicht, aber wir setzen trotzdem die Konfiguration zurück
                if ($filename === $addon->getConfig('editor_file', '')) {
                    $addon->setConfig('license_cke5_js_path', '');
                    $addon->removeConfig('editor_file');
                } else {
                    $editorFiles = $addon->getConfig('editor_files', []);
                    $editorFiles = array_values(array_filter($editorFiles, function($file) use ($filename) {
                        return $file !== $filename;
                    }));
                    $addon->setConfig('editor_files', $editorFiles);
                }

                $success = true;
                $message = 'File reference removed (file not found)';
            }
        } elseif ($type === 'all_editor') {
            // Lösche alle Editor-Dateien
            $assetsDir = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/');
            $success = true;

            // Lösche die Hauptdatei
            $editorFile = $addon->getConfig('editor_file', '');
            if (!empty($editorFile)) {
                $mainFilePath = $assetsDir . $editorFile;
                if (file_exists($mainFilePath) && !unlink($mainFilePath)) {
                    $success = false;
                    $message = 'Error deleting main editor file';
                }
            }

            // Lösche die zusätzlichen Dateien
            $editorFiles = $addon->getConfig('editor_files', []);
            foreach ($editorFiles as $file) {
                $filePath = $assetsDir . $file;
                if (file_exists($filePath) && !unlink($filePath)) {
                    $success = false;
                    $message = 'Error deleting some editor files';
                }
            }

            if ($success) {
                // Kopiere die Original-Dateien zurück
                $originalDir = $addon->getPath('assets/vendor/ckeditor5-classic/');

                // Kopiere die Hauptdatei zurück
                $originalMainFilePath = $originalDir . 'ckeditor.js';
                $assetMainFilePath = $assetsDir . 'ckeditor.js';
                if (file_exists($originalMainFilePath)) {
                    if (!rex_file::copy($originalMainFilePath, $assetMainFilePath)) {
                        $success = false;
                        $message = 'Error restoring main editor file';
                    }
                }

                // Kopiere die Map-Datei zurück, falls vorhanden
                $originalMapFilePath = $originalDir . 'ckeditor.js.map';
                $assetMapFilePath = $assetsDir . 'ckeditor.js.map';
                if (file_exists($originalMapFilePath)) {
                    rex_file::copy($originalMapFilePath, $assetMapFilePath);
                }

                // Setze die Konfiguration zurück
                $addon->setConfig('license_cke5_js_path', '');
                $addon->removeConfig('editor_file');
                $addon->setConfig('editor_files', []);

                $message = $success ? 'All editor files deleted and reset to default successfully' : $message;
            }
        } elseif ($type === 'translations') {
            // Assets-Pfad für die hochgeladene Übersetzungsdatei
            $assetsFilePath = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/translations/' . $filename);
            // Original-Pfad im Addon
            $originalFilePath = $addon->getPath('assets/vendor/ckeditor5-classic/translations/' . $filename);

            if (file_exists($assetsFilePath)) {
                if (unlink($assetsFilePath)) {
                    // Entferne Datei aus Addon-Konfiguration
                    $translationFiles = $addon->getConfig('translation_files', []);
                    $translationFiles = array_values(array_filter($translationFiles, function($file) use ($filename) {
                        return $file !== $filename;
                    }));
                    $addon->setConfig('translation_files', $translationFiles);

                    // Kopiere die Original-Datei zurück, falls vorhanden
                    if (file_exists($originalFilePath)) {
                        if (rex_file::copy($originalFilePath, $assetsFilePath)) {
                            $success = true;
                            $message = 'File deleted and reset to default successfully';
                        } else {
                            $success = true;
                            $message = 'File deleted, but could not restore default file';
                        }
                    } else {
                        $success = true;
                        $message = 'File deleted successfully';
                    }
                } else {
                    $message = 'Error deleting file from assets directory';
                }
            } else {
                // Datei existiert nicht, aber wir entfernen den Eintrag aus der Konfiguration
                $translationFiles = $addon->getConfig('translation_files', []);
                $translationFiles = array_values(array_filter($translationFiles, function($file) use ($filename) {
                    return $file !== $filename;
                }));
                $addon->setConfig('translation_files', $translationFiles);

                $success = true;
                $message = 'File reference removed (file not found)';
            }
        } elseif ($type === 'all_translations') {
            // Lösche alle Übersetzungsdateien
            $translationsDir = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/translations/');
            $success = true;

            if (is_dir($translationsDir)) {
                $translationFiles = $addon->getConfig('translation_files', []);

                // Nur die hochgeladenen Dateien löschen
                foreach ($translationFiles as $file) {
                    $filePath = $translationsDir . $file;
                    if (file_exists($filePath)) {
                        if (!unlink($filePath)) {
                            $success = false;
                            $message = 'Error deleting some files';
                        }
                    }
                }

                if ($success) {
                    // Kopiere alle Original-Dateien zurück
                    $originalDir = $addon->getPath('assets/vendor/ckeditor5-classic/translations/');
                    if (is_dir($originalDir)) {
                        $originalFiles = glob($originalDir . '*.js');
                        if ($originalFiles) {
                            foreach ($originalFiles as $file) {
                                $filename = basename($file);
                                // Stelle sicher, dass die Original-Datei in den public-Ordner kopiert wird
                                if (!rex_file::copy($file, $translationsDir . $filename)) {
                                    $success = false;
                                    $message = 'Error restoring some default files';
                                    break;
                                }
                            }
                        }
                    }

                    // Lösche alle Einträge aus der Konfiguration
                    $addon->setConfig('translation_files', []);
                    $message = $success ? 'All translation files deleted and reset to default successfully' : $message;
                }
            } else {
                $message = 'Translations directory not found';
            }
        } else {
            $message = 'Invalid file type';
        }

        // Nach erfolgreicher Löschung aus dem assets-Ordner, lösche aus dem data-Ordner
        if ($success) {
            $dataDir = rex_path::addonData('cke5', $type === 'editor' || $type === 'all_editor' ? 'editor' : 'translations');

            if ($type === 'editor') {
                $dataFilePath = $dataDir . '/' . $filename;
                if (file_exists($dataFilePath)) {
                    unlink($dataFilePath);
                }
            } else if ($type === 'all_editor') {
                // Lösche alle Editor-Dateien aus dem data-Ordner
                $editorFile = $addon->getConfig('editor_file', '');
                $editorFiles = $addon->getConfig('editor_files', []);

                if (!empty($editorFile)) {
                    $dataFilePath = $dataDir . '/' . $editorFile;
                    if (file_exists($dataFilePath)) {
                        unlink($dataFilePath);
                    }
                }

                foreach ($editorFiles as $file) {
                    $dataFilePath = $dataDir . '/' . $file;
                    if (file_exists($dataFilePath)) {
                        unlink($dataFilePath);
                    }
                }
            } else if ($type === 'translations') {
                $dataFilePath = $dataDir . '/' . $filename;
                if (file_exists($dataFilePath)) {
                    unlink($dataFilePath);
                }
            } else if ($type === 'all_translations') {
                // Lösche alle Übersetzungsdateien aus dem data-Ordner
                $translationFiles = $addon->getConfig('translation_files', []);

                foreach ($translationFiles as $file) {
                    $dataFilePath = $dataDir . '/' . $file;
                    if (file_exists($dataFilePath)) {
                        unlink($dataFilePath);
                    }
                }
            }
        }

        self::sendResponse([
            'success' => $success,
            'message' => $message
        ]);
        exit;
    }

    /**
     * Checks if a file exists in the specified location
     *
     * This function is used to check if a file exists before uploading
     * to provide a warning if it will be overwritten.
     */
    public static function checkFileExists(): void
    {
        // Verify request type
        if (rex_request::request('cke5_check_file', 'bool') !== true) {
            return;
        }

        // Verify CSRF token
        $csrfToken = rex_csrf_token::factory('cke5_check_file');
        if (!$csrfToken->isValid()) {
            self::sendResponse(['success' => false, 'message' => 'CSRF token invalid']);
            exit;
        }

        $type = rex_request::post('type', 'string', '');
        $filename = rex_request::post('filename', 'string', '');
        $exists = false;

        if ($type === 'editor') {
            $filePath = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/' . $filename);
            $exists = file_exists($filePath);
        } elseif ($type === 'translations') {
            $filePath = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/translations/' . $filename);
            $exists = file_exists($filePath);
        }

        self::sendResponse([
            'success' => true,
            'exists' => $exists
        ]);
        exit;
    }

    /**
     * Checks if multiple files exist in the specified location
     *
     * This function is used to check if multiple translation files exist
     * before uploading to provide a warning if they will be overwritten.
     */
    public static function checkFilesExist(): void
    {
        // Verify request type
        if (rex_request::request('cke5_check_files', 'bool') !== true) {
            return;
        }

        // Verify CSRF token
        $csrfToken = rex_csrf_token::factory('cke5_check_files');
        if (!$csrfToken->isValid()) {
            self::sendResponse(['success' => false, 'message' => 'CSRF token invalid']);
            exit;
        }

        $type = rex_request::post('type', 'string', '');
        $filenames = rex_request::post('filenames', 'array', []);
        $existingFiles = [];

        if ($type === 'translations') {
            $baseDir = rex_path::addonAssets('cke5', 'vendor/ckeditor5-classic/translations/');

            foreach ($filenames as $filename) {
                if (file_exists($baseDir . $filename)) {
                    $existingFiles[] = $filename;
                }
            }
        }

        self::sendResponse([
            'success' => true,
            'existing_files' => $existingFiles
        ]);
        exit;
    }

    /**
     * Gets the list of translation files from the configuration
     *
     * This function gets the list of uploaded translation files from the addon configuration
     * and returns it to the client.
     */
    public static function getTranslationFiles(): void
    {
        // Verify request type
        if (rex_request::request('cke5_get_translation_files', 'bool') !== true) {
            return;
        }

        // Verify CSRF token
        $csrfToken = rex_csrf_token::factory('cke5_translation_files');
        if (!$csrfToken->isValid()) {
            self::sendResponse(['success' => false, 'message' => 'CSRF token invalid']);
            exit;
        }

        $addon = rex_addon::get('cke5');
        $files = $addon->getConfig('translation_files', []);

        self::sendResponse([
            'success' => true,
            'files' => $files
        ]);
        exit;
    }

    /**
     * Gets the list of editor files from the configuration
     *
     * This function gets the list of all uploaded editor files from the addon configuration
     * and returns it to the client.
     */
    public static function getEditorFiles(): void
    {
        // Verify request type
        if (rex_request::request('cke5_get_editor_files', 'bool') !== true) {
            return;
        }

        // Verify CSRF token
        $csrfToken = rex_csrf_token::factory('cke5_editor_files');
        if (!$csrfToken->isValid()) {
            self::sendResponse(['success' => false, 'message' => 'CSRF token invalid']);
            exit;
        }

        $addon = rex_addon::get('cke5');
        $mainFile = $addon->getConfig('editor_file', '');
        $additionalFiles = $addon->getConfig('editor_files', []);

        $files = [];
        if (!empty($mainFile)) {
            $files[] = $mainFile;
        }
        $files = array_unique(array_merge($files, $additionalFiles));

        self::sendResponse([
            'success' => true,
            'files' => $files
        ]);
        exit;
    }

    /**
     * Sends a JSON response
     *
     * Helper function to send a consistent JSON response format
     */
    private static function sendResponse(array $data): void
    {
        rex_response::cleanOutputBuffers();
        rex_response::setHeader('Content-Type', 'application/json');
        echo json_encode($data);
    }
}