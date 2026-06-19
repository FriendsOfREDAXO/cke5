<?php
/** @var rex_addon $this */

use Cke5\Handler\Cke5DatabaseHandler;

$func = rex_request::request('func', 'string');
$id = rex_request::request('id', 'int');
$start = rex_request::request('start', 'int', NULL);
$send = rex_request::request('send', 'boolean', false);
$message = '';
$profileTable = rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES);
$csrfToken = rex_csrf_token::factory('cke5_profiles_import');
$importResult = [];

$requiredImportKeys = ['name'];

/**
 * @param non-empty-string $tableName
 * @return array<string,bool>
 */
$getTableColumns = static function (string $tableName): array {
    try {
        $table = rex_sql_table::get(rex::getTable($tableName));
        return array_fill_keys(array_keys($table->getColumns()), true);
    } catch (Exception $e) {
        rex_logger::logException($e);
        return [];
    }
};

/**
 * @param non-empty-string $tableName
 * @param array<string,mixed> $rows
 * @param array<string,bool> $columns
 * @return array{old_to_new: array<int,int>, name_to_new: array<string,int>}
 */
$importRowsByName = static function (string $tableName, array $rows, array $columns): array {
    $result = ['old_to_new' => [], 'name_to_new' => []];

    if ($tableName === '') {
        return $result;
    }

    $table = rex::getTable($tableName);

    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }

        $name = isset($row['name']) ? trim((string) $row['name']) : '';
        if ($name === '') {
            continue;
        }

        $existingId = (int) rex_sql::factory()->getValue(
            'SELECT id FROM ' . $table . ' WHERE name = :name LIMIT 1',
            ['name' => $name]
        );

        $sql = rex_sql::factory();
        $sql->setTable($table);

        foreach ($row as $key => $value) {
            if ($key === 'id') {
                continue;
            }

            if ($columns !== [] && !isset($columns[(string) $key])) {
                continue;
            }

            if (is_array($value)) {
                $value = (string) json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            $sql->setValue((string) $key, $value);
        }

        if ($existingId > 0) {
            $sql->setWhere('id = :id', ['id' => $existingId]);
            $sql->update();
            $newId = $existingId;
        } else {
            $sql->insert();
            $newId = (int) $sql->getLastId();
            if ($newId <= 0) {
                $newId = (int) rex_sql::factory()->getValue(
                    'SELECT id FROM ' . $table . ' WHERE name = :name LIMIT 1',
                    ['name' => $name]
                );
            }
        }

        if ($newId > 0) {
            $result['name_to_new'][$name] = $newId;
            $oldId = isset($row['id']) ? (int) $row['id'] : 0;
            if ($oldId > 0) {
                $result['old_to_new'][$oldId] = $newId;
            }
        }
    }

    return $result;
};

/**
 * @param array<string,mixed> $profile
 * @param string $legacyField
 * @param string $refField
 * @param array<string,int> $nameMap
 * @param array<int,int> $oldToNew
 */
$resolveProfileReferenceIds = static function (array $profile, string $legacyField, string $refField, array $nameMap, array $oldToNew): string {
    $ids = [];

    if (isset($profile[$refField]) && is_array($profile[$refField])) {
        foreach ($profile[$refField] as $name) {
            if (!is_string($name)) {
                continue;
            }

            $name = trim($name);
            if ($name === '' || !isset($nameMap[$name])) {
                continue;
            }

            $ids[] = $nameMap[$name];
        }
    }

    if ($ids === [] && isset($profile[$legacyField]) && is_string($profile[$legacyField]) && $profile[$legacyField] !== '') {
        foreach (array_filter(array_map('trim', explode('|', $profile[$legacyField])), static function (string $value): bool {
            return $value !== '';
        }) as $legacyIdString) {
            $legacyId = (int) $legacyIdString;
            if ($legacyId <= 0) {
                continue;
            }

            $ids[] = $oldToNew[$legacyId] ?? $legacyId;
        }
    }

    $ids = array_values(array_unique(array_filter(array_map('intval', $ids), static function (int $id): bool {
        return $id > 0;
    })));

    return implode('|', $ids);
};

