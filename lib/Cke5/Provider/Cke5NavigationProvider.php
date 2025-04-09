<?php

namespace Cke5\Provider;


use rex_be_controller;
use rex_be_navigation;
use rex_be_page;
use rex_exception;
use rex_fragment;
use rex_i18n;
use rex_logger;

class Cke5NavigationProvider
{
    public static function getMainSubNavigationHeader(): string
    {
        $photoBy = sprintf(rex_i18n::msg('cke5_subnavigation_header_photo'),
            '<a href="https://unsplash.com/photos/mMcqMYJfopo?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Patrick Fore</a>',
            '<a href="https://unsplash.com/search/photos/stars?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Unsplash</a>'
        );
        return '
            <header class="cke5-header">
                <picture>
                    <img src="/assets/addons/cke5/images/header-patrick-fore-357913-unsplash.jpg">
                </picture>
                <div class="header-content">
                    <h1>' . rex_i18n::msg('cke5_subnavigation_header_title') . '</h1>
                </div>
                <div class="photoinfo"><span>' . $photoBy . '</span></div>
            </header>
        ';
    }

    public static function getSubNavigation(string $path): string
    {
        $subPage = self::getSubpage($path);
        $subPages = $subPage->getSubpages();
        $output = '';
        if (count($subPages) > 0) {
            $nav = rex_be_navigation::factory();

            foreach ($subPages as $sPage) {
                $sPage->setHidden(false);
                $nav->addPage($sPage);
            }

            $blocks = $nav->getNavigation();
            $navigation = [];
            if (count($blocks) === 1) {
                $navigation = current($blocks);
                $navigation = $navigation['navigation'];
            }

            if ($navigation !== '') {
                try {
                    $fragment = new rex_fragment();
                    $fragment->setVar('left', $navigation, false);
                    $output = $fragment->parse('core/navigations/content.php');

                } catch (rex_exception $e) {
                    rex_logger::logException($e);
                }
            }
        }
        return $output;
    }

    /**
     * @return array<rex_be_page>
     */
    private static function findSubpageRecursive(rex_be_page $page, array $path): ?rex_be_page
    {
        if (count($path) === 0) {
            return $page;
        }

        $key = array_shift($path);
        $subPages = $page->getSubpages();

        foreach ($subPages as $subPage) {
            if ($subPage->getKey() === $key) {
                return self::findSubpageRecursive($subPage, $path);
            }
        }

        return null;
    }

    private static function getSubpage(string $path): ?rex_be_page
    {
        $path = array_filter(explode('.', $path));
        $page = rex_be_controller::getPageObject('cke5');

        return self::findSubpageRecursive($page, $path);
    }}