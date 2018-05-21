<?php
/**
 * @author mail[at]doerr-softwaredevelopment[dot]com Joachim Doerr
 * @package redaxo5
 * @license MIT
 */

namespace Cke5\Handler;


use rex;
use rex_addon;
use rex_extension;
use rex_extension_point;
use rex_response;

class Cke5UploadHandler
{
    /**
     * @author Joachim Doerr
     */
    public static function uploadCke5Img()
    {
        if (!function_exists('rex_mediapool_saveMedia')) {
            if (rex_addon::exists('mediapool') && rex_addon::get('mediapool')->isAvailable()) {
                require_once rex_addon::get('cke5')->getPath('../mediapool/functions/function_rex_mediapool.php');
            }
        }

        if ($_FILES['upload']['name'] != '' && rex_mediapool_isAllowedMediaType($_FILES['upload']['name'])) {

            $return = rex_mediapool_saveMedia($_FILES['upload'], 0, array('title'=>''), rex::getUser()->getValue('login'));

            if ($return['ok'] == 1) {
                rex_extension::registerPoint(new rex_extension_point('MEDIA_ADDED', '', $return));
            }

            $statusCode = 201;
            $response = array(
                'fileName' => $return['filename'],
                'uploaded' => 1,
                'error' => null,
                'url' => '/media/' . $return['filename'],
            );

        } else {

            $statusCode = 500;
            $response = array(
                'fileName' => null,
                'uploaded' => [
                    'number'    => 500,
                    'message'   => 'Internal server error. The uploaded file was failed',
                ],
                'error' => null,
                'url' => null,
            );

        }

        rex_response::cleanOutputBuffers();
        rex_response::sendContentType('application/json');
        rex_response::setHeader('status', $statusCode);
        rex_response::sendContent(json_encode($response));
        exit;

    }
}