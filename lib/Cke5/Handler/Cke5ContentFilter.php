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
     * Removes CKEditor filler elements that shouldn't be saved to database
     * 
     * Specifically removes:
     * - <br data-cke-filler="true"> tags (CKEditor's internal filler elements)
     * - Empty paragraphs containing only filler: <p><br data-cke-filler="true"></p>
     * 
     * Note: This method directly modifies $_POST superglobal to filter content
     * before it's processed by forms. This approach is chosen because:
     * - It works uniformly across REX_FORM, YForm, and custom forms
     * - It processes data early, before any ORM or validation
     * - It's the most efficient way to handle this across the entire backend
     * 
     * @return void
     */
    public static function filterPostData(): void
    {
        // Only filter POST requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
            return;
        }
        
        // Iterate through all POST values
        foreach ($_POST as $key => $value) {
            if (is_string($value) && self::isLikelyHtml($value)) {
                $_POST[$key] = self::filterBrTags($value);
            } elseif (is_array($value)) {
                $_POST[$key] = self::filterArrayRecursive($value);
            }
        }
    }

    /**
     * Filter CKEditor filler elements from a string
     * 
     * @param string $content
     * @return string
     */
    private static function filterBrTags(string $content): string
    {
        // First, remove empty paragraphs containing only CKEditor filler elements
        // Matches: <p><br data-cke-filler="true"></p> and variations
        $content = preg_replace('/<p>\s*<br\s+data-cke-filler=["\']true["\']\s*\/?>\s*<\/p>/i', '', $content);
        
        // Then remove standalone CKEditor filler br tags
        // Matches: <br data-cke-filler="true"> and <br data-cke-filler="true" />
        $content = preg_replace('/<br\s+data-cke-filler=["\']true["\']\s*\/?>/i', '', $content);
        
        return $content;
    }

    /**
     * Check if content likely contains HTML content
     * 
     * @param string $value
     * @return bool
     */
    private static function isLikelyHtml(string $value): bool
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
            if (is_string($value) && self::isLikelyHtml($value)) {
                $array[$key] = self::filterBrTags($value);
            } elseif (is_array($value)) {
                $array[$key] = self::filterArrayRecursive($value);
            }
        }
        return $array;
    }
}
