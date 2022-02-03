<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Utils;


use rex_clang;
use rex_form;
use rex_i18n;

class Cke5FormHelper
{
    /**
     * @param rex_form $form
     * @param string $type
     * @param null $key
     * @param null $curClang
     * @author Joachim Doerr
     */
    public static function addRexLangTabs(rex_form $form, $type, $key = NULL, $curClang = NULL)
    {
        $locales = rex_i18n::getLocales();
        asort($locales);


        if (count($locales) > 1) {
            switch ($type) {
                case 'wrapper':
                    $form->addRawField('<div class="cke5_clangtabs"><ul class="nav nav-tabs" role="tablist">');
                    foreach ($locales as $lang) {
                        if ($key == $lang) {
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
                    if ($key == $curClang) {
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

    /**
     * @param rex_form $form
     * @param string $type
     * @param null $key
     * @param null $curClang
     * @author Joachim Doerr
     */
    public static function addCLangTabs(rex_form $form, $type, $key = NULL, $curClang = NULL)
    {
        if (rex_clang::count() > 1) {
            switch ($type) {
                case 'wrapper':
                    $form->addRawField('<div class="cke5_clangtabs"><ul class="nav nav-tabs" role="tablist">');
                    foreach (rex_clang::getAll() as $clang) {
                        if ($key == $clang->getId()) {
                            $active = ' active';
                        } else {
                            $active = '';
                        }
                        $form->addRawField("<li role=\"presentation\" class=\"$active\"><a href=\"#lang{$clang->getId()}\" aria-controls=\"home\" role=\"tab\" data-toggle=\"tab\">{$clang->getName()}</a></li>");
                    }
                    $form->addRawField('</ul><div class="tab-content cke5-tabform">');
                    break;

                case 'close_wrapper':
                    $form->addRawField('</div></div>');
                    break;

                case 'inner_wrapper':
                    if ($key == $curClang) {
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

    /**
     * @param rex_form $form
     * @param $type
     * @param string $name
     * @param string $clang
     * @param bool $langField
     * @author Joachim Doerr
     */
    public static function addCollapsePanel(rex_form $form, $type, $name = '', $clang = '', $langField = false)
    {
        $in = '';
        $langkey = '';
        if ($langField) {
            $langkey = '_'.$clang->getId();
        }
        switch ($type) {
            case 'wrapper':
                $keya = uniqid('a');
                $form->addRawField("<div class=\"panel-group cke5-panel\" id=\"$keya.$langkey\">");
                break;

            case 'close_wrapper':
                $form->addRawField('</div>');
                break;

            case 'inner_wrapper_open':
                $in = 'in';
            case 'inner_wrapper':
                $keyp = uniqid('p');
                $keyc = uniqid('c');
                $form->addRawField("<div class=\"panel panel-default\" id=\"$keyp\"><div class=\"panel-heading\"><h4 class=\"panel-title\"><a data-toggle=\"collapse\" data-target=\"#$keyc\" href=\"#$keyc\" class=\"collapsed\">$name</a></h4></div> <div id=\"$keyc\" class=\"panel-collapse collapse$in\"><div class=\"panel-body\">");
                break;

            case 'close_inner_wrapper':
                $form->addRawField('</div></div></div>');
                break;
        }
    }

}
