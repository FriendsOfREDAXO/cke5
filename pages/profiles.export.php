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
$message = '';
$profiles = \Cke5\Handler\Cke5DatabaseHandler::getAllProfiles();

// action
if (count($_POST) > 0) {

    try {
        $exportIds = array_shift($_POST);

        if (!isset($exportIds['profiles'])) {
            throw new LengthException();
        }

        // get the export id's
        $exportIds = (isset($exportIds['profiles'])) ? $exportIds['profiles'] : [];
        $exportProfiles = [];
        $exportNames = [];

        // and use the loaded profiles
        foreach ($profiles as $profile) {
            if (in_array($profile['id'], $exportIds)) {
                $exportProfiles[] = $profile; // to get the entire stuff
                $exportNames[] = $profile['name']; // and to get the file name
            }
        }

        // create filename and export the data set
        $names = (strlen(implode('_', $exportNames)) > 100) ? implode('_', $exportNames) : substr(implode('_', $exportNames), 0, 100) . '_etc_';
        $fileName = 'cke5_profiles_' . $names . '_' . date('YmdHis') . '.json';
        header('Content-Disposition: attachment; filename="' . $fileName . '"; charset=utf-8'); // create header info
        rex_response::sendContent(json_encode($exportProfiles), 'application/octetstream'); // stream it out
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
if ($func == 'error') {
    echo $message;
    $func = '';
}

// get form without action
if ($func == '') {
    // initialize rex form
    $form = rex_config_form::factory('demo_addon');

    // add select
    $field = $form->addSelectField('profiles', null, ['class' => 'form-control']);
    $field->setAttribute('multiple', 'multiple');
    $field->setLabel($this->i18n('profiles_select'));
    $select = $field->getSelect();
    // set select size
    $select->setSize((count($profiles) < 10) ? count($profiles) : 10);

    // add profiles
    if (count($profiles) > 0) {
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