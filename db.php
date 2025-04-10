<?php
// install profiles database
$sql = rex_sql_table::get(rex::getTable('cke5_profiles'));
$sql->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('name', 'varchar(40)', true))
    ->ensureColumn(new rex_sql_column('description', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('toolbar', 'text', true))
    ->ensureColumn(new rex_sql_column('expert_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('expert_suboption', 'text', true))
    ->ensureColumn(new rex_sql_column('expert', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('extra_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('extra', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('code_block', 'text', true))
    ->ensureColumn(new rex_sql_column('special_characters', 'text', true))
    ->ensureColumn(new rex_sql_column('group_when_full', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('table_color_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('table_color', 'text', true))
    ->ensureColumn(new rex_sql_column('ytable', 'text', true))
    ->ensureColumn(new rex_sql_column('transformation', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('transformation_extra', 'text', true))
    ->ensureColumn(new rex_sql_column('transformation_remove', 'text', true))
    ->ensureColumn(new rex_sql_column('transformation_include', 'text', true))
    ->ensureColumn(new rex_sql_column('html_support_allow', 'text', true))
    ->ensureColumn(new rex_sql_column('html_support_disallow', 'text', true))
    ->ensureColumn(new rex_sql_column('blank_to_external', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('link_internalcategory', 'int(4)', true))
    ->ensureColumn(new rex_sql_column('link_mediatypes', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('link_mediacategory', 'int(4)', true))
    ->ensureColumn(new rex_sql_column('link_downloadable', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('link_decorators', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('link_decorators_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('auto_link', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('text_part_language', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('heading', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('alignment', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('image_toolbar', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('image_resize_unit', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('image_resize_handles', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('image_resize_options', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('image_resize_group_options', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('image_resize_options_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('fontsize', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('highlight', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('emoji', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('table_toolbar', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('rexlink', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('list_style', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('list_start_index', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('list_reversed', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('html_preview', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('height_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('min_height', 'int(4)', true))
    ->ensureColumn(new rex_sql_column('max_height', 'int(4)', true))
    ->ensureColumn(new rex_sql_column('lang', 'varchar(2)', true))
    ->ensureColumn(new rex_sql_column('lang_content', 'varchar(2)', true))
    ->ensureColumn(new rex_sql_column('font_color', 'text', true))
    ->ensureColumn(new rex_sql_column('font_color_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('font_background_color', 'text', true))
    ->ensureColumn(new rex_sql_column('font_background_color_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('font_families', 'text', true))
    ->ensureColumn(new rex_sql_column('font_family_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mediaembed', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mentions', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mentions_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('styles', 'text', true))
    ->ensureColumn(new rex_sql_column('group_styles', 'text', true))
    ->ensureColumn(new rex_sql_column('templates', 'text', true))
    ->ensureColumn(new rex_sql_column('group_templates', 'text', true))
    ->ensureColumn(new rex_sql_column('sprog_mention', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('sprog_mention_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('mediatype', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mediatypes', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mediapath', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('mediacategory', 'int(4)', true))
    ->ensureColumn(new rex_sql_column('upload_mediacategory', 'int(4)', true))
    ->ensureColumn(new rex_sql_column('upload_default', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('createdate', 'datetime', true))
    ->ensureColumn(new rex_sql_column('updatedate', 'datetime', true))
    ->ensureColumn(new rex_sql_column('createuser', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('updateuser', 'varchar(255)', true));

foreach (rex_i18n::getLocales() as $locale) {
    $sql->ensureColumn(new rex_sql_column('placeholder_' . $locale, 'varchar(255)', true));
}

$sql->ensure();


// install styles database
$sql = rex_sql_table::get(rex::getTable('cke5_styles'));
$sql->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('name', 'varchar(40)', true))
    ->ensureColumn(new rex_sql_column('element', 'varchar(40)', true))
    ->ensureColumn(new rex_sql_column('classes', 'text', true))
    ->ensureColumn(new rex_sql_column('css', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('css_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('css_path', 'varchar(255)', true))
    ->ensure();

// install style_groups database
$sql = rex_sql_table::get(rex::getTable('cke5_style_groups'));
$sql->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('name', 'varchar(100)', true))
    ->ensureColumn(new rex_sql_column('description', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('json_config', 'text', true))
    ->ensureColumn(new rex_sql_column('css', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('css_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('css_path', 'varchar(255)', true))
    ->ensure();


// install templates database
$sql = rex_sql_table::get(rex::getTable('cke5_templates'));
$sql->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('title', 'varchar(40)', true))
    ->ensureColumn(new rex_sql_column('data', 'text', true))
    ->ensureColumn(new rex_sql_column('icon', 'text', true))
    ->ensureColumn(new rex_sql_column('description', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('css', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('css_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('css_path', 'varchar(255)', true))
    ->ensure();

// install template_groups database
$sql = rex_sql_table::get(rex::getTable('cke5_template_groups'));
$sql->ensurePrimaryIdColumn()
    ->ensureColumn(new rex_sql_column('name', 'varchar(100)', true))
    ->ensureColumn(new rex_sql_column('description', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('json_config', 'text', true))
    ->ensureColumn(new rex_sql_column('css', 'varchar(255)', true))
    ->ensureColumn(new rex_sql_column('css_definition', 'text', true))
    ->ensureColumn(new rex_sql_column('css_path', 'varchar(255)', true))
    ->ensure();
