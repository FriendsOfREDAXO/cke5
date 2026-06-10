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
        $createdBy = sprintf(
            '<a href="https://friendsofredaxo.github.io" target="_blank" rel="noopener">%s</a>',
            rex_i18n::msg('cke5_subnavigation_header_credit')
        );
        return '
            <header class="cke5-header">
                <div class="cke5-scan"></div>
                <div class="header-inner">
                    <div class="header-logo">
                        <img src="/assets/addons/cke5/images/cke5_white.svg" alt="CKEditor Logo" class="cke5-logo-img">
                    </div>
                    <div class="header-content">
                        <h1>' . rex_i18n::msg('cke5_subnavigation_header_title') . '</h1>
                    </div>
                </div>
                <div class="photoinfo"><span>' . $createdBy . '</span></div>
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