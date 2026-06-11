<?php
/** @var rex_addon $this */

use Cke5\Creator\Cke5ProfilesCreator;
use Cke5\Handler\Cke5DatabaseHandler;
use Cke5\Utils\Cke5FormHelper;
use Cke5\Utils\CKE5ISO6391;
use Cke5\Utils\Cke5Lang;
use Cke5\Utils\Cke5ListHelper;
use Cke5\Utils\Cke5PreviewHelper;

$func = rex_request::request('func', 'string');
/** @var int $id */
$id = rex_request::request('id', 'int');
$profileKey = trim(rex_request::request('profile', 'string', ''));
/** @var int $start */
$start = rex_request::request('start', 'int', NULL);
$send = rex_request::request('send', 'boolean', false);

$profileTable = rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES);
$stylesTable = rex::getTable(Cke5DatabaseHandler::CKE5_STYLES);
$snippetsTable = rex::getTable(Cke5DatabaseHandler::CKE5_SNIPPETS);
$message = '';

$protectedDemoDefaultId = (int) rex_sql::factory()->getValue(
    'SELECT id FROM ' . $profileTable . ' WHERE name = ? ORDER BY id ASC LIMIT 1',
    ['demo_default']
);

if ($func === 'clone') {
    $message = Cke5ListHelper::cloneData($profileTable, $id);
    rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_CLONE', $id));
    $func = '';
}


if ($func === 'delete') {
    $profileName = (string) rex_sql::factory()->getValue(
        'SELECT name FROM ' . $profileTable . ' WHERE id = ? LIMIT 1',
        [$id]
    );

    if ($profileName === 'demo_default' && $id === $protectedDemoDefaultId) {
        $message = rex_view::error('Das Profil "demo_default" ist geschützt und kann nicht gelöscht werden.');
    } else {
        $message = Cke5ListHelper::deleteData($profileTable, $id);
        rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_DELETE', $id));
    }

    $func = '';
}

