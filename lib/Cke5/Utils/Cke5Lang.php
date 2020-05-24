<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Utils;


use rex;
use rex_i18n;
use rex_sql_column;
use rex_sql_table;

class Cke5Lang
{
    /**
     * @return string
     * @author Joachim Doerr
     */
    public static function getUserLang()
    {
        if (!empty(rex::getUser()->getLanguage())) {
            $lang = rex::getUser()->getLanguage();
        } else {
            $lang = rex_i18n::getLocale();
        }
        return strtolower(substr($lang, 0, 2));
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    public static function getOutputLang()
    {
        if (strlen(\rex_clang::getCurrent()->getCode()) == 2) {
            return \rex_clang::getCurrent()->getCode();
        } else {
            return 'en';
        }
    }

    /**
     * @author Joachim Doerr
     */
    public static function addPlaceholderLangColumns()
    {
        $sql = rex_sql_table::get(rex::getTable('cke5_profiles'));
        foreach (rex_i18n::getLocales() as $locale) {
            $sql->ensureColumn(new rex_sql_column('placeholder_' . $locale, 'varchar(255)', true));
        }
        $sql->ensure();
    }
}