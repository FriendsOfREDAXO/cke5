<?php

namespace Cke5\Utils;


use Cke5\Creator\Cke5ProfilesCreator;
use rex_addon;
use rex_i18n;

class Cke5PreviewHelper
{
    /**
     * @param array<string,mixed> $profile
     */
    public static function getMFormCode(array $profile): string
    {
        return (rex_addon::exists('mform')) ? '<pre><span style="color: #aa0000">$mform</span>-&gt;<span style="color: #1e90ff">addTextAreaField</span>(<span style="color: #aa5500">&#39;1&#39;</span>, [<span style="color: #aa5500">&#39;class&#39;</span> =&gt; <span style="color: #aa5500">&#39;cke5-editor&#39;</span>, <span style="color: #aa5500">&#39;data-lang&#39;</span> =&gt; \Cke5\Utils\Cke5Lang::<span style="color: #1e90ff">getUserLang</span>(), <span style="color: #aa5500">&#39;data-content-lang&#39;</span> =&gt; \Cke5\Utils\Cke5Lang::<span style="color: #1e90ff">getOutputLang</span>(), <span style="color: #aa5500">&#39;data-profile&#39;</span> =&gt; <span style="color: #aa5500">&#39;' . $profile['name'] . '&#39;</span>]);</pre>' : '';
    }

    /**
     * @param array<string,mixed> $profile
     */
    public static function getHtmlCode(array $profile): string
    {
        return '<pre style=\'color:#000020;background:#f6f8ff;\'><span style=\'color:#0057a6; \'>&lt;</span><span style=\'color:#200080; font-weight:bold; \'>textarea</span><span style=\'color:#474796; \'> </span><span style=\'color:#074726; \'>class</span><span style=\'color:#308080; \'>=</span><span style=\'color:#1060b6; \'>"form-control cke5-editor"</span><span style=\'color:#474796; \'> </span><span style=\'color:#074726; \'>data</span><span style=\'color:#474796; \'>-</span><span style=\'color:#074726; \'>profile</span><span style=\'color:#308080; \'>=</span><span style=\'color:#1060b6; \'>"' . $profile['name'] . '"</span><span style=\'color:#474796; \'> </span><span style=\'color:#074726; \'>data</span><span style=\'color:#474796; \'>-</span><span style=\'color:#074726; \'>lang</span><span style=\'color:#308080; \'>=</span><span style=\'color:#1060b6; \'>"</span><span style=\'color:#333385; background:#cceeee; \'>&lt;?php</span><span style=\'color:#000000; background:#cceeee; \'> </span><span style=\'color:#200080; background:#cceeee; font-weight:bold; \'>echo</span><span style=\'color:#000000; background:#cceeee; \'> </span><span style=\'color:#406080; background:#cceeee; \'>\</span><span style=\'color:#0066ee; background:#cceeee; \'>Cke5</span><span style=\'color:#406080; background:#cceeee; \'>\</span><span style=\'color:#0066ee; background:#cceeee; \'>Utils</span><span style=\'color:#406080; background:#cceeee; \'>\</span><span style=\'color:#000000; background:#cceeee; \'>Cke5Lang</span><span style=\'color:#406080; background:#cceeee; \'>:</span><span style=\'color:#406080; background:#cceeee; \'>:</span><span style=\'color:#000000; background:#cceeee; \'>getUserLang</span><span style=\'color:#308080; background:#cceeee; \'>(</span><span style=\'color:#308080; background:#cceeee; \'>)</span><span style=\'color:#406080; background:#cceeee; \'>;</span><span style=\'color:#000000; background:#cceeee; \'> </span><span style=\'color:#333385; background:#cceeee; \'>?></span><span style=\'color:#1060b6; \'>"</span><span style=\'color:#474796; \'> </span><span style=\'color:#074726; \'>data</span><span style=\'color:#474796; \'>-</span><span style=\'color:#074726; \'>content</span><span style=\'color:#474796; \'>-</span><span style=\'color:#074726; \'>lang</span><span style=\'color:#308080; \'>=</span><span style=\'color:#1060b6; \'>"</span><span style=\'color:#333385; background:#cceeee; \'>&lt;?php</span><span style=\'color:#000000; background:#cceeee; \'> </span><span style=\'color:#200080; background:#cceeee; font-weight:bold; \'>echo</span><span style=\'color:#000000; background:#cceeee; \'> </span><span style=\'color:#406080; background:#cceeee; \'>\</span><span style=\'color:#0066ee; background:#cceeee; \'>Cke5</span><span style=\'color:#406080; background:#cceeee; \'>\</span><span style=\'color:#0066ee; background:#cceeee; \'>Utils</span><span style=\'color:#406080; background:#cceeee; \'>\</span><span style=\'color:#000000; background:#cceeee; \'>Cke5Lang</span><span style=\'color:#406080; background:#cceeee; \'>:</span><span style=\'color:#406080; background:#cceeee; \'>:</span><span style=\'color:#000000; background:#cceeee; \'>getOutputLang</span><span style=\'color:#308080; background:#cceeee; \'>(</span><span style=\'color:#308080; background:#cceeee; \'>)</span><span style=\'color:#406080; background:#cceeee; \'>;</span><span style=\'color:#000000; background:#cceeee; \'> </span><span style=\'color:#333385; background:#cceeee; \'>?></span><span style=\'color:#1060b6; \'>"</span><span style=\'color:#474796; \'> </span><span style=\'color:#074726; \'>name</span><span style=\'color:#308080; \'>=</span><span style=\'color:#1060b6; \'>"REX_INPUT_VALUE[1]"</span><span style=\'color:#0057a6; \'>></span>REX_VALUE[<span style=\'color:#008c00; \'>1</span>]<span style=\'color:#0057a6; \'>&lt;/</span><span style=\'color:#200080; font-weight:bold; \'>textarea</span><span style=\'color:#0057a6; \'>></span></pre>';
    }

    /**
     * @param array<string,int|string|null> $profile
     */
    public static function getProfileDetails(array $profile): string
    {
        /** @var array<string,string> $profile */
        $result = Cke5ProfilesCreator::mapProfile($profile);
        return '<a href="#profile' . $profile['id'] . '" data-toggle="collapse">' . rex_i18n::msg('cke5_editor_preview_shop_profile_settings') . '</a>
            <div id="profile' . $profile['id'] . '" class="collapse">
                <pre class="json_id_' . $profile['id'] . '">
                    ' . print_r(json_encode($result['profile'], JSON_PRETTY_PRINT), true) . '
                </pre>
            </div>
            <script>
            $(document).ready(function(){
                $(\'.json_id_' . $profile['id'] . '\').rainbowJSON();
            });
            </script>';
    }

    /**
     * @param array<string,int|string|null> $profile
     */
    public static function getProfilePreview(array $profile, bool $html = true, bool $mform = true, bool $details = true): string
    {
        $code = '';

        if ($html || $mform || $details) {
            $code = '<div class="cke5-preview-code">';
            $code .= ($html) ? self::getHtmlCode($profile) : '';
            $code .= ($mform) ? self::getMFormCode($profile) : '';
            $code .= ($details) ? self::getProfileDetails($profile) : '';
            $code .= '</div>';
        }

        return '        <div class="cke5-preview-row">
            <div class="cke5-preview-editor">
                <h4>"<a href="index.php?page=cke5/profiles&func=edit&id=' . $profile['id'] . '">' . $profile['name'] . '</a>" - ' . $profile['description'] . '</h4>
                <div class="cke5-editor" data-profile="' . $profile['name'] . '"></div>
                ' . $code . '
            </div>
        </div>';
    }
}