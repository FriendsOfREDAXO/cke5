<?php

namespace Cke5\Provider;

use Cke5\Creator\Cke5ProfilesCreator;
use Cke5\Utils\Cke5CssHandler;
use Cke5\Utils\Cke5Lang;
use rex;
use rex_addon;
use rex_addon_interface;
use rex_be_controller;
use rex_exception;
use rex_logger;
use rex_path;
use rex_sql;
use rex_url;
use rex_view;

class Cke5AssetsProvider
{
    public static function provideCke5CustomData(): void
    {
        $files = [
            'addons/cke5_custom_data/custom-style.css',
            'addons/cke5_custom_data/autogenerated-custom-style.css'
        ];

        foreach ($files as $file) {
            if (file_exists(rex_path::base($file))) {
                try {
                    rex_view::addCssFile(rex_url::assets($file));
                } catch (rex_exception $e) {
                    rex_logger::logException($e);
                }
            }
        }

        // Lade externe CSS-Dateien
        $externalFiles = Cke5CssHandler::getExternalCssFiles();
        foreach ($externalFiles as $file) {
            try {
                rex_view::addCssFile($file);
            } catch (rex_exception $e) {
                rex_logger::logException($e);
            }
        }
    }

    public static function provideCke5BaseData(): void
    {
        try {
            $addon = self::getAddon();

            // Bestimme den Editor-Pfad
            $editorPath = $addon->getConfig('license_cke5_js_path');
            $editorFile = rex_url::base((($editorPath != '') ? $editorPath : 'assets/addons/cke5/vendor/ckeditor5-classic/ckeditor.js'));

            // Bestimme den Übersetzungspfad
            $translationsPath = $addon->getConfig('license_translations_path', 'assets/addons/cke5/vendor/ckeditor5-classic/translations/');
            $translationsUrl = rex_url::base(($translationsPath != '') ? $translationsPath : 'assets/addons/cke5/vendor/ckeditor5-classic/translations/');

            // add cke5 editor and translation
            rex_view::addCssFile(self::getAddon()->getAssetsUrl('css/cke5.css'));
            rex_view::addJsFile($editorFile);

            $sql = rex_sql::factory();
            $result = $sql->getArray('select lang as ui, lang_content as content from ' . rex::getTable('cke5_profiles') . ' where lang != \'\' or lang_content != \'\'');

            $langKit = [];

            if (count($result) > 0) {
                foreach ($result as $lang) {
                    if (isset($lang['ui']) && $lang['ui'] !== '' && !in_array(self::getLang($lang['ui']), $langKit, true)) {
                        rex_view::addJsFile($translationsUrl . self::getLang($lang['ui']) . '.js');
                        $langKit[] = self::getLang($lang['ui']);
                    }
                    if (isset($lang['content']) && $lang['content'] !== '' && !in_array(self::getLang($lang['content']), $langKit, true)) {
                        rex_view::addJsFile($translationsUrl . self::getLang($lang['content']) . '.js');
                        $langKit[] = self::getLang($lang['content']);
                    }
                }
            }

            if (!in_array(self::getLang(Cke5Lang::getUserLang()), $langKit, true)) {
                rex_view::addJsFile($translationsUrl . self::getLang(Cke5Lang::getUserLang()) . '.js');
                $langKit[] = self::getLang(Cke5Lang::getUserLang());
            }
            if (!in_array(self::getLang(Cke5Lang::getOutputLang()), $langKit, true)) {
                rex_view::addJsFile($translationsUrl . self::getLang(Cke5Lang::getOutputLang()) . '.js');
                $langKit[] = self::getLang(Cke5Lang::getOutputLang());
            }

            rex_view::addJsFile(self::getAddon()->getAssetsUrl(Cke5ProfilesCreator::PROFILES_FILENAME));
            rex_view::addJsFile(self::getAddon()->getAssetsUrl('cke5.js'));
        } catch (rex_exception $e) {
            rex_logger::logException($e);
        }
    }

    public static function getLang(string $lang): string
    {
        return ($lang === 'en') ? 'en-gb' : $lang;
    }

    public static function provideCke5ProfileEditData(): void
    {
        // profile edit
        if (rex_be_controller::getCurrentPagePart(2) === 'profiles' && rex_be_controller::getCurrentPagePart(1) === 'cke5') {
            // add js vendors
            self::addJS([
                'cke5InputTags' => 'vendor/cke5InputTags/cke5InputTags.js',
                'bootstrap-slider' => 'vendor/bootstrap-slider/bootstrap-slider.min.js',
                'bootstrap-toggle' => 'vendor/bootstrap-toggle/bootstrap-toggle.min.js',
                'jquery.alphanum' => 'vendor/alphanum/jquery.alphanum.js',
                'cke5profile_edit' => 'js/cke5profile_edit.js',
                'jq.multiinput' => 'vendor/multiinput/dist/js/jq.multiinput.min.js',
                'colpick' => 'vendor/colpick/js/colpick.js',
            ]);
            // add css vendors
            self::addCss([
                'cke5InputTags' => 'vendor/cke5InputTags/cke5InputTags.css',
                'bootstrap-slider' => 'vendor/bootstrap-slider/bootstrap-slider.min.css',
                'bootstrap-toggle' => 'vendor/bootstrap-toggle/bootstrap-toggle.min.css',
                'jq.multiinput' => 'vendor/multiinput/dist/css/jq.multiinput.min.css',
                'colpick' => 'vendor/colpick/css/colpick.css',
            ]);
        }
        // style edit
        if (rex_be_controller::getCurrentPagePart(3) === 'customise' && rex_be_controller::getCurrentPagePart(1) === 'cke5') {
            // add js vendors
            self::addJS([
                'cke5style_edit' => 'js/cke5style_edit.js',
            ]);
        }
    }

    public static function provideCke5PreviewData(): void
    {
        if (rex_be_controller::getCurrentPagePart(3) === 'preview' && rex_be_controller::getCurrentPagePart(1) === 'cke5') {
            // add js vendors
            self::addJS([
                'rainbowjson' => 'vendor/rainbow-json/rainbowjson.js',
            ]);
            // add css vendors
            self::addCss([
                'rainbowjson' => 'vendor/rainbow-json/rainbowjson.css',
            ]);
        }
    }

    /**
     * @param array<string,string> $js
     */
    private static function addJS(array $js): void
    {
        foreach ($js as $name => $fullPathFile) {
            $add = true;
            foreach (rex_view::getJsFiles() as $jsFile) {
                if (strpos($jsFile, $name) !== false) {
                    $add = false;
                }
            }
            if ($add) {
                try {
                    rex_view::addJsFile(self::getAddon()->getAssetsUrl($fullPathFile));
                } catch (rex_exception $e) {
                    rex_logger::logException($e);
                }
            }
        }
    }

    /**
     * @param array<string,string> $css
     */
    private static function addCss(array $css): void
    {
        foreach ($css as $name => $fullPathFile) {
            $add = true;
            if (isset(rex_view::getCssFiles()['all'])) {
                foreach (rex_view::getCssFiles()['all'] as $cssFile) {
                    if (strpos($cssFile, $name) !== false) {
                        $add = false;
                    }
                }
            }
            if ($add) {
                try {
                    rex_view::addCssFile(self::getAddon()->getAssetsUrl($fullPathFile));
                } catch (rex_exception $e) {
                    rex_logger::logException($e);
                }
            }
        }
    }

    private static function getAddon(): rex_addon_interface
    {
        return rex_addon::get('cke5');
    }
}