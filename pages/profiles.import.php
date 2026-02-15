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
$importKeys = [
    "id",
    "name",
    "description",
    "toolbar",
    "expert_definition",
    "expert_suboption",
    "expert",
    "extra_definition",
    "extra",
    "code_block",
    "special_characters",
    "group_when_full",
    "table_color_default",
    "table_color",
    "ytable",
    "transformation",
    "transformation_extra",
    "transformation_remove",
    "transformation_include",
    "html_support_allow",
    "html_support_disallow",
    "blank_to_external",
    "link_internalcategory",
    "link_mediatypes",
    "link_mediacategory",
    "link_downloadable",
    "link_decorators",
    "link_decorators_definition",
    "auto_link",
    "text_part_language",
    "heading",
    "alignment",
    "image_toolbar",
    "image_resize_unit",
    "image_resize_handles",
    "image_resize_options",
    "image_resize_group_options",
    "image_resize_options_definition",
    "fontsize",
    "highlight",
//    "emoji",
    "table_toolbar",
    "rexlink",
    "list_style",
    "list_start_index",
    "list_reversed",
    "html_preview",
    "height_default",
    "min_height",
    "max_height",
    "lang",
    "lang_content",
    "font_color",
    "font_color_default",
    "font_background_color",
    "font_background_color_default",
    "font_families",
    "font_family_default",
    "mediaembed",
    "mentions",
    "mentions_definition",
    "sprog_mention",
    "sprog_mention_definition",
    "mediatype",
    "mediatypes",
    "mediapath",
    "mediacategory",
    "upload_mediacategory",
    "upload_default"
];

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

        $content = file_get_contents($filename);
        $data = json_decode((string)$content, true);

        if (is_array($data)) {
            // Store data in session and show selection form
            rex_set_session('cke5_import_data', $data);
            $func = 'select';
        }
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

// Show import selection form
if ($func === 'select') {
    $data = rex_get_session('cke5_import_data');
    
    if (!is_array($data)) {
        $func = '';
    } else {
        // Detect format and count items
        $isNewFormat = isset($data['version']) && isset($data['profiles']);
        
        $profileCount = 0;
        $styleCount = 0;
        $styleGroupCount = 0;
        $templateCount = 0;
        $templateGroupCount = 0;
        
        if ($isNewFormat) {
            $profileCount = is_array($data['profiles'] ?? null) ? count($data['profiles']) : 0;
            $styleCount = is_array($data['styles'] ?? null) ? count($data['styles']) : 0;
            $styleGroupCount = is_array($data['style_groups'] ?? null) ? count($data['style_groups']) : 0;
            $templateCount = is_array($data['templates'] ?? null) ? count($data['templates']) : 0;
            $templateGroupCount = is_array($data['template_groups'] ?? null) ? count($data['template_groups']) : 0;
        } else {
            $profileCount = is_array($data) ? count($data) : 0;
        }
        
        // Create selection form
        $content = '<div class="rex-form-group">';
        $content .= '<fieldset>';
        $content .= '<legend>' . $this->i18n('profiles_import_select_entities') . '</legend>';
        
        if ($profileCount > 0) {
            $content .= '<label><input type="checkbox" name="import_profiles" value="1" checked> ' . 
                       sprintf($this->i18n('profiles_import_count_profiles'), $profileCount) . '</label><br>';
        }
        
        if ($styleCount > 0) {
            $content .= '<label><input type="checkbox" name="import_styles" value="1" checked> ' . 
                       sprintf($this->i18n('profiles_import_count_styles'), $styleCount) . '</label><br>';
        }
        
        if ($styleGroupCount > 0) {
            $content .= '<label><input type="checkbox" name="import_style_groups" value="1" checked> ' . 
                       sprintf($this->i18n('profiles_import_count_style_groups'), $styleGroupCount) . '</label><br>';
        }
        
        if ($templateCount > 0) {
            $content .= '<label><input type="checkbox" name="import_templates" value="1" checked> ' . 
                       sprintf($this->i18n('profiles_import_count_templates'), $templateCount) . '</label><br>';
        }
        
        if ($templateGroupCount > 0) {
            $content .= '<label><input type="checkbox" name="import_template_groups" value="1" checked> ' . 
                       sprintf($this->i18n('profiles_import_count_template_groups'), $templateGroupCount) . '</label><br>';
        }
        
        $content .= '</fieldset>';
        $content .= '</div>';
        
        $fragment = new rex_fragment();
        $fragment->setVar('class', 'edit', false);
        $fragment->setVar('title', $this->i18n('profiles_import_title'), false);
        $fragment->setVar('body', '<form method="post" action="' . rex_url::currentBackendPage() . '">' . 
            $csrfToken->getHiddenField() . 
            '<input type="hidden" name="func" value="cke5import_execute">' . 
            $content . 
            '<button class="btn btn-send" type="submit">' . $this->i18n('profiles_import') . '</button>' .
            '</form>', false);
        echo $fragment->parse('core/page/section.php');
        
        $func = '';
    }
}

