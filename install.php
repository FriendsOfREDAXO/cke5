<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

include_once (__DIR__ . '/db.php');

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
    // final create profiles
    \Cke5\Handler\Cke5ExtensionHandler::updateOrCreateProfiles();
} catch (rex_sql_exception $e) {
    rex_logger::logException($e);
    print rex_view::error($e->getMessage());
}
