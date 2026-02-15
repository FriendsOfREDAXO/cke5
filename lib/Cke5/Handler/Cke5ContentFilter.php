<?php

namespace Cke5\Handler;

use rex_extension_point;

/**
 * Class Cke5ContentFilter
 * Filters and cleans CKEditor content before saving
 * 
 * @package Cke5\Handler
 */
class Cke5ContentFilter
{
    /**
     * Filter CKEditor content from POST data
     * Removes unwanted <br> tags that are often inserted by Shift+Enter
     * 
     * @param rex_extension_point $ep
     * @return void
     */
    public static function filterPostData(rex_extension_point $ep): void
    {
        // Check if filtering is enabled
        $addon = \rex_addon::get('cke5');
        if (!$addon->getConfig('filter_br_tags', false)) {
            return;
        }

        // Get POST data
        $post = \rex_post::factory();
        
        // Iterate through all POST values
        foreach ($_POST as $key => $value) {
            if (is_string($value) && self::containsCke5Content($value)) {
                $_POST[$key] = self::filterBrTags($value);
            } elseif (is_array($value)) {
                $_POST[$key] = self::filterArrayRecursive($value);
            }
        }
    }

    /**
     * Filter br tags from a string
     * 
     * @param string $content
     * @return string
     */
    private static function filterBrTags(string $content): string
    {
        // Remove standalone <br> tags (with or without attributes, self-closing or not)
        // This regex matches:
        // - <br> 
        // - <br /> 
        // - <br/>
        // - <br class="...">
        // - <BR> (case insensitive)
        $content = preg_replace('/<br\s*\/?>/i', '', $content);
        
        return $content;
    }

    /**
     * Check if content likely contains CKEditor HTML content
     * 
     * @param string $value
     * @return bool
     */
    private static function containsCke5Content(string $value): bool
    {
        // Simple heuristic: contains HTML tags
        return (strip_tags($value) !== $value);
    }

    /**
     * Recursively filter array values
     * 
     * @param array $array
     * @return array
     */
    private static function filterArrayRecursive(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_string($value) && self::containsCke5Content($value)) {
                $array[$key] = self::filterBrTags($value);
            } elseif (is_array($value)) {
                $array[$key] = self::filterArrayRecursive($value);
            }
        }
        return $array;
    }
}