// Execute import with selections
if ($func === 'cke5import_execute') {
    try {
        if (!$csrfToken->isValid()) {
            throw new InvalidArgumentException('csrf_token');
        }

        $data = rex_get_session('cke5_import_data');
        
        if (!is_array($data)) {
            throw new Exception('No import data found');
        }

        // Get user selections
        $importProfiles = rex_request::post('import_profiles', 'bool', false);
        $importStyles = rex_request::post('import_styles', 'bool', false);
        $importStyleGroups = rex_request::post('import_style_groups', 'bool', false);
        $importTemplates = rex_request::post('import_templates', 'bool', false);
        $importTemplateGroups = rex_request::post('import_template_groups', 'bool', false);

        // Detect format
        $isNewFormat = isset($data['version']) && isset($data['profiles']);
        
        if ($isNewFormat) {
            $profilesToImport = $importProfiles ? ($data['profiles'] ?? []) : [];
            $stylesToImport = $importStyles ? ($data['styles'] ?? []) : [];
            $styleGroupsToImport = $importStyleGroups ? ($data['style_groups'] ?? []) : [];
            $templatesToImport = $importTemplates ? ($data['templates'] ?? []) : [];
            $templateGroupsToImport = $importTemplateGroups ? ($data['template_groups'] ?? []) : [];
        } else {
            // Old format - only profiles
            $profilesToImport = $importProfiles ? $data : [];
            $stylesToImport = [];
            $styleGroupsToImport = [];
            $templatesToImport = [];
            $templateGroupsToImport = [];
        }

        // Import profiles
        foreach ($profilesToImport as $i => $profile) {
            $fail = false;
            foreach ($importKeys as $key) {
                if (is_array($profile) && !array_key_exists($key, $profile)) {
                    $importResult[] = rex_view::error(sprintf($this->i18n('profiles_import_validation_fail'), "$i"));
                    $fail = true;
                    break;
                }
            }
            if (!$fail && is_array($profile)) {
                /** @var array<string,string> $profile */
                $result = Cke5DatabaseHandler::importProfile($profile);
                $importResult[] = rex_view::info(sprintf($this->i18n('profiles_import_' . (($result === true) ? 'success' : 'fail')), $profile['name'], $profile['id']));
            }
        }

        // Import styles
        foreach ($stylesToImport as $i => $style) {
            try {
                if (!is_array($style)) {
                    continue;
                }
                $sql = rex_sql::factory();
                $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_STYLES));

                foreach ($style as $key => $value) {
                    if ($key === 'id') continue;
                    $sql->setValue($key, $value);
                }

                if (isset($style['id']) && $style['id'] > 0) {
                    $sql->setWhere('id=:id', ['id' => $style['id']]);
                    $sql->update();
                } else {
                    $sql->insert();
                }
                $importResult[] = rex_view::info('✓ Style "' . ($style['name'] ?? 'N/A') . '" importiert');
            } catch (Exception $e) {
                $importResult[] = rex_view::error('✗ Style konnte nicht importiert werden: ' . $e->getMessage());
            }
        }

        // Import style groups
        foreach ($styleGroupsToImport as $i => $styleGroup) {
            try {
                if (!is_array($styleGroup)) {
                    continue;
                }
                $sql = rex_sql::factory();
                $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_STYLE_GROUPS));

                foreach ($styleGroup as $key => $value) {
                    if ($key === 'id') continue;
                    $sql->setValue($key, $value);
                }

                if (isset($styleGroup['id']) && $styleGroup['id'] > 0) {
                    $sql->setWhere('id=:id', ['id' => $styleGroup['id']]);
                    $sql->update();
                } else {
                    $sql->insert();
                }
                $importResult[] = rex_view::info('✓ Style-Gruppe "' . ($styleGroup['name'] ?? 'N/A') . '" importiert');
            } catch (Exception $e) {
                $importResult[] = rex_view::error('✗ Style-Gruppe konnte nicht importiert werden: ' . $e->getMessage());
            }
        }

        // Import templates
        foreach ($templatesToImport as $i => $template) {
            try {
                if (!is_array($template)) {
                    continue;
                }
                $sql = rex_sql::factory();
                $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_TEMPLATES));

                foreach ($template as $key => $value) {
                    if ($key === 'id') continue;
                    $sql->setValue($key, $value);
                }

                if (isset($template['id']) && $template['id'] > 0) {
                    $sql->setWhere('id=:id', ['id' => $template['id']]);
                    $sql->update();
                } else {
                    $sql->insert();
                }
                $importResult[] = rex_view::info('✓ Template "' . ($template['title'] ?? 'N/A') . '" importiert');
            } catch (Exception $e) {
                $importResult[] = rex_view::error('✗ Template konnte nicht importiert werden: ' . $e->getMessage());
            }
        }

        // Import template groups
        foreach ($templateGroupsToImport as $i => $templateGroup) {
            try {
                if (!is_array($templateGroup)) {
                    continue;
                }
                $sql = rex_sql::factory();
                $sql->setTable(rex::getTable(Cke5DatabaseHandler::CKE5_TEMPLATE_GROUPS));

                foreach ($templateGroup as $key => $value) {
                    if ($key === 'id') continue;
                    $sql->setValue($key, $value);
                }

                if (isset($templateGroup['id']) && $templateGroup['id'] > 0) {
                    $sql->setWhere('id=:id', ['id' => $templateGroup['id']]);
                    $sql->update();
                } else {
                    $sql->insert();
                }
                $importResult[] = rex_view::info('✓ Template-Gruppe "' . ($templateGroup['name'] ?? 'N/A') . '" importiert');
            } catch (Exception $e) {
                $importResult[] = rex_view::error('✗ Template-Gruppe konnte nicht importiert werden: ' . $e->getMessage());
            }
        }
        
        // Clear session
        rex_unset_session('cke5_import_data');
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