// action
if ($func === 'cke5import') {
    try {
        if (!$csrfToken->isValid()) {
            throw new InvalidArgumentException('csrf_token');
        }

        /** @var array<string,array<string,string>> $file */
        $file = rex_request::files('FORM', 'array', []);
        $filename = $file['tmp_name']['importfile'];

        if ($filename === '') {
            throw new InvalidArgumentException($filename);
        }

        $content = rex_file::get($filename);
        $data = json_decode((string)$content, true);

        if (is_array($data)) {
            if (isset($data['profiles']) && is_array($data['profiles'])) {
                $styleGroupColumns = $getTableColumns(Cke5DatabaseHandler::CKE5_STYLE_GROUPS);
                $styleColumns = $getTableColumns(Cke5DatabaseHandler::CKE5_STYLES);
                $snippetColumns = $getTableColumns(Cke5DatabaseHandler::CKE5_SNIPPETS);

                $styleGroupImport = $importRowsByName(
                    Cke5DatabaseHandler::CKE5_STYLE_GROUPS,
                    is_array($data['style_groups'] ?? null) ? $data['style_groups'] : [],
                    $styleGroupColumns
                );
                $styleImport = $importRowsByName(
                    Cke5DatabaseHandler::CKE5_STYLES,
                    is_array($data['styles'] ?? null) ? $data['styles'] : [],
                    $styleColumns
                );
                $snippetImport = $importRowsByName(
                    Cke5DatabaseHandler::CKE5_SNIPPETS,
                    is_array($data['snippets'] ?? null) ? $data['snippets'] : [],
                    $snippetColumns
                );

                foreach ($data['profiles'] as $i => $profile) {
                    $fail = false;
                    foreach ($requiredImportKeys as $key) {
                        if (is_array($profile) && !array_key_exists($key, $profile)) {
                            $importResult[] = rex_view::error(sprintf($this->i18n('profiles_import_validation_fail'), "$i"));
                            $fail = true;
                            break;
                        }
                    }

                    if (!$fail && is_array($profile)) {
                        $profile['group_styles'] = $resolveProfileReferenceIds(
                            $profile,
                            'group_styles',
                            'group_styles_ref',
                            $styleGroupImport['name_to_new'],
                            $styleGroupImport['old_to_new']
                        );
                        $profile['styles'] = $resolveProfileReferenceIds(
                            $profile,
                            'styles',
                            'styles_ref',
                            $styleImport['name_to_new'],
                            $styleImport['old_to_new']
                        );
                        $profile['snippets'] = $resolveProfileReferenceIds(
                            $profile,
                            'snippets',
                            'snippets_ref',
                            $snippetImport['name_to_new'],
                            $snippetImport['old_to_new']
                        );

                        /** @var array<string,string> $profile */
                        $result = Cke5DatabaseHandler::importProfile($profile);
                        $importResult[] = rex_view::info(sprintf($this->i18n('profiles_import_' . (($result === true) ? 'success' : 'fail')), $profile['name'], (string) ($profile['id'] ?? '-')));
                    }
                }
            } else {
                foreach ($data as $i => $profile) {
                    $fail = false;
                    foreach ($requiredImportKeys as $key) {
                        if (is_array($profile) && !array_key_exists($key, $profile)) {
                            $importResult[] = rex_view::error(sprintf($this->i18n('profiles_import_validation_fail'), "$i"));
                            $fail = true;
                            break;
                        }
                    }
                    if (!$fail && is_array($profile)) {
                        /** @var array<string,string> $profile */
                        $result = Cke5DatabaseHandler::importProfile($profile);
                        $importResult[] = rex_view::info(sprintf($this->i18n('profiles_import_' . (($result === true) ? 'success' : 'fail')), $profile['name'], (string) ($profile['id'] ?? '-')));
                    }
                }
            }
        }
        $func = 'imported';
    } catch (InvalidArgumentException $e) {
        if ($e->getMessage() === 'csrf_token') {
            $message = rex_view::error(rex_i18n::msg('csrf_token_invalid'));
            $func = 'error';
        } else {
            $message = rex_view::error($this->i18n('profiles_import_file_missing_error', $e->getMessage()));
            $func = 'error';
        }
    } catch (Exception $e) {
        $message = rex_view::error($this->i18n('profiles_import_error', $e->getMessage()));
        $func = 'error';
    }
}

// get error msg
if ($func === 'error') {
    echo $message;
    $func = '';
}

// get success msg
if ($func === 'imported') {
    echo implode('', $importResult);
    $func = '';
}

// get form without action
if ($func === '') {
    // create form fragment
    $formFragment = new rex_fragment();
    $formFragment->setVar('elements', [[
        'label' => '<label for="rex-form-importdbfile">' . $this->i18n('profiles_import_file') . '</label>',
        'field' => '<input type="file" id="rex-form-importdbfile" name="FORM[importfile]" size="18" />'
    ]], false);

    // create button fragment
    $buttonFragment = new rex_fragment();
    $buttonFragment->setVar('elements', [[
        'field' => '<button class="btn btn-send rex-form-aligned" type="submit" value="import"><i class="rex-icon rex-icon-import"></i> ' . $this->i18n('profiles_import') . '</button>'
    ]], false);

    $msg = rex_view::warning($this->i18n('profiles_import_msg'));

    // create section fragment
    $sectionFragment = new rex_fragment();
    $sectionFragment->setVar('class', 'edit', false);
    $sectionFragment->setVar('title', $this->i18n('profiles_import_title'), false);
    $sectionFragment->setVar('body', '<fieldset><input type="hidden" name="func" value="cke5import" />' . $formFragment->parse('core/form/form.php') . '</fieldset>' . $msg, false); // add form as body to fragment
    $sectionFragment->setVar('buttons', $buttonFragment->parse('core/form/submit.php'), false); // add buttons to fragment

    // add action area and print it out
    echo '<form action="' . rex_url::currentBackendPage() . '" enctype="multipart/form-data" method="post" data-confirm="' . $this->i18n('profiles_proceed_db_import') . '">' . $csrfToken->getHiddenField() . $sectionFragment->parse('core/page/section.php') . '</form>';
}