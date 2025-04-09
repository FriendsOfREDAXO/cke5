<?php

namespace Cke5\Handler;


use rex;
use rex_addon;
use rex_api_exception;
use rex_extension;
use rex_extension_point;
use rex_media_service;
use rex_mediapool;
use rex_request;
use rex_response;

class Cke5UploadHandler
{
    /** @api string */
    const MEDIA_TYPE_PATH = '/index.php?rex_media_type=%s&rex_media_file=';
    /** @api string */
    const MEDIA_PATH = '/media/';

    /**
     * @throws rex_api_exception
     */
    public static function uploadCke5Img(): void
    {
        if (!function_exists('rex_mediapool_saveMedia')) {
            if (rex_addon::exists('mediapool') && rex_addon::get('mediapool')->isAvailable()) {
                require_once rex_addon::get('cke5')->getPath('../mediapool/functions/function_rex_mediapool.php');
            }
        }

        /** @var array<string,string|array<string,string>> $file */
        $file = rex_request::files('upload', 'array', []);

        if (isset($file['name']) && $file['name'] !== '' && is_string($file['name']) && rex_mediapool::isAllowedExtension($file['name'])) {

            /** @var array{category_id: int, title: string, file: array{name: string, path?: string, tmp_name?: string, error?: int}} $data */
            $data = [
                'category_id' => intval(rex_request::get('media_category', 'int', 0)),
                'title' => '',
                'file' => $file
            ];

            $return = rex_media_service::addMedia($data);

            if ($return['ok'] === 1) {
                rex_extension::registerPoint(new rex_extension_point('MEDIA_ADDED', '', $return));
            }

            /** @var string $mediaType */
            $mediaType = rex_request::get('media_type', 'string', '');
            $mediaSrcPath = '/' . rex_request::get('media_path', 'string', self::MEDIA_PATH) . '/';

            if ($mediaType !== '') {
                $mediaSrcPath = sprintf(self::MEDIA_TYPE_PATH, $mediaType);
            }

            $statusCode = 201;
            $response = [
                'fileName' => $return['filename'],
                'uploaded' => 1,
                'error' => null,
                'url' => $mediaSrcPath . $return['filename'],
            ];

        } else {

            $statusCode = 500;
            $response = [
                'fileName' => null,
                'uploaded' => [
                    'number' => 500,
                    'message' => 'Internal server error. The uploaded file was failed',
                ],
                'error' => null,
                'url' => null,
            ];

        }

        $response = json_encode($response);

        rex_response::cleanOutputBuffers();
        rex_response::sendContentType('application/json');
        rex_response::setHeader('status', (string) $statusCode);
        rex_response::sendContent((is_string($response) ? $response : ''));
        exit;

    }
}