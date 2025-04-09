<?php
/** @var rex_addon $this */

use Cke5\Creator\Cke5ProfilesCreator;
use Cke5\Handler\Cke5DatabaseHandler;
use Cke5\Provider\Cke5NavigationProvider;
use Cke5\Utils\Cke5FormHelper;
use Cke5\Utils\CKE5ISO6391;
use Cke5\Utils\Cke5ListHelper;
use Cke5\Utils\Cke5PreviewHelper;


$func = rex_request::request('func', 'string');
/** @var int $id */
$id = rex_request::request('id', 'int');
/** @var int $start */
$start = rex_request::request('start', 'int', NULL);
$send = rex_request::request('send', 'boolean', false);

$stylesTable = rex::getTable(Cke5DatabaseHandler::CKE5_STYLES);
$navigation = '<div class="cke5_subpagenavigation">' . Cke5NavigationProvider::getSubNavigation('profiles.customise') . '</div>';
$message = '';

if ($func === 'delete') {
    $message = Cke5ListHelper::deleteData($stylesTable, $id);
    rex_extension::registerPoint(new rex_extension_point('CKE5_PROFILE_STYLE_DELETE', $id));
    // Regeneriere CSS-Datei nach dem Löschen
    Cke5\Utils\Cke5CssHandler::regenerateCssFile();
    $func = '';
}

if ($func === '') {
    // instance list
    $list = rex_list::factory("SELECT id, name, element, classes FROM $stylesTable ORDER BY id");
    $list->addTableAttribute('class', 'table-striped');

    // column with
    $list->addTableColumnGroup(array(40, '*', '*', '*', 100, 90));

    // hide column
    $list->removeColumn('id');

    // action add/edit
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('cke5_add_style') . '"><i class="rex-icon rex-icon-add-action"></i></a>';
    $tdIcon = '<i class="rex-icon fa-cube"></i>';

    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);

    // name
    $list->setColumnLabel('name', rex_i18n::msg('cke5_name_style'));
    $list->setColumnParams('name', ['func' => 'edit', 'id' => '###id###', 'start' => $start]);

    // element
    $list->setColumnLabel('element', rex_i18n::msg('cke5_style_element'));

    // classes
    $list->setColumnLabel('classes', rex_i18n::msg('cke5_style_classes'));

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

    // show
    $content = $list->get();
    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('cke5_list_styles'));
    $fragment->setVar('content', $message . $content, false);
    $fragment->setVar('before', $navigation, false);
    echo $fragment->parse('core/page/section.php');

} elseif ($func === 'edit' || $func === 'add') {

    $id = rex_request('id', 'int');
    $form = rex_form::factory($stylesTable, '', 'id=' . $id, 'post');
    $form->addParam('start', $start);
    $form->addParam('send', true);

    $result = array();
    $prefix = '';

    if ($func === 'edit') {
        $form->addParam('id', $id);
        $result = rex_request::post($form->getName(), 'array', 'null');

        if (!is_array($result)) {
            $result = $form->getSql()->getRow();
            $prefix = rex::getTable('cke5_styles') . '.';
        }
    }

    $default_value = $func === 'add' && $send === false;
    $css = (is_array($result) && isset($result[$prefix . 'css']) && $result[$prefix . 'css'] !== '');

    // name
    $field = $form->addTextField('name');
    $field->setLabel(rex_i18n::msg('cke5_name_style'));
    $field->setAttribute('placeholder', rex_i18n::msg('cke5_name_style_placeholder'));
    $field->getValidator()->add('notEmpty', rex_i18n::msg('cke5_name_style_empty_error'));

    // element
    $field = $form->addTextField('element');
    $field->setLabel(rex_i18n::msg('cke5_style_element'));
    $field->setAttribute('id', 'cke5-element-area');
    $field->setAttribute('data-tags', '["' . implode('","', Cke5ProfilesCreator::HTML_ELEMENTS) . '"]');
    $field->getValidator()->add('notEmpty', rex_i18n::msg('cke5_style_element_empty_error'));

    // style classes
    $field = $form->addTextField('classes');
    $field->setLabel(rex_i18n::msg('cke5_classes_area'));
    $field->setAttribute('id', 'cke5-classes-area');
    $field->getValidator()->add('notEmpty', rex_i18n::msg('cke5_classes_area_empty_error'));

    // custom area for css
    $field = $form->addCheckboxField('css');
    $field->setLabel(rex_i18n::msg('cke5_css_definition'));
    $field->setAttribute('data-toggle', 'toggle');
    $field->setAttribute('data-collapse-target', 'cssDefinition');
    $field->addOption(rex_i18n::msg('cke5_css_definition_description'), 'css_definition');

    // css area
    $form->addRawField('<div class="collapse '.(($css) ? 'in' : '').'" id="cke5cssDefinition-collapse">');
        // css definition
        $field = $form->addTextAreaField('css_definition');
        $field->setLabel(rex_i18n::msg('cke5_css_definition_area'));
        $field->setAttribute('id', 'cke5-css-definition-area');
        $field->setAttribute('class', 'rex-code');
        $field->setAttribute('data-codemirror-mode', 'text/css');

        // css path
        $field = $form->addTextField('css_path');
        $field->setLabel(rex_i18n::msg('cke5_css_path'));
        $field->setAttribute('placeholder', rex_i18n::msg('cke5_css_path_placeholder'));
    // end collapse
    $form->addRawField('</div>');

    // show
    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', ($func === 'edit') ? rex_i18n::msg('cke5_style_edit') : rex_i18n::msg('cke5_style_add'));
    $fragment->setVar('body', '<div class="cke5_style_edit">' . $form->get() . '</div>', false);
    $fragment->setVar('before', $navigation, false);
    echo $fragment->parse('core/page/section.php');
}

