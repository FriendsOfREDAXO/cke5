<?php

namespace Cke5\Utils;


use rex_i18n;
use rex_logger;
use rex_sql;
use rex_sql_exception;
use rex_view;

class Cke5ListHelper
{
    /**
     * togglet bool data column
     */
    public static function toggleBoolData(string $table, int $id, string $column = NULL): string
    {
        if (!is_null($column)) {
            $sql = rex_sql::factory();
            try {
                $sql->setQuery("UPDATE " . $sql->escapeIdentifier($table) . " SET :column=ABS(1-:column) WHERE id=:id", ['column' => $column, 'id' => $id]);
            } catch (rex_sql_exception $e) {
                rex_logger::logException($e);
                return rex_view::error(rex_i18n::msg($table . '_toggle_' . $column . '_exception'));
            }
            return rex_view::info(rex_i18n::msg($table . '_toggle_' . $column . '_success'));
        } else {
            return rex_view::warning(rex_i18n::msg($table . '_toggle_' . $column . '_error'));
        }
    }

    /**
     * clone data
     */
    static public function cloneData(string $table, int $id): string
    {
        try {
            $sql = rex_sql::factory();
            $fields = $sql->getArray('DESCRIBE ' . $sql->escapeIdentifier($table));
            $queryFields = [];
            if (count($fields) > 0) {
                foreach ($fields as $field) {
                    if ($field['Key'] !== 'PRI' && $field['Field'] !== 'status') {
                        $queryFields[] = $field['Field'];
                    }
                }
            }
            $sql->setQuery('INSERT INTO ' . $sql->escapeIdentifier($table) . ' (`'.implode('`, `', $queryFields).'`) SELECT `'.implode('`, `', $queryFields).'` FROM ' . $sql->escapeIdentifier($table) . ' WHERE id =:id', ['id' => $id]);
        } catch (rex_sql_exception $e) {
            rex_logger::logException($e);
            return rex_view::error(rex_i18n::msg($table . '_clone_exception'));
        }
        return rex_view::info(rex_i18n::msg($table . '_cloned'));
    }

    /**
     * delete data
     */
    static public function deleteData(string $table, int $id): string
    {
        $sql = rex_sql::factory();
        try {
            $sql->setQuery("DELETE FROM " . $sql->escapeIdentifier($table) . " WHERE id=:id", ['id' => $id]);
        } catch (rex_sql_exception $e) {
            rex_logger::logException($e);
            return rex_view::error(rex_i18n::msg($table . '_delete_exception'));
        }
        return rex_view::info(rex_i18n::msg($table . '_deleted'));
    }

}