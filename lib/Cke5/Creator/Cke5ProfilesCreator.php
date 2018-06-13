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

    /**
     * @throws \rex_functional_exception
     * @author Joachim Doerr
     */
    public static function profilesCreate()
    {
        $profiles = Cke5DatabaseHandler::getAllProfiles();
        $content = '';

        if (sizeof($profiles) > 0) {
            $jsonProfiles = array();
            $jsonSuboptions = array();

            foreach ($profiles as $profile) {

                $toolbar = self::toArray($profile['toolbar']);
                $jsonProfile = array('toolbar' => $toolbar);
                $jsonSuboption = array();

                if (in_array('link', $toolbar) && !empty($profile['rexlink'])) {
                    $jsonProfile['link'] = array('rexlink' => self::toArray($profile['rexlink']));
                }

                if (!empty($profile['image_toolbar'])) {
                    $imageKeys = self::toArray($profile['image_toolbar']);
                    $jsonProfile['image'] = array('toolbar' => self::getImageToolbar($imageKeys), 'styles' => self::getImageStyles($imageKeys));
                }

                if (in_array('alignment', $toolbar) && !empty($profile['alignment'])) {
                    $jsonProfile['alignment'] = self::toArray($profile['alignment']);
                }

                if (in_array('fontSize', $toolbar) && !empty($profile['fontsize'])) {
                    $jsonProfile['fontSize'] = array('options' => self::toArray($profile['fontsize']));
                }

                if (in_array('heading', $toolbar) && !empty($profile['heading'])) {
                    $jsonProfile['heading'] = array('options' => self::getHeadings(self::toArray($profile['heading'])));
                }

                if (in_array('highlight', $toolbar) && !empty($profile['highlight'])) {
                    $jsonProfile['highlight'] = array('options' => self::getHighlight(self::toArray($profile['highlight'])));
                }

                // "rexImage": {"media_type" : "testtype"},
                // "ckfinder": {"uploadUrl": ".\/index.php?cke5upload=1&media_type=testtype&media_category=2"}

                if (in_array('rexImage', $toolbar) && !empty($profile['mediatype'])) {
                    $jsonProfile['rexImage'] = array('media_type' => $profile['mediatype']);
                }

                if (!is_null($profile['upload_default']) or !empty($profile['upload_default'])) {
                    $ckFinderUrl = self::UPLOAD_URL;

                    if (!empty($profile['mediatype'])) {
                        $ckFinderUrl .= '&media_type=' . $profile['mediatype'];
                    }

                    if (!empty($profile['mediacategory'])) {
                        $ckFinderUrl .= '&media_category=' . $profile['mediacategory'];
                    }

                    $jsonProfile['ckfinder'] = array('uploadUrl' => $ckFinderUrl);
                }

                if (!empty($profile['lang'])) {
                    $jsonProfile['language'] = $profile['lang'];
                }

                if (is_null($profile['height_default']) or empty($profile['height_default'])) {
                    foreach(array('min', 'max') as $key) {
                        if (!empty($profile[$key . '_height'])) {
                            if ((int)$profile[$key . '_height'] == 0) {
                                $jsonSuboption[] = array($key . '-height' => 'none');
                            } else {
                                $jsonSuboption[] = array($key . '-height' => (int)$profile[$key . '_height']);
                            }
                        }
                    }
                }

                $jsonSuboptions[$profile['name']] = $jsonSuboption;
                $jsonProfiles[$profile['name']] = $jsonProfile;

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
     * @throws \rex_functional_exception
     * @author Joachim Doerr
     */
    public static function languageFileCreate()
    {
        $content = '';
        foreach (rex_i18n::getLocales() as $locale) {
            if (substr($locale, 0, 2) == 'en') { continue; }
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
        $return = array();

        foreach ($keys as $key) {
            if (in_array($key, array('full', 'alignLeft', 'alignCenter', 'alignRight'))){
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
        $return = array();

        foreach ($keys as $key) {
            if (in_array($key, array('full', 'alignLeft', 'alignCenter', 'alignRight'))){
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
        $headings = array(
            'paragraph' => array(
                'model' => 'paragraph',
                'title' => 'Paragraph',
                'class' => 'ck-heading_paragraph'
            ),
            'h1' => array(
                'model' => 'heading1',
                'view' => 'h1',
                'title' => 'Heading 1',
                'class' => 'ck-heading_heading1'
            ),
            'h2' => array(
                'model' => 'heading2',
                'view' => 'h2',
                'title' => 'Heading 2',
                'class' => 'ck-heading_heading2'
            ),
            'h3' => array(
                'model' => 'heading3',
                'view' => 'h3',
                'title' => 'Heading 3',
                'class' => 'ck-heading_heading3'
            ),
            'h4' => array(
                'model' => 'heading4',
                'view' => 'h4',
                'title' => 'Heading 4',
                'class' => 'ck-heading_heading4'
            ),
            'h5' => array(
                'model' => 'heading5',
                'view' => 'h5',
                'title' => 'Heading 5',
                'class' => 'ck-heading_heading5'
            ),
            'h6' => array(
                'model' => 'heading6',
                'view' => 'h6',
                'title' => 'Heading 6',
                'class' => 'ck-heading_heading6'
            ),
        );

        $return = array();

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
        $highlights = array(
            'yellowMarker' => array(
                'model' => 'yellowMarker',
                'class' => 'marker-yellow',
                'title' => 'Yellow Marker',
                'color' => 'var(--ck-highlight-marker-yellow)',
                'type' => 'marker'
            ),
            'greenMarker' => array(
                'model' => 'greenMarker',
                'class' => 'marker-green',
                'title' => 'Green Marker',
                'color' => 'var(--ck-highlight-marker-green)',
                'type' => 'marker'
            ),
            'pinkMarker' => array(
                'model' => 'pinkMarker',
                'class' => 'marker-pink',
                'title' => 'Pink Marker',
                'color' => 'var(--ck-highlight-marker-pink)',
                'type' => 'marker'
            ),
            'blueMarker' => array(
                'model' => 'blueMarker',
                'class' => 'marker-blue',
                'title' => 'Blue Marker',
                'color' => 'var(--ck-highlight-marker-blue)',
                'type' => 'marker'
            ),
            'redPen' => array(
                'model' => 'redPen',
                'class' => 'pen-red',
                'title' => 'Red pen',
                'color' => 'var(--ck-highlight-pen-red)',
                'type' => 'pen'
            ),
            'greenPen' => array(
                'model' => 'greenPen',
                'class' => 'pen-green',
                'title' => 'Green pen',
                'color' => 'var(--ck-highlight-pen-green)',
                'type' => 'pen'
            ),
        );

        $return = array();

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