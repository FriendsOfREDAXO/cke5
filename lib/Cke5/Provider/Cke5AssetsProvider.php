<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Provider;


use Cke5\Creator\Cke5ProfilesCreator;
use rex_be_controller;
use rex_exception;
use rex_logger;
use rex_view;

class Cke5AssetsProvider
{
    /**
     * @author Joachim Doerr
     */
    public static function provideCke5BaseData()
    {
        // add cke5 vendors
        try {
            // add cke5 editor and translation
            rex_view::addCssFile(self::getAddon()->getAssetsUrl('cke5.css'));
            rex_view::addJsFile(self::getAddon()->getAssetsUrl('vendor/ckeditor5-classic/ckeditor.js'));
            rex_view::addJsFile(self::getAddon()->getAssetsUrl(Cke5ProfilesCreator::TRANSLATION_FILENAME));
            rex_view::addJsFile(self::getAddon()->getAssetsUrl(Cke5ProfilesCreator::PROFILES_FILENAME));
            rex_view::addJsFile(self::getAddon()->getAssetsUrl('cke5.js'));
        } catch (rex_exception $e) {
            rex_logger::logException($e);
        }
    }

    /**
     * @author Joachim Doerr
     */
    public static function provideCke5ProfileEditData()
    {
        if (rex_be_controller::getCurrentPagePart(2) == 'profiles' && rex_be_controller::getCurrentPagePart(1) == 'cke5') {
            // add js vendors
            self::addJS([
                'cke5InputTags' => 'vendor/cke5InputTags/cke5InputTags.js',
                'bootstrap-slider' => 'vendor/bootstrap-slider/bootstrap-slider.min.js',
                'bootstrap-toggle' => 'vendor/bootstrap-toggle/bootstrap-toggle.min.js',
                'jquery.alphanum' => 'vendor/alphanum/jquery.alphanum.js',
                'cke5profile_edit' => 'cke5profile_edit.js',
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
    }

    /**
     * @author Joachim Doerr
     */
    public static function provideCke5PreviewData()
    {
        if (rex_be_controller::getCurrentPagePart(3) == 'preview' && rex_be_controller::getCurrentPagePart(1) == 'cke5') {
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
     * @param array $js
     * @author Joachim Doerr
     */
    private static function addJS(array $js)
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
     * @param array $css
     * @author Joachim Doerr
     */
    private static function addCss(array $css)
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

    /**
     * @return \rex_addon
     * @author Joachim Doerr
     */
    private static function getAddon()
    {
        return \rex_addon::get('cke5');
    }
}