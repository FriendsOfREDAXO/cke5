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

class Cke5ProfilesCreator
{
    const UPLOAD_URL = './index.php?cke5upload=1';
    const PROFILES_FILENAME = 'cke5profiles.js';
    const TRANSLATION_FILENAME = 'cke5translations.js';
    const TRANSLATION_PATH = 'vendor/ckeditor5-classic/translations/%s.js';

    const EDITOR_SETTINGS = [
        'cktypes' => ['heading', 'fontSize', 'fontFamily', 'alignment', 'link', 'highlight', 'insertTable'],
        'ckimgtypes' => ['rexImage', 'imageUpload']
    ];

    const ALLOWED_FIELDS = [
        'toolbar' => ['|', 'heading', 'fontSize', 'fontFamily', 'alignment', 'bold', 'italic', 'underline', 'strikethrough', 'subscript','superscript', 'insertTable', 'code', 'link', 'rexImage', 'imageUpload', 'mediaEmbed', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo', 'highlight', 'emoji'],
        'alignment' => ['left', 'right', 'center', 'justify'],
        'table_toolbar' => ['tableColumn', 'tableRow', 'mergeTableCells'],
        'heading' => ['paragraph', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        'highlight' => ['yellowMarker', 'greenMarker', 'pinkMarker', 'blueMarker', 'redPen', 'greenPen'],
        'image_toolbar' => ['|', 'imageTextAlternative', 'full', 'alignLeft', 'alignCenter', 'alignRight'],
        'rexlink' => ['internal', 'media'],
        'fontsize' => ['tiny', 'small', 'big', 'huge', '8', '9',
            '10', '11', '12', '13', '14', '15', '16', '17', '18', '19',
            '20', '21', '22', '23', '24', '25', '26', '27', '28', '29',
            '30', '31', '32', '33', '34', '35', '36', '37', '38', '39',
            '40', '41', '42', '43', '44', '45', '46', '47', '48', '49',
            '50', '51', '52', '53', '54', '55', '56', '57', '58', '59',
            '60', '61', '62', '63', '64', '65', '66', '67', '68', '69',
            '70', '71', '72', '73', '74', '75', '76', '77', '78', '79'],
        'min_height' => ['none', '100px', '200px', '300px', '400px', '500px', '600px'],
        'max_height' => ['none', '200px', '400px', '600px', '800px', '1000px', '1200px'],
    ];

    const DEFAULTS = [
        'toolbar' => 'heading,|',
        'alignment' => 'left,right,center',
        'table_toolbar' => 'tableColumn,tableRow,mergeTableCells',
        'heading' => 'paragraph,h1,h2,h3',
        'highlight' => 'yellowMarker,greenMarker,redPen,greenPen',
        'image_toolbar' => 'imageTextAlternative,|,full,alignLeft,alignRight',
        'rexlink' => 'internal,media',
        'fontsize' => 'tiny,small,default,big,huge',
        'min_height' => [0, 100, 200, 300, 400, 500, 600],
        'max_height' => [0, 200, 400, 600, 800, 1000, 1200],
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

            $profiles = json_encode($jsonProfiles);
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
        $toolbar = self::toArray($profile['toolbar']);
        $jsonProfile = ['toolbar' => $toolbar];
        $jsonSuboption = [];

        if (in_array('link', $toolbar) && !empty($profile['rexlink'])) {
            $jsonProfile['link'] = ['rexlink' => self::toArray($profile['rexlink'])];
        }

        if (!empty($profile['image_toolbar'])) {
            $imageKeys = self::toArray($profile['image_toolbar']);
            $jsonProfile['image'] = ['toolbar' => self::getImageToolbar($imageKeys), 'styles' => self::getImageStyles($imageKeys)];
        }

        if (in_array('insertTable', $toolbar) && !empty($profile['table_toolbar'])) {
            $jsonProfile['table'] = ['toolbar' => self::toArray($profile['table_toolbar'])];
        }

        if (in_array('alignment', $toolbar) && !empty($profile['alignment'])) {
            $jsonProfile['alignment'] = self::toArray($profile['alignment']);
        }

        if (in_array('fontSize', $toolbar) && !empty($profile['fontsize'])) {
            $jsonProfile['fontSize'] = ['options' => self::toArray($profile['fontsize'])];
        }

        if (in_array('heading', $toolbar) && !empty($profile['heading'])) {
            $jsonProfile['heading'] = ['options' => self::getHeadings(self::toArray($profile['heading']))];
        }

        if (in_array('highlight', $toolbar) && !empty($profile['highlight'])) {
            $jsonProfile['highlight'] = ['options' => self::getHighlight(self::toArray($profile['highlight']))];
        }

        // "rexImage": {"media_type" : "testtype"},
        // "ckfinder": {"uploadUrl": ".\/index.php?cke5upload=1&media_type=testtype&media_category=2"}

        if (in_array('rexImage', $toolbar)) {
            if (!empty($profile['mediatype'])) {
                $jsonProfile['rexImage'] = ['media_type' => $profile['mediatype']];
            } else {
                $jsonProfile['rexImage'] = ['media_path' => '/'. $profile['mediapath'] . '/'];
            }
        }

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

        if (!empty($profile['lang'])) {
            $jsonProfile['language'] = $profile['lang'];
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

        return array('suboptions' => $jsonSuboption, 'profile' => $jsonProfile);
    }

    /**
     * @throws \rex_functional_exception
     * @author Joachim Doerr
     */
    public static function languageFileCreate()
    {
        $content = '';
        foreach (rex_i18n::getLocales() as $locale) {
            if (substr($locale, 0, 2) == 'en') {
                continue;
            }
            $content .= rex_file::get(self::getAddon()->getAssetsPath(sprintf(self::TRANSLATION_PATH, substr($locale, 0, 2)))) . "\n";
        }

        if (!rex_file::put(self::getAddon()->getAssetsPath(self::TRANSLATION_FILENAME), $content)) {
            throw new \rex_functional_exception(\rex_i18n::msg('cke5_profiles_creation_exception'));
        }
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
            if (in_array($key, ['full', 'alignLeft', 'alignCenter', 'alignRight'])) {
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
            if (in_array($key, ['full', 'alignLeft', 'alignCenter', 'alignRight'])) {
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
        return explode(',', $string);
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

/*
 * TODO:
 * cke5profiles['default']['fontFamily'] = { 'options' : ['default', 'Ubuntu, Arial, sans-serif', 'Ubuntu Mono, Courier New, Courier, monospace'] };
 */