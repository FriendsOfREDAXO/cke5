<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

/** @var rex_addon $this */

// register permissions
if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('cke5_addon[]');
}

// add assets to backend
if (rex::isBackend() && rex::getUser()) {
    // load assets
    \Cke5\Provider\Cke5AssetsProvider::provideCke5ProfileEditData();
    \Cke5\Provider\Cke5AssetsProvider::provideCke5BaseData();

    // upload image
    if (rex_request::request('cke5upload') == 1) {
        \Cke5\Handler\Cke5UploadHandler::uploadCke5Img();
    }

    // register extension point actions
    if (rex_be_controller::getCurrentPagePart(1) == 'cke5') {
        rex_extension::register('PAGE_TITLE', ['\Cke5\Handler\Cke5ExtensionHandler', 'addIcon'], rex_extension::EARLY);
        rex_extension::register('PAGES_PREPARED', ['\Cke5\Handler\Cke5ExtensionHandler', 'hiddenMain'], rex_extension::EARLY);
        rex_extension::register('REX_FORM_CONTROL_FIELDS', ['\Cke5\Handler\Cke5ExtensionHandler', 'removeDemoControlFields'], rex_extension::LATE);
        rex_extension::register(['REX_FORM_SAVED', 'REX_FORM_DELETED', 'CKE5_PROFILE_CLONE', 'CKE5_PROFILE_DELETE', 'CKE5_PROFILE_ADD'], ['\Cke5\Handler\Cke5ExtensionHandler', 'createProfiles']);
    }
}
