<?php

namespace Cke5\Handler;


use Cke5\Creator\Cke5ProfilesCreator;
use rex_be_controller;
use rex_be_page;
use rex_extension_point;
use rex_functional_exception;
use rex_logger;
use rex_view;

class Cke5ExtensionHandler
{
    public static function addIcon(rex_extension_point $ep): string
    {
        return (rex_be_controller::getCurrentPagePart(1) === 'cke5') ? '<i class="cke5-icon-logo"></i> ' . $ep->getSubject() : '';
    }

    public static function hiddenMain(rex_extension_point $ep): void
    {
        if (rex_be_controller::getCurrentPagePart(1) === 'cke5') {
            /** @var array<string,object>|null $subj */
            $subj = $ep->getSubject();
            if (is_array($subj) && isset($subj['cke5'])) {
                /** @var rex_be_page $page */
                $page = $subj['cke5'];
                $subPages = $page->getSubpages();
                if (count($subPages) > 0) {
                    foreach ($subPages as $subPage) {
                        if ($subPage->getKey() === 'main') {
                            foreach ($subPage->getSubpages() as $sPage) {
                                $sPage->setHidden(true);
                            }
                        }
                    }
                }
            }
        }
    }

    public static function createProfiles(rex_extension_point $ep)
    {
        try {
            if (rex_be_controller::getCurrentPagePart(2) === 'profiles' or $ep->getName() === 'CKE5_PROFILE_ADD') {
                Cke5ProfilesCreator::profilesCreate([]);
            } else if ($ep->getName() === 'CKE5_PROFILE_UPDATED') {
                Cke5ProfilesCreator::profilesCreate($ep->getParams());
            }
        } catch (rex_functional_exception $e) {
            rex_logger::logException($e);
            print rex_view::error($e->getMessage());
        }
    }

    public static function updateOrCreateProfiles(): void
    {
        try {
            Cke5ProfilesCreator::profilesCreate([]);
        } catch (rex_functional_exception $e) {
            rex_logger::logException($e);
            print rex_view::error($e->getMessage());
        }
    }
}