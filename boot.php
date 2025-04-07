<?php
/**
 * @author FriendsOfRedaxo: Joachim Doerr https://github.com/joachimdoerr
 * @package redaxo5
 * @license MIT
 */

/** @var rex_addon $this */

// register permissions
use Cke5\Handler\Cke5ExtensionHandler;
use Cke5\Handler\Cke5UploadHandler;
use Cke5\Provider\Cke5AssetsProvider;

if (rex::isBackend() && is_object(rex::getUser())) {
    rex_perm::register('cke5_addon[]');

    // remove productivity components if no licence provided
    $page = $this->getProperty('page');
    if (empty($this->getConfig('license_code'))) {
        unset($page['subpages']['profiles']['subpages']['templates']);
    }
    $this->setProperty('page', $page);


    // load assets
    Cke5AssetsProvider::provideCke5ProfileEditData();
    Cke5AssetsProvider::provideCke5PreviewData();
    Cke5AssetsProvider::provideCke5BaseData();
    Cke5AssetsProvider::provideCke5CustomData();

    // Check REDAXO version
    if (rex_version::compare(rex::getVersion(), '5.13.0-dev', '>=')) {
        rex_view::addCssFile($this->getAssetsUrl('cke5_dark.css'));
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
        rex_view::setJsProperty('cke5darkcss', rex_url::addonAssets('cke5') . 'dark.css');
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

    // register extension point actions
    if (rex_be_controller::getCurrentPagePart(1) === 'cke5') {
        rex_extension::register('PAGE_TITLE', ['\Cke5\Handler\Cke5ExtensionHandler', 'addIcon'], rex_extension::EARLY);
        rex_extension::register('PAGES_PREPARED', ['\Cke5\Handler\Cke5ExtensionHandler', 'hiddenMain'], rex_extension::EARLY);
        rex_extension::register(['REX_FORM_SAVED', 'REX_FORM_DELETED', 'CKE5_PROFILE_CLONE', 'CKE5_PROFILE_DELETE', 'CKE5_PROFILE_ADD', 'CKE5_PROFILE_UPDATED'], ['\Cke5\Handler\Cke5ExtensionHandler', 'createProfiles']);
    }
}
