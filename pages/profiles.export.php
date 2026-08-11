<?php
/** @var rex_addon $this */

use Cke5\Handler\Cke5DatabaseHandler;

$func = rex_request::request('func', 'string');
$id = rex_request::request('id', 'int');
$start = rex_request::request('start', 'int', NULL);
$send = rex_request::request('send', 'boolean', false);

$profileTable = rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES);
$message = '';
$profiles = Cke5DatabaseHandler::getAllProfiles();
$csrfToken = rex_csrf_token::factory('cke5_profiles_export');

/**
 * @param array<string,string> $profile
 * @param string $fieldName
 * @return array<int,int>
 */
$collectIds = static function (array $profile, string $fieldName): array {
    if (!isset($profile[$fieldName]) || $profile[$fieldName] === '') {
        return [];
    }

    $ids = array_map('trim', explode('|', (string) $profile[$fieldName]));
    $ids = array_filter($ids, static function ($id): bool {
        return $id !== '';
    });

    return array_values(array_unique(array_map('intval', $ids)));
};

/**
 * @param string $tableName
 * @param array<int,int> $ids
 * @return array<int,array<string,string>>
 */
$loadRows = static function (string $tableName, array $ids): array {
    if ($ids === []) {
        return [];
    }

    $sql = rex_sql::factory();
    $table = rex::getTable($tableName);
    $rows = $sql->getArray('SELECT * FROM ' . $table . ' WHERE id IN (' . implode(', ', $ids) . ') ORDER BY id');

    return is_array($rows) ? $rows : [];
};

/**
 * @param array<int,array<string,string>> $rows
 * @return array<int,string>
 */
$createIdNameMap = static function (array $rows): array {
    $map = [];
    foreach ($rows as $row) {
        $id = isset($row['id']) ? (int) $row['id'] : 0;
        $name = isset($row['name']) ? trim((string) $row['name']) : '';
        if ($id <= 0 || $name === '') {
            continue;
        }

        $map[$id] = $name;
    }

    return $map;
};

/**
 * @param array<string,string> $profile
 * @param string $fieldName
 * @param array<int,string> $idNameMap
 * @return array<int,string>
 */
$resolveRefNames = static function (array $profile, string $fieldName, array $idNameMap) use ($collectIds): array {
    $names = [];
    foreach ($collectIds($profile, $fieldName) as $id) {
        if (isset($idNameMap[$id])) {
            $names[] = $idNameMap[$id];
        }
    }

    return array_values(array_unique($names));
};

