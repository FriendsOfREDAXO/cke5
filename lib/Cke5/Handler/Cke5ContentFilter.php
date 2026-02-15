<?php

namespace Cke5\Handler;

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
     * @return void
     */
    public static function filterPostData(): void
    {
        // Only filter if there's POST data
        if (empty($_POST)) {
            return;
        }
        
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
        $content = preg_replace('/<br\b[^>]*\/?>/i', '', $content);
        
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
        // Lightweight check: contains HTML tags
        return strpos($value, '<') !== false && strpos($value, '>') !== false;
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
