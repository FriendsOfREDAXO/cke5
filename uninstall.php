<?php
$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'cke5_profiles');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'cke5_styles');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'cke5_style_groups');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'cke5_templates');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'cke5_template_groups');