if ($func === '') {
    // Profil-Infos vorab laden (außerhalb rex_list, um Spalten-Konflikte zu vermeiden)
    $profileInfos = [];
    $profileIdByName = [];
    foreach (rex_sql::factory()->getArray("SELECT id, name, editor_type, balloon_toolbar_custom, paste_plain_text_default, markdown_paste, lang, ytable, document_title FROM $profileTable") as $row) {
        $profileInfos[(int)$row['id']] = $row;
        $profileName = (string) ($row['name'] ?? '');
        if ($profileName !== '') {
            $profileIdByName[$profileName] = (int) $row['id'];
        }
    }

    // instance list
    $list = rex_list::factory("SELECT id, name, description FROM $profileTable ORDER BY id");
    $list->addTableAttribute('class', 'table-striped');

    $list->addTableColumnGroup([40, 180, '*', 150]);

    $list->removeColumn('id');

    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('cke5_add_profile') . '"><i class="rex-icon rex-icon-add-action"></i></a>';
    $tdIcon = '<i class="rex-icon fa-cube"></i>';

    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnFormat($thIcon, 'custom', static function ($params) use ($start, $profileIdByName) {
        $list = $params['list'];
        $name = (string) $list->getValue('name');
        $id = $profileIdByName[$name] ?? 0;
        $urlParams = ['func' => 'edit', 'start' => $start];
        if ($id > 0) {
            $urlParams['id'] = $id;
            $urlParams['profile'] = $name;
        } else {
            $urlParams['profile'] = $name;
        }
        $url = $list->getUrl($urlParams);

        return '<a href="' . $url . '" title="' . rex_i18n::msg('edit') . '"><i class="rex-icon fa-cube"></i></a>';
    });

    $list->setColumnLabel('name', rex_i18n::msg('cke5_name'));
    $list->setColumnFormat('name', 'custom', static function ($params) use ($start, $profileIdByName) {
        $list = $params['list'];
        $name = (string) $list->getValue('name');
        $id = $profileIdByName[$name] ?? 0;
        $urlParams = ['func' => 'edit', 'start' => $start];
        if ($id > 0) {
            $urlParams['id'] = $id;
            $urlParams['profile'] = $name;
        } else {
            $urlParams['profile'] = $name;
        }
        $url = $list->getUrl($urlParams);

        return '<a href="' . $url . '">' . rex_escape($name) . '</a>';
    });

    $list->setColumnLabel('description', rex_i18n::msg('cke5_description'));
    $list->setColumnFormat('description', 'custom', static function ($params) use ($profileInfos) {
        $list = $params['list'];
        $desc = (string) $list->getValue('description');
        $id   = (int) $list->getValue('id');
        $info = $profileInfos[$id] ?? [];

        $isEnabled = static function ($value): bool {
            $v = trim((string) $value);
            if ($v === '') {
                return false;
            }

            // Platzhalterwerte wie |default_height| gelten nicht als aktiv.
            if (str_starts_with($v, '|') && str_ends_with($v, '|')) {
                return false;
            }

            $normalized = strtolower($v);
            return in_array($normalized, ['1', 'true', 'yes', 'on', 'enabled'], true)
                || !in_array($normalized, ['0', 'false', 'no', 'off', 'disabled'], true);
        };

        $out = '<div>';
        if ($desc !== '') {
            $out .= '<div style="margin-bottom:5px;">' . rex_escape($desc) . '</div>';
        }

        // Relevante Profil-Infos als Badges
        $badges = [];

        $editorType = trim((string) ($info['editor_type'] ?? ''));
        if ($editorType !== '') {
            $badges[] = '<span class="label label-primary"><i class="rex-icon fa-pencil"></i> Editor-Typ: ' . rex_escape($editorType) . '</span>';
        }

        $hasBalloonCustom = trim((string) ($info['balloon_toolbar_custom'] ?? '')) !== '';
        if ($hasBalloonCustom) {
            $badges[] = '<span class="label label-success"><i class="rex-icon fa-list-alt"></i> Eigene Balloon-Toolbar</span>';
        }

        $pastePlainText = $isEnabled($info['paste_plain_text_default'] ?? null);
        if ($pastePlainText) {
            $badges[] = '<span class="label label-success"><i class="rex-icon fa-paste"></i> Unformatiert einfügen</span>';
        }

        $markdownPaste = $isEnabled($info['markdown_paste'] ?? null);
        if ($markdownPaste) {
            $badges[] = '<span class="label label-success"><i class="rex-icon fa-markdown"></i> Markdown beim Einfügen</span>';
        }

        $lang = trim((string) ($info['lang'] ?? ''));
        if ($lang !== '' && strtolower($lang) !== 'auto') {
            $badges[] = '<span class="label label-info"><i class="rex-icon fa-language"></i> UI-Sprache: ' . rex_escape(strtoupper($lang)) . '</span>';
        }

        $hasYtable = trim((string) ($info['ytable'] ?? '')) !== '';
        if ($hasYtable) {
            $badges[] = '<span class="label label-success"><i class="rex-icon fa-table"></i> yTables</span>';
        }

        $hasDocumentTitle = $isEnabled($info['document_title'] ?? null);
        if ($hasDocumentTitle) {
            $badges[] = '<span class="label label-warning"><i class="rex-icon fa-header"></i> Dokumenttitel-Plugin</span>';
        }

        if ($badges !== []) {
            $out .= '<div style="margin-top:3px;line-height:1.8;">' . implode(' ', $badges) . '</div>';
        }

        $out .= '</div>';
        return $out;
    });

    // kompakte Aktionsspalte: Edit-Button + Dropdown (Clone / Delete)
    $list->addColumn('actions', '', -1, ['', '<td>###VALUE###</td>']);
    $list->setColumnLabel('actions', rex_i18n::msg('cke5_list_function'));
    $list->setColumnFormat('actions', 'custom', static function ($params) use ($start, $protectedDemoDefaultId, $profileIdByName) {
        $list = $params['list'];
        $name = (string) $list->getValue('name');
        $id = $profileIdByName[$name] ?? 0;

        $editParams = ['func' => 'edit', 'start' => $start];
        if ($id > 0) {
            $editParams['id'] = $id;
            $editParams['profile'] = $name;
        } else {
            $editParams['profile'] = $name;
        }
        $editUrl = $list->getUrl($editParams);
        $cloneUrl  = $list->getUrl(['func' => 'clone',  'id' => $id,        'start' => $start]);
        $deleteUrl = $list->getUrl(['func' => 'delete', 'id' => $id,        'start' => $start]);

        $btnGroup  = '<div class="btn-group">';
        $btnGroup .= '<a class="btn btn-xs btn-default" href="' . $editUrl . '"><i class="rex-icon fa-pencil-square-o"></i> ' . rex_i18n::msg('edit') . '</a>';
        $btnGroup .= '<button type="button" class="btn btn-xs btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span></button>';
        $btnGroup .= '<ul class="dropdown-menu dropdown-menu-right">';
        if ($id > 0) {
            $btnGroup .= '<li><a href="' . $cloneUrl . '" data-confirm="' . rex_i18n::msg('cke5_clone') . ' ?">'
                . '<i class="rex-icon fa-clone"></i> ' . rex_i18n::msg('cke5_clone') . '</a></li>';
        }

        if ($id > 0 && ($name !== 'demo_default' || $id !== $protectedDemoDefaultId)) {
            $btnGroup .= '<li class="divider"></li>';
            $btnGroup .= '<li><a href="' . $deleteUrl . '" data-confirm="' . rex_i18n::msg('delete') . ' ?">'
                . '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete') . '</a></li>';
        }

        $btnGroup .= '</ul></div>';

        return $btnGroup;
    }, []);

    $content = $list->get();
    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('cke5_list_profiles'));
    $fragment->setVar('content', $message . $content, false);
    echo $fragment->parse('core/page/section.php');

} elseif ($func === 'edit' || $func === 'add') {

    $id = rex_request('id', 'int');
    $profileKey = trim(rex_request('profile', 'string', ''));
    $resolvedProfileRow = null;

    if ($func === 'edit') {
        // Backward compatibility for older links using ?profile=<name>
        if ($id <= 0 && $profileKey !== '') {
            try {
                $profileRows = rex_sql::factory()->getArray(
                    'SELECT id FROM ' . $profileTable . ' WHERE name = ? LIMIT 1',
                    [$profileKey]
                );
            } catch (rex_sql_exception $e) {
                $profileRows = [];
            }

            if (!isset($profileRows[0]['id'])) {
                echo rex_view::error('Das gewünschte Profil wurde nicht gefunden (Key: ' . htmlspecialchars($profileKey) . '). Bitte öffne die Profil-Liste erneut.');
                return;
            }

            $id = (int) $profileRows[0]['id'];
        }

        if ($id <= 0) {
            echo rex_view::error('Ungültige Profil-ID. Bitte öffne die Profil-Liste erneut.');
            return;
        }

        $rowsById = rex_sql::factory()->getArray(
            'SELECT * FROM ' . $profileTable . ' WHERE id = ? LIMIT 1',
            [$id]
        );

        if (isset($rowsById[0]) && is_array($rowsById[0])) {
            $resolvedProfileRow = $rowsById[0];
            $profileKey = (string) ($resolvedProfileRow['name'] ?? $profileKey);
        } elseif ($profileKey !== '') {
            $rowsByName = rex_sql::factory()->getArray(
                'SELECT * FROM ' . $profileTable . ' WHERE name = ? LIMIT 1',
                [$profileKey]
            );

            if (isset($rowsByName[0]) && is_array($rowsByName[0])) {
                $resolvedProfileRow = $rowsByName[0];
                $id = (int) ($resolvedProfileRow['id'] ?? 0);
            }
        } else {
            $rowsDefault = rex_sql::factory()->getArray(
                'SELECT * FROM ' . $profileTable . ' WHERE name = ? LIMIT 1',
                ['demo_default']
            );

            if (isset($rowsDefault[0]) && is_array($rowsDefault[0])) {
                $resolvedProfileRow = $rowsDefault[0];
                $id = (int) ($resolvedProfileRow['id'] ?? 0);
                $profileKey = (string) ($resolvedProfileRow['name'] ?? '');
            }

            if (!is_array($resolvedProfileRow) || $id <= 0) {
                $rowsFirst = rex_sql::factory()->getArray(
                    'SELECT * FROM ' . $profileTable . ' ORDER BY id ASC LIMIT 1'
                );

                if (isset($rowsFirst[0]) && is_array($rowsFirst[0])) {
                    $resolvedProfileRow = $rowsFirst[0];
                    $id = (int) ($resolvedProfileRow['id'] ?? 0);
                    $profileKey = (string) ($resolvedProfileRow['name'] ?? '');
                }
            }
        }

        if (!is_array($resolvedProfileRow) || $id <= 0) {
            if ($profileKey !== '') {
                echo rex_view::error('Das gewünschte Profil wurde nicht gefunden (Key: ' . htmlspecialchars($profileKey) . '). Bitte öffne die Profil-Liste erneut.');
            } else {
                echo rex_view::error('Das gewünschte Profil wurde nicht gefunden (ID: ' . $id . '). Bitte öffne die Profil-Liste erneut.');
            }
            return;
        }
    }

    $form = rex_form::factory($profileTable, '', 'id=' . $id, 'post');
    $form->addParam('start', $start);
    $form->addParam('send', true);

    $result = array();
    $prefix = '';

    if ($func === 'edit') {
        $form->addParam('id', $id);
        $result = rex_request::post($form->getName(), 'array', 'null');

        if ($send && $id === $protectedDemoDefaultId && is_array($result)) {
            $postedName = trim((string) ($result['name'] ?? ''));
            if ($postedName !== '' && $postedName !== 'demo_default') {
                $result['name'] = 'demo_default';

                $formName = $form->getName();
                if (isset($_POST[$formName]) && is_array($_POST[$formName])) {
                    $_POST[$formName]['name'] = 'demo_default';
                }
            }
        }

        if (!is_array($result)) {
            $result = is_array($resolvedProfileRow) ? $resolvedProfileRow : [];
            if ([] === $result) {
                echo rex_view::error('Das gewünschte Profil wurde nicht gefunden (ID: ' . $id . '). Bitte öffne die Profil-Liste erneut.');
                return;
            }
            $prefix = rex::getTable('cke5_profiles') . '.';
        }
    }

    $default_value = $func === 'add' && $send === false;
    $resultValue = static function (array $row, string $field, string $fieldPrefix = '') {
        if ('' !== $fieldPrefix && array_key_exists($fieldPrefix . $field, $row)) {
            return $row[$fieldPrefix . $field];
        }

        return $row[$field] ?? null;
    };

    $min_height = is_array($result) ? intval((string) ($resultValue($result, 'min_height', $prefix) ?? '0')) : 0;
    $max_height = is_array($result) ? intval((string) ($resultValue($result, 'max_height', $prefix) ?? '0')) : 0;
    $profile = is_array($result) ? (string) ($resultValue($result, 'name', $prefix) ?? '') : '';
    $isProtectedDemoDefault = $func === 'edit' && strtolower($profile) === 'demo_default';

    $mediaPathValue = is_array($result) ? $resultValue($result, 'mediapath', $prefix) : null;
    $mediaPath = is_string($mediaPathValue) && '' !== $mediaPathValue ? $mediaPathValue : str_replace(['../', '/'], '', rex_url::media());

    $expertValue = is_array($result) ? $resultValue($result, 'expert', $prefix) : null;
    $expert = is_string($expertValue) && '' !== $expertValue;

    // wrapper
    $form->addRawField('<div class="cke5_wrap_rex_profile_data">');
        // name
        $field = $form->addTextField('name');
        $field->setAttribute('id', 'cke5name-input');
        $field->setLabel(rex_i18n::msg('cke5_name'));
        $field->setAttribute('placeholder', rex_i18n::msg('cke5_name_placeholder'));
        $field->getValidator()->add('notEmpty', rex_i18n::msg('cke5_profile_name_empty_error'));
        if ($isProtectedDemoDefault) {
            $field->setAttribute('readonly', 'readonly');
            $field->setAttribute('disabled', 'disabled');
            $field->setNotice('Das Profil "demo_default" ist geschützt und kann nicht umbenannt werden.');

            $hiddenNameField = $form->addHiddenField('name');
            $hiddenNameField->setValue('demo_default');
        }

        // description
        $field = $form->addTextField('description');
        $field->setLabel(rex_i18n::msg('cke5_description'));
        $field->setAttribute('placeholder', rex_i18n::msg('cke5_description_placeholder'));
        $field->getValidator()->add('notEmpty', rex_i18n::msg('cke5_description_empty_error'));

        // custom area for experts
        $field = $form->addCheckboxField('expert');
        $field->setAttribute('id', 'cke5-expert-toggle');
        $field->setAttribute('data-toggle', 'toggle');
        $field->setLabel(rex_i18n::msg('cke5_expert_definition'));
        $field->addOption(rex_i18n::msg('cke5_expert_definition_description'), 'expert_definition');

        // expert area
        $form->addRawField('<div class="collapse '.(($expert) ? 'in' : '').'" id="cke5expertDefinition-collapse">');
            // text area
            $field = $form->addTextAreaField('expert_definition');
            $field->setAttribute('id', 'cke5-expert-definition-area');
            $field->setAttribute('class', 'rex-code');
            $field->setAttribute('rows', '2');
            $field->setLabel(rex_i18n::msg('cke5_expert_definition_area'));
            // text area
            $field = $form->addTextAreaField('expert_suboption');
            $field->setAttribute('id', 'cke5-expert-suboption-area');
            $field->setAttribute('class', 'rex-code');
            $field->setAttribute('rows', '2');
            $field->setLabel(rex_i18n::msg('cke5_expert_suboption_area'));
        // end collapse
        $form->addRawField('</div>');
    // end wrapper
    $form->addRawField('</div>');

    // profile editor wrapper
    $form->addRawField('<div class="collapse '.(($expert) ? '' : 'in').'" id="cke5profileEditor-collapse">');
        $locales = rex_i18n::getLocales();
        asort($locales);

        $form->addRawField('<div class="cke5-placeholder-scope-note text-muted"><small>' . rex_i18n::msg('cke5_placeholder_tabs_scope_note') . '</small></div>');

        Cke5FormHelper::addRexLangTabs($form, 'wrapper', rex_i18n::getLocale());
        foreach ($locales as $locale) {
            Cke5FormHelper::addRexLangTabs($form, 'inner_wrapper', $locale, rex_i18n::getLocale());
            $field = $form->addTextField('placeholder_' . $locale);
            $field->setLabel(rex_i18n::msg('cke5_placeholder'));
            $field->setAttribute('placeholder', rex_i18n::msg('cke5_placeholder_placeholder') . ' ' . rex_i18n::msgInLocale('lang', $locale));
            Cke5FormHelper::addRexLangTabs($form, 'close_inner_wrapper');
        }
        Cke5FormHelper::addRexLangTabs($form, 'close_wrapper');
        $form->addRawField('<p class="cke5-profile-settings-note text-muted"><small>' . rex_i18n::msg('cke5_profile_global_settings_note') . '</small></p>');

// TOOLBAR
        $form->addRawField('<fieldset><legend>'.rex_i18n::msg('cke5_toolbar').'</legend>');
            $field = $form->addSelectField('editor_type');
            $field->setAttribute('id', 'cke5editor-type-select');
            $field->setLabel(rex_i18n::msg('cke5_editor_type'));
            $field->setNotice(rex_i18n::msg('cke5_editor_type_notice'));
            $field->getSelect()->addOption(rex_i18n::msg('cke5_editor_type_classic'), Cke5ProfilesCreator::EDITOR_TYPES['classic']);
            $field->getSelect()->addOption(rex_i18n::msg('cke5_editor_type_classic_balloon'), Cke5ProfilesCreator::EDITOR_TYPES['classic_balloon']);
            $field->getSelect()->addOption(rex_i18n::msg('cke5_editor_type_balloon_block'), Cke5ProfilesCreator::EDITOR_TYPES['balloon_block']);
            if ($default_value) {
                $field->setValue(Cke5ProfilesCreator::EDITOR_TYPES['classic']);
            }

            // toolbar
            $toolbarItems = Cke5FormHelper::potentialRemoveLicenseItems(Cke5ProfilesCreator::ALLOWED_FIELDS['toolbar'], Cke5ProfilesCreator::LICENSE_FIELDS['toolbar']);

            $field = $form->addTextField('toolbar');
            $field->setAttribute('id', 'cke5toolbar-input');
            $field->setAttribute('data-tag-init', 1);
            $field->setAttribute('data-tags', '["' . implode('","', $toolbarItems) . '"]');
            $field->setLabel(rex_i18n::msg('cke5_toolbar_elements'));
            if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['toolbar']);

            $field = $form->addCheckboxField('balloon_toolbar_custom');
            $field->setAttribute('id', 'cke5balloon-toolbar-custom-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setAttribute('data-collapse-target', 'balloonToolbar');
            $field->setLabel(rex_i18n::msg('cke5_balloon_toolbar_custom'));
            $field->addOption(rex_i18n::msg('cke5_balloon_toolbar_custom_description'), 'balloon_toolbar_custom');
            if ($default_value) {
                $field->setValue('');
            }

            $form->addRawField('<div class="collapse" id="cke5balloonToolbar-collapse">');
            $field = $form->addTextField('balloon_toolbar');
            $field->setAttribute('id', 'cke5balloon-toolbar-input');
            $field->setAttribute('data-tag-init', 1);
            $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['balloon_toolbar']) . '"]');
            $field->setLabel(rex_i18n::msg('cke5_balloon_toolbar_elements'));
            $form->addRawField('</div>');

            // group when full
            $field = $form->addCheckboxField('group_when_full');
            $field->setAttribute('id', 'cke5group-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setLabel(rex_i18n::msg('cke5_group_when_full_default'));
            $field->addOption(rex_i18n::msg('cke5_group_when_full_description'), 'group_when_full');
            if ($default_value) $field->setValue('group_when_full');

            // paste plain text default
            $field = $form->addCheckboxField('paste_plain_text_default');
            $field->setAttribute('id', 'cke5pasteplaintextdefault-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setLabel(rex_i18n::msg('cke5_paste_plain_text_default'));
            $field->addOption(rex_i18n::msg('cke5_paste_plain_text_default_description'), 'paste_plain_text_default');
            if ($default_value) {
                $field->setValue('paste_plain_text_default');
            }

            // markdown paste
            $field = $form->addCheckboxField('markdown_paste');
            $field->setAttribute('id', 'cke5markdownpaste-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setLabel(rex_i18n::msg('cke5_markdown_paste'));
            $field->addOption(rex_i18n::msg('cke5_markdown_paste_description'), 'markdown_paste');
            if ($default_value) {
                $field->setValue('');
            }

            // minimap
            $field = $form->addCheckboxField('minimap');
            $field->setAttribute('id', 'cke5minimap-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setLabel(rex_i18n::msg('cke5_minimap'));
            $field->addOption(rex_i18n::msg('cke5_minimap_description'), 'minimap');
            if ($default_value) {
                $field->setValue('');
            }

            // text part lang
            $form->addRawField('<div class="collapse" id="cke5textPartLanguage-collapse">');
                $field = $form->addSelectField('text_part_language');
                $field->setAttribute('class', 'form-control selectpicker');
                $field->setAttribute('data-live-search', 'true');
                $field->setAttribute('multiple', 'multiple');
                $field->setLabel(rex_i18n::msg('cke5_textpartlanguage'));
                $field->getSelect()->addOption('default', '');
                foreach (CKE5ISO6391::$isolang as $key => $value) {
                    $field->getSelect()->addOption($value . ' [' . $key . ']', $key);
                }
            $form->addRawField('</div>');

        $form->addRawField('</fieldset>');

// STYLES
    $form->addRawField('<div class="collapse" id="cke5style-collapse">');
        $form->addRawField('<fieldset><legend>'.rex_i18n::msg('cke5_style').'</legend>');

                $field = $form->addSelectField('group_styles');
                $field->setAttribute('class', 'form-control selectpicker');
                $field->setAttribute('data-live-search', 'true');
                $field->setAttribute('multiple', 'multiple');
                $field->setLabel(rex_i18n::msg('cke5_style_groups'));

                // Laden der Style Groups aus der Datenbank
                $stylesGroupTable = rex::getTable(Cke5DatabaseHandler::CKE5_STYLE_GROUPS);
                $sql = rex_sql::factory();
                $sqlResult = $sql->getArray("select id, name from $stylesGroupTable");

                foreach ($sqlResult as $group) {
                    $field->getSelect()->addOption($group['name'] . ' [' . $group['id'] . ']', $group['id']);
                }

                $field = $form->addSelectField('styles');
                $field->setAttribute('class', 'form-control selectpicker');
                $field->setAttribute('data-live-search', 'true');
                $field->setAttribute('multiple', 'multiple');
                $field->setLabel(rex_i18n::msg('cke5_style'));
                $sql = rex_sql::factory();
                $sqlResult = $sql->getArray('select id, name from ' . $stylesTable);
                foreach ($sqlResult as $key => $value) {
                    $field->getSelect()->addOption($value['name'] . ' [' . $value['id'] . ']', $value['id']);
                }
        $form->addRawField('</fieldset>');
    $form->addRawField('</div>');

// SNIPPETS
    $form->addRawField('<div class="collapse" id="cke5snippets-collapse">');
        $form->addRawField('<fieldset><legend>' . rex_i18n::msg('cke5_snippets') . '</legend>');
            $field = $form->addSelectField('snippets');
            $field->setAttribute('class', 'form-control selectpicker');
            $field->setAttribute('data-live-search', 'true');
            $field->setAttribute('multiple', 'multiple');
            $field->setLabel(rex_i18n::msg('cke5_snippets_profile_select'));

            $sql = rex_sql::factory();
            $sqlResult = $sql->getArray('select id, name, active from ' . $snippetsTable . ' order by name');
            foreach ($sqlResult as $value) {
                $suffix = ((int) $value['active'] === 1) ? '' : ' (' . rex_i18n::msg('offline') . ')';
                $field->getSelect()->addOption($value['name'] . $suffix . ' [' . $value['id'] . ']', $value['id']);
            }
        $form->addRawField('</fieldset>');
    $form->addRawField('</div>');

// BASIS TEXT STYLES
        $form->addRawField('<fieldset><legend>'.rex_i18n::msg('cke5_base_text_styles').'</legend>');
            $field = $form->addCheckboxField('document_title');
            $field->setAttribute('id', 'cke5document-title-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setLabel(rex_i18n::msg('cke5_document_title'));
            $field->addOption(rex_i18n::msg('cke5_document_title_description'), 'document_title');
            $field->setNotice(rex_i18n::msg('cke5_document_title_notice'));
            if ($default_value) {
                $field->setValue('');
            }

            // heading
            $form->addRawField('<div class="collapse" id="cke5heading-collapse">');
                $field = $form->addTextField('heading');
                $field->setAttribute('id', 'cke5heading-input');
                $field->setAttribute('data-tag-init', 1);
                $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['heading']) . '"]');
                $field->setLabel(rex_i18n::msg('cke5_heading'));
                if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['heading']);
            $form->addRawField('</div>');

            // alignment
            $form->addRawField('<div class="collapse" id="cke5alignment-collapse">');
                $field = $form->addTextField('alignment');
                $field->setAttribute('id', 'cke5alignment-input');
                $field->setAttribute('data-tag-init', 1);
                $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['alignment']) . '"]');
                $field->setLabel(rex_i18n::msg('cke5_alignment'));
                if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['alignment']);
            $form->addRawField('</div>');

            // liststyle
            $form->addRawField('<div class="collapse" id="cke5liststyle-collapse">');
                $field = $form->addCheckboxField('list_style');
                $field->setAttribute('id', 'cke5liststyle-input');
                $field->setAttribute('data-toggle', 'toggle');
                $field->setLabel(rex_i18n::msg('cke5_liststyle'));
                $field->addOption(rex_i18n::msg('cke5_liststyle_description'), 'liststyle');
                if ($default_value) $field->setValue('liststyle');
            $form->addRawField('</div>');

            // number list
            $form->addRawField('<div class="collapse" id="cke5numberedList-collapse">');
                $field = $form->addCheckboxField('list_start_index');
                $field->setAttribute('id', 'cke5liststartindex-input');
                $field->setAttribute('data-toggle', 'toggle');
                $field->setLabel(rex_i18n::msg('cke5_liststartindex'));
                $field->addOption(rex_i18n::msg('cke5_liststartindex_description'), 'liststartindex');

                $field = $form->addCheckboxField('list_reversed');
                $field->setAttribute('id', 'cke5listreversed-input');
                $field->setAttribute('data-toggle', 'toggle');
                $field->setLabel(rex_i18n::msg('cke5_listreversed'));
                $field->addOption(rex_i18n::msg('cke5_listreversed_description'), 'listreversed');
            $form->addRawField('</div>');

            // code block
            $form->addRawField('<div class="collapse" id="cke5codeBlock-collapse">');
                $field = $form->addTextField('code_block');
                $field->setAttribute('id', 'cke5codeBlock-input');
                $field->setAttribute('data-tag-init', 1);
                $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['code_block']) . '"]');
                $field->setLabel(rex_i18n::msg('cke5_code_block'));
                if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['code_block']);
            $form->addRawField('</div>');
        $form->addRawField('</fieldset>');

