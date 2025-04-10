<?php
/** @var rex_addon $this */

// register permissions
use Cke5\Handler\Cke5ExtensionHandler;
use Cke5\Handler\Cke5FileRestoreHandler;
use Cke5\Handler\Cke5FileUploadHandler;
use Cke5\Handler\Cke5UploadHandler;
use Cke5\Provider\Cke5AssetsProvider;

if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('cke5_addon[]');

    // restore config editor files
    if ($this->getConfig('restore_files', false) === true) {
        Cke5FileRestoreHandler::restoreEditorFiles($this);
        $this->removeConfig('restore_files');
    }

    // load assets
    Cke5AssetsProvider::provideCke5ProfileEditData();
    Cke5AssetsProvider::provideCke5PreviewData();
    Cke5AssetsProvider::provideCke5BaseData();
    Cke5AssetsProvider::provideCke5CustomData();

    // Check REDAXO version
    if (rex_version::compare(rex::getVersion(), '5.13.0-dev', '>=')) {
        rex_view::addCssFile($this->getAssetsUrl('css/cke5_dark.css'));
        $user = rex::requireUser();

        // get user settings for theme
        $themeType = $user->getValue('theme');
        $theme = (!is_null($themeType) && $themeType !== '') ? $themeType : 'auto';

        if (rex::getProperty('theme') === 'light') {
            $theme = 'light';
        }
        if (rex::getProperty('theme') === 'dark') {
            $theme = 'dark';
        }

        // set theme properties
        rex_view::setJsProperty('cke5theme', $theme);
        rex_view::setJsProperty('cke5darkcss', rex_url::addonAssets('cke5') . 'css/dark.css');
    } else {
        rex_view::setJsProperty('cke5theme', 'notheme');
    }

    if ($this->getConfig('updated') === true) {
        Cke5ExtensionHandler::updateOrCreateProfiles();
        $this->setConfig('updated', false);
    }

    // upload image
    if (rex_request::request('cke5upload', 'bool') === true) {
        Cke5UploadHandler::uploadCke5Img();
    }

    // handle file uploads and file operations for CKEditor and translations
    Cke5FileUploadHandler::handleFileUpload();
    Cke5FileUploadHandler::checkFileExists();
    Cke5FileUploadHandler::checkFilesExist();
    Cke5FileUploadHandler::getTranslationFiles();
    Cke5FileUploadHandler::handleFileDelete();
    Cke5FileUploadHandler::getEditorFiles();

    // register extension point actions
    if (rex_be_controller::getCurrentPagePart(1) === 'cke5') {
        rex_extension::register('PAGE_TITLE', ['\Cke5\Handler\Cke5ExtensionHandler', 'addIcon'], rex_extension::EARLY);
        rex_extension::register('PAGES_PREPARED', ['\Cke5\Handler\Cke5ExtensionHandler', 'hiddenMain'], rex_extension::EARLY);
        rex_extension::register(['REX_FORM_SAVED', 'REX_FORM_DELETED', 'CKE5_PROFILE_CLONE', 'CKE5_PROFILE_DELETE', 'CKE5_PROFILE_ADD', 'CKE5_PROFILE_UPDATED'], ['\Cke5\Handler\Cke5ExtensionHandler', 'createProfiles']);

        rex_extension::register('REX_FORM_SAVED', function(rex_extension_point $ep) {
            $params = $ep->getParams();
            $savedId = $params['id'] ?? null;
            $tableName = $params['table'] ?? '';

            // Prüfen, ob es sich um eine der relevanten Tabellen handelt
            if ($tableName === rex::getTable('cke5_styles') ||
                $tableName === rex::getTable('cke5_style_groups') ||
                $tableName === rex::getTable('cke5_templates')) {

                Cke5\Utils\Cke5CssHandler::regenerateCssFile();
            }
        });
    }
}