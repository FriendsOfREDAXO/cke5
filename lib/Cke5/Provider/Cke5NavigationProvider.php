<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Provider;


use rex_be_controller;
use rex_be_navigation;
use rex_fragment;

class Cke5NavigationProvider
{
    /**
     * @return null|\rex_be_page
     * @author Joachim Doerr
     */
    public static function getMainSubPage()
    {
        $page = rex_be_controller::getPageObject('cke5');
        foreach ($page->getSubPages() as $subPage) {
            if ($subPage->getKey() == 'main') {
                return $subPage;
            }
        }
        return null;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public static function getSubNavigationHeader()
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
                    <h1>'.\rex_i18n::msg('cke5_subnavigation_header_title').'</h1>
                </div>
                <div class="photoinfo"><span>'.$photoBy.'</span></div>
            </header>
        ';
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public static function getSubNavigation()
    {
        $subpage = self::getMainSubPage();
        $subtitle = '';

        if (sizeof($subpage->getSubpages()) > 0) {
            $nav = rex_be_navigation::factory();

            foreach ($subpage->getSubpages() as $sPage) {
                $sPage->setHidden(false);
                $nav->addPage($sPage);
            }

            $blocks = $nav->getNavigation();
            $navigation = [];
            if (count($blocks) == 1) {
                $navigation = current($blocks);
                $navigation = $navigation['navigation'];
            }

            if (!empty($navigation)) {
                try {
                    $fragment = new rex_fragment();
                    $fragment->setVar('left', $navigation, false);
                    $subtitle = $fragment->parse('core/navigations/content.php');

                    $subtitle = str_replace('/mblock_demo', '/mblock_demo&id=1&func=edit&list=300200', $subtitle);

                } catch (\rex_exception $e) {
                    \rex_logger::logException($e);
                }

                $subtitle = str_replace(['nav nav-tabs', 'rex-page-nav'], ['nav nav-pills list-inline center-block text-center', 'cke5-mainnav'], $subtitle);

            } else {
                $subtitle = '';
            }
        }

        return $subtitle;
    }
}