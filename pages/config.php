<?php
/** @var rex_addon $this */

// Instanzieren des Formulars
$form = rex_config_form::factory('cke5');

// Fieldset 1
$form->addFieldset($this->i18n('config'));

// license cke5 js path
$field = $form->addInputField('text', 'license_cke5_js_path', null, ['class' => 'form-control', 'placeholder' => $this->i18n('license_cke5_js_path_placeholder')]);
$field->setLabel($this->i18n('license_cke5_js_path'));

// license cke5 translations path
$field = $form->addInputField('text', 'license_translations_path', null, ['class' => 'form-control', 'placeholder' => $this->i18n('license_translations_path_placeholder')]);
$field->setLabel($this->i18n('license_translations_path'));

// cke5 license code
$field = $form->addInputField('text', 'license_code', null, ['class' => 'form-control', 'placeholder' => $this->i18n('license_code_placeholder')]);
$field->setLabel($this->i18n('license_code'));

// Ausgabe des Formulars
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('config_title'), false);
$fragment->setVar('body', $form->get(), false);
echo $fragment->parse('core/page/section.php');