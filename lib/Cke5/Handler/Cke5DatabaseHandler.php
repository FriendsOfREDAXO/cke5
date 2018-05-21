<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Handler;


use rex;
use rex_sql;

class Cke5DatabaseHandler
{
    const CKE5_PROFILES = 'cke5_profiles';
    const CKE5_MBLOCK_DEMO = 'cke5_mblock_demo'; // cke5_mblock_demo

    /**
     * @return array|null
     * @author Joachim Doerr
     */
    public static function getAllProfiles()
    {
        $sql = rex_sql::factory();
        try {
            return $sql->getArray("SELECT * FROM " . rex::getTable(self::CKE5_PROFILES));
        } catch (\rex_sql_exception $e) {
            \rex_logger::logException($e);
            return null;
        }
    }
}