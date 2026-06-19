<?php

namespace Cke5\Handler;


use DateTime;
use DateTimeInterface;
use Exception;
use rex;
use rex_extension;
use rex_extension_point;
use rex_logger;
use rex_sql;
use rex_sql_exception;
use rex_sql_table;
use rex_user;

class Cke5DatabaseHandler
{
    const CKE5_PROFILES = 'cke5_profiles';
    const CKE5_STYLES = 'cke5_styles';
    const CKE5_STYLE_GROUPS = 'cke5_style_groups';
    const CKE5_SNIPPETS = 'cke5_snippets';
    const CKE5_TEMPLATES = 'cke5_templates';
    const CKE5_TEMPLATE_GROUPS = 'cke5_template_groups';

    public static function profileExist(string $name): bool
    {
        return is_array(self::loadProfile($name));
    }

    public static function importProfile(array $profile): bool
    {
        try {
            $now = new DateTime();
            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES));

            // Determine the columns that actually exist in the destination
            // table, so the import is robust against schema differences
            // (e.g. locale-specific `placeholder_*` columns or columns
            // added/removed between versions).
            $allowedColumns = self::getProfileTableColumns();

            foreach ($profile as $key => $value) {
                if ($key === 'id') {
                    continue;
                }

                if ($allowedColumns !== [] && !isset($allowedColumns[(string) $key])) {
                    continue;
                }

                if (is_array($value)) {
                    $value = (string) json_encode($value, JSON_UNESCAPED_UNICODE);
                }

                $sql->setValue((string) $key, $value);
            }

            $sql->setValue('createuser', self::getLogin())
                ->setValue('updateuser', self::getLogin())
                ->setValue('createdate', $now->format(DateTimeInterface::ATOM))
                ->setValue('updatedate', $now->format(DateTimeInterface::ATOM));

            if (is_array($loadedProfile = self::loadProfile($profile['name']))) {
                $sql->setWhere('id=:id', ['id' => (int) $loadedProfile['id']]);
                $sql->update();
                rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_UPDATE', '', $profile, true));
            } else {
                $sql->insert();
                rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_ADD', '', $profile, true));
            }
            return true;
        } catch (Exception $e) {
            rex_logger::logException($e);
            return false;
        }
    }

    /**
     * Returns the column names of the profiles table as an associative array
     * (column name => true) for fast lookup. Empty array if the table is not
     * yet available.
     *
     * @return array<string,true>
     */
    private static function getProfileTableColumns(): array
    {
        try {
            $table = rex_sql_table::get(rex::getTable(self::CKE5_PROFILES));
            $columns = [];
            foreach ($table->getColumns() as $column) {
                $columns[$column->getName()] = true;
            }
            return $columns;
        } catch (Exception $e) {
            rex_logger::logException($e);
            return [];
        }
    }

    /**
     * @return array<string,string>|null
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
        } catch (rex_sql_exception $e) {
            rex_logger::logException($e);
            return null;
        }
    }

    /**
     * @return array<string,array<string,string>>|null
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
        } catch (rex_sql_exception $e) {
            rex_logger::logException($e);
            return null;
        }
    }

    protected static function getLogin(): string
    {
        $user = rex::getUser();
        return ($user instanceof rex_user) ? $user->getLogin() : '';
    }
}