// EMBED & CODE
        $form->addRawField('<div class="collapse" id="cke5embed-collapse-parent"><fieldset><legend>'.rex_i18n::msg('cke5_embed_support').'</legend>');
            // mediaEmbed provider
            $form->addRawField('<div class="collapse" id="cke5mediaEmbed-collapse">');
                $field = $form->addTextField('mediaembed');
                $field->setLabel(rex_i18n::msg('cke5_mediaembed'));
                $field->setAttribute('id', 'cke5mediaEmbed-input');
                $field->setAttribute('data-tag-init', 1);
                $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['providers']) . '"]');
                if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['mediaembed']);

                $field = $form->addTextAreaField('media_embed_styles_definition');
                $field->setLabel(rex_i18n::msg('cke5_media_embed_styles'));
                $field->setAttribute('id', 'cke5-media-embed-styles-definition');
                $field->setAttribute('data-label-placeholder', rex_i18n::msg('label'));
                $field->setAttribute('data-class-placeholder', rex_i18n::msg('cke5_classes_area'));
                $field->setNotice(rex_i18n::msg('cke5_media_embed_styles_description'));
                if ($default_value) {
                    $field->setValue(Cke5ProfilesCreator::DEFAULT_VALUES['media_embed_styles_definition']);
                }

                $field = $form->addTextAreaField('media_embed_width_styles_definition');
                $field->setLabel(rex_i18n::msg('cke5_media_embed_width_styles'));
                $field->setAttribute('id', 'cke5-media-embed-width-styles-definition');
                $field->setAttribute('data-label-placeholder', rex_i18n::msg('label'));
                $field->setAttribute('data-class-placeholder', rex_i18n::msg('cke5_classes_area'));
                $field->setNotice(rex_i18n::msg('cke5_media_embed_width_styles_description'));
                if ($default_value) {
                    $field->setValue(Cke5ProfilesCreator::DEFAULT_VALUES['media_embed_width_styles_definition']);
                }
            $form->addRawField('</div>');

            $form->addRawField('<div class="collapse" id="cke5for_video-collapse">');
                $field = $form->addTextAreaField('video_styles_definition');
                $field->setLabel(rex_i18n::msg('cke5_video_styles'));
                $field->setAttribute('id', 'cke5-video-styles-definition');
                $field->setAttribute('data-label-placeholder', rex_i18n::msg('label'));
                $field->setAttribute('data-class-placeholder', rex_i18n::msg('cke5_classes_area'));
                $field->setNotice(rex_i18n::msg('cke5_video_styles_description'));
                if ($default_value) {
                    $field->setValue(Cke5ProfilesCreator::DEFAULT_VALUES['video_styles_definition']);
                }

                $field = $form->addTextAreaField('video_width_styles_definition');
                $field->setLabel(rex_i18n::msg('cke5_video_width_styles'));
                $field->setAttribute('id', 'cke5-video-width-styles-definition');
                $field->setAttribute('data-label-placeholder', rex_i18n::msg('label'));
                $field->setAttribute('data-class-placeholder', rex_i18n::msg('cke5_classes_area'));
                $field->setNotice(rex_i18n::msg('cke5_video_width_styles_description'));
                if ($default_value) {
                    $field->setValue(Cke5ProfilesCreator::DEFAULT_VALUES['video_width_styles_definition']);
                }

                $form->addRawField('<fieldset><legend>' . rex_i18n::msg('cke5_video_defaults') . '</legend>');
                    $field = $form->addCheckboxField('video_controls_default');
                    $field->setAttribute('id', 'cke5video-controls-default-input');
                    $field->setAttribute('data-toggle', 'toggle');
                    $field->setLabel(rex_i18n::msg('cke5_video_controls_default'));
                    $field->addOption(rex_i18n::msg('cke5_video_controls_default_description'), 'video_controls_default');
                    if ($default_value) {
                        $field->setValue('video_controls_default');
                    }

                    $field = $form->addCheckboxField('video_autoplay_default');
                    $field->setAttribute('id', 'cke5video-autoplay-default-input');
                    $field->setAttribute('data-toggle', 'toggle');
                    $field->setLabel(rex_i18n::msg('cke5_video_autoplay_default'));
                    $field->addOption(rex_i18n::msg('cke5_video_autoplay_default_description'), 'video_autoplay_default');

                    $field = $form->addCheckboxField('video_muted_default');
                    $field->setAttribute('id', 'cke5video-muted-default-input');
                    $field->setAttribute('data-toggle', 'toggle');
                    $field->setLabel(rex_i18n::msg('cke5_video_muted_default'));
                    $field->addOption(rex_i18n::msg('cke5_video_muted_default_description'), 'video_muted_default');

                    $field = $form->addCheckboxField('video_loop_default');
                    $field->setAttribute('id', 'cke5video-loop-default-input');
                    $field->setAttribute('data-toggle', 'toggle');
                    $field->setLabel(rex_i18n::msg('cke5_video_loop_default'));
                    $field->addOption(rex_i18n::msg('cke5_video_loop_default_description'), 'video_loop_default');

                    $field = $form->addCheckboxField('video_playsinline_default');
                    $field->setAttribute('id', 'cke5video-playsinline-default-input');
                    $field->setAttribute('data-toggle', 'toggle');
                    $field->setLabel(rex_i18n::msg('cke5_video_playsinline_default'));
                    $field->addOption(rex_i18n::msg('cke5_video_playsinline_default_description'), 'video_playsinline_default');
                    if ($default_value) {
                        $field->setValue('video_playsinline_default');
                    }
                $form->addRawField('</fieldset>');
            $form->addRawField('</div>');

            // embed html_preview
            $form->addRawField('<div class="collapse" id="cke5htmlEmbed-collapse">');
                $field = $form->addCheckboxField('html_preview');
                $field->setAttribute('id', 'cke5htmlEmbed-input');
                $field->setAttribute('data-toggle', 'toggle');
                $field->setLabel(rex_i18n::msg('cke5_html_embed_default'));
                $field->addOption(rex_i18n::msg('cke5_html_embed_default_description'), 'html_preview');
            $form->addRawField('</div>');
        $form->addRawField('</fieldset></div>');

        /*
         * todo: not work because : https://github.com/ckeditor/ckeditor5/issues/6160
        // special chars
        $form->addRawField('<div class="collapse  ' . ((in_array('codeBlock', $toolbar)) ? 'in' : '') . '" id="cke5specialCharacters-collapse">');
            $field = $form->addTextField('special_characters');
            $field->setAttribute('id', 'cke5specialCharacters-input');
            $field->setAttribute('data-tag-init', 1);
            $field->setAttribute('data-defaults', Cke5ProfilesCreator::DEFAULTS['special_characters']);
            $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['special_characters']) . '"]');
            $field->setLabel(rex_i18n::msg('cke5_special_characters'));
            if ($default_value) $field->setAttribute('data-default-tags', 1);
        $form->addRawField('</div>');
        */

