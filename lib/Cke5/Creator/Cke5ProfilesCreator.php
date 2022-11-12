<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Creator;

use Cke5\Handler\Cke5DatabaseHandler;
use rex_addon;
use rex_addon_interface;
use rex_file;
use rex_i18n;
use rex_sql;

class Cke5ProfilesCreator
{
    /** @api string */
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
        'toolbar' => ['|', 'heading', 'fontSize', 'fontFamily', 'fontColor', 'fontBackgroundColor', 'alignment', 'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'insertTable', 'code', 'codeBlock', 'link', 'rexImage', 'imageUpload', 'mediaEmbed', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo', 'highlight', 'emoji', 'removeFormat', 'outdent', 'indent', 'horizontalLine', 'todoList', 'pageBreak', 'selectAll', 'specialCharacters', 'pastePlainText', 'htmlEmbed', 'fullScreen', 'sourceEditing', 'selectAll'],
        'alignment' => ['left', 'right', 'center', 'justify'],
        'table_toolbar' => ['|', 'tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties', 'toggleTableCaption'],
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
        ],
    ];

    /** @api array<string,array<string,string>> */
    const HIGHLIGHTS = [
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


    const DEFAULTS = [
        'toolbar' => 'heading,|',
        'alignment' => 'left,right,center',
        'table_toolbar' => 'tableColumn,tableRow,mergeTableCells,tableProperties,tableCellProperties,toggleTableCaption',
        'heading' => 'paragraph,h1,h2,h3',
        'highlight' => 'yellowMarker,greenMarker,redPen,greenPen',
        'image_toolbar' => 'imageTextAlternative,|,block,alignLeft,alignRight,linkImage',
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
     * @param array<string,string> $getProfile
     * @throws \rex_functional_exception
     * @author Joachim Doerr
     */
    public static function profilesCreate(array $getProfile = null): void
    {
        $profiles = Cke5DatabaseHandler::getAllProfiles();
        $content = '';

        if (!is_null($profiles) && sizeof($profiles) > 0) {
            $jsonProfiles = [];
            $jsonSubOptions = [];

            foreach ($profiles as $profile) {
                if (!is_null($getProfile) && isset($getProfile['name']) && $profile['name'] === $getProfile['name']) {
                    $profile = $getProfile;
                }

                $result = self::mapProfile($profile);
                $jsonSubOptions[$profile['name']] = $result['suboptions'];
                $jsonProfiles[$profile['name']] = $result['profile'];
            }

            $profiles = str_replace([":\/\/", "null", '"regex(\/', '\/)"'], ["://", null, '/', '/'], (string)json_encode($jsonProfiles));
            $subOptions = json_encode($jsonSubOptions);

            $content =
                "
const cke5profiles = $profiles;
const cke5suboptions = $subOptions;
";
        }

        if (!rex_file::put(self::getAddon()->getAssetsPath(self::PROFILES_FILENAME), $content)) {
            throw new \rex_functional_exception(\rex_i18n::msg('cke5_profiles_creation_exception'));
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

            if (is_array($jsonProfile)) {
                return ['suboptions' => $jsonSubOption, 'profile' => $jsonProfile];
            } else {
                return ['suboptions' => [], 'profile' => []];
            }
        }

        $toolbar = self::toArray($profile['toolbar']);
        $linkToolbar = self::toArray($profile['rexlink']);
        $tableToolbar = self::toArray($profile['table_toolbar']);
        $jsonProfile = ['toolbar' => ['items' => $toolbar, 'shouldNotGroupWhenFull' => (isset($profile['group_when_full']) && $profile['group_when_full'] !== '')]];
        $jsonSubOption = [];
        $jsonProfile['removePlugins'] = [];

        if (in_array('link', $toolbar, true) && count($linkToolbar) > 0) {
            $jsonProfile['link'] = ['rexlink' => $linkToolbar];

            if (in_array('ytable', $linkToolbar, true) && isset($profile['ytable']) && $profile['ytable'] !== '') {
                $jsonProfile['link']['ytable'] = json_decode($profile['ytable'], true);
            }
        }

        // image unit
        $jsonProfile['image'] = [];
        $resizeOptions = null;

        if (isset($profile['image_resize_unit']) && $profile['image_resize_unit'] !== '') {
            $jsonProfile['image']['resizeUnit'] = $profile['image_resize_unit'];
        }

        if (isset($profile['image_resize_options_definition']) && $profile['image_resize_options_definition'] !== '') {
            /** @var array<string,array<string,string>> $resizeOptions */
            $resizeOptions = array_filter((array)json_decode($profile['image_resize_options_definition'], true));
            foreach ($resizeOptions as $key => $option) {
                if (isset($option['name']) && $option['name'] !== '') {
                    $resizeOptions[$key]['name'] = 'imageResize:' . $option['name'];
                }
            }
            $jsonProfile['image'] = ['resizeOptions' => $resizeOptions];
        }

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

        if (isset($profile['blank_to_external']) && $profile['blank_to_external'] !== '') {
            $jsonProfile['link']['addTargetToExternalLinks'] = true;
        }

        if (isset($profile['link_downloadable']) && $profile['link_downloadable'] !== '') {
            $jsonProfile['link']['decorators'] = ['downloadable' => ['mode' => 'manual', 'label' => 'Downloadable', 'attributes' => ['download' => '']]];
        }

        if (isset($profile['link_decorators']) && $profile['link_decorators'] !== '' && isset($profile['link_decorators_definition']) && $profile['link_decorators_definition'] !== '') {
            $definition = json_decode($profile['link_decorators_definition'], true);
            if (is_array($definition)) {
                if (!isset($jsonProfile['link']['decorators']) || !is_array($jsonProfile['link']['decorators'])) {
                    $jsonProfile['link']['decorators'] = [];
                }
                $jsonProfile['link']['decorators'] = array_merge($jsonProfile['link']['decorators'], $definition);
            }
        }

        if (in_array('alignment', $toolbar, true) && isset($profile['alignment']) && $profile['alignment'] !== '') {
            $jsonProfile['alignment'] = self::toArray($profile['alignment']);
        } else {
            $jsonProfile['removePlugins'][] = 'Alignment';
        }

        if (isset($profile['styleEditing']) && $profile['styleEditing'] === '') {
            $jsonProfile['removePlugins'][] = 'StyleEditing';
        }

        if (!in_array('sourceEditing', $toolbar, true)) {
            $jsonProfile['removePlugins'][] = 'SourceEditing';
        }

        if (!in_array('style', $toolbar, true)) {
            $jsonProfile['removePlugins'][] = 'Style';
        }

        if (!in_array('sourceEditing', $toolbar, true) && !in_array('style', $toolbar, true)) {
            $jsonProfile['removePlugins'][] = 'GeneralHtmlSupport';
        }

        if (in_array('heading', $toolbar, true) && isset($profile['heading']) && $profile['heading'] !== '') {
            $jsonProfile['heading'] = ['options' => self::getHeadings(self::toArray($profile['heading']))];
        }

        if (in_array('emoji', $toolbar, true) && isset($profile['emoji']) && $profile['emoji'] !== '') {
            $emojiGroups = self::toArray($profile['emoji']);
            foreach (self::ALLOWED_FIELDS['emoji'] as $emoji) {
                if (!in_array($emoji, $emojiGroups, true)) {
                    $jsonProfile['removePlugins'][] = $emoji;
                }
            }
        } else {
            $jsonProfile['removePlugins'] = array_merge($jsonProfile['removePlugins'], self::ALLOWED_FIELDS['emoji']);
        }

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

        if (in_array('rexImage', $toolbar, true)) {
            if (isset($profile['mediatype']) && $profile['mediatype'] !== '') {
                $jsonProfile['rexImage'] = ['media_type' => $profile['mediatype']];
            } else {
                $path = (isset($profile['mediapath']) && $profile['mediapath'] !== '') ? $profile['mediapath'] : 'media';
                $jsonProfile['rexImage'] = ['media_path' => '/' . $path . '/'];
            }
        }

        if (!is_null($profile['upload_default']) or isset($profile['upload_default']) && $profile['upload_default'] !== '') {
            $ckFinderUrl = self::UPLOAD_URL;

            if (isset($profile['mediatype']) && $profile['mediatype'] !== '') {
                $ckFinderUrl .= '&media_type=' . $profile['mediatype'];
            } else {
                $ckFinderUrl .= '&media_path=' . $profile['mediapath'];
            }

            if (isset($profile['mediacategory']) && $profile['mediacategory'] !== '') {
                $ckFinderUrl .= '&media_category=' . $profile['mediacategory'];
            }

            $jsonProfile['ckfinder'] = ['uploadUrl' => $ckFinderUrl];
        }

        foreach (rex_i18n::getLocales() as $locale) {
            if (isset($profile['placeholder_' . $locale]) && $profile['placeholder_' . $locale] !== '') {
                $jsonProfile['placeholder_' . substr($locale, 0, 2)] = $profile['placeholder_' . $locale];
            }
        }

        if (isset($profile['lang_content']) && $profile['lang_content'] !== '' || isset($profile['lang']) && $profile['lang'] !== '') {
            $jsonProfile['language'] = [];
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
            if (!in_array('styleEditing', $toolbar, true)) {
                $jsonProfile['removePlugins'][] = 'GeneralHtmlSupport';
            }
        }

        if (isset($profile['extra']) && $profile['extra'] !== '') {
            $definition = json_decode($profile['extra_definition'], true);
            if (is_array($definition)) {
                if (isset($definition['removePlugins'])) {
                    $jsonProfile['removePlugins'] = array_values(array_unique(array_merge((array)$jsonProfile['removePlugins'], (array)$definition['removePlugins'])));
                    unset($definition['removePlugins']);
                }
                $jsonProfile = array_merge($jsonProfile, $definition);
            }
        }

        return ['suboptions' => $jsonSubOption, 'profile' => $jsonProfile];
    }

    /**
     * @return rex_addon_interface|rex_addon
     * @author Joachim Doerr
     */
    private static function getAddon(): rex_addon_interface
    {
        return \rex_addon::get('cke5');
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
                $return[] = $key;
            }
        }
        return $return;
    }

    /**
     * @param string $string
     * @return array<int,string>
     * @author Joachim Doerr
     */
    private static function toArray(string $string): array
    {
        return array_filter(explode(',', $string));
    }

    /**
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
        return self::getReturnValues($keys,self::HIGHLIGHTS);
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
}
