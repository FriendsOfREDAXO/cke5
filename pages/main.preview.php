<?php
/** @var rex_addon $this */

use Cke5\Handler\Cke5DatabaseHandler;
use Cke5\Provider\Cke5NavigationProvider;
use Cke5\Utils\Cke5PreviewHelper;

$content = '';

$table = rex::getTable(Cke5DatabaseHandler::CKE5_PROFILES);
$sql = rex_sql::factory();
$profiles = $sql->getArray("SELECT * FROM $table");

$content .= '<div class="cke5-preview">';

foreach ($profiles as $profile) {
    $content .= Cke5PreviewHelper::getProfilePreview($profile);
}

$content .= '</div>';

$content = Cke5NavigationProvider::getMainSubNavigationHeader() .
    Cke5NavigationProvider::getSubNavigation('main') .
    $content;

$fragment = new rex_fragment();
$fragment->setVar('class', 'cke5-preview', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
