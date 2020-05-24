<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

use Cke5\Creator\Cke5ProfilesCreator;
use Cke5\Utils\CKE5ISO6391;

/** @var rex_addon $this */

$func = rex_request::request('func', 'string');
$id = rex_request::request('id', 'int');
$start = rex_request::request('start', 'int', NULL);
$send = rex_request::request('send', 'boolean', false);

$profileTable = rex::getTable(\Cke5\Handler\Cke5DatabaseHandler::CKE5_PROFILES);
$message = '';

if ($func == 'clone') {
    $message = \Cke5\Utils\Cke5ListHelper::cloneData($profileTable, $id);
    rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_CLONE', $id));
    $func = '';
}

if ($func == 'delete') {
    $message = \Cke5\Utils\Cke5ListHelper::deleteData($profileTable, $id);
    rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_DELETE', $id));
    $func = '';
}

if ($func == '') {

    // instance list
    $list = rex_list::factory("SELECT id, name, description FROM $profileTable ORDER BY id");
    $list->addTableAttribute('class', 'table-striped');

    // column with
    $list->addTableColumnGroup(array(40, '*', '*', 100, 90, 120));

    // hide column
    $list->removeColumn('id');

    // action add/edit
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('cke5_add_profile') . '"><i class="rex-icon rex-icon-add-action"></i></a>';
    $tdIcon = '<i class="rex-icon fa-cube"></i>';

    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);

    // name
    $list->setColumnLabel('name', rex_i18n::msg('cke5_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'id' => '###id###', 'start' => $start]);

    // description
    $list->setColumnLabel('description', rex_i18n::msg('cke5_description'));

    // edit
    $list->addColumn('edit', '<i class="rex-icon fa-pencil-square-o"></i> ' . rex_i18n::msg('edit'), -1, ['', '<td>###VALUE###</td>']);
    $list->setColumnLabel('edit', rex_i18n::msg('cke5_list_function'));
    $list->setColumnLayout('edit', array('<th colspan="3">###VALUE###</th>', '<td>###VALUE###</td>'));
    $list->setColumnParams('edit', ['func' => 'edit', 'id' => '###id###', 'start' => $start]);

    // delete
    $list->addColumn('delete', '');
    $list->setColumnLayout('delete', array('', '<td>###VALUE###</td>'));
    $list->setColumnParams('delete', ['func' => 'delete', 'id' => '###id###', 'start' => $start]);
    $list->setColumnFormat('delete', 'custom', function ($params) {
        $list = $params['list'];
        return $list->getColumnLink($params['params']['name'], "<span class=\"{$params['params']['icon_type']}\"><i class=\"rex-icon {$params['params']['icon']}\"></i> {$params['params']['msg']}</span>");

    }, array('list' => $list, 'name' => 'delete', 'icon' => 'rex-icon-delete', 'icon_type' => 'rex-offline', 'msg' => rex_i18n::msg('delete')));
    $list->addLinkAttribute('delete', 'data-confirm', rex_i18n::msg('delete') . ' ?');

    // clone
    $list->addColumn('clone', '<i class="rex-icon fa-clone"></i> ' . rex_i18n::msg('cke5_clone'), -1, ['', '<td>###VALUE###</td>']);
    $list->setColumnParams('clone', ['func' => 'clone', 'id' => '###id###', 'start' => $start]);
    $list->addLinkAttribute('clone', 'data-confirm', rex_i18n::msg('cke5_clone') . ' ?');

    // show
    $content = $list->get();
    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('cke5_list_profiles'));
    $fragment->setVar('content', $message . $content, false);
    echo $fragment->parse('core/page/section.php');

} elseif ($func == 'edit' || $func == 'add') {

    $id = rex_request('id', 'int');
    $form = rex_form::factory($profileTable, '', 'id=' . $id, 'post', false);
    $form->addParam('start', $start);
    $form->addParam('send', true);

    $min_height = 0;
    $max_height = 0;
    $default_value = ($func == 'add' && $send == false) ? true : false;
    $profile = '';
    $in_mediapath = '';
    $mediapath = str_replace(['../', '/'], '', rex_url::media());

    if ($func == 'add') {
        $toolbar = array('heading', 'imageUpload');
    }
    if ($func == 'edit') {
        $form->addParam('id', $id);
        $result = rex_request::post($form->getName(), 'array', 'null');

        if (is_array($result)) {
            $prefix = '';
        } else {
            $result = $form->getSql()->getRow();
            $prefix = rex::getTable('cke5_profiles') . '.';
        }

        $toolbar = explode(',', $result[$prefix . 'toolbar']);

        $min_height = (int)$result[$prefix . 'min_height'];
        $max_height = (int)$result[$prefix . 'max_height'];
        $in_mediapath = (empty($result[$prefix . 'mediatype'])) ? 'in' : '';
        $profile = $result[$prefix . 'name'];
        $mediapath = (empty($result[$prefix . 'mediapath'])) ? $mediapath : $result[$prefix . 'mediapath'];

        #if (rex_request::get('send', 'boolean', false)) {
        #    rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_UPDATED', '', $result, true));
        #}
    }

    // wrapper
    $form->addRawField('<div class="cke5_wrap_rex_profile_data">');

    // name
    $field = $form->addTextField('name');
    $field->setAttribute('id', 'cke5name-input');
    $field->setLabel(rex_i18n::msg('cke5_name'));
    $field->setAttribute('placeholder', rex_i18n::msg('cke5_name_placeholder'));
    $field->getValidator()->add('notEmpty', rex_i18n::msg('cke5_profile_name_empty_error'));

    // description
    $field = $form->addTextField('description');
    $field->setLabel(rex_i18n::msg('cke5_description'));
    $field->setAttribute('placeholder', rex_i18n::msg('cke5_description_placeholder'));
    $field->getValidator()->add('notEmpty', rex_i18n::msg('cke5_description_empty_error'));

    // end wrapper
    $form->addRawField('</div>');

    $locales = rex_i18n::getLocales();
    asort($locales);

    \Sked\Utils\Cke5FormHelper::addRexLangTabs($form, 'wrapper', rex_i18n::getLocale());
    foreach ($locales as $locale) {
        \Sked\Utils\Cke5FormHelper::addRexLangTabs($form, 'inner_wrapper', $locale, rex_i18n::getLocale());

        $field = $form->addTextField('placeholder_' . $locale);
        $field->setLabel(rex_i18n::msg('cke5_placeholder'));
        $field->setAttribute('placeholder', rex_i18n::msg('cke5_placeholder_placeholder') . ' ' . rex_i18n::msgInLocale('lang', $locale));

        \Sked\Utils\Cke5FormHelper::addRexLangTabs($form, 'close_inner_wrapper');
    }
    \Sked\Utils\Cke5FormHelper::addRexLangTabs($form, 'close_wrapper');

    // toolbar
    $field = $form->addTextField('toolbar');
    $field->setAttribute('id', 'cke5toolbar-input');
    $field->setAttribute('data-tag-init', 1);
    $field->setAttribute('data-defaults', Cke5ProfilesCreator::DEFAULTS['toolbar']);
    $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['toolbar']) . '"]');
    $field->setLabel(rex_i18n::msg('cke5_toolbar'));
    if ($default_value) $field->setAttribute('data-default-tags', 1);

    // heading
    $form->addRawField('<div class="collapse ' . ((in_array('heading', $toolbar)) ? 'in' : '') . '" id="cke5heading-collapse">');
    $field = $form->addTextField('heading');
    $field->setAttribute('id', 'cke5heading-input');
    $field->setAttribute('data-tag-init', 1);
    $field->setAttribute('data-defaults', Cke5ProfilesCreator::DEFAULTS['heading']);
    $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['heading']) . '"]');
    $field->setLabel(rex_i18n::msg('cke5_heading'));
    if ($default_value) $field->setAttribute('data-default-tags', 1);
    $form->addRawField('</div>');

    // alignment
    $form->addRawField('<div class="collapse  ' . ((in_array('alignment', $toolbar)) ? 'in' : '') . '" id="cke5alignment-collapse">');
    $field = $form->addTextField('alignment');
    $field->setAttribute('id', 'cke5alignment-input');
    $field->setAttribute('data-tag-init', 1);
    $field->setAttribute('data-defaults', Cke5ProfilesCreator::DEFAULTS['alignment']);
    $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['alignment']) . '"]');
    $field->setLabel(rex_i18n::msg('cke5_alignment'));
    if ($default_value) $field->setAttribute('data-default-tags', 1);
    $form->addRawField('</div>');

    // table
    $form->addRawField('<div class="collapse  ' . ((in_array('insertTable', $toolbar)) ? 'in' : '') . '" id="cke5insertTable-collapse">');
    $field = $form->addTextField('table_toolbar');
    $field->setAttribute('id', 'cke5inserttable-input');
    $field->setAttribute('data-tag-init', 1);
    $field->setAttribute('data-defaults', Cke5ProfilesCreator::DEFAULTS['table_toolbar']);
    $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['table_toolbar']) . '"]');
    $field->setLabel(rex_i18n::msg('cke5_table_toolbar'));
    if ($default_value) $field->setAttribute('data-default-tags', 1);
    $form->addRawField('</div>');

    // fontsize
    $form->addRawField('<div class="collapse ' . ((in_array('fontSize', $toolbar)) ? 'in' : '') . '" id="cke5fontSize-collapse">');
    $field = $form->addTextField('fontsize');
    $field->setLabel(rex_i18n::msg('cke5_fontSize'));
    $field->setAttribute('id', 'cke5fontsize-input');
    $field->setAttribute('data-tag-init', 1);
    $field->setAttribute('data-defaults', Cke5ProfilesCreator::DEFAULTS['fontsize']);
    $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['fontsize']) . '"]');
    if ($default_value) $field->setAttribute('data-default-tags', 1);
    $form->addRawField('</div>');

    // image toolbar
    $form->addRawField('<div class="collapse ' . ((in_array('rexImage', $toolbar) || in_array('imageUpload', $toolbar)) ? 'in' : '') . '" id="cke5imagetoolbar-collapse">');
    $field = $form->addTextField('image_toolbar');
    $field->setAttribute('id', 'cke5image-input');
    $field->setAttribute('data-tag-init', 1);
    $field->setAttribute('data-defaults', Cke5ProfilesCreator::DEFAULTS['image_toolbar']);
    $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['image_toolbar']) . '"]');
    $field->setLabel(rex_i18n::msg('cke5_image_toolbar'));
    if ($default_value) $field->setAttribute('data-default-tags', 1);
    $form->addRawField('</div>');

    // font color
    $form->addRawField('<div class="collapse ' . ((in_array('fontColor', $toolbar)) ? 'in' : '') . '" id="cke5fontColor-collapse">');
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
    $form->addRawField('</div></div>');

    // font background color
    $form->addRawField('<div class="collapse ' . ((in_array('fontBackgroundColor', $toolbar)) ? 'in' : '') . '" id="cke5fontBackgroundColor-collapse">');
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
    $form->addRawField('</div></div>');

    // font family
    $form->addRawField('<div class="collapse ' . ((in_array('fontFamily', $toolbar)) ? 'in' : '') . '" id="cke5fontFamily-collapse">');
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
    $form->addRawField('</div></div>');

    // rex link
    $form->addRawField('<div class="collapse ' . ((in_array('link', $toolbar)) ? 'in' : '') . '" id="cke5link-collapse">');
    $field = $form->addTextField('rexlink');
    $field->setAttribute('id', 'cke5link-input');
    $field->setAttribute('data-tag-init', 1);
    $field->setAttribute('data-defaults', Cke5ProfilesCreator::DEFAULTS['rexlink']);
    $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['rexlink']) . '"]');
    $field->setLabel(rex_i18n::msg('cke5_link'));
    if ($default_value) $field->setAttribute('data-default-tags', 1);
    $form->addRawField('</div>');

    // highlight
    $form->addRawField('<div class="collapse ' . ((in_array('highlight', $toolbar)) ? 'in' : '') . '" id="cke5highlight-collapse">');
    $field = $form->addTextField('highlight');
    $field->setAttribute('id', 'cke5highlight-input');
    $field->setAttribute('data-tag-init', 1);
    $field->setAttribute('data-defaults', Cke5ProfilesCreator::DEFAULTS['highlight']);
    $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['highlight']) . '"]');
    $field->setLabel(rex_i18n::msg('cke5_highlight'));
    if ($default_value) $field->setAttribute('data-default-tags', 1);
    $form->addRawField('</div>');

    // mediaEmbed provider
    $form->addRawField('<div class="collapse ' . ((in_array('mediaEmbed', $toolbar)) ? 'in' : '') . '" id="cke5mediaEmbed-collapse">');
    $field = $form->addTextField('mediaembed');
    $field->setLabel(rex_i18n::msg('cke5_mediaembed'));
    $field->setAttribute('id', 'cke5mediaEmbed-input');
    $field->setAttribute('data-tag-init', 1);
    $field->setAttribute('data-defaults', Cke5ProfilesCreator::DEFAULTS['mediaembed']);
    $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::ALLOWED_FIELDS['providers']) . '"]');
    if ($default_value) $field->setAttribute('data-default-tags', 1);
    $form->addRawField('</div>');

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

    // upload default
    $field = $form->addCheckboxField('upload_default');
    $field->setAttribute('data-toggle', 'toggle');
    $field->setAttribute('id', 'cke5uploaddefault-input');
    $field->setLabel(rex_i18n::msg('cke5_upload_default'));
    $field->addOption(rex_i18n::msg('cke5_upload_default_description'), 'default_upload');
    if ($default_value) $field->setValue('default_upload');

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
    $langFiles = glob($this->getPath('assets/vendor/ckeditor5-classic/translations/*.js'));

    foreach ($langFiles as $langFile) {
        $key = substr(pathinfo($langFile, PATHINFO_FILENAME), 0, 2);
        if (isset(CKE5ISO6391::$isolang[$key])) {
            $field->getSelect()->addOption(CKE5ISO6391::$isolang[$key] . ' [' . pathinfo($langFile, PATHINFO_FILENAME) . ']', pathinfo($langFile, PATHINFO_FILENAME));
            $fieldContentLang->getSelect()->addOption(CKE5ISO6391::$isolang[$key] . ' [' . pathinfo($langFile, PATHINFO_FILENAME) . ']', pathinfo($langFile, PATHINFO_FILENAME));
        }
    }

    // set current lang again to fix lang problem with php 7.0 and php 5.x
    rex_i18n::setLocale($lang, false);

    // mediapath
    $form->addRawField('
    <div class="collapse  ' . $in_mediapath . '" id="cke5insertMediapath-collapse">
        <dl class="rex-form-group form-group">
            <dt><label class="control-label" for="mediapath">' . rex_i18n::msg('cke5_mediapath') . '</label></dt>
            <dd class="form-inline">
              <div class="input-group col-sm-12">
                <span class="input-group-addon" style="width: 20px">/</span>
                <input id ="cke5mediapath-input" class="form-control" type="text" value="' . $mediapath . '" placeholder="default /' . $mediapath . '/">
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
        $field->getSelect()->addOption('default /' . $mediapath . '/', '');
        $field->getSelect()->addDBSqlOptions('SELECT name, name FROM ' . rex::getTablePrefix() . 'media_manager_type ORDER BY status, name');
    }

    if (rex_addon::exists('mediapool') && rex_addon::get('mediapool')->isAvailable()) {
        // mediacategory
        $form->addRawField('<div class="collapse" id="cke5mediacat-collapse">');
        $field = $form->addSelectField('mediacategory');
        $field->setAttribute('class', 'form-control selectpicker');
        $field->setLabel(rex_i18n::msg('cke5_media_category'));
        $cats_sel = new rex_media_category_select();
        $cats_sel->setStyle('class="form-control selectpicker"');
        $cats_sel->setName('mediacategory');
        $cats_sel->addOption(rex_i18n::msg('pool_kats_no'), '0');
        $field->setSelect($cats_sel);
        $form->addRawField('</div>');
    }

    if ($func == 'edit') {
        $profileResult = array();
        foreach ($result as $key => $value) {
            $profileResult[str_replace($prefix, '', $key)] = $value;
        }
        $form->addRawField('
        <div class="cke5-preview-row">
            <dl class="rex-form-group form-group">
                <dt>
                    <label class="control-label">' . rex_i18n::msg('cke5_editor_preview') . '</label>
                </dt>
                <dd>
                    <div class="cke5-editor" data-profile="' . $profile . '" data-lang="' . \Cke5\Utils\Cke5Lang::getUserLang() . '"></div>           
                    <div class="cke5-editor-info"><p>' . rex_i18n::msg('cke5_editor_preview_info') . '</p></div>
                    <div class="cke5-preview-code">
                        ' . \Cke5\Utils\Cke5PreviewHelper::getHtmlCode($profileResult) . '
                        ' . \Cke5\Utils\Cke5PreviewHelper::getMFormCode($profileResult) . '
                    </div>
                </dd>
            </dl>
        </div>
        ');
    }

    // show
    $content = '<div class="cke5_profile_edit" data-cktypes=\'["' . implode('","', Cke5ProfilesCreator::EDITOR_SETTINGS['cktypes']) . '"]\' data-ckimgtypes=\'["' . implode('","', Cke5ProfilesCreator::EDITOR_SETTINGS['ckimgtypes']) . '"]\'>' . $form->get() . '</div>';

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', ($func == 'edit') ? rex_i18n::msg('cke5_profile_edit') : rex_i18n::msg('cke5_profile_add'));
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
}
