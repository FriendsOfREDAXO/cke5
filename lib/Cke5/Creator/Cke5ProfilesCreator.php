<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Creator;

use Cke5\Handler\Cke5DatabaseHandler;
use rex_file;
use rex_i18n;
use rex_sql;

class Cke5ProfilesCreator
{
    const UPLOAD_URL = './index.php?cke5upload=1';
    const PROFILES_FILENAME = 'cke5profiles.js';

    const EDITOR_SETTINGS = [
        /* todo: specialCharacters not work because : https://github.com/ckeditor/ckeditor5/issues/6160 */
        'cktypes' => ['heading', 'fontSize', 'mediaEmbed', 'fontFamily', 'alignment', 'link', 'highlight', 'insertTable', 'fontBackgroundColor', 'fontColor', 'codeBlock', 'bulletedList', 'numberedList', 'htmlEmbed', 'emoji', 'sourceEditing'/*, 'specialCharacters' */],
        'ckimgtypes' => ['rexImage', 'imageUpload'],
        'cklinktypes' => ['ytable'],
        'cktabletypes' => ['tableProperties', 'tableCellProperties']
    ];

    const DEFAULT_VALUES = [
        'html_support_allow' => '[
    {
        "name": "regex(/.*/)",
        "attributes": true,
        "classes": true,
        "styles": true
    }
]',
    ];

    const ALLOWED_FIELDS = [
        'toolbar' => ['|', 'heading', 'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'alignment', 'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'insertTable', 'code', 'codeBlock', 'link', 'rexImage', 'imageUpload', 'mediaEmbed', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo', 'highlight', 'emoji', 'removeFormat', 'outdent', 'indent', 'horizontalLine', 'todoList', 'pageBreak', 'selectAll', 'specialCharacters', 'pastePlainText', 'htmlEmbed', 'fullScreen', 'sourceEditing'],
        'alignment' => ['left', 'right', 'center', 'justify'],
        'table_toolbar' => ['|','tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties', 'toggleTableCaption'],
        'heading' => ['paragraph', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        'emoji' => ['EmojiPeople', 'EmojiNature', 'EmojiPlaces', 'EmojiFood', 'EmojiActivity', 'EmojiObjects', 'EmojiSymbols', 'EmojiFlags'],
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

    const DEFAULTS = [
        'toolbar' => 'heading,|',
        'alignment' => 'left,right,center',
        'table_toolbar' => 'tableColumn,tableRow,mergeTableCells,tableProperties,tableCellProperties,toggleTableCaption',
        'heading' => 'paragraph,h1,h2,h3',
        'highlight' => 'yellowMarker,greenMarker,redPen,greenPen',
        'image_toolbar' => 'imageTextAlternative,|,full,alignLeft,alignRight,linkImage',
        'rexlink' => 'internal,media,email,phone',
        'fontsize' => 'tiny,small,default,big,huge',
        'min_height' => [0, 100, 200, 300, 400, 500, 600],
        'max_height' => [0, 200, 400, 600, 800, 1000, 1200],
        'mediaembed' => 'youtube,vimeo',
        'code_block' => 'plaintext,php,javascript,python',
        'special_characters' => 'currency,mathematical,latin,arrows,text',
        'emoji' => 'EmojiPeople,EmojiSymbols,EmojiFlags',
    ];

    /**
     * @param null|array $profile
     * @throws \rex_functional_exception
     * @author Joachim Doerr
     */
    public static function profilesCreate($getProfile = null)
    {
        $profiles = Cke5DatabaseHandler::getAllProfiles();
        $content = '';

        if (sizeof($profiles) > 0) {
            $jsonProfiles = [];
            $jsonSuboptions = [];

            foreach ($profiles as $profile) {
                if (isset($getProfile['name']) && $profile['name'] == $getProfile['name']) {
                    $profile = $getProfile;
                }

                $result = self::mapProfile($profile);
                $jsonSuboptions[$profile['name']] = $result['suboptions'];
                $jsonProfiles[$profile['name']] = $result['profile'];
            }

            $profiles = str_replace([":\/\/","null",'"regex(\/', '\/)"'], ["://",null,'/','/'], json_encode($jsonProfiles));
            $suboptions = json_encode($jsonSuboptions);

            $content =
                "
const cke5profiles = $profiles;
const cke5suboptions = $suboptions;
";
        }

        if (!rex_file::put(self::getAddon()->getAssetsPath(self::PROFILES_FILENAME), $content)) {
            throw new \rex_functional_exception(\rex_i18n::msg('cke5_profiles_creation_exception'));
        }
    }

    /**
     * @param array $profile
     * @return array
     * @author Joachim Doerr
     */
    public static function mapProfile(array $profile)
    {
        if (!empty($profile['expert'])) {
            $jsonProfile = json_decode($profile['expert_definition'], true);
            $jsonSuboption = json_decode($profile['expert_suboption'], true);

            if (is_null($jsonSuboption)) {
                $jsonSuboption = [];
            }

            if (is_array($jsonProfile)) {
                return ['suboptions' => $jsonSuboption, 'profile' => $jsonProfile];
            } else {
                return ['suboptions' => [], 'profile' => []];
            }
        }

        $toolbar = self::toArray($profile['toolbar']);
        $linkToolbar = self::toArray($profile['rexlink']);
        $tableToolbar = self::toArray($profile['table_toolbar']);
        $jsonProfile = ['toolbar' => ['items' => $toolbar, 'shouldNotGroupWhenFull' => (empty($profile['group_when_full']))]];
        $jsonSuboption = [];
        $jsonProfile['removePlugins'] = [];

        if (in_array('link', $toolbar) && count($linkToolbar) > 0) {
            $jsonProfile['link'] = ['rexlink' => $linkToolbar];

            if (in_array('ytable', $linkToolbar) && !empty($profile['ytable'])) {
                $jsonProfile['link']['ytable'] = json_decode($profile['ytable'], true);
            }
        }

        // image unit
        $jsonProfile['image'] = [];
        $resizeOptions = null;

        if (!empty($profile['image_resize_unit'])) {
            $jsonProfile['image']['resizeUnit'] = $profile['image_resize_unit'];
        }

        if (!empty($profile['image_resize_options_definition'])) {
            $resizeOptions = array_filter(json_decode($profile['image_resize_options_definition'], true));
            foreach ($resizeOptions as $key => $option) {
                $resizeOptions[$key]['name'] = 'imageResize:' . $option['name'];
            }
            $jsonProfile['image'] = ['resizeOptions' => $resizeOptions];
        }

        if (!empty($profile['image_toolbar'])) {
            $imageKeys = self::toArray($profile['image_toolbar']);
            $jsonProfile['image']['toolbar'] = self::getImageToolbar($imageKeys);
            $jsonProfile['image']['styles'] = self::getImageStyles($imageKeys);

            if (!is_null($resizeOptions) && sizeof($resizeOptions) > 0) {
                // if toggle setting group sizes...
                if (!empty($profile['image_resize_group_options'])) {
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

        if (in_array('insertTable', $toolbar) && !empty($profile['table_toolbar'])) {
            $jsonProfile['table'] = ['contentToolbar' => $tableToolbar];

            foreach (self::EDITOR_SETTINGS['cktabletypes'] as $ckKey) {
                if (in_array($ckKey, $tableToolbar) && !empty($profile['table_color']) &&
                    (is_null($profile['table_color_default']) or empty($profile['table_color_default']))) {
                    $jsonProfile['table'][$ckKey] = [
                        'borderColors' => json_decode($profile['table_color'], true),
                        'backgroundColors' => json_decode($profile['table_color'], true)
                    ];
                }
            }
        }

        if (!empty($profile['transformation']) && !empty($profile['transformation_extra'])) {
            $definition = json_decode($profile['transformation_extra'], true);
            if (is_array($definition)) {
                $jsonProfile['typing']['transformations'] = ['extra' => $definition];
            }
        }

//        $jsonProfile['removePlugins'][] = 'AutoLink';

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

        if (!empty($profile['blank_to_external'])) {
            $jsonProfile['link']['addTargetToExternalLinks'] = true;
        }

        if (!empty($profile['link_downloadable'])) {
            $jsonProfile['link']['decorators'] = ['downloadable' => ['mode' => 'manual', 'label' => 'Downloadable', 'attributes' => ['download' => '']]];
        }

        if (!empty($profile['link_decorators']) && !empty($profile['link_decorators_definition'])) {
            $definition = json_decode($profile['link_decorators_definition'], true);
            if (is_array($definition)) {
                if (!isset($jsonProfile['link']['decorators']) || !is_array($jsonProfile['link']['decorators'])) {
                    $jsonProfile['link']['decorators'] = [];
                }
                $jsonProfile['link']['decorators'] = array_merge($jsonProfile['link']['decorators'], $definition);
            }
        }

        if (in_array('alignment', $toolbar) && !empty($profile['alignment'])) {
            $jsonProfile['alignment'] = self::toArray($profile['alignment']);
        } else {
            $jsonProfile['removePlugins'][] = 'Alignment';
        }

        if (empty($profile['list_style'])) {
            $jsonProfile['removePlugins'][] = 'ListStyle';
        }

        if (!in_array('sourceEditing', $toolbar)) {
            $jsonProfile['removePlugins'][] = 'SourceEditing';
            $jsonProfile['removePlugins'][] = 'GeneralHtmlSupport';
        }

        if (in_array('heading', $toolbar) && !empty($profile['heading'])) {
            $jsonProfile['heading'] = ['options' => self::getHeadings(self::toArray($profile['heading']))];
        }

        if (in_array('emoji', $toolbar) && !empty($profile['emoji'])) {
            $emojiGroups = self::toArray($profile['emoji']);
            foreach (self::ALLOWED_FIELDS['emoji'] as $emoji) {
                if (!in_array($emoji, $emojiGroups)) {
                    $jsonProfile['removePlugins'][] = $emoji;
                }
            }
        } else {
            $jsonProfile['removePlugins'] = array_merge($jsonProfile['removePlugins'], self::ALLOWED_FIELDS['emoji']);
        }

        if (in_array('highlight', $toolbar) && !empty($profile['highlight'])) {
            $jsonProfile['highlight'] = ['options' => self::getHighlight(self::toArray($profile['highlight']))];
        }

        if (in_array('htmlEmbed', $toolbar)) {
            if (!empty($profile['html_preview'])) $jsonProfile['htmlEmbed'] = ['showPreviews' => true];
        } else {
            $jsonProfile['removePlugins'][] = 'HtmlEmbed';
        }

        $noFontSize = true;
        if (in_array('fontSize', $toolbar) && !empty($profile['fontsize'])) {
            $jsonProfile['fontSize'] = ['options' => self::toArray($profile['fontsize'])];
            $noFontSize = false;
        }

        $noFontColor = true;
        if (in_array('fontColor', $toolbar) && !empty($profile['font_color_default'])) {
            $noFontColor = false;
        }


        if (in_array('fontColor', $toolbar) && !empty($profile['font_color']) &&
            (is_null($profile['font_color_default']) or empty($profile['font_color_default']))) {
            $jsonProfile['fontColor'] = ['colors' => json_decode($profile['font_color'], true)];
            $noFontColor = false;
        }
       
        $noFontBgColor = true;
        if (in_array('fontBackgroundColor', $toolbar) && !empty($profile['font_background_color_default'])) {
            $noFontBgColor = false;
        }

        if (in_array('fontBackgroundColor', $toolbar) && !empty($profile['font_background_color']) &&
            (is_null($profile['font_background_color_default']) or empty($profile['font_background_color_default']))) {
            $jsonProfile['fontBackgroundColor'] = ['colors' => json_decode($profile['font_background_color'], true)];
            $noFontBgColor = false;
        }
        
        if (in_array('fontFamily', $toolbar) &&
            (is_null($profile['font_family_default']) or empty($profile['font_family_default'])) &&
            !empty($profile['font_families'])) {
            $values = array_values(json_decode($profile['font_families'], true));
            $options = [];
            foreach ($values as $value) {
                $options[] = $value['family'];
            }
            $jsonProfile['fontFamily'] = ['options' => $options];
        }

        if ($noFontSize && $noFontColor && $noFontBgColor && !in_array('fontFamily', $toolbar)) {
            $jsonProfile['removePlugins'][] = 'Font';
        }

        if (in_array('codeBlock', $toolbar) && !empty($profile['code_block'])) {
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

        if (in_array('mediaEmbed', $toolbar) && !empty($profile['mediaembed'])) {
            $provider = [];
            $hold = self::toArray($profile['mediaembed']);
            foreach (self::ALLOWED_FIELDS['providers'] as $value) {
                if (!in_array($value, $hold)) {
                    $provider[] = $value;
                }
            }
            $jsonProfile['mediaEmbed'] = ['removeProviders' => $provider];
        }

//        dump($profile);

        if (in_array('rexImage', $toolbar)) {
            if (!empty($profile['mediatype'])) {
                $jsonProfile['rexImage'] = ['media_type' => $profile['mediatype']];
            } else {
                $path = (!empty($profile['mediapath'])) ? $profile['mediapath'] : 'media';
                $jsonProfile['rexImage'] = ['media_path' => '/' . $path . '/'];
            }
        }

//        dump($jsonProfile);
        if (!is_null($profile['upload_default']) or !empty($profile['upload_default'])) {
            $ckFinderUrl = self::UPLOAD_URL;

            if (!empty($profile['mediatype'])) {
                $ckFinderUrl .= '&media_type=' . $profile['mediatype'];
            } else {
                $ckFinderUrl .= '&media_path=' . $profile['mediapath'];
            }

            if (!empty($profile['mediacategory'])) {
                $ckFinderUrl .= '&media_category=' . $profile['mediacategory'];
            }

            $jsonProfile['ckfinder'] = ['uploadUrl' => $ckFinderUrl];
        }

        foreach (rex_i18n::getLocales() as $locale) {
            if (!empty($profile['placeholder_' . $locale])) {
                $jsonProfile['placeholder_' . substr($locale, 0, 2)] = $profile['placeholder_' . $locale];
            }
        }

        if (!empty($profile['lang_content']) || !empty($profile['lang'])) {
            $jsonProfile['language'] = [];
            if (!empty($profile['lang'])) {
                $jsonProfile['language']['ui'] = $profile['lang'];
            }
            if (!empty($profile['lang_content'])) {
                $jsonProfile['language']['content'] = $profile['lang_content'];
            }
        }

        if (is_null($profile['height_default']) or empty($profile['height_default'])) {
            foreach (['min', 'max'] as $key) {
                if (!empty($profile[$key . '_height'])) {
                    if ((int)$profile[$key . '_height'] == 0) {
                        $jsonSuboption[] = [$key . '-height' => 'none'];
                    } else {
                        $jsonSuboption[] = [$key . '-height' => (int)$profile[$key . '_height']];
                    }
                }
            }
        }

        if (!empty($profile['html_support_allow']) || !empty($profile['html_support_disallow'])) {
            $htmlSupport = ['htmlSupport' => []];
            if (!empty($profile['html_support_allow'])) {
                $htmlSupport['htmlSupport']['allow'] = json_decode($profile['html_support_allow'], true);
            }
            if (!empty($profile['html_support_disallow'])) {
                $htmlSupport['htmlSupport']['disallow'] = json_decode($profile['html_support_disallow'], true);
            }
            $jsonProfile = array_merge($jsonProfile, $htmlSupport);
        } else {
            $jsonProfile['removePlugins'][] = 'SourceEditing';
            $jsonProfile['removePlugins'][] = 'GeneralHtmlSupport';
        }

        if (!empty($profile['extra'])) {
            $definition = json_decode($profile['extra_definition'], true);
            if (is_array($definition)) {
                $jsonProfile = array_merge($jsonProfile, $definition);
            }

            $jsonProfile['removePlugins'] = array_unique($jsonProfile['removePlugins']);
        }

        return ['suboptions' => $jsonSuboption, 'profile' => $jsonProfile];
    }

    /**
     * @return \rex_addon
     * @author Joachim Doerr
     */
    private static function getAddon()
    {
        return \rex_addon::get('cke5');
    }

    /**
     * @param $keys
     * @return array
     * @author Joachim Doerr
     */
    private static function getImageToolbar($keys)
    {
        $return = [];

        foreach ($keys as $key) {
            if (in_array($key, ['block', 'inline', 'side', 'alignLeft', 'alignCenter', 'alignRight', 'alignBlockRight', 'alignBlockLeft'])) {
                $return[] = 'imageStyle:' . $key;
            } else {
                $return[] = $key;
            }
        }

        return $return;
    }

    /**
     * @param $keys
     * @return array
     * @author Joachim Doerr
     */
    private static function getImageStyles($keys)
    {
        $return = [];

        foreach ($keys as $key) {
            if (in_array($key, ['block', 'inline', 'side', 'alignLeft', 'alignCenter', 'alignRight', 'alignBlockRight', 'alignBlockLeft'])) {
                $return[] = $key;
            }
        }

        return $return;
    }

    /**
     * @param $string
     * @return array
     * @author Joachim Doerr
     */
    private static function toArray($string)
    {
        return array_filter(explode(',', $string));
    }

    /**
     * @param $keys
     * @return array
     * @author Joachim Doerr
     */
    private static function getHeadings($keys)
    {
        $headings = [
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
            ],
        ];

        $return = [];

        foreach ($keys as $key) {
            if (key_exists($key, $headings)) {
                $return[] = $headings[$key];
            }
        }

        return $return;
    }

    /**
     * @param $keys
     * @return array
     * @author Joachim Doerr
     */
    private static function getHighlight($keys)
    {
        $highlights = [
            'yellowMarker' => [
                'model' => 'yellowMarker',
                'class' => 'marker-yellow',
                'title' => 'Yellow Marker',
                'color' => 'var(--ck-highlight-marker-yellow)',
                'type' => 'marker'
            ],
            'greenMarker' => [
                'model' => 'greenMarker',
                'class' => 'marker-green',
                'title' => 'Green Marker',
                'color' => 'var(--ck-highlight-marker-green)',
                'type' => 'marker'
            ],
            'pinkMarker' => [
                'model' => 'pinkMarker',
                'class' => 'marker-pink',
                'title' => 'Pink Marker',
                'color' => 'var(--ck-highlight-marker-pink)',
                'type' => 'marker'
            ],
            'blueMarker' => [
                'model' => 'blueMarker',
                'class' => 'marker-blue',
                'title' => 'Blue Marker',
                'color' => 'var(--ck-highlight-marker-blue)',
                'type' => 'marker'
            ],
            'redPen' => [
                'model' => 'redPen',
                'class' => 'pen-red',
                'title' => 'Red pen',
                'color' => 'var(--ck-highlight-pen-red)',
                'type' => 'pen'
            ],
            'greenPen' => [
                'model' => 'greenPen',
                'class' => 'pen-green',
                'title' => 'Green pen',
                'color' => 'var(--ck-highlight-pen-green)',
                'type' => 'pen'
            ],
        ];

        $return = [];

        foreach ($keys as $key) {
            if (key_exists($key, $highlights)) {
                $return[] = $highlights[$key];
            }
        }

        return $return;
    }
}
