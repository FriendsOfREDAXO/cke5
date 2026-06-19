<?php

namespace Cke5\Handler;

use Cke5\Utils\Cke5CssHandler;
use rex;
use rex_file;
use rex_logger;
use rex_sql;
use Throwable;

class Cke5DefaultDataService
{
    /**
     * @param array<int,string> $overwriteProfileNames
     */
    public static function importBundle(string $bundlePath, array $overwriteProfileNames = []): void
    {
        try {
            $content = rex_file::get($bundlePath);
            if (!is_string($content) || $content === '') {
                return;
            }

            $data = json_decode($content, true);
            if (!is_array($data)) {
                return;
            }

            $styleGroupMap = self::importNamedRows(Cke5DatabaseHandler::CKE5_STYLE_GROUPS, self::getRows($data, 'style_groups'), true);
            $styleMap = self::importNamedRows(Cke5DatabaseHandler::CKE5_STYLES, self::getRows($data, 'styles'), true);
            $snippetMap = self::importNamedRows(Cke5DatabaseHandler::CKE5_SNIPPETS, self::getRows($data, 'snippets'), true);

            foreach (self::getRows($data, 'profiles') as $profile) {
                $profile = self::normalizeProfileRow($profile, $styleGroupMap, $styleMap, $snippetMap);
                $name = isset($profile['name']) && is_string($profile['name']) ? trim($profile['name']) : '';
                if ($name === '') {
                    continue;
                }

                $existingProfile = Cke5DatabaseHandler::loadProfile($name);
                if (is_array($existingProfile)) {
                    if (!in_array($name, $overwriteProfileNames, true)) {
                        continue;
                    }

                    self::deleteRowByName(Cke5DatabaseHandler::CKE5_PROFILES, $name);
                }

                Cke5DatabaseHandler::importProfile($profile);
            }

            Cke5CssHandler::regenerateCssFile();
        } catch (Throwable $exception) {
            rex_logger::logException($exception);
        }
    }

    /**
     * @param array<string,mixed> $data
     * @return array<int,array<string,mixed>>
     */
    private static function getRows(array $data, string $key): array
    {
        $rows = $data[$key] ?? [];

        return is_array($rows) ? array_values(array_filter($rows, 'is_array')) : [];
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     * @return array<string,int>
     */
    private static function importNamedRows(string $tableName, array $rows, bool $overwriteExisting): array
    {
        $idMap = [];

        foreach ($rows as $row) {
            $name = isset($row['name']) && is_string($row['name']) ? trim($row['name']) : '';
            if ($name === '') {
                continue;
            }

            $existingId = self::findIdByName($tableName, $name);
            if ($existingId > 0 && !$overwriteExisting) {
                $idMap[$name] = $existingId;
                continue;
            }

            if ($existingId > 0) {
                self::deleteRowByName($tableName, $name);
            }

            $sql = rex_sql::factory();
            $sql->setTable(rex::getTable($tableName));
            foreach ($row as $key => $value) {
                if ($key === 'id') {
                    continue;
                }

                if (is_array($value)) {
                    $value = (string) json_encode($value, JSON_UNESCAPED_UNICODE);
                }

                $sql->setValue((string) $key, $value);
            }
            $sql->insert();

            $id = self::findIdByName($tableName, $name);
            if ($id > 0) {
                $idMap[$name] = $id;
            }
        }

        return $idMap;
    }

    /**
     * @param array<string,mixed> $profile
     * @param array<string,int> $styleGroupMap
     * @param array<string,int> $styleMap
     * @param array<string,int> $snippetMap
     * @return array<string,mixed>
     */
    private static function normalizeProfileRow(array $profile, array $styleGroupMap, array $styleMap, array $snippetMap): array
    {
        if (isset($profile['group_styles_ref']) && is_array($profile['group_styles_ref'])) {
            $profile['group_styles'] = self::resolveReferences($profile['group_styles_ref'], $styleGroupMap);
            unset($profile['group_styles_ref']);
        }

        if (isset($profile['styles_ref']) && is_array($profile['styles_ref'])) {
            $profile['styles'] = self::resolveReferences($profile['styles_ref'], $styleMap);
            unset($profile['styles_ref']);
        }

        if (isset($profile['snippets_ref']) && is_array($profile['snippets_ref'])) {
            $profile['snippets'] = self::resolveReferences($profile['snippets_ref'], $snippetMap);
            unset($profile['snippets_ref']);
        }

        foreach (array_keys($profile) as $key) {
            if (!is_string($key) || substr($key, -5) !== '_data') {
                continue;
            }

            $baseKey = substr($key, 0, -5);
            $profile[$baseKey] = (string) json_encode($profile[$key], JSON_UNESCAPED_UNICODE);
            unset($profile[$key]);
        }

        if (isset($profile['expert_definition']) && is_string($profile['expert_definition']) && $profile['expert_definition'] !== '') {
            $profile['expert'] = '|expert_definition|';
        }

        return $profile;
    }

    /**
     * @param array<int,mixed> $references
     * @param array<string,int> $idMap
     */
    private static function resolveReferences(array $references, array $idMap): string
    {
        $ids = [];

        foreach ($references as $reference) {
            if (!is_string($reference) || !isset($idMap[$reference])) {
                continue;
            }

            $ids[] = (string) $idMap[$reference];
        }

        return implode('|', $ids);
    }

    private static function findIdByName(string $tableName, string $name): int
    {
        $sql = rex_sql::factory();
        $row = $sql->getArray('SELECT id FROM ' . rex::getTable($tableName) . ' WHERE name = :name LIMIT 1', ['name' => $name]);

        return isset($row[0]['id']) ? (int) $row[0]['id'] : 0;
    }

    private static function deleteRowByName(string $tableName, string $name): void
    {
        $sql = rex_sql::factory();
        $sql->setTable(rex::getTable($tableName));
        $sql->setWhere('name = :name', ['name' => $name]);
        $sql->delete();
    }
}