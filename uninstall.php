<?php
$sql = rex_sql::factory();

// Delete tables
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'cke5_profiles');
$sql->setQuery('DROP TABLE IF EXISTS ' . rex::getTablePrefix() . 'cke5_mblock_demo');
