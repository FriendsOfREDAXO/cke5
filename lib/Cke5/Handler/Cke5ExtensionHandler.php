<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Handler;


use Cke5\Creator\Cke5ProfilesCreator;
use rex_be_controller;
use rex_extension_point;
use rex_view;

class Cke5ExtensionHandler
{
    /**
     * @param rex_extension_point $ep
     * @return string
     * @author Joachim Doerr
     */
    public static function addIcon(rex_extension_point $ep)
    {
        if (rex_be_controller::getCurrentPagePart(1) == 'cke5') {
            return '<i class="cke5-icon-logo"></i> ' . $ep->getSubject();
        }
    }

    /**
     * @param rex_extension_point $ep
     * @author Joachim Doerr
     */
    public static function hiddenMain(rex_extension_point $ep)
    {
        if (rex_be_controller::getCurrentPagePart(1) == 'cke5') {
            $subj = $ep->getSubject();
            if (array_key_exists('cke5', $subj)) {
                /** @var \rex_be_page $page */
                $page = $subj['cke5'];
                foreach ($page->getSubPages() as $subPage) {
                    if ($subPage->getKey() == 'main') {
                        foreach ($subPage->getSubpages() as $sPage) {
                            $sPage->setHidden(true);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param rex_extension_point $ep
     * @author Joachim Doerr
     */
    public static function removeDemoControlFields(rex_extension_point $ep)
    {
        if (rex_be_controller::getCurrentPagePart(3) == 'mblock_demo') {
            try {
                $ep->setSubject(array(
                    "save" => "",
                    "apply" => "",
                    "delete" => "",
                    "reset" => "",
                    "abort" => ""
                ));
            } catch (\rex_exception $e) {
                \rex_logger::logException($e);
            }
        }
    }

    /**
     * @param rex_extension_point $ep
     * @return void
     * @author Joachim Doerr
     */
    public static function createProfiles(rex_extension_point $ep)
    {
        try {
            if (rex_be_controller::getCurrentPagePart(2) == 'profiles' or $ep->getName() == 'CKE5_PROFILE_ADD') {
                Cke5ProfilesCreator::profilesCreate();
            } else if ($ep->getName() == 'CKE5_PROFILE_UPDATED') {
                Cke5ProfilesCreator::profilesCreate($ep->getParams());
            }
        } catch (\rex_functional_exception $e) {
            \rex_logger::logException($e);
            print rex_view::error($e->getMessage());
        }
    }

    /**
     * @author Joachim Doerr
     */
    public static function updateProfile() : void
    {
        try {
            Cke5ProfilesCreator::profilesCreate();
        } catch (\rex_functional_exception $e) {
            \rex_logger::logException($e);
            print rex_view::error($e->getMessage());
        }
    }
}