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
     * Builds the export bundle for the given profile ids.
     *
     * Same structure importBundle() reads back: styles, style groups and
     * snippets travel with the profiles, and the profiles reference them by
     * name (*_ref) so the ids of the target installation do not matter.
     *
     * @param array<int,int> $profileIds
     * @return array<string,mixed>
     */
    public static function exportBundle(array $profileIds): array
    {
        $profileIds = array_values(array_unique(array_filter(array_map('intval', $profileIds), static function (int $id): bool {
            return $id > 0;
        })));

        $profiles = [];
        $styleGroupIds = [];
        $styleIds = [];
        $snippetIds = [];

        foreach (Cke5DatabaseHandler::getAllProfiles() ?: [] as $profile) {
            if (!is_array($profile) || !in_array((int) ($profile['id'] ?? 0), $profileIds, true)) {
                continue;
            }

            $profiles[] = $profile;
            $styleGroupIds = array_merge($styleGroupIds, self::collectIds($profile, 'group_styles'));
            $styleIds = array_merge($styleIds, self::collectIds($profile, 'styles'));
            $snippetIds = array_merge($snippetIds, self::collectIds($profile, 'snippets'));
        }

        $styleGroups = self::loadRowsById(Cke5DatabaseHandler::CKE5_STYLE_GROUPS, array_values(array_unique($styleGroupIds)));
        $styles = self::loadRowsById(Cke5DatabaseHandler::CKE5_STYLES, array_values(array_unique($styleIds)));
        $snippets = self::loadRowsById(Cke5DatabaseHandler::CKE5_SNIPPETS, array_values(array_unique($snippetIds)));

        $styleGroupMap = self::createIdNameMap($styleGroups);
        $styleMap = self::createIdNameMap($styles);
        $snippetMap = self::createIdNameMap($snippets);

        foreach ($profiles as &$profile) {
            $profile['group_styles_ref'] = self::resolveNames($profile, 'group_styles', $styleGroupMap);
            $profile['styles_ref'] = self::resolveNames($profile, 'styles', $styleMap);
            $profile['snippets_ref'] = self::resolveNames($profile, 'snippets', $snippetMap);
        }
        unset($profile);

        return [
            '_meta' => [
                'schema_version' => 2,
                'reference_mode' => 'name-first',
                'exported_at' => date(DATE_ATOM),
            ],
            'profiles' => $profiles,
            'style_groups' => $styleGroups,
            'styles' => $styles,
            'snippets' => $snippets,
        ];
    }

    /**
     * @param array<string,mixed> $profile
     * @return array<int,int>
     */
    private static function collectIds(array $profile, string $fieldName): array
    {
        if (!isset($profile[$fieldName]) || $profile[$fieldName] === '') {
            return [];
        }

        $ids = array_filter(array_map('trim', explode('|', (string) $profile[$fieldName])), static function (string $id): bool {
            return $id !== '';
        });

        return array_values(array_unique(array_map('intval', $ids)));
    }

    /**
     * @param array<int,int> $ids
     * @return array<int,array<string,mixed>>
     */
    private static function loadRowsById(string $tableName, array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        $rows = rex_sql::factory()->getArray(
            'SELECT * FROM ' . rex::getTable($tableName) . ' WHERE id IN (' . implode(', ', $ids) . ') ORDER BY id',
        );

        return is_array($rows) ? $rows : [];
    }

    /**
     * @param array<int,array<string,mixed>> $rows
     * @return array<int,string>
     */
    private static function createIdNameMap(array $rows): array
    {
        $map = [];

        foreach ($rows as $row) {
            $id = isset($row['id']) ? (int) $row['id'] : 0;
            $name = isset($row['name']) ? trim((string) $row['name']) : '';

            if ($id > 0 && $name !== '') {
                $map[$id] = $name;
            }
        }

        return $map;
    }

    /**
     * @param array<string,mixed> $profile
     * @param array<int,string> $idNameMap
     * @return array<int,string>
     */
    private static function resolveNames(array $profile, string $fieldName, array $idNameMap): array
    {
        $names = [];

        foreach (self::collectIds($profile, $fieldName) as $id) {
            if (isset($idNameMap[$id])) {
                $names[] = $idNameMap[$id];
            }
        }

        return array_values(array_unique($names));
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