<?php

namespace Cke5\Creator;


use Cke5\Handler\Cke5DatabaseHandler;
use DateTime;
use DateTimeInterface;
use rex;
use rex_exception;
use rex_extension;
use rex_extension_point;
use rex_logger;
use rex_sql;

class Cke5ProfilesApi extends Cke5DatabaseHandler
{
    /**
     * @param string $name
     * @param string $description
     * @param string $definition
     * @param string|null $subOptions
     * @return array<string,string>
     * @author Joachim Doerr
     */
    public static function addProfile(string $name, string $description, string $definition, string $subOptions = null): array
    {
        try {
            if (self::profileExist($name)) {
                throw new rex_exception("Profile with name $name already exist");
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
        } catch (rex_exception $e) {
            rex_logger::logException($e);
        }

        if (is_array($profile = self::loadProfile($name))) {
            rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_ADD', '', $profile, true));
            return $profile;
        }
        return [];
    }
}