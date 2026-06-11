<?php

namespace Cke5\Utils;


use rex_addon;
use rex_form;
use rex_i18n;

class Cke5FormHelper
{
    public static function addRexLangTabs(rex_form $form, string $type, ?string $key = null, ?string $curClang = null): void
    {
        $locales = rex_i18n::getLocales();
        asort($locales);

        if (count($locales) > 1) {
            switch ($type) {
                case 'wrapper':
                    $form->addRawField('<div class="cke5_clangtabs"><ul class="nav nav-tabs" role="tablist">');
                    foreach ($locales as $lang) {
                        $active = ($key === $lang) ? ' active' : '';
                        $form->addRawField("<li role=\"presentation\" class=\"$active\"><a href=\"#lang{$lang}\" aria-controls=\"home\" role=\"tab\" data-toggle=\"tab\">".rex_i18n::msgInLocale('lang', $lang)."</a></li>");
                    }
                    $form->addRawField('</ul><div class="tab-content cke5-tabform">');

                    break;

                case 'close_wrapper':
                    $form->addRawField('</div></div>');
                    break;

                case 'inner_wrapper':
                    $active = ($key === $curClang) ? ' active' : '';
                    $form->addRawField("\n\n\n<div id=\"lang$key\" role=\"tabpanel\" class=\"tab-pane $active\">\n");
                    break;

                case 'close_inner_wrapper':
                    $form->addRawField('</div>');
                    break;
            }
        }
    }

    /**
     * @param array<int|string,mixed> $items
     * @param array<int,mixed> $licenseItemsToRemove
     * @return array<int|string,mixed>
     */
    public static function potentialRemoveLicenseItems(array $items, array $licenseItemsToRemove): array
    {
        $licenseCode = (string) rex_addon::get('cke5')->getConfig('license_code', '');
        if (trim($licenseCode) === '' || strtolower(trim($licenseCode)) === 'gpl') {
            foreach ($items as $key => $item) {
                foreach ($licenseItemsToRemove as $lItem) {
                    if ($item == $lItem) {
                        unset($items[$key]);
                    }
                }
            }
        }
        return $items;
    }
}
