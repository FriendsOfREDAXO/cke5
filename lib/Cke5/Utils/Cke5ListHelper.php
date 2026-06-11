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
    public static function toggleBoolData(string $table, int $id, ?string $column = null): string
    {
        if ($column === null || $column === '') {
            return rex_view::warning(rex_i18n::msg($table . '_toggle_error'));
        }

        $sql = rex_sql::factory();

        try {
            $fields = $sql->getArray('DESCRIBE ' . $sql->escapeIdentifier($table));
            $allowedColumns = array_column($fields, 'Field');
            if (!in_array($column, $allowedColumns, true)) {
                return rex_view::warning(rex_i18n::msg($table . '_toggle_' . $column . '_error'));
            }

            $sql->setQuery(
                'UPDATE ' . $sql->escapeIdentifier($table)
                . ' SET ' . $sql->escapeIdentifier($column) . ' = ABS(1 - ' . $sql->escapeIdentifier($column) . ')'
                . ' WHERE id = :id',
                ['id' => $id]
            );
        } catch (rex_sql_exception $e) {
            rex_logger::logException($e);
            return rex_view::error(rex_i18n::msg($table . '_toggle_' . $column . '_exception'));
        }

        return rex_view::info(rex_i18n::msg($table . '_toggle_' . $column . '_success'));
    }

    /**
     * clone data
     */
    public static function cloneData(string $table, int $id): string
    {
        try {
            $rows = rex_sql::factory()
                ->setTable($table)
                ->setWhere('id=:id', ['id' => $id])
                ->select()
                ->getArray();

            if (!isset($rows[0]) || !is_array($rows[0])) {
                return rex_view::warning(rex_i18n::msg($table . '_clone_error'));
            }

            $sourceRow = $rows[0];
            unset($sourceRow['id']);
            unset($sourceRow['status']);

            if ([] === $sourceRow) {
                return rex_view::warning(rex_i18n::msg($table . '_clone_error'));
            }

            if (array_key_exists('name', $sourceRow)) {
                $sourceName = (string) $sourceRow['name'];
                $sourceRow['name'] = self::buildUniqueCloneName($table, $sourceName);
            }

            $insert = rex_sql::factory();
            $insert->setTable($table);
            foreach ($sourceRow as $column => $value) {
                if (!is_string($column) || $column === '') {
                    continue;
                }
                $insert->setValue($column, $value);
            }
            $insert->insert();
        } catch (rex_sql_exception $e) {
            rex_logger::logException($e);
            return rex_view::error(rex_i18n::msg($table . '_clone_exception'));
        }
        return rex_view::info(rex_i18n::msg($table . '_cloned'));
    }

    private static function buildUniqueCloneName(string $table, string $sourceName): string
    {
        $baseName = trim($sourceName) !== '' ? trim($sourceName) : 'profile';
        $candidate = $baseName . '_copy';
        $counter = 2;
        $sql = rex_sql::factory();

        while ((int) $sql->getValue(
            'SELECT COUNT(*) FROM ' . $sql->escapeIdentifier($table) . ' WHERE name = ?',
            [$candidate]
        ) > 0) {
            $candidate = $baseName . '_copy_' . $counter;
            ++$counter;
        }

        return $candidate;
    }

    /**
     * delete data
     */
    public static function deleteData(string $table, int $id): string
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