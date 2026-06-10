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

    $ids = array_map('intval', $ids);
    $ids = array_filter($ids, static function (int $id): bool {
        return $id > 0;
    });

    return array_values(array_unique($ids));
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

// action
if (rex_request::post('_csrf_token', 'string', '') !== '') {
    try {
        /** @var array<string,array<int,string>> $exportIds */
        $exportIds = rex_request::post('profiles', 'array', []);

        if (!isset($exportIds['profiles'])) {
            throw new LengthException();
        }

        // get the export id's
        $exportIds = $exportIds['profiles'];
        $exportProfiles = [];
        $exportNames = [];
        $styleGroupIds = [];
        $styleIds = [];
        $snippetIds = [];

        // and use the loaded profiles
        if (!is_null($profiles)) {
            foreach ($profiles as $profile) {
                foreach ($exportIds as $exportId) {
                    if ($exportId == $profile['id']) {
                        $exportProfiles[] = $profile; // to get the entire stuff
                        $exportNames[] = $profile['name']; // and to get the file name
                        $styleGroupIds = array_merge($styleGroupIds, $collectIds($profile, 'group_styles'));
                        $styleIds = array_merge($styleIds, $collectIds($profile, 'styles'));
                        $snippetIds = array_merge($snippetIds, $collectIds($profile, 'snippets'));
                    }
                }
            }
        }

        $exportStyleGroups = $loadRows(Cke5DatabaseHandler::CKE5_STYLE_GROUPS, array_values(array_unique($styleGroupIds)));
        $exportStyles = $loadRows(Cke5DatabaseHandler::CKE5_STYLES, array_values(array_unique($styleIds)));
        $exportSnippets = $loadRows(Cke5DatabaseHandler::CKE5_SNIPPETS, array_values(array_unique($snippetIds)));

        $exportData = [
            'profiles' => $exportProfiles,
            'style_groups' => $exportStyleGroups,
            'styles' => $exportStyles,
            'snippets' => $exportSnippets,
        ];

        // create filename and export the data set
        $names = implode('_', $exportNames);
        if (strlen($names) > 100) {
            $names = substr($names, 0, 100) . '_etc_';
        }
        $fileName = 'cke5_export_' . $names . '_' . date('YmdHis') . '.json';
        header('Content-Disposition: attachment; filename="' . $fileName . '"; charset=utf-8'); // create header info
        rex_response::sendContent((string) json_encode($exportData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT), 'application/octetstream'); // stream it out
        exit; // stop process
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
    // initialize rex form
    $form = rex_config_form::factory('cke5_export', 'Profiles');

    // add select
    $field = $form->addSelectField('profiles', null, ['class' => 'form-control']);
    $field->setAttribute('multiple', 'multiple');
    $field->setLabel($this->i18n('profiles_select'));
    $select = $field->getSelect();
    // set select size
    $select->setSize((!is_null($profiles) && count($profiles) < 10) ? count($profiles) : 10);

    // add profiles
    if (!is_null($profiles) && count($profiles) > 0) {
        foreach ($profiles as $profile) {
            $select->addOption($profile['name'] . ' [' . $profile['description'] . ']', $profile['id']); // add profile key as option
        }
    }


    $attr = ['type' => 'submit', 'internal::useArraySyntax' => false, 'internal::fieldSeparateEnding' => true];
    $form->addControlField(
        null,
        $form->addField('button', 'save', $this->i18n('export_profiles'), $attr, false)
    );

    $class = '<style>.rex-form-group + .rex-form-panel-footer {display:none}</style>';

    // show
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', $this->i18n('profiles_export_title'), false);
    $fragment->setVar('body', $class . $form->get(), false);
    echo $fragment->parse('core/page/section.php');
}