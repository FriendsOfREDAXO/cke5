<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Handler;


use Cke5\Creator\Cke5ProfilesCreator;
use DateTime;
use DateTimeInterface;
use rex;
use rex_extension;
use rex_extension_point;
use rex_sql;
use rex_user;

class Cke5DatabaseHandler
{
    const CKE5_PROFILES = 'cke5_profiles';

    /**
     * @param string $name
     * @return bool
     * @author Joachim Doerr
     */
    public static function profileExist(string $name): bool
    {
        return is_array(self::loadProfile($name));
    }

    /**
     * @param string $name
     * @param string $description
     * @param string $definition
     * @param string|null $subOptions
     * @return array<string,string>
     * @deprecated will be remove in cke5 v6.0
     * @author Joachim Doerr
     */
    public static function addProfile(string $name, string $description, string $definition, string $subOptions = null): array
    {
        try {
            if (self::profileExist($name)) {
                throw new \rex_exception("Profile with name $name already exist");
            }

            $now = new DateTime();
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES))
                ->setValue('name', $name)
                ->setValue('description', $description)
                ->setValue('expert', '|expert_definition|')
                ->setValue('expert_definition', $definition)
                ->setValue('expert_suboption', $subOptions)
                ->setValue('createuser', self::getLogin())
                ->setValue('updateuser', self::getLogin())
                ->setValue('createdate', $now->format(DateTimeInterface::ATOM))
                ->setValue('updatedate', $now->format(DateTimeInterface::ATOM))
                ->insert();
        } catch (\rex_exception $e) {
            \rex_logger::logException($e);
        }

        if (is_array($profile = self::loadProfile($name))) {
            rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_ADD', '', $profile, true));
            return $profile;
        }
        return [];
    }

    /**
     * @param array<string,string> $profile
     * @return bool
     * @author Joachim Doerr
     */
    public static function importProfile(array $profile): bool
    {
        try {
            $now = new \DateTime();
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES));

            foreach ($profile as $key => $value) {
                if ($key === 'id') continue;
                $sql->setValue($key, $value);
            }

            $sql->setValue('createuser', self::getLogin())
                ->setValue('updateuser', self::getLogin())
                ->setValue('createdate', $now->format(DateTimeInterface::ATOM))
                ->setValue('updatedate', $now->format(DateTimeInterface::ATOM));

            if (is_array($loadedProfile = self::loadProfile($profile['name']))) {
                $sql->setWhere('id=:id', ['id' => $loadedProfile['rex_cke5_profiles.id']]);
                $sql->update();
                rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_UPDATE', '', $profile, true));
            } else {
                $sql->insert();
                rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_ADD', '', $profile, true));
            }
            return true;
        } catch (\Exception $e) {
            \rex_logger::logException($e);
            return false;
        }
    }

    /**
     * @param string $name
     * @return array<string,string>|null
     * @author Joachim Doerr
     */
    public static function loadProfile(string $name): ?array
    {
        try {
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES))
                ->setWhere(['name' => $name])
                ->select('*');
            /** @var array<string,string>|null $result */
            $result = $sql->getRow();
            if (is_null($result)) return null;
            return $result;
        } catch (\rex_sql_exception $e) {
            \rex_logger::logException($e);
            return null;
        }
    }

    /**
     * @return array<string,array<string,string>>|null
     * @author Joachim Doerr
     */
    public static function getAllProfiles(): ?array
    {
        try {
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable(self::CKE5_PROFILES))
                ->select('*');
            /** @var array<string,array<string,string>>|null $result */
            $result = $sql->getArray();
            if (is_null($result)) return null;
            return $result;
        } catch (\rex_sql_exception $e) {
            \rex_logger::logException($e);
            return null;
        }
    }

    /**
     * @param array<string,string> $settings
     * @param string $name
     * @return null|string
     * @author Joachim Doerr
     */
    protected static function getSettings(array $settings, string $name): ?string
    {
        if (count($settings) > 0) {
            $array = Cke5ProfilesCreator::ALLOWED_FIELDS[$name];
            $settings = array_filter($settings, function ($item) use ($array) {
                if (in_array($item, $array, true)) return $item;
            });
            return implode(',', $settings);
        }
        return null;
    }

    /**
     * @return string
     * @author Joachim Doerr
     */
    protected static function getLogin(): string
    {
        $user = rex::getUser();
        return ($user instanceof rex_user) ? $user->getLogin() : '';
    }
}
