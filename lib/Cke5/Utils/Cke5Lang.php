<?php

namespace Cke5\Utils;


use rex;
use rex_clang;
use rex_i18n;
use rex_sql_column;
use rex_sql_table;
class Cke5Lang
{
    public static function getUserLang(): string
    {
        $user = rex::getUser();
        $lang = $user && $user->getLanguage() !== ''
            ? $user->getLanguage()
            : rex_i18n::getLocale();

        return strtolower(substr((string) $lang, 0, 2));
    }

    public static function getOutputLang(): string
    {
        $current = rex_clang::getCurrent();
        if (!$current) {
            return 'en';
        }

        $code = (string) $current->getCode();
        return strlen($code) === 2 ? $code : 'en';
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