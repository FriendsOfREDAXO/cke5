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
            foreach ($data as $i => $profile) {
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