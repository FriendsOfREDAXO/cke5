<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */
use Cke5\Creator\Cke5ProfilesCreator;
use Cke5\Handler\Cke5DatabaseHandler;

try {
    if (rex_string::versionCompare($this->getVersion(), '3.3.0', '<')) {
        // add content lang column
        rex_sql_table::get(rex::getTable('cke5_profiles'))
            ->ensureColumn(new rex_sql_column('lang_content', 'varchar(2)', true))
            ->ensureColumn(new rex_sql_column('font_color', 'text', true))
            ->ensureColumn(new rex_sql_column('font_color_default', 'varchar(255)', true))
            ->ensureColumn(new rex_sql_column('font_background_color', 'text', true))
            ->ensureColumn(new rex_sql_column('font_background_color_default', 'varchar(255)', true))
            ->ensureColumn(new rex_sql_column('font_families', 'text', true))
            ->ensureColumn(new rex_sql_column('font_family_default', 'varchar(255)', true))
            ->ensure();
        // copy custom data to assets folder
        if (!file_exists(rex_path::assets('addons/cke5_custom_data'))) {
            mkdir(rex_path::assets('addons/cke5_custom_data'));
        }
        if (!file_exists(rex_path::assets('addons/cke5_custom_data/custom-style.css'))) {
            rex_file::copy($this->getPath('custom_data/custom-styles.css'), rex_path::assets('addons/cke5_custom_data/custom-style.css'));
        }
    }
    if (rex_string::versionCompare($this->getVersion(), '4.0.0', '<')) {
        // add placeholder lang columns
        $sql = rex_sql_table::get(rex::getTable('cke5_profiles'));
        foreach (rex_i18n::getLocales() as $locale) {
            $sql->ensureColumn(new rex_sql_column('placeholder_' . $locale, 'varchar(255)', true));
        }
        $sql->ensureColumn(new rex_sql_column('expert_definition', 'varchar(255)', true))
            ->ensureColumn(new rex_sql_column('definition', 'text', true))
            ->ensureColumn(new rex_sql_column('extra_definition', 'text', true))
            ->ensureColumn(new rex_sql_column('special_characters', 'text', true))
            ->ensureColumn(new rex_sql_column('code_block', 'text', true))
            ->ensureColumn(new rex_sql_column('table_color_default', 'varchar(255)', true))
            ->ensureColumn(new rex_sql_column('table_color', 'text', true))
            ->ensureColumn(new rex_sql_column('transformation', 'varchar(255)', true))
            ->ensureColumn(new rex_sql_column('transformation_extra', 'text', true))
            ->ensureColumn(new rex_sql_column('transformation_remove', 'text', true))
            ->ensureColumn(new rex_sql_column('transformation_include', 'text', true))
            ->ensureColumn(new rex_sql_column('blank_to_external', 'varchar(255)', true))
            ->ensureColumn(new rex_sql_column('link_downloadable', 'varchar(255)', true))
            ->ensureColumn(new rex_sql_column('link_decorators', 'varchar(255)', true))
            ->ensureColumn(new rex_sql_column('link_decorators_definition', 'text', true))
            ->ensureColumn(new rex_sql_column('ytable', 'text', true))
            ->ensureColumn(new rex_sql_column('group_when_full', 'varchar(255)', true))
            ->ensure();
    }
} catch (rex_functional_exception $e) {
    rex_logger::logException($e);
}
