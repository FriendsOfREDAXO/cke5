<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

/** @var rex_addon $this */

$func = rex_request::request('func', 'string');
$id = rex_request::request('id', 'int');
$start = rex_request::request('start', 'int', NULL);
$send = rex_request::request('send', 'boolean', false);

$profileTable = rex::getTable(\Cke5\Handler\Cke5DatabaseHandler::CKE5_PROFILES);
$csrfToken = rex_csrf_token::factory('cke5_profiles_import');

// action

// get success msg

// get form without action
if ($func == '') {
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
    $sectionFragment->setVar('body', '<fieldset><input type="hidden" name="function" value="cke5import" />' . $formFragment->parse('core/form/form.php') . '</fieldset>' . $msg, false); // add form as body to fragment
    $sectionFragment->setVar('buttons', $buttonFragment->parse('core/form/submit.php'), false); // add buttons to fragment

    // add action area and print it out
    echo '<form action="' . rex_url::currentBackendPage() . '" enctype="multipart/form-data" method="post" data-confirm="' . $this->i18n('profiles_proceed_db_import') . '">' . $csrfToken->getHiddenField() . $sectionFragment->parse('core/page/section.php') . '</form>';
}