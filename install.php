<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

/** @var rex_addon $this */
// install profiles database
rex_sql_table::get(rex::getTable('cke5_profiles'))
    ->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('name', 'varchar(40)', true))
    ->ensureColumn(new rex_sql_column('description', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('toolbar', 'text', true))
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
    ->ensureColumn(new rex_sql_column('mediatype', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mediapath', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mediacategory', 'int(4)', true))
    ->ensureColumn(new rex_sql_column('upload_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('createdate', 'datetime', true))
    ->ensureColumn(new rex_sql_column('updatedate', 'datetime', true))
    ->ensureColumn(new rex_sql_column('createuser', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('updateuser', 'varchar(255)', true))
    ->ensure();

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
} catch (rex_sql_exception $e) {
    rex_logger::logException($e);
    print rex_view::error($e->getMessage());
}
