<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

/** @var rex_addon $this */

include_once (__DIR__ . '/db.php');

try {
    if (rex_version::compare($this->getVersion(), '3.3.0', '<')) {
        // copy custom data to assets folder
        if (!file_exists(rex_path::assets('addons/cke5_custom_data'))) {
            mkdir(rex_path::assets('addons/cke5_custom_data'));
        }
        if (!file_exists(rex_path::assets('addons/cke5_custom_data/custom-style.css'))) {
            rex_file::copy($this->getPath('custom_data/custom-styles.css'), rex_path::assets('addons/cke5_custom_data/custom-style.css'));
        }
    }
    if (rex_version::compare($this->getVersion(), '5.2.0', '>')) {
        try {
            rex_sql::factory()->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'cke5_mblock_demo');
        } catch (rex_sql_exception $e) {
            rex_logger::logException($e);
        }
    }
} catch (rex_functional_exception $e) {
    rex_logger::logException($e);
}

$addon = rex_addon::get('cke5');
$addon->setConfig('updated', true);