// action
if ($func === 'cke5export') {
    try {
        if (!$csrfToken->isValid()) {
            throw new InvalidArgumentException('csrf_token');
        }

        /** @var array<int,string|int> $exportIds */
        $exportIds = rex_request::post('profiles', 'array', []);
        if (!is_array($exportIds) || $exportIds === []) {
            throw new LengthException();
        }

        $exportIds = array_values(array_unique(array_filter(array_map('intval', $exportIds), static function (int $id): bool {
            return $id > 0;
        })));
        if ($exportIds === []) {
            throw new LengthException();
        }

        $exportProfiles = [];
        $exportNames = [];
        $styleGroupIds = [];
        $styleIds = [];
        $snippetIds = [];

        if (!is_null($profiles)) {
            foreach ($profiles as $profile) {
                $profileId = isset($profile['id']) ? (int) $profile['id'] : 0;
                if (!in_array($profileId, $exportIds, true)) {
                    continue;
                }

                $exportProfiles[] = $profile;
                $exportNames[] = (string) ($profile['name'] ?? 'profile');
                $styleGroupIds = array_merge($styleGroupIds, $collectIds($profile, 'group_styles'));
                $styleIds = array_merge($styleIds, $collectIds($profile, 'styles'));
                $snippetIds = array_merge($snippetIds, $collectIds($profile, 'snippets'));
            }
        }

        $exportStyleGroups = $loadRows(Cke5DatabaseHandler::CKE5_STYLE_GROUPS, array_values(array_unique($styleGroupIds)));
        $exportStyles = $loadRows(Cke5DatabaseHandler::CKE5_STYLES, array_values(array_unique($styleIds)));
        $exportSnippets = $loadRows(Cke5DatabaseHandler::CKE5_SNIPPETS, array_values(array_unique($snippetIds)));

        $styleGroupMap = $createIdNameMap($exportStyleGroups);
        $styleMap = $createIdNameMap($exportStyles);
        $snippetMap = $createIdNameMap($exportSnippets);

        foreach ($exportProfiles as &$exportProfile) {
            $exportProfile['group_styles_ref'] = $resolveRefNames($exportProfile, 'group_styles', $styleGroupMap);
            $exportProfile['styles_ref'] = $resolveRefNames($exportProfile, 'styles', $styleMap);
            $exportProfile['snippets_ref'] = $resolveRefNames($exportProfile, 'snippets', $snippetMap);
        }
        unset($exportProfile);

        $exportData = [
            '_meta' => [
                'schema_version' => 2,
                'reference_mode' => 'name-first',
                'exported_at' => date(DATE_ATOM),
            ],
            'profiles' => $exportProfiles,
            'style_groups' => $exportStyleGroups,
            'styles' => $exportStyles,
            'snippets' => $exportSnippets,
        ];

        $joinedNames = implode('_', $exportNames);
        $names = (strlen($joinedNames) > 100) ? substr($joinedNames, 0, 100) . '_etc_' : $joinedNames;
        $fileName = 'cke5_export_' . $names . '_' . date('YmdHis') . '.json';
        header('Content-Disposition: attachment; filename="' . $fileName . '"; charset=utf-8');
        rex_response::sendContent((string) json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 'application/json');
        exit;
    } catch (InvalidArgumentException $e) {
        if ($e->getMessage() === 'csrf_token') {
            $message = rex_view::error(rex_i18n::msg('csrf_token_invalid'));
            $func = 'error';
        } else {
            $message = rex_view::error($this->i18n('profiles_export_error', $e->getMessage()));
            $func = 'error';
        }
    } catch (LengthException $e) {
        $message = rex_view::error($this->i18n('profiles_export_missing_input_error', $e->getMessage()));
        $func = 'error';
    } catch (Exception $e) {
        $message = rex_view::error($this->i18n('profiles_export_error', $e->getMessage()));
        $func = 'error';
    }
}

// get error msg
if ($func === 'error') {
    echo $message;
    $func = '';
}

// get form without action
if ($func === '') {
    $size = (!is_null($profiles) && count($profiles) > 0 && count($profiles) < 10) ? count($profiles) : 10;
    $options = '';
    if (!is_null($profiles) && count($profiles) > 0) {
        foreach ($profiles as $profile) {
            $name = isset($profile['name']) ? (string) $profile['name'] : '';
            $description = isset($profile['description']) ? (string) $profile['description'] : '';
            $id = isset($profile['id']) ? (int) $profile['id'] : 0;
            $options .= '<option value="' . $id . '">' . rex_escape($name . ' [' . $description . ']') . '</option>';
        }
    }

    $formBody = '<fieldset>'
        . '<input type="hidden" name="func" value="cke5export" />'
        . '<div class="form-group">'
        . '<label>' . rex_escape($this->i18n('profiles_select')) . '</label>'
        . '<select class="form-control" name="profiles[]" multiple="multiple" size="' . $size . '">'
        . $options
        . '</select>'
        . '</div>'
        . '</fieldset>';

    $buttonFragment = new rex_fragment();
    $buttonFragment->setVar('elements', [[
        'field' => '<button class="btn btn-save rex-form-aligned" type="submit" value="export"><i class="rex-icon rex-icon-export"></i> ' . rex_escape($this->i18n('export_profiles')) . '</button>'
    ]], false);

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', $this->i18n('profiles_export_title'), false);
    $fragment->setVar('body', $formBody, false);
    $fragment->setVar('buttons', $buttonFragment->parse('core/form/submit.php'), false);
    echo '<form action="' . rex_url::currentBackendPage() . '" method="post">'
        . $csrfToken->getHiddenField()
        . $fragment->parse('core/page/section.php')
        . '</form>';
}