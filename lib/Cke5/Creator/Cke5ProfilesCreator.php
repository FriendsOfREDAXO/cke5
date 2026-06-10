<?php

namespace Cke5\Creator;

use Cke5\Handler\Cke5DatabaseHandler;
use Cke5\Utils\CKE5ISO6391;
use Exception;
use rex;
use rex_addon;
use rex_addon_interface;
use rex_file;
use rex_functional_exception;
use rex_i18n;
use rex_logger;
use rex_sql;

class Cke5ProfilesCreator
{
    /** @api string */
    const UPLOAD_URL = './index.php?cke5upload=1';
    const PROFILES_FILENAME = 'cke5profiles.js';
    const HTML_ELEMENTS = [
        'a',
        'abbr',
        'address',
        'area',
        'article',
        'aside',
        'audio',
        'b',
        'base',
        'bdi',
        'bdo',
        'blockquote',
        'body',
        'br',
        'button',
        'canvas',
        'caption',
        'cite',
        'code',
        'col',
        'colgroup',
        'data',
        'datalist',
        'dd',
        'del',
        'details',
        'dfn',
        'dialog',
        'div',
        'dl',
        'dt',
        'em',
        'embed',
        'fieldset',
        'figcaption',
        'figure',
        'footer',
        'form',
        'h1',
        'h2',
        'h3',
        'h4',
        'h5',
        'h6',
        'head',
        'header',
        'hgroup',
        'hr',
        'html',
        'i',
        'iframe',
        'img',
        'input',
        'ins',
        'kbd',
        'label',
        'legend',
        'li',
        'link',
        'main',
        'map',
        'mark',
        'meta',
        'meter',
        'nav',
        'noscript',
        'object',
        'ol',
        'optgroup',
        'option',
        'output',
        'p',
        'param',
        'picture',
        'pre',
        'progress',
        'q',
        'rp',
        'rt',
        'ruby',
        's',
        'samp',
        'script',
        'section',
        'select',
        'small',
        'source',
        'span',
        'strong',
        'style',
        'sub',
        'summary',
        'sup',
        'svg',
        'table',
        'tbody',
        'td',
        'template',
        'textarea',
        'tfoot',
        'th',
        'thead',
        'time',
        'title',
        'tr',
        'track',
        'u',
        'ul',
        'var',
        'video',
        'wbr'
    ];
    const EDITOR_SETTINGS = [
        /* todo: specialCharacters not work because : https://github.com/ckeditor/ckeditor5/issues/6160 */
        'cktypes' => ['heading', 'fontSize', 'mediaEmbed', 'fontFamily', 'alignment', 'link', 'highlight', 'insertTable', 'fontBackgroundColor', 'fontColor', 'codeBlock', 'bulletedList', 'numberedList', 'htmlEmbed'/*, 'emoji'*/, 'sourceEditing', 'textPartLanguage'/*, 'specialCharacters' */, 'style', 'snippets', 'for_video'],
        'ckimgtypes' => ['rexImage', 'imageUpload'],
        'cklinktypes' => ['ytable', 'media', 'internal'],
        'cktabletypes' => ['tableProperties', 'tableCellProperties']
    ];
    const DEFAULT_VALUES = [
        'toolbar' => 'bold,italic,bulletedList,numberedList,undo,redo,link,pastePlainText',
        'heading' => 'paragraph,h1,h2,h3,h4,h5,h6',
        'highlight' => 'yellowMarker,greenMarker,pinkMarker,blueMarker,redPen,greenPen',
        'alignment' => 'left,right,center,justify',
        'code_block' => 'plaintext,c,cs,cpp,css,diff,html,java,javascript,php,python,ruby,typescript,xml',
        'mediaembed' => 'dailymotion,spotify,youtube,vimeo,instagram,twitter,googleMaps,flickr,facebook',
        'special_characters' => 'currency,mathematical,latin,arrows,text',
        'table_toolbar' => '|,tableColumn,tableRow,mergeTableCells,tableProperties,tableCellProperties,toggleTableCaption',
        'image_toolbar' => '|,imageTextAlternative,block,inline,side,alignLeft,alignCenter,alignRight,alignBlockLeft,alignBlockRight,linkImage,toggleImageCaption',
        'fontsize' => 'default,tiny,small,big,huge',
        'rexlink' => 'internal,media,email,phone,ytable',
        'min_height' => ['"none"', '"100px"', '"200px"', '"300px"', '"400px"', '"500px"', '"600px"'],
        'max_height' => ['"none"', '"200px"', '"400px"', '"600px"', '"800px"', '"1000px"', '"1200px"'],
        'html_support_allow' => '[
    {
        "name": "regex(/^(div|section|article)$/)",
        "attributes": true,
        "classes": true,
        "styles": true
    }
]',
        'media_embed_styles_definition' => '[
    {
        "label": "Standard",
        "class": ""
    },
    {
        "label": "Links",
        "class": "media-embed--left"
    },
    {
        "label": "Zentriert",
        "class": "media-embed--center"
    },
    {
        "label": "Rechts",
        "class": "media-embed--right"
    },
    {
        "label": "Volle Breite",
        "class": "media-embed--full"
    }
]',
        'media_embed_width_styles_definition' => '[
    {
        "label": "Auto",
        "class": "media-embed--w-auto"
    },
    {
        "label": "50%",
        "class": "media-embed--w-50"
    },
    {
        "label": "75%",
        "class": "media-embed--w-75"
    },
    {
        "label": "100%",
        "class": "media-embed--w-100"
    }
]',
        'video_styles_definition' => '[
    {
        "label": "Standard",
        "class": "video--pos-default"
    },
    {
        "label": "Links",
        "class": "video--pos-left"
    },
    {
        "label": "Zentriert",
        "class": "video--pos-center"
    },
    {
        "label": "Rechts",
        "class": "video--pos-right"
    }
]',
        'video_width_styles_definition' => '[
    {
        "label": "Auto",
        "class": "video--w-auto"
    },
    {
        "label": "50%",
        "class": "video--w-50"
    },
    {
        "label": "75%",
        "class": "video--w-75"
    },
    {
        "label": "100%",
        "class": "video--w-100"
    }
]',
    ];
    const LICENSE_FIELDS = [
        'toolbar' => []
    ];
    const DEFAULTS = self::DEFAULT_VALUES;
    const ALLOWED_FIELDS = [
        'toolbar' => ['|', 'heading', 'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'alignment', 'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'insertTable', 'code', 'codeBlock', 'link', 'rexImage', 'imageUpload', 'mediaEmbed', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo', 'highlight', 'emoji', 'removeFormat', 'outdent', 'indent', 'horizontalLine', 'todoList', 'pageBreak', 'selectAll', 'specialCharacters', 'pastePlainText', 'redaxoMarkdownPasteToggle', 'redaxoMinimapToggle', 'htmlEmbed', 'sourceEditing', 'textPartLanguage', 'findAndReplace', 'style', 'snippets', 'for_video', 'showBlocks', 'bookmark', 'accessibilityHelp'],
        'balloon_toolbar' => ['style', '|', 'paragraph', 'heading', 'bulletedList', 'numberedList', 'todoList', 'outdent', 'indent', 'blockQuote', 'insertTable', 'mediaEmbed', 'for_video', 'codeBlock', 'link', 'horizontalLine', 'specialCharacters', 'removeFormat', 'undo', 'redo'],
        'alignment' => ['left', 'right', 'center', 'justify'],
        'table_toolbar' => ['|', 'tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties', 'toggleTableCaption'],
        'heading' => ['paragraph', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        //'emoji' => ['EmojiPeople', 'EmojiNature', 'EmojiPlaces', 'EmojiFood', 'EmojiActivity', 'EmojiObjects', 'EmojiSymbols', 'EmojiFlags'],
        'highlight' => ['yellowMarker', 'greenMarker', 'pinkMarker', 'blueMarker', 'redPen', 'greenPen'],
        'image_toolbar' => ['|', 'imageTextAlternative', 'block', 'inline', 'side', 'alignLeft', 'alignCenter', 'alignRight', 'alignBlockLeft', 'alignBlockRight', 'linkImage', 'toggleImageCaption'],
        'rexlink' => ['internal', 'media', 'email', 'phone', 'ytable'],
        'fontsize' => ['default', 'tiny', 'small', 'big', 'huge', '8', '9',
            '10', '11', '12', '13', '14', '15', '16', '17', '18', '19',
            '20', '21', '22', '23', '24', '25', '26', '27', '28', '29',
            '30', '31', '32', '33', '34', '35', '36', '37', '38', '39',
            '40', '41', '42', '43', '44', '45', '46', '47', '48', '49',
            '50', '51', '52', '53', '54', '55', '56', '57', '58', '59',
            '60', '61', '62', '63', '64', '65', '66', '67', '68', '69',
            '70', '71', '72', '73', '74', '75', '76', '77', '78', '79'],
        'min_height' => ['none', '100px', '200px', '300px', '400px', '500px', '600px'],
        'max_height' => ['none', '200px', '400px', '600px', '800px', '1000px', '1200px'],
        'providers' => ['dailymotion', 'spotify', 'youtube', 'vimeo', 'instagram', 'twitter', 'googleMaps', 'flickr', 'facebook'],
        'code_block' => ['plaintext', 'c', 'cs', 'cpp', 'css', 'diff', 'html', 'java', 'javascript', 'php', 'python', 'ruby', 'typescript', 'xml'],
        'special_characters' => ['currency', 'mathematical', 'latin', 'arrows', 'text']
    ];

    const EDITOR_TYPES = [
        'classic' => 'classic',
        'classic_balloon' => 'classic_balloon',
        'balloon_block' => 'balloon_block',
    ];

    /** @api array<string,array<string,string>> */
    const CODE_BLOCK = [
        'plaintext' => ['label' => 'Plain Text', 'class' => 'block_plain_text'],
        'c' => ['label' => 'C', 'class' => 'block_c'],
        'cs' => ['label' => 'C#', 'class' => 'block_cs'],
        'cpp' => ['label' => 'C++', 'class' => 'block_cpp'],
        'css' => ['label' => 'CSS', 'class' => 'block_css'],
        'diff' => ['label' => 'Diff', 'class' => 'block_diff'],
        'html' => ['label' => 'HTML', 'class' => 'block_html'],
        'java' => ['label' => 'Java', 'class' => 'block_java'],
        'javascript' => ['label' => 'JavaScript', 'class' => 'block_java_script'],
        'php' => ['label' => 'PHP', 'class' => 'block_php'],
        'python' => ['label' => 'Python', 'class' => 'block_python'],
        'ruby' => ['label' => 'Ruby', 'class' => 'block_ruby'],
        'typescript' => ['label' => 'TypeScript', 'css' => 'block_type_script'],
        'xml' => ['label' => 'XML', 'class' => 'block_xml']
    ];

    /** @api array<string,array<string,string>> */
    const HEADINGS = [
        'paragraph' => [
            'model' => 'paragraph',
            'title' => 'Paragraph',
            'class' => 'ck-heading_paragraph'
        ],
        'h1' => [
            'model' => 'heading1',
            'view' => 'h1',
            'title' => 'Heading 1',
            'class' => 'ck-heading_heading1'
        ],
        'h2' => [
            'model' => 'heading2',
            'view' => 'h2',
            'title' => 'Heading 2',
            'class' => 'ck-heading_heading2'
        ],
        'h3' => [
            'model' => 'heading3',
            'view' => 'h3',
            'title' => 'Heading 3',
            'class' => 'ck-heading_heading3'
        ],
        'h4' => [
            'model' => 'heading4',
            'view' => 'h4',
            'title' => 'Heading 4',
            'class' => 'ck-heading_heading4'
        ],
        'h5' => [
            'model' => 'heading5',
            'view' => 'h5',
            'title' => 'Heading 5',
            'class' => 'ck-heading_heading5'
        ],
        'h6' => [
            'model' => 'heading6',
            'view' => 'h6',
            'title' => 'Heading 6',
            'class' => 'ck-heading_heading6'
        ]
    ];

    /**
     * @param array<string,string> $getProfile
     * @throws rex_functional_exception
     * @author Joachim Doerr
     */
    public static function profilesCreate(array $getProfile = null): void
    {
        $profiles = Cke5DatabaseHandler::getAllProfiles();
        $content = [];

        if (!is_null($profiles) && sizeof($profiles) > 0) {
            $jsonProfiles = [];
            $jsonSubOptions = [];
            $sprogDefinition = [];

            foreach ($profiles as $profile) {
                if (!is_null($getProfile) && isset($getProfile['name']) && $profile['name'] === $getProfile['name']) {
                    $profile = $getProfile;
                }

                $result = self::mapProfile($profile);
                $jsonSubOptions[$profile['name']] = $result['suboptions'];
                $jsonProfiles[$profile['name']] = $result['profile'];
                $sprogDefinition[$profile['name']] = (isset($result['sprog_mention'])) ? $result['sprog_mention'] : [];
            }

            $profiles = str_replace([":\/\/", "null", '"regex(\/', '\/)"'], ["://", null, '/', '/'], (string)json_encode($jsonProfiles));
            $subOptions = json_encode($jsonSubOptions);

            // sprog replacements
            if (count($sprogDefinition) > 0) {
                $printSprogMention = false;
                $search = [];
                $replace = [];
                foreach ($sprogDefinition as $key => $items) {
                    if (is_array($items) && count($items) > 0) {
                        $printSprogMention = true;
                        $search[] = "\"getSprogFeedItems".$key."\"";
                        $replace[] = "getSprogFeedItems".$key;
                        $content[] = "function getSprogFeedItems".$key."( queryText ) {return new Promise( resolve => {setTimeout(()=>{const itemsToDisplay = sprogItems".$key.".filter( isItemMatching ).slice( 0, 10 );resolve( itemsToDisplay );},100);});function isItemMatching( item ) {const searchString = queryText.toLowerCase();return (item.name.toLowerCase().includes( searchString ) ||item.id.toLowerCase().includes( searchString ));}}";
                        $definition = [];
                        foreach ($items as $item) {
                            $definition[] = [
                                'id' => $item['sprog_key'],
                                'name' => $item['sprog_description']
                            ];
                        }
                        $content[] = "const sprogItems".$key." = " . (string)json_encode($definition);
                    }
                }
                if ($printSprogMention) {
                    $search[] = "\"sprogItemRenderer\"";
                    $replace[] = "sprogItemRenderer";
                    $content[] = "function sprogItemRenderer( item ) {const itemElement = document.createElement( 'span' );itemElement.classList.add('ck');itemElement.classList.add('ck-button');itemElement.classList.add('ck-button_with-text');itemElement.textContent = `\${ item.name } `;const sprogElement = document.createElement( 'span' );sprogElement.setAttribute('style', 'margin-left:5px');sprogElement.textContent = item.id;itemElement.appendChild( sprogElement );return itemElement;}";
                }
                $profiles = str_replace($search, $replace, $profiles);
            }

            $content[] = "const cke5profiles = $profiles;";
            $content[] = "const cke5suboptions = $subOptions;";
        }

        if (!rex_file::put(self::getAddon()->getAssetsPath(self::PROFILES_FILENAME), implode("\n", $content))) {
            throw new rex_functional_exception(rex_i18n::msg('cke5_profiles_creation_exception'));
        }
    }

    /**
     * @param array<string,string> $profile
     * @return array<string,mixed>
     * @author Joachim Doerr
     */
    public static function mapProfile(array $profile): array
    {
        if (isset($profile['expert']) && $profile['expert'] !== '') {
            $jsonProfile = json_decode($profile['expert_definition'], true);
            $jsonSubOption = json_decode($profile['expert_suboption'], true);

            if (is_null($jsonSubOption)) {
                $jsonSubOption = [];
            }

            if (!is_array($jsonProfile)) {
                $jsonProfile = [];
            }

            $jsonProfile['licenseKey'] = 'GPL';
            if (!empty(self::getAddon()->getConfig('license_code'))) {
                $jsonProfile['licenseKey'] = self::getAddon()->getConfig('license_code');
                if (!isset($jsonProfile['ui'])) {
                    $jsonProfile['ui'] = [];
                }
                if (!isset($jsonProfile['ui']['poweredBy'])) {
                    $jsonProfile['ui']['poweredBy'] = [];
                }
                $jsonProfile['ui']['poweredBy']['forceVisible'] = false;
            }

            return ['suboptions' => $jsonSubOption, 'profile' => $jsonProfile];
        }

        $toolbar = self::toArray($profile['toolbar']);
        $balloonToolbar = self::toArray(isset($profile['balloon_toolbar']) ? $profile['balloon_toolbar'] : null);
        $linkToolbar = self::toArray($profile['rexlink']);
        $tableToolbar = self::toArray($profile['table_toolbar']);
        $globalSettings = self::getGlobalProfileSettings();
        $globalMentions = self::isGlobalSettingEnabled($globalSettings, 'global_mentions_enabled') ? self::decodeGlobalJsonList($globalSettings, 'global_mentions_definition') : [];
        $globalSprogMentions = self::isGlobalSettingEnabled($globalSettings, 'global_sprog_enabled') ? self::decodeGlobalJsonList($globalSettings, 'global_sprog_mention_definition') : [];
        $globalYtables = self::isGlobalSettingEnabled($globalSettings, 'global_ytable_enabled') ? self::decodeGlobalJsonList($globalSettings, 'global_ytable_definition') : [];

        if (self::isGlobalSettingEnabled($globalSettings, 'global_media_enabled') && (isset($profile['mediatypes']) && '' === trim((string) $profile['mediatypes']) || !isset($profile['mediatypes'])) && isset($globalSettings['global_mediatypes']) && '' !== trim((string) $globalSettings['global_mediatypes'])) {
            $profile['mediatypes'] = trim((string) $globalSettings['global_mediatypes']);
        }

        if (self::isGlobalSettingEnabled($globalSettings, 'global_media_enabled') && (isset($profile['mediatype']) && '' === trim((string) $profile['mediatype']) || !isset($profile['mediatype'])) && isset($globalSettings['global_mediatype']) && '' !== trim((string) $globalSettings['global_mediatype'])) {
            $profile['mediatype'] = trim((string) $globalSettings['global_mediatype']);
        }

        if (self::isGlobalSettingEnabled($globalSettings, 'global_media_enabled') && (isset($profile['mediapath']) && '' === trim((string) $profile['mediapath']) || !isset($profile['mediapath'])) && isset($globalSettings['global_mediapath']) && '' !== trim((string) $globalSettings['global_mediapath'])) {
            $profile['mediapath'] = trim((string) $globalSettings['global_mediapath']);
        }

        $profileHasFontFamilyDefault = isset($profile['font_family_default']) && '' !== trim((string) $profile['font_family_default']);
        $profileHasCustomFontFamilies = isset($profile['font_families']) && '' !== trim((string) $profile['font_families']);

        if (self::isGlobalSettingEnabled($globalSettings, 'global_font_family_default') && !$profileHasFontFamilyDefault && !$profileHasCustomFontFamilies) {
            $profile['font_family_default'] = 'default_font_family';
        }

        if (!self::isGlobalSettingEnabled($globalSettings, 'global_font_family_default') && !$profileHasCustomFontFamilies && '' !== trim((string) $globalSettings['global_font_families'])) {
            $profile['font_families'] = trim((string) $globalSettings['global_font_families']);
        }

        $jsonProfile = ['toolbar' => ['items' => $toolbar, 'shouldNotGroupWhenFull' => (!(isset($profile['group_when_full']) && $profile['group_when_full'] !== ''))]];
        $jsonSubOption = [];
        $jsonProfile['removePlugins'] = [];
        $sprogDefinition = [];

        $jsonProfile['redaxoEditorType'] = self::normalizeEditorType(isset($profile['editor_type']) && is_string($profile['editor_type']) ? $profile['editor_type'] : '');

        if (isset($profile['balloon_toolbar_custom']) && '' !== $profile['balloon_toolbar_custom'] && count($balloonToolbar) > 0) {
            $jsonProfile['balloonToolbar'] = $balloonToolbar;
            $jsonProfile['blockToolbar'] = $balloonToolbar;
        }

        if (isset($profile['paste_plain_text_default']) && $profile['paste_plain_text_default'] !== '') {
            $jsonProfile['pastePlainTextDefault'] = true;
        }

        $jsonProfile['redaxoMarkdownPasteEnabled'] = self::profileFlagEnabled($profile, 'markdown_paste', false);
        $jsonProfile['redaxoMinimapEnabled'] = self::profileFlagEnabled($profile, 'minimap', false);

        if ($jsonProfile['redaxoMarkdownPasteEnabled'] && !in_array('redaxoMarkdownPasteToggle', $jsonProfile['toolbar']['items'], true)) {
            $jsonProfile['toolbar']['items'][] = 'redaxoMarkdownPasteToggle';
        }

        if ($jsonProfile['redaxoMinimapEnabled'] && !in_array('redaxoMinimapToggle', $jsonProfile['toolbar']['items'], true)) {
            $jsonProfile['toolbar']['items'][] = 'redaxoMinimapToggle';
        }

        if (in_array('link', $toolbar, true) && count($linkToolbar) > 0) {
            $jsonProfile['link'] = ['rexlink' => $linkToolbar];

            if (in_array('ytable', $linkToolbar, true)) {
                $profileYtables = isset($profile['ytable']) && '' !== $profile['ytable'] ? (array) json_decode($profile['ytable'], true) : [];
                $mergedYtables = self::mergeConfigLists($globalYtables, $profileYtables);
                if (count($mergedYtables) > 0) {
                    $jsonProfile['link']['ytable'] = $mergedYtables;
                }
            }
        }

        // image unit
        $resizeOptions = null;

        if (isset($profile['mediacategory']) && $profile['mediacategory'] !== '') {
            $jsonProfile['image']['rexmedia_category'] = $profile['mediacategory'];
        }

        if (isset($profile['mediatypes']) && $profile['mediatypes'] !== '') {
            $jsonProfile['image']['rexmedia_types'] = $profile['mediatypes'];
        }

        if (isset($profile['image_resize_unit']) && $profile['image_resize_unit'] !== '') {
            $jsonProfile['image']['resizeUnit'] = $profile['image_resize_unit'];
        }

        $jsonProfile['redaxoImageResizeHandles'] = !((isset($profile['image_resize_handles']) && $profile['image_resize_handles'] === '') || is_null($profile['image_resize_handles']));

        if (isset($profile['image_resize_options']) && $profile['image_resize_options'] !== '' && isset($profile['image_resize_options_definition']) && $profile['image_resize_options_definition'] !== '') {
            /** @var array<string,array<string,string>> $resizeOptions */
            $resizeOptions = array_filter((array)json_decode($profile['image_resize_options_definition'], true));
            foreach ($resizeOptions as $key => $option) {
                if (isset($option['name']) && $option['name'] !== '') {
                    $resizeOptions[$key]['name'] = 'imageResize:' . $option['name'];
                }
            }
            $jsonProfile['image']['resizeOptions'] = $resizeOptions;
        }

        // disable menubar ever
        $jsonProfile['menuBar'] = ['isVisible' => false];

        if (isset($profile['image_toolbar']) && $profile['image_toolbar'] !== '') {
            $imageKeys = self::toArray($profile['image_toolbar']);
            $jsonProfile['image']['toolbar'] = self::getImageToolbar($imageKeys);
            $jsonProfile['image']['styles'] = self::getImageStyles($imageKeys);

            if (!is_null($resizeOptions) && sizeof($resizeOptions) > 0) {
                // if toggle setting group sizes...
                if (isset($profile['image_resize_group_options']) && $profile['image_resize_group_options'] !== '') {
                    if (sizeof($jsonProfile['image']['toolbar']) > 0) $jsonProfile['image']['toolbar'][] = '|';
                    $jsonProfile['image']['toolbar'][] = 'resizeImage';
                } else {
                    $jsonProfile['image']['toolbar'][] = '|';
                    foreach ($resizeOptions as $option) {
                        $jsonProfile['image']['toolbar'][] = $option['name'];
                    }
                }
            }
        }

        if (in_array('insertTable', $toolbar, true) && isset($profile['table_toolbar']) && $profile['table_toolbar'] !== '') {
            $jsonProfile['table'] = ['contentToolbar' => $tableToolbar];

            foreach (self::EDITOR_SETTINGS['cktabletypes'] as $ckKey) {
                if (in_array($ckKey, $tableToolbar, true) && isset($profile['table_color']) && $profile['table_color'] !== '' &&
                    (isset($profile['table_color_default']) && $profile['table_color_default'] === '' or is_null($profile['table_color_default']))) {
                    $jsonProfile['table'][$ckKey] = [
                        'borderColors' => json_decode($profile['table_color'], true),
                        'backgroundColors' => json_decode($profile['table_color'], true)
                    ];
                }
            }
        }

        if (isset($profile['transformation']) && $profile['transformation'] !== '' && isset($profile['transformation_extra']) && $profile['transformation_extra'] !== '') {
            $definition = json_decode($profile['transformation_extra'], true);
            if (is_array($definition)) {
                $jsonProfile['typing']['transformations'] = ['extra' => $definition];
            }
        }

        if (isset($profile['blank_to_external']) && $profile['blank_to_external'] !== '') {
            $jsonProfile['link']['addTargetToExternalLinks'] = true;
        } else {
            $jsonProfile['link']['addTargetToExternalLinks'] = false;
        }

        if (isset($profile['link_internalcategory']) && $profile['link_internalcategory'] !== '') {
            $jsonProfile['link']['rexlink_category'] = $profile['link_internalcategory'];
        }

        if (isset($profile['link_mediacategory']) && $profile['link_mediacategory'] !== '') {
            $jsonProfile['link']['rexmedia_category'] = $profile['link_mediacategory'];
        }

        if (isset($profile['link_mediatypes']) && $profile['link_mediatypes'] !== '') {
            $jsonProfile['link']['rexmedia_types'] = $profile['link_mediatypes'];
        }

        /*
        switch($profile['auto_link']) {
            case '0':
            default:
                $jsonProfile['removePlugins'][] = 'AutoLink';
                break;
            case 'http':
            case 'https':
                $jsonProfile['link']['defaultProtocol'] = $profile['auto_link'] . '://';
                break;
        }
        */

        if (isset($profile['link_downloadable']) && $profile['link_downloadable'] !== '') {
            $jsonProfile['link']['decorators']['toggleDownloadable'] = ['mode' => 'manual', 'label' => 'Downloadable', 'attributes' => ['download' => 'file']];
        }

        if (isset($profile['link_decorators']) && $profile['link_decorators'] !== '' && isset($profile['link_decorators_definition']) && $profile['link_decorators_definition'] !== '') {
            $definition = json_decode($profile['link_decorators_definition'], true);
            if (!isset($jsonProfile['link']['decorators']) || !is_array($jsonProfile['link']['decorators'])) {
                $jsonProfile['link']['decorators'] = [];
            }
            if (is_array($definition) && count($definition) > 0) {
                foreach ($definition as $item) {
                    $jsonProfile['link']['decorators'] = array_merge($jsonProfile['link']['decorators'], $item);
                }
                $jsonProfile['link']['decorators'] = self::normalizeLinkDecorators($jsonProfile['link']['decorators']);
            }
        }

        if (in_array('alignment', $toolbar, true) && isset($profile['alignment']) && $profile['alignment'] !== '') {
            $jsonProfile['alignment'] = self::toArray($profile['alignment']);
        } else {
            $jsonProfile['removePlugins'][] = 'Alignment';
        }

        if (isset($profile['document_outline']) && $profile['document_outline'] !== '') {
            $jsonProfile['documentOutline']['container'] = "document.querySelector( '.document-outline-container' )";
        } else {
            $jsonProfile['removePlugins'][] = 'DocumentOutline';
        }

        if (isset($profile['group_styles']) && $profile['group_styles'] !== '') {
            $styleGroups = array_filter(explode('|', $profile['group_styles']));
            $stylesGroupTable = rex::getTable(Cke5DatabaseHandler::CKE5_STYLE_GROUPS);
            $sql = rex_sql::factory();
            $sqlResult = $sql->getArray("select * from $stylesGroupTable where id in (".implode(', ', $styleGroups).")");
            if (count($sqlResult) > 0) {
                foreach ($sqlResult as $result) {
                    if (!empty($result['json_config'])) {
                        try {
                            $jsonConfig = json_decode($result['json_config'], true);
                        } catch (Exception $e) {
                            rex_logger::logException($e);
                            throw $e;
                        }
                        // Parsen der JSON-Konfiguration
                        if (count($jsonConfig) > 0) {
                            foreach ($jsonConfig as $value) {
                                $jsonProfile['style']['definitions'][] = [
                                    'name' => $value['name'],
                                    'element' => $value['element'],
                                    'classes' => $value['classes']
                                ];
                            }
                        }
                    }
                }
            }
        }

        if (isset($profile['styles']) && $profile['styles'] !== '') {
            $styles = array_filter(explode('|', $profile['styles']));
            $stylesTable = rex::getTable(Cke5DatabaseHandler::CKE5_STYLES);
            $sql = rex_sql::factory();
            $sqlResult = $sql->getArray("select * from $stylesTable where id in (".implode(', ', $styles).")");
            if (count($sqlResult) > 0) {
                foreach ($sqlResult as $result) {
                    $classes = array_filter(explode(',', $result['classes']));
                    $jsonProfile['style']['definitions'][] = [
                        'name' => $result['name'],
                        'element' => $result['element'],
                        'classes' => $classes
                    ];
                }
            }
        }

        if (!empty($jsonProfile['style']['definitions'])) {
            $jsonProfile['style']['definitions'] = self::getUniqueStylesByName($jsonProfile['style']['definitions']);
        }

        if (isset($profile['snippets']) && $profile['snippets'] !== '') {
            $snippetIds = array_filter(explode('|', $profile['snippets']));
            if ($snippetIds !== []) {
                $snippetTable = rex::getTable(Cke5DatabaseHandler::CKE5_SNIPPETS);
                $sql = rex_sql::factory();
                $sqlResult = $sql->getArray('select id, name, content, active from ' . $snippetTable . ' where id in (' . implode(', ', $snippetIds) . ') order by name');
                foreach ($sqlResult as $snippet) {
                    if ((int) ($snippet['active'] ?? 0) !== 1) {
                        continue;
                    }
                    $jsonProfile['redaxoSnippets'][] = [
                        'id' => (int) ($snippet['id'] ?? 0),
                        'name' => (string) ($snippet['name'] ?? ''),
                        'content' => (string) ($snippet['content'] ?? ''),
                    ];
                }
            }
        }

        if (!in_array('sourceEditing', $toolbar, true)) {
            $jsonProfile['removePlugins'][] = 'SourceEditing';
        }

        if (!in_array('style', $toolbar, true)) {
            $jsonProfile['removePlugins'][] = 'Style';
        }

        $requiresCustomHtml = in_array('for_video', $toolbar, true) || in_array('for_video_widget_test', $toolbar, true);
        if (!in_array('sourceEditing', $toolbar, true) && !in_array('style', $toolbar, true) && !$requiresCustomHtml) {
            $jsonProfile['removePlugins'][] = 'GeneralHtmlSupport';
        }

        if (in_array('textPartLanguage', $toolbar, true) && isset($profile['text_part_language']) && $profile['text_part_language'] !== '') {
            $textPartLang = array_filter(explode('|', $profile['text_part_language']));
            foreach ($textPartLang as $key) {
                $jsonProfile['language']['textPartLanguage'][] = ['title' => CKE5ISO6391::$isolang[$key], 'languageCode' => $key];
            }
        }

        if (in_array('heading', $toolbar, true) && isset($profile['heading']) && $profile['heading'] !== '') {
            $jsonProfile['heading'] = ['options' => self::getHeadings(self::toArray($profile['heading']))];
        }

        if (self::profileFlagEnabled($profile, 'document_title', false)) {
            $jsonProfile['title'] = [
                'placeholder' => rex_i18n::msg('cke5_document_title_placeholder'),
            ];
        } else {
            $jsonProfile['removePlugins'][] = 'Title';
        }

        if (in_array('bulletedList', $toolbar, true) || in_array('numberedList', $toolbar)) {
            $jsonProfile['list']['properties'] = [
                'styles' => (isset($profile['list_style']) && $profile['list_style'] !== ''),
                'startIndex' => (isset($profile['list_start_index']) && $profile['list_start_index'] !== ''),
                'reversed' => (isset($profile['list_reversed']) && $profile['list_reversed'] !== ''),
            ];
        }

        /*if (in_array('emoji', $toolbar, true) && isset($profile['emoji']) && $profile['emoji'] !== '') {
            $emojiGroups = self::toArray($profile['emoji']);
            foreach (self::ALLOWED_FIELDS['emoji'] as $emoji) {
                if (!in_array($emoji, $emojiGroups, true)) {
                    $jsonProfile['removePlugins'][] = $emoji;
                }
            }
        } else {
            $jsonProfile['removePlugins'] = array_merge($jsonProfile['removePlugins'], self::ALLOWED_FIELDS['emoji']);
        }*/

        if (in_array('highlight', $toolbar, true) && isset($profile['highlight']) && $profile['highlight'] !== '') {
            $jsonProfile['highlight'] = ['options' => self::getHighlight(self::toArray($profile['highlight']))];
        }

        if (in_array('htmlEmbed', $toolbar, true)) {
            if (isset($profile['html_preview']) && $profile['html_preview'] !== '') $jsonProfile['htmlEmbed'] = ['showPreviews' => true];
        } else {
            $jsonProfile['removePlugins'][] = 'HtmlEmbed';
        }

        $noFontSize = true;
        if (in_array('fontSize', $toolbar, true) && isset($profile['fontsize']) && $profile['fontsize'] !== '') {
            $jsonProfile['fontSize'] = ['options' => self::toArray($profile['fontsize'])];
            $noFontSize = false;
        }

        $noFontColor = true;
        if (in_array('fontColor', $toolbar, true) && isset($profile['font_color_default']) && $profile['font_color_default'] !== '') {
            $noFontColor = false;
        }

        if (in_array('fontColor', $toolbar, true) && isset($profile['font_color']) && $profile['font_color'] !== '' &&
            (isset($profile['font_color_default']) && $profile['font_color_default'] === '' || is_null($profile['font_color_default']))) {
            $jsonProfile['fontColor'] = ['colors' => json_decode($profile['font_color'], true)];
            $noFontColor = false;
        }

        $noFontBgColor = true;
        if (in_array('fontBackgroundColor', $toolbar, true) && isset($profile['font_background_color_default']) && $profile['font_background_color_default'] !== '') {
            $noFontBgColor = false;
        }

        if (in_array('fontBackgroundColor', $toolbar, true) && isset($profile['font_background_color']) && $profile['font_background_color'] !== '' &&
            (isset($profile['font_background_color_default']) && $profile['font_background_color_default'] === '' || is_null($profile['font_background_color_default']))) {
            $jsonProfile['fontBackgroundColor'] = ['colors' => json_decode($profile['font_background_color'], true)];
            $noFontBgColor = false;
        }

        if (in_array('fontFamily', $toolbar, true) &&
            (isset($profile['font_family_default']) && $profile['font_family_default'] === '' || is_null($profile['font_family_default'])) &&
            isset($profile['font_families']) && $profile['font_families'] !== '') {
            $values = array_values((array)json_decode($profile['font_families'], true));
            $options = [];
            /** @var array<string,string> $value */
            foreach ($values as $value) {
                if (isset($value['family']) && $value['family'] !== '') {
                    $options[] = $value['family'];
                }
            }
            $jsonProfile['fontFamily'] = ['options' => $options];
        }

        if ($noFontSize && $noFontColor && $noFontBgColor && !in_array('fontFamily', $toolbar, true)) {
            $jsonProfile['removePlugins'][] = 'Font';
        }

        if (in_array('codeBlock', $toolbar, true) && isset($profile['code_block']) && $profile['code_block'] !== '') {
            $codeBlocks = self::toArray($profile['code_block']);
            if (sizeof($codeBlocks) > 0) {
                $jsonProfile['codeBlock']['languages'] = [];
                foreach ($codeBlocks as $codeBlock) {
                    if (isset(self::CODE_BLOCK[$codeBlock])) {
                        $jsonProfile['codeBlock']['languages'][] = array_merge(['language' => $codeBlock], self::CODE_BLOCK[$codeBlock]);
                    }
                }
            }
        }

        if (in_array('mediaEmbed', $toolbar, true) && isset($profile['mediaembed']) && $profile['mediaembed'] !== '') {
            $provider = [];
            $hold = self::toArray($profile['mediaembed']);
            foreach (self::ALLOWED_FIELDS['providers'] as $value) {
                if (!in_array($value, $hold, true)) {
                    $provider[] = $value;
                }
            }
            $jsonProfile['mediaEmbed'] = ['removeProviders' => $provider];
        }

        $mediaEmbedStyles = self::getMediaEmbedStyles($profile);
        $mediaEmbedWidthStyles = self::getMediaEmbedWidthStyles($profile);
        $mediaEmbedStyles = array_merge($mediaEmbedStyles, $mediaEmbedWidthStyles);
        if ($mediaEmbedStyles !== []) {
            if (!isset($jsonProfile['mediaEmbed']) || !is_array($jsonProfile['mediaEmbed'])) {
                $jsonProfile['mediaEmbed'] = [];
            }
            $jsonProfile['mediaEmbed']['styles'] = $mediaEmbedStyles;
        }

        $videoStyles = self::getVideoStyles($profile);
        if ($videoStyles !== []) {
            $jsonProfile['redaxoVideo']['styles'] = $videoStyles;
        }

        $videoWidthStyles = self::getVideoWidthStyles($profile);
        if ($videoWidthStyles !== []) {
            $jsonProfile['redaxoVideo']['widthStyles'] = $videoWidthStyles;
        }

        $jsonProfile['redaxoVideo']['defaults'] = [
            'controls' => self::profileFlagEnabled($profile, 'video_controls_default', true),
            'autoplay' => self::profileFlagEnabled($profile, 'video_autoplay_default', false),
            'muted' => self::profileFlagEnabled($profile, 'video_muted_default', false),
            'loop' => self::profileFlagEnabled($profile, 'video_loop_default', false),
            'playsinline' => self::profileFlagEnabled($profile, 'video_playsinline_default', true),
        ];

        if ($jsonProfile['redaxoVideo']['defaults']['autoplay'] && !$jsonProfile['redaxoVideo']['defaults']['muted']) {
            $jsonProfile['redaxoVideo']['defaults']['muted'] = true;
        }

        if (in_array('rexImage', $toolbar, true)) {
            if (isset($profile['mediatype']) && $profile['mediatype'] !== '') {
                $jsonProfile['image']['rexmedia_type'] = $profile['mediatype'];
            } else {
                $path = (isset($profile['mediapath']) && $profile['mediapath'] !== '') ? $profile['mediapath'] : 'media';
                $jsonProfile['image']['rexmedia_path'] = '/' . $path . '/';
            }
            if (isset($profile['rexmedia_types']) && $profile['rexmedia_types'] !== '') {
                $jsonProfile['image']['rexmedia_types'] = $profile['rexmedia_types'];
            }
        }

        if (!is_null($profile['upload_default']) or isset($profile['upload_default']) && $profile['upload_default'] !== '') {
            $ckFinderUrl = self::UPLOAD_URL;

            if (isset($profile['mediatype']) && $profile['mediatype'] !== '') {
                $ckFinderUrl .= '&media_type=' . $profile['mediatype'];
            } else {
                $mediaPath = 'media';
                if (isset($profile['mediapath']) && is_string($profile['mediapath']) && trim($profile['mediapath']) !== '') {
                    $mediaPath = trim($profile['mediapath']);
                }

                $ckFinderUrl .= '&media_path=' . rawurlencode(trim($mediaPath, '/'));
            }

            if (isset($profile['upload_mediacategory']) && $profile['upload_mediacategory'] !== '') {
                $ckFinderUrl .= '&media_category=' . $profile['upload_mediacategory'];
            }

            $jsonProfile['ckfinder'] = ['uploadUrl' => $ckFinderUrl];
        }

        foreach (rex_i18n::getLocales() as $locale) {
            if (isset($profile['placeholder_' . $locale]) && $profile['placeholder_' . $locale] !== '') {
                $jsonProfile['placeholder_' . substr($locale, 0, 2)] = $profile['placeholder_' . $locale];
            }
        }

        if (isset($profile['lang_content']) && $profile['lang_content'] !== '' || isset($profile['lang']) && $profile['lang'] !== '') {
            if (isset($profile['lang']) && $profile['lang'] !== '') {
                $jsonProfile['language']['ui'] = $profile['lang'];
            }
            if (isset($profile['lang_content']) && $profile['lang_content'] !== '') {
                $jsonProfile['language']['content'] = $profile['lang_content'];
            }
        }

        if (is_null($profile['height_default']) or isset($profile['height_default']) && $profile['height_default'] === '') {
            foreach (['min', 'max'] as $key) {
                if (isset($profile[$key . '_height']) && $profile[$key . '_height'] !== '') {
                    if ((int)$profile[$key . '_height'] === 0) {
                        $jsonSubOption[] = [$key . '-height' => 'none'];
                    } else {
                        $jsonSubOption[] = [$key . '-height' => (int)$profile[$key . '_height']];
                    }
                }
            }
        }

        if (isset($profile['html_support_allow']) && $profile['html_support_allow'] !== '' ||
            isset($profile['html_support_disallow']) && $profile['html_support_disallow'] !== '') {
            $htmlSupport = ['htmlSupport' => []];
            if (isset($profile['html_support_allow']) && $profile['html_support_allow'] !== '') {
                $htmlSupport['htmlSupport']['allow'] = json_decode($profile['html_support_allow'], true);
            }
            if (isset($profile['html_support_disallow']) && $profile['html_support_disallow'] !== '') {
                $htmlSupport['htmlSupport']['disallow'] = json_decode($profile['html_support_disallow'], true);
            }
            $jsonProfile = array_merge($jsonProfile, $htmlSupport);
        } else {
            $jsonProfile['removePlugins'][] = 'SourceEditing';
            if (!in_array('style', $toolbar, true) && !$requiresCustomHtml) {
                $jsonProfile['removePlugins'][] = 'GeneralHtmlSupport';
            }
        }

        if (in_array('for_video', $toolbar, true) || in_array('for_video_widget_test', $toolbar, true)) {
            $jsonProfile = self::addVideoHtmlSupport($jsonProfile);
        }

        if (isset($profile['extra']) && $profile['extra'] !== '' && isset($profile['extra_definition']) && $profile['extra_definition'] !== '') {
            $definition = json_decode($profile['extra_definition'], true);
            if (is_array($definition)) {
                if (isset($definition['removePlugins'])) {
                    $jsonProfile['removePlugins'] = array_values(array_unique(array_merge((array)$jsonProfile['removePlugins'], (array)$definition['removePlugins'])));
                    unset($definition['removePlugins']);
                }
                $jsonProfile = array_merge($jsonProfile, $definition);
            }
        }

        $profileSprogMentions = [];
        if (isset($profile['sprog_mention']) && $profile['sprog_mention'] !== '' && isset($profile['sprog_mention_definition']) && $profile['sprog_mention_definition'] !== '') {
            $definition = json_decode($profile['sprog_mention_definition'], true);
            if (is_array($definition)) {
                $profileSprogMentions = $definition;
            }
        }

        $mergedSprogMentions = self::mergeConfigLists($globalSprogMentions, $profileSprogMentions);
        if (count($mergedSprogMentions) > 0) {
            $sprogDefinition = $mergedSprogMentions;
            $jsonProfile['mention']['feeds'][] = ['marker' => '{', 'feed' => 'getSprogFeedItems' . $profile['name'], 'itemRenderer' => 'sprogItemRenderer', 'minimumCharacters' => 1];
        }

        $profileMentions = [];
        if (isset($profile['mentions']) && $profile['mentions'] !== '' && isset($profile['mentions_definition']) && $profile['mentions_definition'] !== '') {
            $definition = json_decode($profile['mentions_definition'], true);
            if (is_array($definition)) {
                $profileMentions = $definition;
            }
        }

        $mergedMentions = self::mergeConfigLists($globalMentions, $profileMentions);
        if (count($mergedMentions) > 0) {
            if (!isset($jsonProfile['mention']['feeds']) || !is_array($jsonProfile['mention']['feeds'])) {
                $jsonProfile['mention']['feeds'] = [];
            }
            $jsonProfile['mention']['feeds'] = self::mergeConfigLists($jsonProfile['mention']['feeds'], $mergedMentions);
        }

        // licence
        $jsonProfile['licenseKey'] = 'GPL';

        if (!empty(self::getAddon()->getConfig('license_code'))) {
            $jsonProfile['licenseKey'] = self::getAddon()->getConfig('license_code');
            $jsonProfile['ui']['poweredBy']['forceVisible'] = false;
        }

        return ['suboptions' => $jsonSubOption, 'profile' => $jsonProfile, 'sprog_mention' => $sprogDefinition];
    }

    /**
     * @param array<string,string> $profile
     * @return array<int,array<string,string>>
     */
    private static function getMediaEmbedStyles(array $profile): array
    {
        $definition = $profile['media_embed_styles_definition'] ?? self::DEFAULT_VALUES['media_embed_styles_definition'];
        if (!is_string($definition) || $definition === '') {
            return [];
        }

        $decoded = json_decode($definition, true);
        if (!is_array($decoded)) {
            return [];
        }

        $styles = [];
        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }
            $label = isset($item['label']) && is_string($item['label']) ? trim($item['label']) : '';
            $class = isset($item['class']) && is_string($item['class']) ? trim($item['class']) : '';
            if ($label === '') {
                continue;
            }
            $styles[] = ['label' => $label, 'class' => $class];
        }

        return $styles;
    }

    /**
     * @param array<string,string> $profile
     * @return array<int,array<string,string>>
     */
    private static function getMediaEmbedWidthStyles(array $profile): array
    {
        $definition = $profile['media_embed_width_styles_definition'] ?? self::DEFAULT_VALUES['media_embed_width_styles_definition'];
        if (!is_string($definition) || $definition === '') {
            return [];
        }

        $decoded = json_decode($definition, true);
        if (!is_array($decoded)) {
            return [];
        }

        $styles = [];
        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }
            $label = isset($item['label']) && is_string($item['label']) ? trim($item['label']) : '';
            $class = isset($item['class']) && is_string($item['class']) ? trim($item['class']) : '';
            if ($label === '') {
                continue;
            }
            $styles[] = ['label' => $label, 'class' => $class];
        }

        return $styles;
    }

    /**
     * @param array<string,string> $profile
     * @return array<int,array<string,string>>
     */
    private static function getVideoStyles(array $profile): array
    {
        $definition = $profile['video_styles_definition'] ?? self::DEFAULT_VALUES['video_styles_definition'];
        if (is_string($definition) && $definition !== '') {
            $decoded = json_decode($definition, true);
            if (is_array($decoded)) {
                $styles = [];
                foreach ($decoded as $item) {
                    if (!is_array($item)) {
                        continue;
                    }
                    $label = isset($item['label']) && is_string($item['label']) ? trim($item['label']) : '';
                    $class = isset($item['class']) && is_string($item['class']) ? trim($item['class']) : '';
                    if ($label === '') {
                        continue;
                    }
                    $styles[] = ['label' => $label, 'class' => $class];
                }
                if ($styles !== []) {
                    return $styles;
                }
            }
        }

        $fallback = [];
        $imageToolbar = isset($profile['image_toolbar']) && is_string($profile['image_toolbar']) ? self::toArray($profile['image_toolbar']) : [];
        $map = [
            'block' => 'Block',
            'inline' => 'Inline',
            'side' => 'Seitlich',
            'alignLeft' => 'Links',
            'alignCenter' => 'Zentriert',
            'alignRight' => 'Rechts',
            'alignBlockLeft' => 'Block links',
            'alignBlockRight' => 'Block rechts',
        ];

        foreach ($imageToolbar as $item) {
            if (isset($map[$item])) {
                $fallback[] = ['label' => $map[$item], 'class' => $item];
            }
        }

        return $fallback;
    }

    /**
     * @param array<string,string> $profile
     * @return array<int,array<string,string>>
     */
    private static function getVideoWidthStyles(array $profile): array
    {
        $definition = $profile['video_width_styles_definition'] ?? self::DEFAULT_VALUES['video_width_styles_definition'];
        if (!is_string($definition) || $definition === '') {
            return [];
        }

        $decoded = json_decode($definition, true);
        if (!is_array($decoded)) {
            return [];
        }

        $styles = [];
        foreach ($decoded as $item) {
            if (!is_array($item)) {
                continue;
            }

            $label = isset($item['label']) && is_string($item['label']) ? trim($item['label']) : '';
            $class = isset($item['class']) && is_string($item['class']) ? trim($item['class']) : '';
            if ($label === '') {
                continue;
            }

            $styles[] = ['label' => $label, 'class' => $class];
        }

        return $styles;
    }

    /**
     * @param array<string,string> $profile
     */
    private static function profileFlagEnabled(array $profile, string $key, bool $default): bool
    {
        if (!array_key_exists($key, $profile)) {
            return $default;
        }

        return isset($profile[$key]) && $profile[$key] !== '';
    }

    /**
     * @param array<string,mixed> $jsonProfile
     * @return array<string,mixed>
     */
    private static function addVideoHtmlSupport(array $jsonProfile): array
    {
        if (!isset($jsonProfile['htmlSupport']) || !is_array($jsonProfile['htmlSupport'])) {
            $jsonProfile['htmlSupport'] = [];
        }

        if (!isset($jsonProfile['htmlSupport']['allow']) || !is_array($jsonProfile['htmlSupport']['allow'])) {
            $jsonProfile['htmlSupport']['allow'] = [];
        }

        $jsonProfile['htmlSupport']['allow'][] = [
            'name' => 'video',
            'attributes' => true,
            'classes' => true,
            'styles' => true,
        ];
        $jsonProfile['htmlSupport']['allow'][] = [
            'name' => 'source',
            'attributes' => true,
            'classes' => true,
            'styles' => true,
        ];
        $jsonProfile['htmlSupport']['allow'][] = [
            'name' => 'track',
            'attributes' => true,
            'classes' => true,
            'styles' => true,
        ];
        $jsonProfile['htmlSupport']['allow'][] = [
            'name' => 'figure',
            'attributes' => true,
            'classes' => true,
            'styles' => true,
        ];

        return $jsonProfile;
    }

    /**
     * Normalize custom link decorators to CKEditor-compatible structure.
     *
     * @param mixed $decorators
     * @return array<string,array<string,mixed>>
     */
    private static function normalizeLinkDecorators($decorators): array
    {
        if (!is_array($decorators)) {
            return [];
        }

        $normalized = [];

        foreach ($decorators as $name => $config) {
            if (!is_string($name) || trim($name) === '') {
                continue;
            }

            if (!is_array($config)) {
                continue;
            }

            $mode = isset($config['mode']) && is_string($config['mode']) ? trim($config['mode']) : '';
            if (!in_array($mode, ['manual', 'automatic'], true)) {
                continue;
            }

            $entry = ['mode' => $mode];

            if (isset($config['label']) && is_string($config['label']) && trim($config['label']) !== '') {
                $entry['label'] = trim($config['label']);
            }

            if ($mode === 'manual') {
                if (!isset($config['attributes']) || !is_array($config['attributes']) || $config['attributes'] === []) {
                    continue;
                }
                $entry['attributes'] = $config['attributes'];
            }

            if ($mode === 'automatic') {
                if (isset($config['attributes']) && is_array($config['attributes']) && $config['attributes'] !== []) {
                    $entry['attributes'] = $config['attributes'];
                }
                if (isset($config['callback']) && is_string($config['callback']) && trim($config['callback']) !== '') {
                    $entry['callback'] = trim($config['callback']);
                }
                if (!isset($entry['attributes']) && !isset($entry['callback'])) {
                    continue;
                }
            }

            $normalized[$name] = $entry;
        }

        return $normalized;
    }

    private static function normalizeEditorType(string $editorType): string
    {
        $normalized = trim(strtolower($editorType));

        if (in_array($normalized, ['classic_balloon', 'classic-balloon', 'classic_with_balloon', 'classic-with-balloon', 'hybrid', 'combo'], true)) {
            return self::EDITOR_TYPES['classic_balloon'];
        }

        if (in_array($normalized, ['balloon_block', 'balloon-block', 'balloon', 'balloon_block_editor', 'balloon-block-editor'], true)) {
            return self::EDITOR_TYPES['balloon_block'];
        }

        return self::EDITOR_TYPES['classic'];
    }

    /**
     * @return array<string,mixed>
     */
    private static function getGlobalProfileSettings(): array
    {
        $settings = self::getAddon()->getConfig('global_profile_settings');
        if (is_array($settings)) {
            return $settings;
        }

        return [
            'global_mentions_enabled' => (string) self::getAddon()->getConfig('global_mentions_enabled', ''),
            'global_mentions_definition' => (string) self::getAddon()->getConfig('global_mentions_definition', ''),
            'global_sprog_enabled' => (string) self::getAddon()->getConfig('global_sprog_enabled', ''),
            'global_sprog_mention_definition' => (string) self::getAddon()->getConfig('global_sprog_mention_definition', ''),
            'global_ytable_enabled' => (string) self::getAddon()->getConfig('global_ytable_enabled', ''),
            'global_ytable_definition' => (string) self::getAddon()->getConfig('global_ytable_definition', ''),
            'global_media_enabled' => (string) self::getAddon()->getConfig('global_media_enabled', ''),
            'global_mediatypes' => (string) self::getAddon()->getConfig('global_mediatypes', ''),
            'global_mediatype' => (string) self::getAddon()->getConfig('global_mediatype', ''),
            'global_mediapath' => (string) self::getAddon()->getConfig('global_mediapath', ''),
            'global_font_family_default' => (string) self::getAddon()->getConfig('global_font_family_default', ''),
            'global_font_families' => (string) self::getAddon()->getConfig('global_font_families', ''),
        ];
    }

    /**
     * @param array<string,mixed> $settings
     * @return array<int,mixed>
     */
    private static function decodeGlobalJsonList(array $settings, string $key): array
    {
        if (!isset($settings[$key]) || !is_string($settings[$key]) || '' === trim($settings[$key])) {
            return [];
        }

        $decoded = json_decode($settings[$key], true);
        return is_array($decoded) ? array_values($decoded) : [];
    }

    /**
     * @param array<string,mixed> $settings
     */
    private static function isGlobalSettingEnabled(array $settings, string $key): bool
    {
        if (!isset($settings[$key])) {
            return false;
        }

        $value = (string) $settings[$key];
        return '' !== trim($value);
    }

    /**
     * @param array<int,mixed> $base
     * @param array<int,mixed> $override
     * @return array<int,mixed>
     */
    private static function mergeConfigLists(array $base, array $override): array
    {
        $mergedByKey = [];
        $order = [];

        foreach ($base as $item) {
            $key = self::configListItemKey($item);
            if (!isset($mergedByKey[$key])) {
                $order[] = $key;
            }
            $mergedByKey[$key] = $item;
        }

        foreach ($override as $item) {
            $key = self::configListItemKey($item);
            if (!isset($mergedByKey[$key])) {
                $order[] = $key;
            }
            // Override base entries with profile-specific ones where keys match.
            $mergedByKey[$key] = $item;
        }

        $merged = [];
        foreach ($order as $key) {
            if (isset($mergedByKey[$key])) {
                $merged[] = $mergedByKey[$key];
            }
        }

        return $merged;
    }

    /**
     * @param mixed $item
     */
    private static function configListItemKey($item): string
    {
        if (is_array($item)) {
            if (isset($item['sprog_key']) && is_string($item['sprog_key']) && '' !== $item['sprog_key']) {
                return 'sprog:' . $item['sprog_key'];
            }
            if (isset($item['table']) && is_string($item['table']) && isset($item['column']) && is_string($item['column'])) {
                return 'ytable:' . $item['table'] . '|' . $item['column'];
            }
            if (isset($item['marker']) && is_string($item['marker'])) {
                $feed = isset($item['feed']) ? (is_string($item['feed']) ? $item['feed'] : json_encode($item['feed'])) : '';
                return 'mention:' . $item['marker'] . '|' . (string) $feed;
            }
            return 'json:' . (string) json_encode($item);
        }

        return 'scalar:' . (string) $item;
    }

    /**
     * @param array<string,mixed> $jsonProfile
     * @return array<string,mixed>
     */

    /**
     * @return rex_addon_interface|rex_addon
     * @author Joachim Doerr
     */
    private static function getAddon(): rex_addon_interface
    {
        return rex_addon::get('cke5');
    }

    /**
     * @param array<string> $keys
     * @return array<int,string>
     * @author Joachim Doerr
     */
    private static function getImageToolbar(array $keys): array
    {
        $return = [];
        foreach ($keys as $key) {
            if (in_array($key, ['block', 'inline', 'side', 'alignLeft', 'alignCenter', 'alignRight', 'alignBlockRight', 'alignBlockLeft'], true)) {
                $return[] = 'imageStyle:' . $key;
            } else {
                $return[] = $key;
            }
        }
        return $return;
    }

    /**
     * @param array<string> $keys
     * @return array<int,string>
     * @author Joachim Doerr
     */
    private static function getImageStyles(array $keys): array
    {
        $return = [];
        foreach ($keys as $key) {
            if (in_array($key, ['block', 'inline', 'side', 'alignLeft', 'alignCenter', 'alignRight', 'alignBlockRight', 'alignBlockLeft'], true)) {
                $return[] = ['name' => $key];
            }
        }
        return $return;
    }

    /**
     * @param string|null $string $string
     * @return array<int,string>
     * @author Joachim Doerr
     */
    private static function toArray(string $string = null): array
    {
        if (!is_string($string)) return [];
        return array_filter(explode(',', $string));
    }

    /**s
     * @param array<string> $keys
     * @return array<int,array<string,string>>
     * @author Joachim Doerr
     */
    private static function getHeadings(array $keys): array
    {
        return self::getReturnValues($keys,self::HEADINGS);
    }

    /**
     * @param array<string> $keys
     * @return array<int,array<string,string>>
     * @author Joachim Doerr
     */
    private static function getHighlight(array $keys): array
    {
        $highlights = [
            'yellowMarker' => [
                'model' => 'yellowMarker',
                'class' => 'marker-yellow',
                'title' => 'Yellow Marker',
                'color' => 'var(--ck-highlight-marker-yellow)',
                'type' => 'marker',
            ],
            'greenMarker' => [
                'model' => 'greenMarker',
                'class' => 'marker-green',
                'title' => 'Green Marker',
                'color' => 'var(--ck-highlight-marker-green)',
                'type' => 'marker',
            ],
            'pinkMarker' => [
                'model' => 'pinkMarker',
                'class' => 'marker-pink',
                'title' => 'Pink Marker',
                'color' => 'var(--ck-highlight-marker-pink)',
                'type' => 'marker',
            ],
            'blueMarker' => [
                'model' => 'blueMarker',
                'class' => 'marker-blue',
                'title' => 'Blue Marker',
                'color' => 'var(--ck-highlight-marker-blue)',
                'type' => 'marker',
            ],
            'redPen' => [
                'model' => 'redPen',
                'class' => 'pen-red',
                'title' => 'Red pen',
                'color' => 'var(--ck-highlight-pen-red)',
                'type' => 'pen',
            ],
            'greenPen' => [
                'model' => 'greenPen',
                'class' => 'pen-green',
                'title' => 'Green pen',
                'color' => 'var(--ck-highlight-pen-green)',
                'type' => 'pen',
            ],
        ];

        return self::getReturnValues($keys, $highlights);
    }

    /**
     * @param array<string> $keys
     * @param array<string,array<string,string>> $values
     * @return array<int,array<string,string>>
     * @author Joachim Doerr
     */
    private static function getReturnValues(array $keys, array $values): array
    {
        $return = [];
        foreach ($keys as $key) {
            if (key_exists($key, $values)) {
                $return[] = $values[$key];
            }
        }
        return $return;
    }

    /**
     * Entfernt Duplikate aus einem Array basierend auf dem 'name'-Attribut
     * und dem 'element'-Attribut
     *
     * @param array $stylesArray Array mit Stil-Definitionen
     * @return array Bereinigtes Array ohne Duplikate
     */
    public static function getUniqueStylesByName(array $stylesArray): array
    {
        $uniqueStyles = [];
        $usedCombinations = []; // Speichert Kombinationen aus Name und Element

        foreach ($stylesArray as $style) {
            if (isset($style['name'])) {
                $key = $style['name'];
                if (isset($style['element'])) {
                    $key .= '|' . $style['element']; // Kombinierter Schlüssel aus Name und Element
                }

                if (!in_array($key, $usedCombinations)) {
                    $usedCombinations[] = $key;
                    $uniqueStyles[] = $style;
                }
            }
        }

        return $uniqueStyles;
    }

    /**
     * Entfernt Duplikate aus einem Array basierend auf dem 'title'-Attribut
     *
     * @param array $templatesArray Array mit Template-Definitionen
     * @return array Bereinigtes Array ohne Duplikate
     */
    public static function getUniqueTemplatesByTitle(array $templatesArray): array
    {
        $uniqueTemplates = [];
        $usedTitles = []; // Speichert bereits verwendete Titel

        foreach ($templatesArray as $template) {
            if (isset($template['title'])) {
                $title = $template['title'];

                if (!in_array($title, $usedTitles)) {
                    $usedTitles[] = $title;
                    $uniqueTemplates[] = $template;
                }
            }
        }

        return $uniqueTemplates;
    }
}
