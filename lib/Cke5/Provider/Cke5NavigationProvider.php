<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Provider;


use rex_be_controller;
use rex_be_navigation;
use rex_be_page;
use rex_fragment;

class Cke5NavigationProvider
{
    /**
     * @return null|rex_be_page
     * @author Joachim Doerr
     */
    public static function getMainSubPage(): ?rex_be_page
    {
        /** @var rex_be_page $page */
        $page = rex_be_controller::getPageObject('cke5');
        $subPages = $page->getSubpages();
        if (count($subPages) > 0) {
            foreach ($subPages as $subPage) {
                if ($subPage->getKey() === 'main') {
                    return $subPage;
                }
            }
        }
        return null;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public static function getSubNavigationHeader(): string
    {
        $photoBy = sprintf(\rex_i18n::msg('cke5_subnavigation_header_photo'),
            '<a href="https://unsplash.com/photos/mMcqMYJfopo?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Patrick Fore</a>',
            '<a href="https://unsplash.com/search/photos/stars?utm_source=unsplash&amp;utm_medium=referral&amp;utm_content=creditCopyText">Unsplash</a>'
        );
        return '
            <header class="cke5-header">
                <picture>
                    <img src="/assets/addons/cke5/images/header-patrick-fore-357913-unsplash.jpg">
                </picture>
                <div class="header-content">
                    <h1>' . \rex_i18n::msg('cke5_subnavigation_header_title') . '</h1>
                </div>
                <div class="photoinfo"><span>' . $photoBy . '</span></div>
            </header>
        ';
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public static function getSubNavigation(): string
    {
        /** @var rex_be_page $subpage */
        $subpage = self::getMainSubPage();
        $subPages = $subpage->getSubpages();
        $subtitle = '';

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
                    $subtitle = $fragment->parse('core/navigations/content.php');

                } catch (\rex_exception $e) {
                    \rex_logger::logException($e);
                }
            }
        }
        return $subtitle;
    }
}