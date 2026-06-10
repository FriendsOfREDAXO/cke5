<?php
/** @var rex_addon $this */

use Cke5\Provider\Cke5NavigationProvider;
use Cke5\Handler\Cke5ExtensionHandler;
use Cke5\Creator\Cke5ProfilesCreator;

$navigation = '<div class="cke5_subpagenavigation">' . Cke5NavigationProvider::getSubNavigation('profiles.customise') . '</div>';
$defaultMediaPath = str_replace(['../', '/'], '', rex_url::media());

$form = rex_config_form::factory('cke5');
$form->addFieldset($this->i18n('cke5_global_settings'));

$field = $form->addCheckboxField('global_mentions_enabled');
$field->setAttribute('id', 'cke5global-mentions-enabled-input');
$field->setAttribute('data-toggle', 'toggle');
$field->setAttribute('data-collapse-target', 'globalMentions');
$field->setLabel($this->i18n('cke5_global_mentions_definition'));
$field->addOption($this->i18n('cke5_global_mentions_definition_notice'), '1');

$form->addRawField('<div class="collapse" id="cke5globalMentions-collapse">');
$field = $form->addTextAreaField('global_mentions_definition');
$field->setLabel($this->i18n('cke5_global_mentions_definition'));
$field->setAttribute('id', 'cke5-global-mentions-area');
$field->setAttribute('class', 'rex-code');
$field->setAttribute('data-codemirror-mode', 'application/json');
$field->setNotice($this->i18n('cke5_mentions_definition_example'));
$form->addRawField('</div>');

$field = $form->addCheckboxField('global_sprog_enabled');
$field->setAttribute('id', 'cke5global-sprog-enabled-input');
$field->setAttribute('data-toggle', 'toggle');
$field->setAttribute('data-collapse-target', 'globalSprog');
$field->setLabel($this->i18n('cke5_global_sprog_mention_definition'));
$field->addOption($this->i18n('cke5_global_sprog_mention_definition_notice'), '1');

$form->addRawField('<div class="collapse" id="cke5globalSprog-collapse">');
$field = $form->addTextAreaField('global_sprog_mention_definition');
$field->setLabel($this->i18n('cke5_global_sprog_mention_definition'));
$field->setAttribute('id', 'cke5-global-sprog-area');
$field->setAttribute('data-sprog-key-placeholder', $this->i18n('cke5_sprog_mention_key'));
$field->setAttribute('data-sprog-description-placeholder', $this->i18n('cke5_sprog_mention_description'));
$form->addRawField('</div>');

$field = $form->addCheckboxField('global_ytable_enabled');
$field->setAttribute('id', 'cke5global-ytable-enabled-input');
$field->setAttribute('data-toggle', 'toggle');
$field->setAttribute('data-collapse-target', 'globalYtable');
$field->setLabel($this->i18n('cke5_global_ytable_definition'));
$field->addOption($this->i18n('cke5_global_ytable_definition_notice'), '1');

$form->addRawField('<div class="collapse" id="cke5globalYtable-collapse">');
$field = $form->addTextAreaField('global_ytable_definition');
$field->setLabel($this->i18n('cke5_global_ytable_definition'));
$field->setAttribute('id', 'cke5-global-ytable-area');
$field->setAttribute('data-ytable-table-placeholder', $this->i18n('cke5_ytable_table_placeholder'));
$field->setAttribute('data-ytable-column-placeholder', $this->i18n('cke5_ytable_column_placeholder'));
$field->setAttribute('data-ytable-title-placeholder', $this->i18n('cke5_ytable_title_placeholder'));
$form->addRawField('</div>');

$field = $form->addCheckboxField('global_media_enabled');
$field->setAttribute('id', 'cke5global-media-enabled-input');
$field->setAttribute('data-toggle', 'toggle');
$field->setAttribute('data-collapse-target', 'globalMedia');
$field->setLabel($this->i18n('cke5_global_mediatypes'));
$field->addOption($this->i18n('cke5_global_mediatypes'), '1');

$form->addRawField('<div class="collapse" id="cke5globalMedia-collapse">');
$field = $form->addInputField('text', 'global_mediatypes', null, ['class' => 'form-control', 'placeholder' => $this->i18n('cke5_mediatypes_placeholder')]);
$field->setLabel($this->i18n('cke5_global_mediatypes'));

    $field = $form->addInputField('text', 'global_mediapath', null, ['class' => 'form-control', 'placeholder' => 'default /' . $defaultMediaPath . '/']);
    $field->setLabel($this->i18n('cke5_global_mediapath'));

    if (rex_addon::exists('media_manager') && rex_addon::get('media_manager')->isAvailable()) {
        $field = $form->addSelectField('global_mediatype');
        $field->setAttribute('class', 'form-control selectpicker');
        $field->setLabel($this->i18n('cke5_global_mediatype'));
        $field->getSelect()->addOption('default /' . $defaultMediaPath . '/', '');
        $field->getSelect()->addDBSqlOptions('SELECT name, name FROM ' . rex::getTablePrefix() . 'media_manager_type ORDER BY status, name');
    }
$form->addRawField('</div>');

$field = $form->addCheckboxField('global_font_family_default');
$field->setAttribute('id', 'cke5global-font-family-default-input');
$field->setAttribute('data-toggle', 'toggle');
$field->setAttribute('data-collapse-target', 'globalCustomFontFamily');
$field->setLabel($this->i18n('cke5_global_font_family_default'));
$field->addOption($this->i18n('cke5_global_font_family_default_notice'), '1');

$form->addRawField('<div class="collapse" id="cke5globalCustomFontFamily-collapse">');
$field = $form->addTextAreaField('global_font_families');
$field->setLabel($this->i18n('cke5_font_family'));
$field->setAttribute('id', 'cke5-global-fontfamily-area');
$field->setAttribute('data-family-placeholder', $this->i18n('cke5_family_name_placeholder'));
$form->addRawField('</div>');

$field = $form->addCheckboxField('global_clear_widget_enabled');
$field->setAttribute('id', 'cke5global-clear-widget-enabled-input');
$field->setAttribute('data-toggle', 'toggle');
$field->setAttribute('data-collapse-target', 'globalClearWidget');
$field->setLabel($this->i18n('cke5_global_clear_widget_definition'));
$field->addOption($this->i18n('cke5_global_clear_widget_definition_notice'), '1');

$form->addRawField('<div class="collapse" id="cke5globalClearWidget-collapse">');
$field = $form->addTextAreaField('global_clear_widget_definition');
$field->setLabel($this->i18n('cke5_global_clear_widget_definition'));
$field->setAttribute('id', 'cke5-global-clear-widget-area');
$field->setAttribute('class', 'rex-code');
$field->setAttribute('data-codemirror-mode', 'application/json');
$field->setNotice($this->i18n('cke5_global_clear_widget_definition_example'));

$currentClearDefinition = (string) $this->getConfig('global_clear_widget_definition', '');
if ('' === trim($currentClearDefinition)) {
    $field->setValue(Cke5ProfilesCreator::DEFAULT_VALUES['global_clear_widget_definition']);
}
$form->addRawField('</div>');

$fragment = new rex_fragment();
$fragment->setVar('class', 'edit', false);
$fragment->setVar('title', $this->i18n('cke5_global_settings'), false);
$fragment->setVar('before', $navigation, false);
$fragment->setVar('body', '<div class="cke5_global_settings">' . $form->get() . '</div>', false);
echo $fragment->parse('core/page/section.php');

if (rex_request_method() === 'post') {
    Cke5ExtensionHandler::updateOrCreateProfiles();
}
