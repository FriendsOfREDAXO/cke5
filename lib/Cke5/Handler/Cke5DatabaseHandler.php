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
        try {
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES))
                ->setWhere(['name' => $name])
                ->select('name');

            return $sql->getRows() != 0;
        } catch (\rex_sql_exception $e) {
            \rex_logger::logException($e);
            return false;
        }
    }

    /**
     * @param $name
     * @param $description
     * @param $definition
     * @param $subOptions
     * @return array|null|string
     * @author Joachim Doerr
     */
    public static function addProfile($name, $description, $definition, $subOptions = '')
    {
        try {
            if (self::profileExist($name)) {
                throw new \rex_exception("Profile with name $name already exist");
            }

            // verify json syntax
            $subOptions = json_decode($subOptions, true);

            $now = new \DateTime();
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES))
                ->setValue('name', $name)
                ->setValue('description', $description)
                ->setValue('expert', '|expert_definition|')
                ->setValue('expert_definition', json_encode($definition))
                ->setValue('expert_suboption', (is_array($subOptions) && count($subOptions) > 0) ? json_encode($subOptions) : '')
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
