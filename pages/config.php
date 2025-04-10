<?php
/** @var rex_addon $this */

// Instanzieren des Formulars
$form = rex_config_form::factory('cke5');

// fieldset
$form->addFieldset($this->i18n('cke5_config_header'));

// Informationstext zur Dateiverwaltung hinzufügen
$file_management_info = '<p><strong>' . $this->i18n('cke5_files_management_title') . '</strong></p>
    <p>' . $this->i18n('cke5_config_info') . '</p>
    <ul>
        <li>' . $this->i18n('cke5_files_upload_info') . '</li>
        <li>' . $this->i18n('cke5_files_delete_info') . '</li>
    </ul>';

// info area
$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', '', false);
$fragment->setVar('body', $file_management_info, false);
$info = $fragment->parse('core/page/section.php');

// Editor-Datei aus Konfiguration holen
$editorFile = $this->getConfig('editor_file', '');
$showEditorFile = !empty($editorFile) ? $editorFile : 'ckeditor.js';
$hasCustomEditor = !empty($editorFile);

// CKEditor-Dateien Bereich
$form->addRawField('
<dl class="rex-form-group form-group">
    <dt><label class="control-label " for="ckeditor-5-konfiguration-license-code">' . $this->i18n('cke5_editor_upload_title') . '</label></dt>
    <dd>
        <div class="cke5-uplaod-wrapper">
            <div class="cke5-upload-area" id="ckeditor-upload-area">
                <div class="cke5-upload-dropzone">
                    <i class="fa fa-upload"></i>
                    <p>' . $this->i18n('cke5_editor_upload_dropzone') . '</p>
                    <input type="file" id="ckeditor-file-upload" accept=".js,.js.map" style="display: none;">
                </div>
                <div class="btn-group">
                    <button class="btn btn-primary" id="ckeditor-upload-btn">' . $this->i18n('cke5_editor_upload_button') . '</button>
                    <button class="btn btn-danger" id="delete-all-editor-btn">' . $this->i18n('cke5_delete_all_editor') . '</button>
                </div>
                <div class="cke5-upload-status" id="ckeditor-upload-status"' . (!$hasCustomEditor ? ' style="display: none;"' : '') . '>
                    <p>' . $this->i18n('cke5_editor_current_files') . ':</p>
                    <div class="files-list" id="editor-files-list">
                        <div class="no-files">' . $this->i18n('cke5_no_files_uploaded') . '</div>
                    </div>
                </div>
            </div>
        </div>
    </dd>
</dl>

', false);
$editor_upload_area = $fragment->parse('core/page/section.php');

// Übersetzungsdateien Bereich
$form->addRawField('
<dl class="rex-form-group form-group">
    <dt><label class="control-label " for="ckeditor-5-konfiguration-license-code">' . $this->i18n('cke5_translations_upload_title') . '</label></dt>
    <dd>
        <div class="cke5-uplaod-wrapper">
            <div class="cke5-upload-area" id="translations-upload-area">
                <div class="cke5-upload-dropzone">
                    <i class="fa fa-upload"></i>
                    <p>' . $this->i18n('cke5_translations_upload_dropzone') . '</p>
                    <input type="file" id="translations-file-upload" accept=".js" multiple style="display: none;">
                </div>
                <div class="btn-group">
                    <button class="btn btn-primary" id="translations-upload-btn">' . $this->i18n('cke5_translations_upload_button') . '</button>
                    <button class="btn btn-danger" id="delete-all-translations-btn">' . $this->i18n('cke5_delete_all_translations') . '</button>
                </div>
                <div class="cke5-upload-status" id="translations-upload-status">
                    <p>' . $this->i18n('cke5_translations_uploaded_files') . ':</p>
                    <div class="files-list" id="translations-files-list">
                        <div class="no-files">' . $this->i18n('cke5_no_files_uploaded') . '</div>
                    </div>
                </div>
            </div>
        </div>
    </dd>
</dl>
', false);
$translations_upload_area = $fragment->parse('core/page/section.php');

// cke5 license code
$field = $form->addInputField('text', 'license_code', null, ['class' => 'form-control', 'placeholder' => $this->i18n('license_code_placeholder')]);
$field->setLabel($this->i18n('license_code'));
$field->setNotice($this->i18n('license_code_info'));

// Ausgabe des Formulars
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('config_title'), false);
$fragment->setVar('body', $info . $form->get(), false);
echo $fragment->parse('core/page/section.php');

// JS für Drag & Drop und Upload
?>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function () {
        // CKEditor Upload
        const editorDropzone = document.querySelector('#ckeditor-upload-area .cke5-upload-dropzone');
        const editorFileInput = document.querySelector('#ckeditor-file-upload');
        const editorUploadBtn = document.querySelector('#ckeditor-upload-btn');
        const editorStatusArea = document.querySelector('#ckeditor-upload-status');
        const editorFilesList = document.querySelector('#editor-files-list');
        const deleteAllEditorBtn = document.querySelector('#delete-all-editor-btn');

// Translations Upload
        const translationsDropzone = document.querySelector('#translations-upload-area .cke5-upload-dropzone');
        const translationsFileInput = document.querySelector('#translations-file-upload');
        const translationsUploadBtn = document.querySelector('#translations-upload-btn');
        const translationsStatusArea = document.querySelector('#translations-upload-status');
        const translationsFilesList = document.querySelector('#translations-files-list');
        const deleteAllTranslationsBtn = document.querySelector('#delete-all-translations-btn');

        // Beim Laden die Übersetzungsdateien und Editor-Dateien anzeigen
        loadTranslationFiles();
        loadEditorFiles();

        // Löschen einer Editor-Datei durch Event-Delegation
        editorFilesList.addEventListener('click', function (e) {
            const target = e.target;

            // Prüfen, ob auf den Lösch-Button oder das Mülleimer-Icon geklickt wurde
            if (target.classList.contains('fa-trash') || target.classList.contains('btn-delete') || target.closest('.btn-delete')) {
                e.preventDefault();
                e.stopPropagation();

                // Den Button-Container finden
                const deleteBtn = target.classList.contains('btn-delete') ?
                    target : (target.classList.contains('fa-trash') ?
                        target.closest('.btn-delete') : target.closest('.btn-delete'));

                if (deleteBtn) {
                    const filename = deleteBtn.getAttribute('data-filename');

                    if (filename && confirm('<?= $this->i18n('cke5_confirm_delete_file') ?>')) {
                        console.log('Deleting editor file:', filename); // Debug-Information
                        deleteFile('editor', filename);
                    }
                }
            }
        });

        // Löschen einer Übersetzungsdatei durch Event-Delegation
        translationsFilesList.addEventListener('click', function (e) {
            const target = e.target;

            // Prüfen, ob auf den Lösch-Button oder das Mülleimer-Icon geklickt wurde
            if (target.classList.contains('fa-trash') || target.classList.contains('btn-delete') || target.closest('.btn-delete')) {
                e.preventDefault();
                e.stopPropagation();

                // Den Button-Container finden
                const deleteBtn = target.classList.contains('btn-delete') ?
                    target : (target.classList.contains('fa-trash') ?
                        target.closest('.btn-delete') : target.closest('.btn-delete'));

                if (deleteBtn) {
                    const filename = deleteBtn.getAttribute('data-filename');

                    if (filename && confirm('<?= $this->i18n('cke5_confirm_delete_file') ?>')) {
                        console.log('Deleting translation file:', filename); // Debug-Information
                        deleteFile('translations', filename);
                    }
                }
            }
        });

        // Alle Editor-Dateien löschen
        if (deleteAllEditorBtn) {
            deleteAllEditorBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                if (confirm('<?= $this->i18n('cke5_confirm_delete_all_editor') ?>')) {
                    deleteAllEditorFiles();
                }
            });
        }

        // Alle Übersetzungsdateien löschen
        if (deleteAllTranslationsBtn) {
            deleteAllTranslationsBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                if (confirm('<?= $this->i18n('cke5_confirm_delete_all_translations') ?>')) {
                    deleteAllTranslationFiles();
                }
            });
        }

        // CKEditor Upload Events
        editorDropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('active');
        });

        editorDropzone.addEventListener('dragleave', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('active');
        });

        editorDropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('active');

            if (e.dataTransfer.files.length) {
                const file = e.dataTransfer.files[0];
                if (file.name.endsWith('.js') || file.name.endsWith('.js.map')) {
                    uploadEditorFile(file);
                } else {
                    alert('<?= $this->i18n('cke5_upload_js_only') ?>');
                }
            }
        });

        editorDropzone.addEventListener('click', function () {
            editorFileInput.click();
        });

        editorFileInput.addEventListener('change', function () {
            if (this.files.length) {
                uploadEditorFile(this.files[0]);
            }
        });

        editorUploadBtn.addEventListener('click', function () {
            editorFileInput.click();
        });

        // Translations Upload Events
        translationsDropzone.addEventListener('dragover', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.add('active');
        });

        translationsDropzone.addEventListener('dragleave', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('active');
        });

        translationsDropzone.addEventListener('drop', function (e) {
            e.preventDefault();
            e.stopPropagation();
            this.classList.remove('active');

            if (e.dataTransfer.files.length) {
                const files = Array.from(e.dataTransfer.files);
                const jsFiles = files.filter(file => file.name.endsWith('.js'));

                if (jsFiles.length) {
                    uploadTranslationFiles(jsFiles);
                } else {
                    alert('<?= $this->i18n('cke5_upload_js_only') ?>');
                }
            }
        });

        translationsDropzone.addEventListener('click', function () {
            translationsFileInput.click();
        });

        translationsFileInput.addEventListener('change', function () {
            if (this.files.length) {
                uploadTranslationFiles(Array.from(this.files));
            }
        });

        translationsUploadBtn.addEventListener('click', function () {
            translationsFileInput.click();
        });

        // Upload Functions
        function uploadEditorFile(file) {
            const formData = new FormData();
            formData.append('file', file);
            formData.append('type', 'editor');
            formData.append('_csrf_token', '<?= rex_csrf_token::factory('cke5_upload')->getValue() ?>');

            // Hier prüfen wir explizit, ob es sich um eine .js.map Datei handelt
            const isMapFile = file.name.endsWith('.js.map');

            // Check if file already exists
            checkFileExists('editor', file.name, function (exists) {
                if (exists) {
                    if (confirm('<?= $this->i18n('cke5_file_already_exists') ?>')) {
                        processEditorUpload(formData, file, isMapFile);
                    }
                } else {
                    processEditorUpload(formData, file, isMapFile);
                }
            });
        }

        function uploadTranslationFiles(files) {
            const formData = new FormData();
            files.forEach(file => {
                formData.append('files[]', file);
            });
            formData.append('type', 'translations');
            formData.append('_csrf_token', '<?= rex_csrf_token::factory('cke5_upload')->getValue() ?>');

            // Check for existing files
            checkFilesExist('translations', files.map(f => f.name), function (existingFiles) {
                if (existingFiles.length > 0) {
                    if (confirm('<?= $this->i18n('cke5_files_already_exist') ?> ' + existingFiles.join(', '))) {
                        processTranslationsUpload(formData, files);
                    }
                } else {
                    processTranslationsUpload(formData, files);
                }
            });
        }

        function processEditorUpload(formData, file, isMapFile) {
            $.ajax({
                url: 'index.php?page=cke5/config&cke5_file_upload=1' + (isMapFile ? '&is_map_file=1' : ''),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            // Aktualisiere die Liste der hochgeladenen Dateien
                            loadEditorFiles();

                            // Stelle sicher, dass der Status-Bereich sichtbar ist
                            editorStatusArea.style.display = 'block';

                            showNotification('success', '<?= $this->i18n('cke5_upload_success') ?>');
                        } else {
                            showNotification('error', data.message || '<?= $this->i18n('cke5_upload_error') ?>');
                        }
                    } catch (e) {
                        console.error(e);
                        showNotification('error', '<?= $this->i18n('cke5_upload_error') ?>');
                    }
                },
                error: function () {
                    showNotification('error', '<?= $this->i18n('cke5_upload_error') ?>');
                }
            });
        }

        function processTranslationsUpload(formData, files) {
            $.ajax({
                url: 'index.php?page=cke5/config&cke5_file_upload=1',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            // Aktualisiere die Liste der hochgeladenen Dateien
                            loadTranslationFiles();

                            showNotification('success', '<?= $this->i18n('cke5_upload_success') ?>');
                        } else {
                            showNotification('error', data.message || '<?= $this->i18n('cke5_upload_error') ?>');
                        }
                    } catch (e) {
                        console.error(e);
                        showNotification('error', '<?= $this->i18n('cke5_upload_error') ?>');
                    }
                },
                error: function () {
                    showNotification('error', '<?= $this->i18n('cke5_upload_error') ?>');
                }
            });
        }

        /**
         * Löscht eine Datei vom Server
         *
         * Beim Editor wird die ckeditor.js im öffentlichen Assets-Ordner gelöscht und somit
         * die Standarddatei aus dem Addon-Ordner wiederhergestellt.
         *
         * Bei Übersetzungsdateien wird die Datei aus dem öffentlichen Assets-Ordner gelöscht.
         */
        function deleteFile(type, filename) {
            console.log('Delete request:', type, filename); // Debug-Information

            $.ajax({
                url: 'index.php?page=cke5/config&cke5_delete_file=1',
                type: 'POST',
                data: {
                    type: type,
                    filename: filename,
                    _csrf_token: '<?= rex_csrf_token::factory('cke5_delete_file')->getValue() ?>'
                },
                success: function (response) {
                    console.log('Delete response:', response); // Debug-Information

                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            if (type === 'editor') {
                                // Aktualisiere die Liste der Editor-Dateien
                                loadEditorFiles();
                            } else if (type === 'translations') {
                                // Aktualisiere die Liste der Übersetzungsdateien
                                loadTranslationFiles();
                            }

                            showNotification('success', '<?= $this->i18n('cke5_delete_success') ?>');
                        } else {
                            showNotification('error', data.message || '<?= $this->i18n('cke5_delete_error') ?>');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e, response);
                        showNotification('error', '<?= $this->i18n('cke5_delete_error') ?>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', status, error, xhr.responseText);
                    showNotification('error', '<?= $this->i18n('cke5_delete_error') ?>');
                }
            });
        }

        /**
         * Löscht alle Editor-Dateien auf einmal
         */
        function deleteAllEditorFiles() {
            $.ajax({
                url: 'index.php?page=cke5/config&cke5_delete_file=1',
                type: 'POST',
                data: {
                    type: 'all_editor',
                    _csrf_token: '<?= rex_csrf_token::factory('cke5_delete_file')->getValue() ?>'
                },
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            // Aktualisiere die Liste der Editor-Dateien
                            loadEditorFiles();

                            showNotification('success', '<?= $this->i18n('cke5_delete_all_success') ?>');
                        } else {
                            showNotification('error', data.message || '<?= $this->i18n('cke5_delete_error') ?>');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e, response);
                        showNotification('error', '<?= $this->i18n('cke5_delete_error') ?>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', status, error, xhr.responseText);
                    showNotification('error', '<?= $this->i18n('cke5_delete_error') ?>');
                }
            });
        }

        /**
         * Löscht alle Übersetzungsdateien auf einmal
         */
        function deleteAllTranslationFiles() {
            $.ajax({
                url: 'index.php?page=cke5/config&cke5_delete_file=1',
                type: 'POST',
                data: {
                    type: 'all_translations',
                    _csrf_token: '<?= rex_csrf_token::factory('cke5_delete_file')->getValue() ?>'
                },
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success) {
                            // Aktualisiere die Liste der Übersetzungsdateien
                            loadTranslationFiles();

                            showNotification('success', '<?= $this->i18n('cke5_delete_all_success') ?>');
                        } else {
                            showNotification('error', data.message || '<?= $this->i18n('cke5_delete_error') ?>');
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e, response);
                        showNotification('error', '<?= $this->i18n('cke5_delete_error') ?>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', status, error, xhr.responseText);
                    showNotification('error', '<?= $this->i18n('cke5_delete_error') ?>');
                }
            });
        }

        /**
         * Lädt die Liste der vorhandenen Editor-Dateien
         *
         * Diese Funktion ruft die aktuellen Dateien vom Server ab und aktualisiert die Liste
         * in der Benutzeroberfläche.
         */
        function loadEditorFiles() {
            // Füge eine visuelle Rückmeldung hinzu, dass etwas passiert
            editorFilesList.innerHTML = '<div class="no-files"><?= $this->i18n('cke5_loading_files') ?></div>';

            // Cache-Busting durch Hinzufügen eines Zeitstempels zur URL
            const timestamp = new Date().getTime();

            $.ajax({
                url: 'index.php?page=cke5/config&cke5_get_editor_files=1&_=' + timestamp,
                type: 'POST',
                data: {
                    _csrf_token: '<?= rex_csrf_token::factory('cke5_editor_files')->getValue() ?>'
                },
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success && data.files && data.files.length > 0) {
                            // Aktualisiere die Liste der Dateien
                            editorFilesList.innerHTML = '';
                            data.files.forEach(file => {
                                const fileItem = document.createElement('div');
                                fileItem.className = 'file-item';
                                fileItem.innerHTML = `
                                    <span class="filename">${file}</span>
                                    <button class="btn btn-delete" data-filename="${file}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                `;
                                editorFilesList.appendChild(fileItem);
                            });

                            // Stelle sicher, dass der Status-Bereich sichtbar ist
                            editorStatusArea.style.display = 'block';
                        } else {
                            editorFilesList.innerHTML = '<div class="no-files"><?= $this->i18n('cke5_no_files_uploaded') ?></div>';
                            // Wenn keine Dateien vorhanden sind, verstecke den Status-Bereich
                            editorStatusArea.style.display = 'none';
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e, response);
                        editorFilesList.innerHTML = '<div class="no-files"><?= $this->i18n('cke5_editor_files_error') ?></div>';
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', status, error, xhr.responseText);
                    editorFilesList.innerHTML = '<div class="no-files"><?= $this->i18n('cke5_editor_files_error') ?></div>';
                }
            });
        }

        /**
         * Lädt die Liste der vorhandenen Übersetzungsdateien
         *
         * Diese Funktion ruft die aktuellen Dateien vom Server ab und aktualisiert die Liste
         * in der Benutzeroberfläche.
         */
        function loadTranslationFiles() {
            // Füge eine visuelle Rückmeldung hinzu, dass etwas passiert
            translationsFilesList.innerHTML = '<div class="no-files"><?= $this->i18n('cke5_loading_files') ?></div>';

            // Cache-Busting durch Hinzufügen eines Zeitstempels zur URL
            const timestamp = new Date().getTime();

            $.ajax({
                url: 'index.php?page=cke5/config&cke5_get_translation_files=1&_=' + timestamp,
                type: 'POST',
                data: {
                    _csrf_token: '<?= rex_csrf_token::factory('cke5_translation_files')->getValue() ?>'
                },
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        if (data.success && data.files && data.files.length > 0) {
                            // Aktualisiere die Liste der Dateien
                            translationsFilesList.innerHTML = '';
                            data.files.forEach(file => {
                                const fileItem = document.createElement('div');
                                fileItem.className = 'file-item';
                                fileItem.innerHTML = `
                                    <span class="filename">${file}</span>
                                    <button class="btn btn-delete" data-filename="${file}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                `;
                                translationsFilesList.appendChild(fileItem);
                            });
                        } else {
                            translationsFilesList.innerHTML = '<div class="no-files"><?= $this->i18n('cke5_no_files_uploaded') ?></div>';
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e, response);
                        translationsFilesList.innerHTML = '<div class="no-files"><?= $this->i18n('cke5_translation_files_error') ?></div>';
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', status, error, xhr.responseText);
                    translationsFilesList.innerHTML = '<div class="no-files"><?= $this->i18n('cke5_translation_files_error') ?></div>';
                }
            });
        }

        function checkFileExists(type, filename, callback) {
            $.ajax({
                url: 'index.php?page=cke5/config&cke5_check_file=1',
                type: 'POST',
                data: {
                    type: type,
                    filename: filename,
                    _csrf_token: '<?= rex_csrf_token::factory('cke5_check_file')->getValue() ?>'
                },
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        callback(data.exists);
                    } catch (e) {
                        callback(false);
                    }
                },
                error: function () {
                    callback(false);
                }
            });
        }

        function checkFilesExist(type, filenames, callback) {
            $.ajax({
                url: 'index.php?page=cke5/config&cke5_check_files=1',
                type: 'POST',
                data: {
                    type: type,
                    filenames: filenames,
                    _csrf_token: '<?= rex_csrf_token::factory('cke5_check_files')->getValue() ?>'
                },
                success: function (response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;
                        callback(data.existing_files);
                    } catch (e) {
                        callback([]);
                    }
                },
                error: function () {
                    callback([]);
                }
            });
        }

        function showNotification(type, message) {
            if (typeof rex !== 'undefined') {
                if (typeof rex.notification !== 'undefined') {
                    // REDAXO 5.10+
                    rex.notification(type, message);
                } else if (type === 'success' && typeof rex.info !== 'undefined') {
                    // Ältere REDAXO-Versionen
                    rex.info(message);
                } else if (type === 'error' && typeof rex.error !== 'undefined') {
                    // Ältere REDAXO-Versionen
                    rex.error(message);
                } else {
                    // Statt Fallback mit Alert, füge eine Message in den DOM ein
                    createDomNotification(type, message);
                }
            } else {
                // Wenn rex nicht verfügbar ist, DOM Benachrichtigung erstellen
                createDomNotification(type, message);
            }
        }

        /**
         * Erstellt eine Benachrichtigung im DOM im REDAXO-Stil
         * @param {string} type - Art der Benachrichtigung ('success', 'error', etc.)
         * @param {string} message - Anzuzeigender Text
         */
        function createDomNotification(type, message) {
            // CSS-Klasse basierend auf dem Typ bestimmen
            let alertClass = 'alert-info';
            if (type === 'success') {
                alertClass = 'alert-success';
            } else if (type === 'error') {
                alertClass = 'alert-danger';
            }

            // Neues Alert-Element erstellen
            const alertElement = document.createElement('div');
            alertElement.className = `alert ${alertClass}`;
            alertElement.setAttribute('role', 'alert');
            alertElement.innerHTML = message;

            // Schließen-Button hinzufügen
            const closeButton = document.createElement('button');
            closeButton.className = 'close';
            closeButton.setAttribute('type', 'button');
            closeButton.setAttribute('data-dismiss', 'alert');
            closeButton.setAttribute('aria-label', 'Close');
            closeButton.innerHTML = '<span aria-hidden="true">&times;</span>';
            alertElement.appendChild(closeButton);

            // Benachrichtigungscontainer suchen oder erstellen
            let container = document.querySelector('.rex-message-container');
            if (!container) {
                // Falls kein Container existiert, einen erstellen
                container = document.createElement('div');
                container.className = 'rex-message-container';

                // Versuchen, den panel-body über dem Formular zu finden
                const panelBody = document.querySelector('.panel-body');
                const form = document.querySelector('form');

                if (panelBody && form && panelBody.contains(form)) {
                    // Container vor dem Formular im panel-body einfügen
                    panelBody.insertBefore(container, form);
                } else {
                    // Alternative Platzierungen, falls panel-body nicht gefunden wird
                    // Zuerst nach der typischen REDAXO-Struktur suchen
                    const pageSection = document.querySelector('.rex-page-section');
                    if (pageSection) {
                        const content = pageSection.querySelector('.panel-body');
                        if (content) {
                            // Am Anfang des panel-body einfügen
                            content.insertBefore(container, content.firstChild);
                        } else {
                            // Als erstes Kind des page-section einfügen
                            pageSection.insertBefore(container, pageSection.firstChild);
                        }
                    } else {
                        // Weitere Fallbacks
                        const content = document.querySelector('.rex-main-frame') || document.querySelector('#rex-page-main');
                        if (content) {
                            content.insertBefore(container, content.firstChild);
                        } else {
                            // Absoluter Fallback: In body einfügen
                            document.body.insertBefore(container, document.body.firstChild);
                        }
                    }
                }
            }

            // Benachrichtigung zum Container hinzufügen
            container.appendChild(alertElement);

            // Optional: Automatisches Ausblenden nach 5 Sekunden für Erfolgs-Nachrichten
            if (type === 'success') {
                setTimeout(() => {
                    alertElement.classList.add('fade-out');
                    setTimeout(() => {
                        if (alertElement.parentNode) {
                            alertElement.parentNode.removeChild(alertElement);
                        }
                    }, 500);
                }, 5000);
            }

            // CSS für Fade-Out-Animation hinzufügen, falls noch nicht vorhanden
            if (!document.getElementById('notification-styles')) {
                const style = document.createElement('style');
                style.id = 'notification-styles';
                style.textContent = `
            .fade-out {
                opacity: 0;
                transition: opacity 0.5s;
            }
            .rex-message-container {
                margin-bottom: 20px;
            }
        `;
                document.head.appendChild(style);
            }
        }
    });
</script>
