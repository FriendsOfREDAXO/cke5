<?php
/**
 * This file is part of the cke5 package.
 *
 * @author (c) Friends Of REDAXO
 * @author <friendsof@redaxo.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
if (rex_i18n::getLocale()!='de_de'){
     $file = rex_file::get(rex_path::addon('cke5','README.md'));
}
else {
     $file = rex_file::get(rex_path::addon('cke5','README_de_de.md')); 
}
$body = '<div class="markdown-body">' . rex_markdown::factory()->parse($file) . '</div>';
$fragment = new rex_fragment();
$fragment->setVar('body', $body, false);
$content = '<div class="FoR-help">'.$fragment->parse('core/page/section.php').'</div>';
echo '<div class="cke5-help-page">'. $content .'</div>';
?>
