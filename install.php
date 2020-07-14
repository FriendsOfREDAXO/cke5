<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

/** @var rex_addon $this */
// install profiles database
$sql = rex_sql_table::get(rex::getTable('cke5_profiles'));
$sql->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('name', 'varchar(40)', true))
    ->ensureColumn(new rex_sql_column('description', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('toolbar', 'text', true))
    ->ensureColumn(new rex_sql_column('expert_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('expert_suboption', 'text', true))
    ->ensureColumn(new rex_sql_column('expert', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('extra_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('extra', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('code_block', 'text', true))
    ->ensureColumn(new rex_sql_column('special_characters', 'text', true))
    ->ensureColumn(new rex_sql_column('group_when_full', 'varchar(255)', true))
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
    ->ensureColumn(new rex_sql_column('heading', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('alignment', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('image_toolbar', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('fontsize', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('highlight', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('table_toolbar', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('rexlink', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('height_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('min_height', 'int(4)', true))
    ->ensureColumn(new rex_sql_column('max_height', 'int(4)', true))
    ->ensureColumn(new rex_sql_column('lang', 'varchar(2)', true))
    ->ensureColumn(new rex_sql_column('lang_content', 'varchar(2)', true))
    ->ensureColumn(new rex_sql_column('font_color', 'text', true))
    ->ensureColumn(new rex_sql_column('font_color_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('font_background_color', 'text', true))
    ->ensureColumn(new rex_sql_column('font_background_color_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('font_families', 'text', true))
    ->ensureColumn(new rex_sql_column('font_family_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mediaembed', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mediatype', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mediapath', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mediacategory', 'int(4)', true))
    ->ensureColumn(new rex_sql_column('upload_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('createdate', 'datetime', true))
    ->ensureColumn(new rex_sql_column('updatedate', 'datetime', true))
    ->ensureColumn(new rex_sql_column('createuser', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('updateuser', 'varchar(255)', true));

foreach (rex_i18n::getLocales() as $locale) {
    $sql->ensureColumn(new rex_sql_column('placeholder_' . $locale, 'varchar(255)', true));
}

$sql->ensure();

// install mblock demo database
rex_sql_table::get(rex::getTable('cke5_mblock_demo'))
    ->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('name', 'varchar(40)', true))
    ->ensureColumn(new rex_sql_column('mblock_field', 'text', true))
    ->ensureColumn(new rex_sql_column('createdate', 'datetime', true))
    ->ensureColumn(new rex_sql_column('updatedate', 'datetime', true))
    ->ensureColumn(new rex_sql_column('createuser', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('updateuser', 'varchar(255)', true))
    ->ensure();

// install default demo profile and mblock demo data
try {
    $sql = rex_sql::factory();
    if (
        sizeof($sql->getArray("SELECT id FROM " . rex::getTable('cke5_profiles') . " WHERE id=1")) <= 0 &&
        sizeof($sql->getArray("SELECT id FROM " . rex::getTable('cke5_mblock_demo') . " WHERE id=1")) <= 0
    ) {
        rex_sql_util::importDump($this->getPath('data.sql'));
    }
    // copy custom data to assets folder
    if (!file_exists(rex_path::assets('addons/cke5_custom_data'))) {
        mkdir(rex_path::assets('addons/cke5_custom_data'));
    }
    if (!file_exists(rex_path::assets('addons/cke5_custom_data/custom-style.css'))) {
        rex_file::copy($this->getPath('custom_data/custom-styles.css'), rex_path::assets('addons/cke5_custom_data/custom-style.css'));
    }
} catch (rex_sql_exception $e) {
    rex_logger::logException($e);
    print rex_view::error($e->getMessage());
}
