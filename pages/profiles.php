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

$profileTable = rex::getTable(\Cke5\Handler\Cke5DatabaseHandler::CKE5_PROFILES);
$message = '';

if ($func == 'clone') {
    $message = \Cke5\Utils\Cke5ListHelper::cloneData($profileTable, $id);
    rex_extension::registerPoint(new rex_extension_point('CKE5_CLONE_PROFILE', $id));
    $func = '';
}

if ($func == 'delete') {
    $message = \Cke5\Utils\Cke5ListHelper::deleteData($profileTable, $id);
    rex_extension::registerPoint(new rex_extension_point('CKE5_DELETE_PROFILE', $id));
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
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="'.rex_i18n::msg('cke5_add_profile').'"><i class="rex-icon rex-icon-add-action"></i></a>';
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

    }, array('list'=> $list, 'name' => 'delete', 'icon' => 'rex-icon-delete', 'icon_type' => 'rex-offline', 'msg' => rex_i18n::msg('delete')));
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
    $form = rex_form::factory($profileTable, '', 'id=' . $id);
    $form->addParam('start', $start);

    $in_heading = '';
    $in_alignment = '';
    $in_fontsize = '';
    $in_rexlink = '';
    $in_minmax = '';
    $in_highlight = '';
    $min_height = 0;
    $max_height = 0;

    if ($func == 'add') { $in_heading = 'in'; }
    if ($func == 'edit') {
        $form->addParam('id', $id);

        $result = $form->getSql()->getRow();
        $prefix = rex::getTable('cke5_profiles');
        $toolbar = explode(',', $result[$prefix . '.toolbar']);

        if (in_array('heading', $toolbar)) $in_heading = 'in';
        if (in_array('alignment', $toolbar)) $in_alignment = 'in';
        if (in_array('fontSize', $toolbar)) $in_fontsize = 'in';
        if (in_array('link', $toolbar)) $in_rexlink = 'in';
        if (in_array('$in_highlight', $toolbar)) $in_highlight = 'in';

        $min_height = (int) $result[$prefix . '.min_height'];
        $max_height = (int) $result[$prefix . '.max_height'];
    }

    // name
    $field = $form->addTextField('name');
    $field->setAttribute('id', 'cke5name-input');
    $field->setLabel(rex_i18n::msg('cke5_name'));
    $field->setAttribute('placeholder', rex_i18n::msg('cke5_name_placeholder'));

    // description
    $field = $form->addTextField('description');
    $field->setLabel(rex_i18n::msg('cke5_description'));
    $field->setAttribute('placeholder', rex_i18n::msg('cke5_description_placeholder'));
    $field->getValidator()->add( 'notEmpty', rex_i18n::msg('cke5_description_empty_error'));

    // toolbar
    $field = $form->addTextField('toolbar');
    $field->setAttribute('id', 'cke5toolbar-input');
    $field->setLabel(rex_i18n::msg('cke5_toolbar'));
    if ($func == 'add') $field->setAttribute('data-default-tags',1);

    // heading
    $form->addRawField('<div class="collapse ' . $in_heading . '" id="cke5heading-collapse">');
    $field = $form->addTextField('heading');
    $field->setAttribute('id', 'cke5heading-input');
    $field->setLabel(rex_i18n::msg('cke5_heading'));
    if ($func == 'add') $field->setAttribute('data-default-tags',1);
    $form->addRawField('</div>');

    // alignment
    $form->addRawField('<div class="collapse  ' . $in_alignment . '" id="cke5alignment-collapse">');
    $field = $form->addTextField('alignment');
    $field->setAttribute('id', 'cke5alignment-input');
    $field->setLabel(rex_i18n::msg('cke5_alignment'));
    if ($func == 'add') $field->setAttribute('data-default-tags',1);
    $form->addRawField('</div>');

    // fontsize
    $form->addRawField('<div class="collapse ' . $in_fontsize . '" id="cke5fontSize-collapse">');
    $field = $form->addTextField('fontsize');
    $field->setAttribute('id', 'cke5fontsize-input');
    $field->setLabel(rex_i18n::msg('cke5_fontSize'));
    if ($func == 'add') $field->setAttribute('data-default-tags',1);
    $form->addRawField('</div>');

    // rex link
    $form->addRawField('<div class="collapse ' . $in_rexlink . '" id="cke5link-collapse">');
    $field = $form->addTextField('rexlink');
    $field->setAttribute('id', 'cke5link-input');
    $field->setLabel(rex_i18n::msg('cke5_link'));
    if ($func == 'add') $field->setAttribute('data-default-tags',1);
    $form->addRawField('</div>');

    // highlight
    $form->addRawField('<div class="collapse ' . $in_highlight . '" id="cke5highlight-collapse">');
    $field = $form->addTextField('highlight');
    $field->setAttribute('id', 'cke5highlight-input');
    // TODO load defaults and tags from config
    $field->setAttribute('data-defaults', 'yellowMarker,greenMarker,redPen,greenPen');
    $field->setAttribute('data-tags', '["yellowMarker", "greenMarker", "pinkMarker", "blueMarker", "redPen", "greenPen"]');
    $field->setLabel(rex_i18n::msg('cke5_highlight'));
    if ($func == 'add') $field->setAttribute('data-default-tags',1);
    $form->addRawField('</div>');

    // TODO add special fonts

    // image toolbar
    $field = $form->addTextField('image_toolbar');
    $field->setAttribute('id', 'cke5image-input');
    $field->setLabel(rex_i18n::msg('cke5_image_toolbar'));
    if ($func == 'add') $field->setAttribute('data-default-tags',1);

    // default height
    $field = $form->addCheckboxField('height_default');
    $field->setAttribute('id', 'cke5height-input');
    $field->setLabel(rex_i18n::msg('cke5_height_default'));
    $field->addOption(rex_i18n::msg('cke5_height_default_description'), 'default_height');
    if ($func == 'add') $field->setValue('default_height');

    // min max height collapse
    $form->addRawField('<div class="collapse" id="cke5minmax-collapse">');
    // min height default 0 = none
    $field = $form->addTextField('min_height');
    $field->setAttribute('id', 'cke5minheight-input');
    $field->setLabel(rex_i18n::msg('cke5_min_height'));
    $field->setAttribute('data-slider-value', $min_height);

    // max height default 0 = none
    $field = $form->addTextField('max_height');
    $field->setAttribute('id', 'cke5maxheight-input');
    $field->setLabel(rex_i18n::msg('cke5_max_height'));
    $field->setAttribute('data-slider-value', $max_height);
    $form->addRawField('</div>');

    // upload default
    $field = $form->addCheckboxField('upload_default');
    $field->setAttribute('data-toggle', 'toggle');
    $field->setLabel(rex_i18n::msg('cke5_upload_default'));
    $field->addOption(rex_i18n::msg('cke5_upload_default_description'), 'default_upload');
    if ($func == 'add') $field->setValue('default_upload');

    // lang
    $field = $form->addSelectField('lang');
    $field->setAttribute('class', 'form-control selectpicker');
    $field->setLabel(rex_i18n::msg('cke5_lang'));
    $field->getSelect()->addOption('default', '');
    foreach (rex_i18n::getLocales() as $locale) {
        rex_i18n::setLocale($locale, false); // Locale nicht neu setzen
        $field->getSelect()->addOption(rex_i18n::msg('lang'), $locale);
    }

    if (rex_addon::exists('media_manager') && rex_addon::get('media_manager')->isAvailable()) {
        $field = $form->addSelectField('mediatype');
        $field->setAttribute('class', 'form-control selectpicker');
        $field->setLabel(rex_i18n::msg('cke5_media_manager_type'));
        $field->getSelect()->addOption('default ./media/', '');
        $field->getSelect()->addDBSqlOptions('SELECT name, name FROM ' . rex::getTablePrefix() . 'media_manager_type ORDER BY status, name');
    }

    if (rex_addon::exists('mediapool') && rex_addon::get('mediapool')->isAvailable()) {
        $field = $form->addSelectField('mediacategory');
        $field->setAttribute('class', 'form-control selectpicker');
        $field->setLabel(rex_i18n::msg('cke5_media_category'));
        $cats_sel = new rex_media_category_select();
        $cats_sel->setStyle('class="form-control selectpicker"');
        $cats_sel->setName('mediacategory');
        $cats_sel->addOption(rex_i18n::msg('pool_kats_no'), '0');
        $field->setSelect($cats_sel);
    }

    // show
    $content = $form->get();

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit cke5_profile_edit', false);
    $fragment->setVar('title', ($func == 'edit') ? rex_i18n::msg('cke5_profile_edit') : rex_i18n::msg('cke5_profile_add'));
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
}
