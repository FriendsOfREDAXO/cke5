<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

/** @var rex_addon $this */

$content = '';


$table = rex::getTable(\Cke5\Handler\Cke5DatabaseHandler::CKE5_PROFILES);
$sql = rex_sql::factory();
$profiles = $sql->getArray("SELECT * FROM $table");

$content .= '<div class="cke5-preview">';
// https://localhost:29443/redaxo/index.php?page=cke5/profiles&func=edit&id=1&start=&list=b27e9c07
foreach ($profiles as $profile) {
    $content .= '
        <div class="cke5-preview-row">
            <div class="cke5-preview-editor">
                <h4>"<a href="index.php?page=cke5/profiles&func=edit&id=' . $profile['id'] . '">' . $profile['name'] . '</a>" - ' . $profile['description'] . '</h4>
                    <div class="cke5-editor" data-profile="' . $profile['name'] . '"></div>
                <div class="cke5-preview-code">
                    <pre style=\'color:#000020;background:#f6f8ff;\'><span style=\'color:#0057a6; \'>&lt;</span><span style=\'color:#200080; font-weight:bold; \'>textarea</span><span style=\'color:#474796; \'> </span><span style=\'color:#074726; \'>class</span><span style=\'color:#308080; \'>=</span><span style=\'color:#1060b6; \'>"form-control cke5-editor"</span><span style=\'color:#474796; \'> </span><span style=\'color:#074726; \'>data</span><span style=\'color:#474796; \'>-</span><span style=\'color:#074726; \'>profile</span><span style=\'color:#308080; \'>=</span><span style=\'color:#1060b6; \'>"' . $profile['name'] . '"</span><span style=\'color:#474796; \'> </span><span style=\'color:#074726; \'>data</span><span style=\'color:#474796; \'>-</span><span style=\'color:#074726; \'>lang</span><span style=\'color:#308080; \'>=</span><span style=\'color:#1060b6; \'>"</span><span style=\'color:#333385; background:#cceeee; \'>&lt;?php</span><span style=\'color:#000000; background:#cceeee; \'> </span><span style=\'color:#200080; background:#cceeee; font-weight:bold; \'>echo</span><span style=\'color:#000000; background:#cceeee; \'> </span><span style=\'color:#406080; background:#cceeee; \'>\</span><span style=\'color:#0066ee; background:#cceeee; \'>Cke5</span><span style=\'color:#406080; background:#cceeee; \'>\</span><span style=\'color:#0066ee; background:#cceeee; \'>Utils</span><span style=\'color:#406080; background:#cceeee; \'>\</span><span style=\'color:#000000; background:#cceeee; \'>Cke5Lang</span><span style=\'color:#406080; background:#cceeee; \'>:</span><span style=\'color:#406080; background:#cceeee; \'>:</span><span style=\'color:#000000; background:#cceeee; \'>getUserLang</span><span style=\'color:#308080; background:#cceeee; \'>(</span><span style=\'color:#308080; background:#cceeee; \'>)</span><span style=\'color:#406080; background:#cceeee; \'>;</span><span style=\'color:#000000; background:#cceeee; \'> </span><span style=\'color:#333385; background:#cceeee; \'>?></span><span style=\'color:#1060b6; \'>"</span><span style=\'color:#474796; \'> </span><span style=\'color:#074726; \'>name</span><span style=\'color:#308080; \'>=</span><span style=\'color:#1060b6; \'>"REX_INPUT_VALUE[1]"</span><span style=\'color:#0057a6; \'>></span>REX_VALUE[<span style=\'color:#008c00; \'>1</span>]<span style=\'color:#0057a6; \'>&lt;/</span><span style=\'color:#200080; font-weight:bold; \'>textarea</span><span style=\'color:#0057a6; \'>></span></pre>
                </div>
            </div>
        </div>
    ';
}

$content .= '</div>';


$content = \Cke5\Provider\Cke5NavigationProvider::getSubNavigationHeader() .
    \Cke5\Provider\Cke5NavigationProvider::getSubNavigation() .
    $content;

$fragment = new rex_fragment();
$fragment->setVar('class', 'cke5-preview', false);
$fragment->setVar('body', $content, false);
echo $fragment->parse('core/page/section.php');
