<?php

namespace Cke5\Utils;


use rex_addon;
use rex_clang;
use rex_form;
use rex_i18n;

class Cke5FormHelper
{
    public static function addRexLangTabs(rex_form $form, string $type, string $key = NULL, string $curClang = NULL): void
    {
        $locales = rex_i18n::getLocales();
        asort($locales);

        if (count($locales) > 1) {
            switch ($type) {
                case 'wrapper':
                    $form->addRawField('<div class="cke5_clangtabs"><ul class="nav nav-tabs" role="tablist">');
                    foreach ($locales as $lang) {
                        if ($key === $lang) {
                            $active = ' active';
                        } else {
                            $active = '';
                        }
                        $form->addRawField("<li role=\"presentation\" class=\"$active\"><a href=\"#lang{$lang}\" aria-controls=\"home\" role=\"tab\" data-toggle=\"tab\">".rex_i18n::msgInLocale('lang', $lang)."</a></li>");
                    }
                    $form->addRawField('</ul><div class="tab-content cke5-tabform">');

                    break;

                case 'close_wrapper':
                    $form->addRawField('</div></div>');
                    break;

                case 'inner_wrapper':
                    if ($key === $curClang) {
                        $active = ' active';
                    } else {
                        $active = '';
                    }
                    $form->addRawField("\n\n\n<div id=\"lang$key\" role=\"tabpanel\" class=\"tab-pane $active\">\n");
                    break;

                case 'close_inner_wrapper':
                    $form->addRawField('</div>');
                    break;
            }
        }
    }

    public static function potentialRemoveLicenseItems($items, $licenseItemsToRemove):array
    {
        if (empty(rex_addon::get('cke5')->getConfig('license_code'))) {
            foreach ($items as $key => $item) {
                foreach ($licenseItemsToRemove as $lItem) {
                    if ($item == $lItem) unset($items[$key]);
                }
            }
        }
        return $items;
    }
}