// HTML SUPPORT
        $form->addRawField('<div class="collapse" id="cke5sourceEditing-collapse"><fieldset><legend>'.rex_i18n::msg('cke5_html_support').'</legend>');
            // text area
            $field = $form->addTextAreaField('html_support_allow');
            $field->setAttribute('id', 'cke5-html-support-allow');
            $field->setAttribute('rows', '2');
            $field->setLabel(rex_i18n::msg('cke5_html_support_allow'));
            if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULT_VALUES['html_support_allow']);

            // text area
            $field = $form->addTextAreaField('html_support_disallow');
            $field->setAttribute('id', 'cke5-html-support-disallow');
            $field->setAttribute('rows', '2');
            $field->setLabel(rex_i18n::msg('cke5_html_support_disallow'));
        $form->addRawField('</fieldset></div>');

// TABLE
        $form->addRawField('<div class="collapse" id="cke5insertTable-collapse"><fieldset><legend>Tables</legend>');
            $field = $form->addTextField('table_toolbar');
            $field->setAttribute('id', 'cke5inserttable-input');
            $field->setAttribute('data-tag-init', 1);
            $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['table_toolbar']) . '"]');
            $field->setLabel(rex_i18n::msg('cke5_table_toolbar'));
            if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['table_toolbar']);

            // table color
            $form->addRawField('<div class="collapse" id="cke5tableColor-collapse">');
                // default font color
                $field = $form->addCheckboxField('table_color_default');
                $field->setAttribute('id', 'cke5table-color-default-input');
                $field->setAttribute('data-toggle', 'toggle');
                $field->setAttribute('data-collapse-target', 'customTableColor');
                $field->setLabel(rex_i18n::msg('cke5_table_color_default'));
                $field->addOption(rex_i18n::msg('cke5_table_color_default_description'), 'default_table_color');
                if ($default_value) $field->setValue('default_table_color');

                // custom table color
                $form->addRawField('<div class="collapse" id="cke5customTableColor-collapse">');
                    $field = $form->addTextAreaField('table_color');
                    $field->setLabel(rex_i18n::msg('cke5_table_color'));
                    $field->setAttribute('id', 'cke5tablecolor-area');
                    $field->setAttribute('data-color-placeholder', rex_i18n::msg('cke5_color_placeholder'));
                    $field->setAttribute('data-color-name-placeholder', rex_i18n::msg('cke5_color_name_placeholder'));
                    $field->setAttribute('data-has-border-label', rex_i18n::msg('cke5_has_border_label'));
                $form->addRawField('</div>');
            $form->addRawField('</div>');
        $form->addRawField('</fieldset></div>');

