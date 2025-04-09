<?php
/** @var rex_addon $this */

// Instanzieren des Formulars
$form = rex_config_form::factory('cke5');

// fieldset
$form->addFieldset($this->i18n('cke5_config_header'));

// info area
$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', '', false);
$fragment->setVar('body', $this->i18n('cke5_config_info'), false);
$info = $fragment->parse('core/page/section.php');

// license cke5 js path
$field = $form->addInputField('text', 'license_cke5_js_path', null, ['class' => 'form-control', 'placeholder' => $this->i18n('license_cke5_js_path_placeholder')]);
$field->setLabel($this->i18n('license_cke5_js_path'));
$field->setNotice($this->i18n('license_cke5_js_path_info'));

// license cke5 translations path
$field = $form->addInputField('text', 'license_translations_path', null, ['class' => 'form-control', 'placeholder' => $this->i18n('license_translations_path_placeholder')]);
$field->setLabel($this->i18n('license_translations_path'));
$field->setNotice($this->i18n('license_translations_path_info'));

// cke5 license code
$field = $form->addInputField('text', 'license_code', null, ['class' => 'form-control', 'placeholder' => $this->i18n('license_code_placeholder')]);
$field->setLabel($this->i18n('license_code'));
$field->setNotice($this->i18n('license_code_info'));
// Setze GPL als Standardwert, falls kein Wert vorhanden ist
if ($this->getConfig('license_code') === null) {
    $field->setValue('GPL');
}

// Ausgabe des Formulars
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('config_title'), false);
$fragment->setVar('body', $info . $form->get(), false);
echo $fragment->parse('core/page/section.php');