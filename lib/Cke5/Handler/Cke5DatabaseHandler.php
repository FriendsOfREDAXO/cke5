<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Handler;


use Cke5\Creator\Cke5ProfilesCreator;
use rex;
use rex_extension;
use rex_extension_point;
use rex_sql;

class Cke5DatabaseHandler
{
    const CKE5_PROFILES = 'cke5_profiles';
    const CKE5_MBLOCK_DEMO = 'cke5_mblock_demo'; // cke5_mblock_demo

    /**
     * @param null $name
     * @return bool
     * @author Joachim Doerr
     */
    public static function profileExist($name = null)
    {
        return (self::loadProfile($name) !== false) ? true : false;
    }

    /**
     * @param $name
     * @param $description
     * @param array $toolbar
     * @param array $heading
     * @param array $alignment
     * @param array $image_toolbar
     * @param array $fontsize
     * @param array $highlight
     * @param array $table_toolbar
     * @param array $rexlink
     * @param int|null $min_height
     * @param int|null $max_height
     * @param string|null $lang
     * @param string|null $mediatype
     * @param int $mediacategory
     * @param bool $upload_default
     * @return array|null|string
     * @author Joachim Doerr
     */
    public static function addProfile($name,
                                      $description,
                                      array $toolbar = [],
                                      array $heading = [],
                                      array $alignment = [],
                                      array $image_toolbar = [],
                                      array $fontsize = [],
                                      array $highlight = [],
                                      array $table_toolbar = [],
                                      array $rexlink = [],
                                      int $min_height = null,
                                      int $max_height = null,
                                      string $lang = null,
                                      string $mediatype = null,
                                      int $mediacategory = 0,
                                      bool $upload_default = true)
    {
        $height_default = (is_null($min_height) && (is_null($max_height))) ? '|default_height|' : '';
        $upload_default = ($upload_default === true) ? '|default_upload|' : '';

        try {
            if (self::profileExist($name)) {
                throw new \rex_exception("Profile with name $name already exist");
            }
            $now = new \DateTime();
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES))
                ->setValue('name', $name)
                ->setValue('description', $description)
                ->setValue('toolbar', self::getSettings($toolbar, 'toolbar'))
                ->setValue('heading', self::getSettings($heading, 'heading'))
                ->setValue('alignment', self::getSettings($alignment, 'alignment'))
                ->setValue('image_toolbar', self::getSettings($image_toolbar, 'image_toolbar'))
                ->setValue('fontsize', self::getSettings($fontsize, 'fontsize'))
                ->setValue('highlight', self::getSettings($highlight, 'highlight'))
                ->setValue('table_toolbar', self::getSettings($table_toolbar, 'table_toolbar'))
                ->setValue('rexlink', self::getSettings($rexlink, 'rexlink'))
                ->setValue('height_default', $height_default)
                ->setValue('min_height', $min_height)
                ->setValue('max_height', $max_height)
                ->setValue('lang', $lang)
                ->setValue('mediatype', $mediatype)
                ->setValue('mediacategory', $mediacategory)
                ->setValue('upload_default', $upload_default)
                ->setValue('createuser', rex::getUser()->getLogin())
                ->setValue('updateuser', rex::getUser()->getLogin())
                ->setValue('createdate', $now->format(\DateTime::ISO8601))
                ->setValue('updatedate', $now->format(\DateTime::ISO8601))
                ->insert();
        } catch (\rex_exception $e) {
            \rex_logger::logException($e);
            return $e->getMessage();
        }

        if (is_array($profile = self::loadProfile($name))) {
            rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_ADD', '', $profile, true));
        }

        return $profile;
    }

    /**
     * @param null $name
     * @return array|null
     * @author Joachim Doerr
     */
    public static function loadProfile($name = null)
    {
        try {
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES))
                ->setWhere(['name' => $name])
                ->select('*');
            return $sql->getRow();
        } catch (\rex_sql_exception $e) {
            \rex_logger::logException($e);
            return null;
        }
    }

    /**
     * @return array|null
     * @author Joachim Doerr
     */
    public static function getAllProfiles()
    {
        try {
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable(self::CKE5_PROFILES))
                ->select('*');
            return $sql->getArray();
        } catch (\rex_sql_exception $e) {
            \rex_logger::logException($e);
            return null;
        }
    }

    /**
     * @param array $settings
     * @param $name
     * @return null|string
     * @author Joachim Doerr
     */
    private static function getSettings(array $settings, $name)
    {
        if (count($settings) > 0) {
            $array = Cke5ProfilesCreator::ALLOWED_FIELDS[$name];
            $settings = array_filter($settings, function ($item) use ($array) {
                if (in_array($item, $array)) return $item;
            });
            return implode(',', $settings);
        }
        return null;
    }
}