// IMAGES
        $form->addRawField('<fieldset><legend>'.rex_i18n::msg('cke5_images').'</legend>');
            // image toolbar
            $form->addRawField('<div class="collapse" id="cke5imagetoolbar-collapse">');
                $field = $form->addTextField('image_toolbar');
                $field->setAttribute('id', 'cke5image-input');
                $field->setAttribute('data-tag-init', 1);
                $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['image_toolbar']) . '"]');
                $field->setLabel(rex_i18n::msg('cke5_image_toolbar'));
                if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['image_toolbar']);

                // media types
                $field = $form->addTextField('mediatypes');
                $field->setLabel(rex_i18n::msg('cke5_mediatypes'));
                $field->setAttribute('placeholder', rex_i18n::msg('cke5_mediatypes_placeholder'));

                // image resize unit
                $field = $form->addSelectField('image_resize_unit');
                $field->setAttribute('class', 'form-control selectpicker');
                $field->setLabel(rex_i18n::msg('cke5_resize_unit'));
                $auto_sel = new rex_select();
                $auto_sel->setStyle('class="form-control selectpicker"');
                $auto_sel->setName('resizeunit');
                $auto_sel->addOption(rex_i18n::msg('cke5_image_resize_unit_percent'), '%');
                $auto_sel->addOption(rex_i18n::msg('cke5_image_resize_unit_px'), 'px');
                $field->setSelect($auto_sel);

                // image resize handles
                $field = $form->addCheckboxField('image_resize_handles');
                $field->setAttribute('id', 'cke5image-resize-handles-input');
                $field->setAttribute('data-toggle', 'toggle');
                $field->setLabel(rex_i18n::msg('cke5_image_resize_handles'));
                $field->addOption(rex_i18n::msg('cke5_image_resize_handles_description'), 'default_resize_handles');
                if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['image_resize_handles']);

                // image resize options
                $field = $form->addCheckboxField('image_resize_options');
                $field->setAttribute('id', 'cke5image-resize-option-input');
                $field->setAttribute('data-toggle', 'toggle');
                $field->setAttribute('data-collapse-target', 'resizeOptions');
                $field->setLabel(rex_i18n::msg('cke5_image_resize_options'));
                $field->addOption(rex_i18n::msg('cke5_image_resize_options_description'), 'default_resize_options');

                // custom image resize options
                $form->addRawField('<div class="collapse" id="cke5resizeOptions-collapse">');
                    $field = $form->addTextAreaField('image_resize_options_definition');
                    $field->setLabel(rex_i18n::msg('cke5_image_resize_options_definition'));
                    $field->setAttribute('id', 'cke5resizeoptions-area');
                    $field->setAttribute('data-name-placeholder', rex_i18n::msg('cke5_resize_option_placeholder'));
                    $field->setAttribute('data-icon-placeholder', rex_i18n::msg('cke5_icon_placeholder'));
                    $field->setAttribute('data-value-placeholder', rex_i18n::msg('cke5_value_placeholder'));

                    // image resize group options
                    $field = $form->addCheckboxField('image_resize_group_options');
                    $field->setAttribute('id', 'cke5image-resize-group-option-input');
                    $field->setAttribute('data-toggle', 'toggle');
                    $field->setLabel(rex_i18n::msg('cke5_image_resize_group_options'));
                    $field->addOption(rex_i18n::msg('cke5_image_resize_group_options_description'), 'default_resize_group_options');
                    if ($default_value) $field->setValue('default_resize_group_options');
                $form->addRawField('</div>');

                // mediapath
                $form->addRawField('
                    <div class="collapse" id="cke5insertMediapath-collapse">
                        <dl class="rex-form-group form-group">
                            <dt><label class="control-label" for="mediapath">' . rex_i18n::msg('cke5_mediapath') . '</label></dt>
                            <dd class="form-inline">
                              <div class="input-group col-sm-12">
                                <span class="input-group-addon" style="width: 20px">/</span>
                                <input id ="cke5mediapath-input" class="form-control" type="text" value="' . $mediaPath . '" placeholder="default /' . $mediaPath . '/">
                                <span class="input-group-addon" style="width: 20px">/</span>
                              </div>
                            </dd>
                        </dl>
                    </div>
                    ');

                $field = $form->addHiddenField('mediapath');
                $field->setAttribute('id', 'cke5mediapath-hidden');

                if (rex_addon::exists('media_manager') && rex_addon::get('media_manager')->isAvailable()) {
                    // medaitype
                    $field = $form->addSelectField('mediatype');
                    $field->setAttribute('class', 'form-control selectpicker');
                    $field->setAttribute('id', 'cke5mediatype-select');
                    $field->setLabel(rex_i18n::msg('cke5_media_manager_type'));
                    $field->getSelect()->addOption('default /' . $mediaPath . '/', '');
                    $field->getSelect()->addDBSqlOptions('SELECT name, name FROM ' . rex::getTablePrefix() . 'media_manager_type ORDER BY status, name');
                }
            // close collapse
            $form->addRawField('</div>');

            // upload default
            $field = $form->addCheckboxField('upload_default');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setAttribute('id', 'cke5uploaddefault-input');
            $field->setLabel(rex_i18n::msg('cke5_upload_default'));
            $field->addOption(rex_i18n::msg('cke5_upload_default_description'), 'default_upload');
            if ($default_value) $field->setValue('default_upload');

            if (rex_addon::exists('mediapool') && rex_addon::get('mediapool')->isAvailable()) {
                // mediacategory
                $form->addRawField('<div class="collapse" id="cke5mediacat-collapse">');
                    $field = $form->addSelectField('mediacategory');
                    $field->setAttribute('class', 'form-control selectpicker');
                    $field->setLabel(rex_i18n::msg('cke5_media_category_upload'));
                    $cats_sel = new rex_media_category_select();
                    $cats_sel->setStyle('class="form-control selectpicker"');
                    $cats_sel->setName('mediacategory');
                    $cats_sel->addOption(rex_i18n::msg('pool_kats_no'), '0');
                    $field->setSelect($cats_sel);
                $form->addRawField('</div>');
            }
        $form->addRawField('</fieldset>');

// FONTS
        $form->addRawField('<div class="collapse" id="cke5font-collapse-parent"><fieldset><legend>'.rex_i18n::msg('cke5_fonts').'</legend>');
            // fontsize
            $form->addRawField('<div class="collapse" id="cke5fontSize-collapse">');
                $field = $form->addTextField('fontsize');
                $field->setLabel(rex_i18n::msg('cke5_fontSize'));
                $field->setAttribute('id', 'cke5fontsize-input');
                $field->setAttribute('data-tag-init', 1);
                $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['fontsize']) . '"]');
                if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['fontsize']);
            $form->addRawField('</div>');

            // font color
            $form->addRawField('<div class="collapse" id="cke5fontColor-collapse">');
                // default font color
                $field = $form->addCheckboxField('font_color_default');
                $field->setAttribute('id', 'cke5font-color-default-input');
                $field->setAttribute('data-toggle', 'toggle');
                $field->setAttribute('data-collapse-target', 'customFontColor');
                $field->setLabel(rex_i18n::msg('cke5_font_color_default'));
                $field->addOption(rex_i18n::msg('cke5_font_color_default_description'), 'default_font_color');
                if ($default_value) $field->setValue('default_font_color');

                // custom font color
                $form->addRawField('<div class="collapse" id="cke5customFontColor-collapse">');
                    $field = $form->addTextAreaField('font_color');
                    $field->setLabel(rex_i18n::msg('cke5_font_color'));
                    $field->setAttribute('id', 'cke5fontcolor-area');
                    $field->setAttribute('data-color-placeholder', rex_i18n::msg('cke5_color_placeholder'));
                    $field->setAttribute('data-color-name-placeholder', rex_i18n::msg('cke5_color_name_placeholder'));
                    $field->setAttribute('data-has-border-label', rex_i18n::msg('cke5_has_border_label'));
                $form->addRawField('</div>');
            $form->addRawField('</div>');

            // font background color
            $form->addRawField('<div class="collapse" id="cke5fontBackgroundColor-collapse">');
            // default font color
                $field = $form->addCheckboxField('font_background_color_default');
                $field->setAttribute('id', 'cke5font-background-color-default-input');
                $field->setAttribute('data-toggle', 'toggle');
                $field->setAttribute('data-collapse-target', 'customFontBackgroundColor');
                $field->setLabel(rex_i18n::msg('cke5_font_background_color_default'));
                $field->addOption(rex_i18n::msg('cke5_font_background_color_default_description'), 'default_font_background_color');
                if ($default_value) $field->setValue('default_font_background_color');

                // custom font color
                $form->addRawField('<div class="collapse" id="cke5customFontBackgroundColor-collapse">');
                    $field = $form->addTextAreaField('font_background_color');
                    $field->setLabel(rex_i18n::msg('cke5_font_background_color'));
                    $field->setAttribute('id', 'cke5fontbgcolor-area');
                    $field->setAttribute('data-color-placeholder', rex_i18n::msg('cke5_color_placeholder'));
                    $field->setAttribute('data-color-name-placeholder', rex_i18n::msg('cke5_color_name_placeholder'));
                    $field->setAttribute('data-has-border-label', rex_i18n::msg('cke5_has_border_label'));
                    $form->addRawField('</div>');
            $form->addRawField('</div>');

            // font family
            $form->addRawField('<div class="collapse" id="cke5fontFamily-collapse">');
                // default font family
                $field = $form->addCheckboxField('font_family_default');
                $field->setAttribute('id', 'cke5font-family-default-input');
                $field->setAttribute('data-toggle', 'toggle');
                $field->setAttribute('data-collapse-target', 'customFontFamily');
                $field->setLabel(rex_i18n::msg('cke5_font_family_default'));
                $field->addOption(rex_i18n::msg('cke5_font_family_default_description'), 'default_font_family');
                if ($default_value) $field->setValue('default_font_family');

                // custom font family
                $form->addRawField('<div class="collapse" id="cke5customFontFamily-collapse">');
                    $field = $form->addTextAreaField('font_families');
                    $field->setLabel(rex_i18n::msg('cke5_font_family'));
                    $field->setAttribute('id', 'cke5fontfamily-area');
                    $field->setAttribute('data-family-placeholder', rex_i18n::msg('cke5_family_name_placeholder'));
                $form->addRawField('</div>');
            $form->addRawField('</div>');
        // close fieldset FONTS
        $form->addRawField('</fieldset></div>');

