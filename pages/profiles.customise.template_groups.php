<?php
/** @var rex_addon $this */

use Cke5\Handler\Cke5DatabaseHandler;
use Cke5\Provider\Cke5NavigationProvider;
use Cke5\Utils\Cke5ListHelper;

$func = rex_request::request('func', 'string');
/** @var int $id */
$id = rex_request::request('id', 'int');
/** @var int $start */
$start = rex_request::request('start', 'int', NULL);
$send = rex_request::request('send', 'boolean', false);

$groupsTable = rex::getTable(Cke5DatabaseHandler::CKE5_TEMPLATE_GROUPS);
$templatesTable = rex::getTable(Cke5DatabaseHandler::CKE5_TEMPLATES);
$navigation = '<div class="cke5_subpagenavigation">' .
    Cke5NavigationProvider::getSubNavigation('profiles.customise') .
    '</div>';
$message = '';

// info area
$fragment = new rex_fragment();
$fragment->setVar('class', 'info" style="padding-bottom:0;margin-bottom:10px', false);
$fragment->setVar('title', '', false);
$fragment->setVar('body', '<strong><i class="fa fa-star-o" aria-hidden="true"></i> ' . $this->i18n('cke5_pro_feature_info') . '</strong>', false);
$info = $fragment->parse('core/page/section.php');

if ($func === 'delete') {
    $message = Cke5ListHelper::deleteData($groupsTable, $id);
    rex_extension::registerPoint(new rex_extension_point('CKE5_TEMPLATE_GROUP_DELETE', $id));
    // Regeneriere CSS-Datei nach dem Löschen
    Cke5\Utils\Cke5CssHandler::regenerateCssFile();
    $func = '';
}

if ($func === '') {
    // Listenansicht
    $list = rex_list::factory("SELECT id, name, description FROM $groupsTable ORDER BY id");
    $list->addTableAttribute('class', 'table-striped');

    // column with
    $list->addTableColumnGroup(array(40, '*', '*', 100, 90));

    // hide column
    $list->removeColumn('id');

    // action add/edit
    $thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('cke5_add_template_group') . '"><i class="rex-icon rex-icon-add-action"></i></a>';
    $tdIcon = '<i class="rex-icon fa-cube"></i>';

    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'id' => '###id###']);

    // name
    $list->setColumnLabel('name', rex_i18n::msg('cke5_template_group_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'id' => '###id###', 'start' => $start]);

    // description
    $list->setColumnLabel('description', rex_i18n::msg('cke5_template_group_description'));

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
    $fragment->setVar('title', rex_i18n::msg('cke5_template_groups'));
    $fragment->setVar('content', $message . $content, false);
    $fragment->setVar('before', $navigation . $info, false);
    echo $fragment->parse('core/page/section.php');

} elseif ($func === 'edit' || $func === 'add') {
    $id = rex_request('id', 'int');

    $form = rex_form::factory($groupsTable, '', 'id=' . $id, 'post');
    $form->addParam('start', $start);
    $form->addParam('send', true);

    $result = array();
    $prefix = '';

    if ($func === 'edit') {
        $form->addParam('id', $id);
        $result = rex_request::post($form->getName(), 'array', 'null');

        if (!is_array($result)) {
            $result = $form->getSql()->getRow();
            $prefix = rex::getTable('cke5_templates') . '.';
        }
    }

    $default_value = $func === 'add' && $send === false;
    $css = (is_array($result) && isset($result[$prefix . 'css']) && $result[$prefix . 'css'] !== '');

    // Name
    $field = $form->addTextField('name');
    $field->setLabel(rex_i18n::msg('cke5_template_group_name'));
    $field->setAttribute('placeholder', rex_i18n::msg('cke5_template_group_name_placeholder'));
    $field->getValidator()->add('notEmpty', rex_i18n::msg('cke5_template_group_name_error'));

    // Beschreibung
    $field = $form->addTextField('description');
    $field->setLabel(rex_i18n::msg('cke5_template_group_description'));
    $field->setAttribute('placeholder', rex_i18n::msg('cke5_template_group_description_placeholder'));

    // JSON Konfiguration
    $field = $form->addTextAreaField('json_config');
    $field->setLabel(rex_i18n::msg('cke5_template_group_json_configuration'));
    $field->setAttribute('class', 'rex-code');
    $field->setAttribute('data-codemirror-mode', 'application/json');
    $field->setNotice('
<div class="cke5-json-config-notice">
    <p><strong>Hinweis:</strong> Die hier eingegebene JSON-Konfiguration und die ausgewählten Templates werden im Editor-Profil zusammengeführt.</p>
    <p><strong>Beispiel-Konfiguration:</strong></p>
    <pre><code class="language-json">[
    {
        "title": "Callout Box",
        "data": "<div class=\"callout\"><p>Callout-Text hier einfügen</p></div>",
        "description": "Eine Callout-Box für wichtige Hinweise"
    },
    {
        "title": "Info Box", 
        "data": "<div class=\"info-box\"><h4>Überschrift</h4><p>Text hier einfügen</p></div>", 
        "description": "Informationsbox mit Überschrift und Text"
    }
]</code></pre>
</div>');

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

    $fragment = new rex_fragment();
    $fragment->setVar('class', 'edit', false);
    $fragment->setVar('title', ($func === 'edit') ? rex_i18n::msg('cke5_template_group_edit') : rex_i18n::msg('cke5_template_group_add'));
    $fragment->setVar('body', '<div class="cke5_template_edit">' . $form->get() . '</div>', false);
    $fragment->setVar('before', $navigation, false);
    echo $fragment->parse('core/page/section.php');
}