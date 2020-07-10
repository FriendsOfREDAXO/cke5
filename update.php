<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */
use Cke5\Creator\Cke5ProfilesCreator;
use Cke5\Handler\Cke5DatabaseHandler;

include_once (__DIR__ . '/install.php');

try {
    if (rex_string::versionCompare($this->getVersion(), '3.0.0', '<')) {
        // open db replace sub and sup
        $profiles = Cke5DatabaseHandler::getAllProfiles();
        foreach ($profiles as $key => $profile) {
            if ((isset($profile['toolbar']) && isset($profile['id'])) &&
                (strpos($profile['toolbar'] . ',', 'sub,') !== false || strpos($profile['toolbar'] . ',', 'sup,') !== false)) {
                $sql = rex_sql::factory();
                try {
                    $sql->setTable(rex::getTable('cke5_profiles'))
                        ->setWhere('id = :id', ['id' => $profile['id']])
                        ->setValue('toolbar', substr(str_replace(['sub,', 'sup,'], ['subscript,', 'superscript,'], $profile['toolbar'] . ','), 0, -1))
                        ->update();
                } catch (rex_sql_exception $e) {
                    rex_logger::logException($e);
                }
            }
        }
        // regenerate profiles general
        Cke5ProfilesCreator::profilesCreate();
    }
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
            ->ensureColumn(new rex_sql_column('code_block', 'text', true))
            ->ensure();
    }
} catch (rex_functional_exception $e) {
    rex_logger::logException($e);
}
