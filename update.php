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
            ->addColumn(new rex_sql_column('lang_content', 'varchar(2)', true))
            ->alter();
        // regenerate lang file general
        Cke5ProfilesCreator::languageFileCreate();
    }
} catch (rex_functional_exception $e) {
    rex_logger::logException($e);
}
