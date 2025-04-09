<?php

namespace Cke5\Utils;


use rex;
use rex_clang;
use rex_i18n;
use rex_sql_column;
use rex_sql_table;
use rex_user;

class Cke5Lang
{
    public static function getUserLang(): string
    {
        /** @var rex_user $user */
        $user = rex::getUser();
        $userLang = $user->getLanguage();
        if ($userLang !== '') {
            $lang = $userLang;
        } else {
            $lang = rex_i18n::getLocale();
        }
        return strtolower(substr($lang, 0, 2));
    }

    public static function getOutputLang(): string
    {
        if (strlen(rex_clang::getCurrent()->getCode()) === 2) {
            return rex_clang::getCurrent()->getCode();
        } else {
            return 'en';
        }
    }

    public static function addPlaceholderLangColumns(): void
    {
        $sql = rex_sql_table::get(rex::getTable('cke5_profiles'));
        foreach (rex_i18n::getLocales() as $locale) {
            $sql->ensureColumn(new rex_sql_column('placeholder_' . $locale, 'varchar(255)', true));
        }
        $sql->ensure();
    }
}