/*
// EMOJI
        $form->addRawField('<div class="collapse" id="cke5emoji-collapse"><fieldset><legend>'.rex_i18n::msg('cke5_emoji').'</legend>');
            $field = $form->addTextField('emoji');
            $field->setAttribute('id', 'cke5emoji-input');
            $field->setAttribute('data-tag-init', 1);
            $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['emoji']) . '"]');
            $field->setLabel(rex_i18n::msg('cke5_empji'));
            if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['emoji']);
        $form->addRawField('</fieldset></div>');
*/
// LINKS
        $form->addRawField('<div class="collapse" id="cke5link-collapse"><fieldset><legend>'.rex_i18n::msg('cke5_links').'</legend>');
            $field = $form->addTextField('rexlink');
            $field->setAttribute('id', 'cke5link-input');
            $field->setAttribute('data-tag-init', 1);
            $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['rexlink']) . '"]');
            $field->setLabel(rex_i18n::msg('cke5_link'));
            if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['rexlink']);

            // internal link stuff
            $form->addRawField('<div class="collapse" id="cke5internal-collapse">');
                // add select for article categories
                if (rex_addon::exists('structure') && rex_addon::get('structure')->isAvailable()) {
                    // structure category
                    $field = $form->addSelectField('link_internalcategory');
                    $field->setAttribute('class', 'form-control selectpicker');
                    $field->setLabel(rex_i18n::msg('cke5_internal_category'));
                    $cats_sel = new rex_category_select();
                    $cats_sel->setStyle('class="form-control selectpicker"');
                    $cats_sel->setName('link_internalcategory');
                    $field->setSelect($cats_sel);
                }
            $form->addRawField('</div>');

            // media link stuff
            $form->addRawField('<div class="collapse" id="cke5media-collapse">');
                $field = $form->addTextField('link_mediatypes');
                $field->setLabel(rex_i18n::msg('cke5_mediatypes'));
                $field->setAttribute('placeholder', rex_i18n::msg('cke5_mediatypes_placeholder'));
                // add select for media categories
                if (rex_addon::exists('mediapool') && rex_addon::get('mediapool')->isAvailable()) {
                    // mediacategory
                    $field = $form->addSelectField('link_mediacategory');
                    $field->setAttribute('class', 'form-control selectpicker');
                    $field->setLabel(rex_i18n::msg('cke5_media_category'));
                    $cats_sel = new rex_media_category_select();
                    $cats_sel->setStyle('class="form-control selectpicker"');
                    $cats_sel->setName('link_mediacategory');
                    $cats_sel->addOption(rex_i18n::msg('pool_kats_no'), '0');
                    $field->setSelect($cats_sel);
                }
            $form->addRawField('</div>');

            // yform link stuff
            $form->addRawField('<div class="collapse" id="cke5ytable-collapse">');
                $field = $form->addTextAreaField('ytable');
                $field->setLabel(rex_i18n::msg('cke5_ytable'));
                $field->setAttribute('id', 'cke5ytable-area');
                $field->setAttribute('data-ytable-table-placeholder', rex_i18n::msg('cke5_ytable_table_placeholder'));
                $field->setAttribute('data-ytable-column-placeholder', rex_i18n::msg('cke5_ytable_column_placeholder'));
                $field->setAttribute('data-ytable-title-placeholder', rex_i18n::msg('cke5_ytable_title_placeholder'));
            $form->addRawField('</div>');

            /*
            // https
            $field = $form->addSelectField('auto_link');
            $field->setAttribute('class', 'form-control selectpicker');
            $field->setLabel(rex_i18n::msg('cke5_auto_link'));
            $auto_sel = new rex_select();
            $auto_sel->setStyle('class="form-control selectpicker"');
            $auto_sel->setName('autolink');
            $auto_sel->addOption(rex_i18n::msg('cke5_auto_link_https'), 'https');
            $auto_sel->addOption(rex_i18n::msg('cke5_auto_link_http'), 'http');
            $auto_sel->addOption(rex_i18n::msg('cke5_auto_link_disable'), '0');
            $field->setSelect($auto_sel);
            $field = $form->addCheckboxField('auto_link');
            $field->setAttribute('id', 'cke5auto_link-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setLabel(rex_i18n::msg('cke5_auto_link'));
            $field->addOption(rex_i18n::msg('cke5_auto_link_description'), 'auto_link');
            if ($default_value) $field->setValue('auto_link');
            */

            // extern blank
            $field = $form->addCheckboxField('blank_to_external');
            $field->setAttribute('id', 'cke5blank-to-external-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setLabel(rex_i18n::msg('cke5_blank_to_external'));
            $field->addOption(rex_i18n::msg('cke5_blank_to_external_description'), 'blank_to_external');

            // downloadable
            $field = $form->addCheckboxField('link_downloadable');
            $field->setAttribute('id', 'cke5link-downloadable-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setLabel(rex_i18n::msg('cke5_link_downloadable'));
            $field->addOption(rex_i18n::msg('cke5_link_downloadable_description'), 'link_downloadable');

            // custom area for link decorators
            $field = $form->addCheckboxField('link_decorators');
            $field->setAttribute('id', 'cke5link-decorators-definition-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setAttribute('data-collapse-target', 'linkDecoratorsDefinition');
            $field->setLabel(rex_i18n::msg('cke5_link_decorators_definition'));
            $field->addOption(rex_i18n::msg('cke5_link_decorators_definition_description'), 'link_decorators_definition');

            // link decorators area
            $form->addRawField('<div class="collapse" id="cke5linkDecoratorsDefinition-collapse">');
                $field = $form->addTextAreaField('link_decorators_definition');
                $field->setAttribute('id', 'cke5-link-decorators-definition-area');
                $field->setAttribute('rows', '2');
                $field->setLabel(rex_i18n::msg('cke5_link_decorators_definition_area'));
                $field->setNotice('CKEditor API: JSON-Objekt mit Decorator-Key => Konfiguration, z. B. {"isDownloadable":{"mode":"manual","label":"Download","attributes":{"download":"download"}}}');
            $form->addRawField('</div>');
        // close fieldset LINKS
        $form->addRawField('</fieldset></div>');

// HIGHTLIGHT
        $form->addRawField('<div class="collapse" id="cke5highlight-collapse"><fieldset><legend>'.rex_i18n::msg('cke5_highlights').'</legend>');
            $field = $form->addTextField('highlight');
            $field->setAttribute('id', 'cke5highlight-input');
            $field->setAttribute('data-tag-init', 1);
            $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['highlight']) . '"]');
            $field->setLabel(rex_i18n::msg('cke5_highlight'));
            if ($default_value) $field->setValue(Cke5ProfilesCreator::DEFAULTS['highlight']);
        $form->addRawField('</fieldset></div>');

// MENTIONS
        $form->addRawField("<fieldset><legend>".rex_i18n::msg('cke5_mentions')."</legend>");

            // mentions for experts
            $field = $form->addCheckboxField('mentions');
            $field->setAttribute('id', 'cke5mentions-definition-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setAttribute('data-collapse-target', 'mentionsDefinition');
            $field->setLabel(rex_i18n::msg('cke5_mentions_definition'));
            $field->addOption(rex_i18n::msg('cke5_mentions_definition_description'), 'mentions_definition');

            // mentions area
            $form->addRawField('<div class="collapse" id="cke5mentionsDefinition-collapse">');
            $field = $form->addTextAreaField('mentions_definition');
            $field->setAttribute('id', 'cke5-mentions-area');
            $field->setAttribute('rows', '2');
            $field->setLabel(rex_i18n::msg('cke5_mentions_definition_area'));
            $field->setNotice(rex_i18n::msg('cke5_mentions_definition_example'));
            $form->addRawField('</div>');

            // sprog_mention for experts
            $field = $form->addCheckboxField('sprog_mention');
            $field->setAttribute('id', 'cke5sprog_mention-definition-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setAttribute('data-collapse-target', 'sprog_mentionDefinition');
            $field->setLabel(rex_i18n::msg('cke5_sprog_mention_definition'));
            $field->addOption(rex_i18n::msg('cke5_sprog_mention_definition_description'), 'sprog_mention_definition');

            // sprog_mention area
            $form->addRawField('<div class="collapse" id="cke5sprog_mentionDefinition-collapse">');
                $field = $form->addTextAreaField('sprog_mention_definition');
                $field->setAttribute('id', 'cke5-sprog-mention-area');
                $field->setAttribute('data-sprog-key-placeholder', rex_i18n::msg('cke5_sprog_mention_key'));
                $field->setAttribute('data-sprog-description-placeholder', rex_i18n::msg('cke5_sprog_mention_description'));
                $field->setLabel(rex_i18n::msg('cke5_sprog_mention_definition_area'));
            $form->addRawField('</div>');

        $form->addRawField('</fieldset>');



// DEFAULT SETUP
        $form->addRawField("<fieldset><legend>".rex_i18n::msg('cke5_default_setup')."</legend>");
            // lang
            $field = $form->addSelectField('lang');
            $field->setAttribute('class', 'form-control selectpicker');
            $field->setAttribute('data-live-search', 'true');
            $field->setLabel(rex_i18n::msg('cke5_lang'));
            $field->getSelect()->addOption('default', '');

            // content lang
            $fieldContentLang = $form->addSelectField('lang_content');
            $fieldContentLang->setAttribute('class', 'form-control selectpicker');
            $fieldContentLang->setAttribute('data-live-search', 'true');
            $fieldContentLang->setLabel(rex_i18n::msg('cke5_content_lang'));
            $fieldContentLang->getSelect()->addOption('default', '');

            // get current lang
            $lang = rex_i18n::getLocale();
            $langFiles = glob($this->getPath('assets/vendor/ckeditor5-modern/translations/*.js'));

            if (is_array($langFiles)) {
                foreach ($langFiles as $langFile) {
                    $key = substr(pathinfo($langFile, PATHINFO_FILENAME), 0, 2);
                    if (isset(CKE5ISO6391::$isolang[$key])) {
                        $field->getSelect()->addOption(CKE5ISO6391::$isolang[$key] . ' [' . pathinfo($langFile, PATHINFO_FILENAME) . ']', pathinfo($langFile, PATHINFO_FILENAME));
                        $fieldContentLang->getSelect()->addOption(CKE5ISO6391::$isolang[$key] . ' [' . pathinfo($langFile, PATHINFO_FILENAME) . ']', pathinfo($langFile, PATHINFO_FILENAME));
                    }
                }
            }
            // set current lang again to fix lang problem with php 7.0 and php 5.x
            rex_i18n::setLocale($lang, false);

            // default height
            $field = $form->addCheckboxField('height_default');
            $field->setAttribute('id', 'cke5height-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setAttribute('data-collapse-target', 'minmax');
            $field->setLabel(rex_i18n::msg('cke5_height_default'));
            $field->addOption(rex_i18n::msg('cke5_height_default_description'), 'default_height');
            if ($default_value) $field->setValue('default_height');

            // min max height collapse
            $form->addRawField('<div class="collapse" id="cke5minmax-collapse"><div class="minmax-inner">');
                // min height default 0 = none
                $field = $form->addTextField('min_height');
                $field->setAttribute('id', 'cke5minheight-input');
                $field->setAttribute('data-range-values', '[' . implode(',', Cke5ProfilesCreator::DEFAULTS['min_height']) . ']');
                $field->setAttribute('data-range', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['min_height']) . '"]');
                $field->setAttribute('data-slider-value', $min_height);
                $field->setLabel(rex_i18n::msg('cke5_min_height'));

                // max height default 0 = none
                $field = $form->addTextField('max_height');
                $field->setAttribute('id', 'cke5maxheight-input');
                $field->setAttribute('data-range-values', '[' . implode(',', Cke5ProfilesCreator::DEFAULTS['max_height']) . ']');
                $field->setAttribute('data-range', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['max_height']) . '"]');
                $field->setAttribute('data-slider-value', $max_height);
                $field->setLabel(rex_i18n::msg('cke5_max_height'));
            $form->addRawField('</div></div>');

            // transformations
            $field = $form->addCheckboxField('transformation');
            $field->setAttribute('id', 'cke5transformation-definition-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setAttribute('data-collapse-target', 'transformationDefinition');
            $field->setLabel(rex_i18n::msg('cke5_transformation_definition'));
            $field->addOption(rex_i18n::msg('cke5_transformation_definition_description'), 'transformation_definition');

            // transformations area
            $form->addRawField('<div class="collapse" id="cke5transformationDefinition-collapse">');
                $field = $form->addTextAreaField('transformation_extra');
                $field->setAttribute('id', 'cke5-transformation-extra-area');
                $field->setAttribute('data-from-placeholder', 'from');
                $field->setAttribute('data-to-placeholder', 'to');
                $field->setLabel(rex_i18n::msg('cke5_transformation_extra_definition_area'));
            $form->addRawField('</div>');

            // custom area for experts
            $field = $form->addCheckboxField('extra');
            $field->setAttribute('id', 'cke5extra-definition-input');
            $field->setAttribute('data-toggle', 'toggle');
            $field->setAttribute('data-collapse-target', 'extraDefinition');
            $field->setLabel(rex_i18n::msg('cke5_extra_definition'));
            $field->addOption(rex_i18n::msg('cke5_extra_definition_description'), 'extra_definition');

            // expert area
            $form->addRawField('<div class="collapse" id="cke5extraDefinition-collapse">');
                $field = $form->addTextAreaField('extra_definition');
                $field->setAttribute('id', 'cke5-extra-area');
                $field->setAttribute('rows', '2');
                $field->setLabel(rex_i18n::msg('cke5_extra_definition_area'));
            $form->addRawField('</div>');

            $form->addRawField('<div class="collapse" id="cke5for_clear-collapse">');
                $field = $form->addSelectField('clear_widget_preset');
                $field->setAttribute('class', 'form-control selectpicker');
                $field->setLabel(rex_i18n::msg('cke5_clear_widget_preset'));
                $field->setNotice(rex_i18n::msg('cke5_clear_widget_preset_notice'));
                $field->getSelect()->addOption(rex_i18n::msg('cke5_clear_widget_preset_global_default'), '');
                $field->getSelect()->addOption('UIkit 3', 'uikit3');
                $field->getSelect()->addOption('Bootstrap 5', 'bootstrap5');
                $field->getSelect()->addOption('Tailwind', 'tailwind');
                if ($default_value) {
                    $field->setValue('');
                }
            $form->addRawField('</div>');
        // close fieldset DEFAULT SETUP
        $form->addRawField('</fieldset>');

        $externalWidgetHtml = rex_extension::registerPoint(new rex_extension_point(
            'CKE5_PROFILE_WIDGETS',
            '',
            [
                'form' => $form,
                'result' => $result,
                'default_value' => $default_value,
                'func' => $func,
            ]
        ));
        if (is_string($externalWidgetHtml) && trim($externalWidgetHtml) !== '') {
            $form->addRawField('<fieldset><legend>Plugin-Widgets</legend>');
            $form->addRawField($externalWidgetHtml);
            $form->addRawField('</fieldset>');
        }

    // close form wrapper collapse
    $form->addRawField('</div>');

    if ($func === 'edit') {
        $profileResult = array();
        $previewSeedHtml = '<h3>Preview</h3><p>Dies ist ein kurzer Beispieltext zur Editor-Vorschau.</p><p><a href="#">Beispiel-Link</a></p>';
        if (is_array($result)) {
            foreach ($result as $key => $value) {
                $profileResult[str_replace($prefix, '', $key)] = $value;
            }
        }
        $form->addRawField('
        <div class="cke5-preview-row">
            <dl class="rex-form-group form-group">
                <dt>
                    <label class="control-label">' . rex_i18n::msg('cke5_editor_preview') . '</label>
                </dt>
                <dd>
                    <div class="cke5-editor" data-profile="' . $profile . '" data-lang="' . Cke5Lang::getUserLang() . '">' . $previewSeedHtml . '</div>
                    <div class="cke5-editor-info"><p>' . rex_i18n::msg('cke5_editor_preview_info') . '</p></div>
                    <div class="cke5-preview-code">
                        ' . Cke5PreviewHelper::getHtmlCode($profileResult) . '
                        ' . Cke5PreviewHelper::getYFormJsonCode($profileResult) . '
                        ' . Cke5PreviewHelper::getMFormCode($profileResult) . '
                    </div>
                </dd>
            </dl>
        </div>
        ');
    }

    // show
    $content = '<div class="cke5_profile_edit" data-cktypes=\'["' . implode('","', Cke5ProfilesCreator::EDITOR_SETTINGS['cktypes']) . '"]\' data-cklinktypes=\'["' . implode('","', Cke5ProfilesCreator::EDITOR_SETTINGS['cklinktypes']) . '"]\' data-ckimgtypes=\'["' . implode('","', Cke5ProfilesCreator::EDITOR_SETTINGS['ckimgtypes']) . '"]\' data-cktabletypes=\'["' . implode('","', Cke5ProfilesCreator::EDITOR_SETTINGS['cktabletypes']) . '"]\'>' . $form->get() . '</div>';

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', ($func === 'edit') ? rex_i18n::msg('cke5_profile_edit') : rex_i18n::msg('cke5_profile_add'));
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
}
