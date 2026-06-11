<?php

namespace Cke5\Utils;

use Cke5\Creator\Cke5ProfilesCreator;
use rex_addon;
use rex_i18n;

class Cke5PreviewHelper
{
    /**
     * @param non-empty-string $label
     */
    private static function renderCodeSample(string $label, string $code, string $languageClass = ''): string
    {
        $sampleId = 'cke5-code-' . md5($label . $code . $languageClass);
        $codeClass = '' !== $languageClass ? ' class="' . htmlspecialchars($languageClass, ENT_QUOTES) . '"' : '';

        return '<div class="cke5-code-sample">'
            . '<div class="cke5-code-sample-head">'
            . '<strong>' . htmlspecialchars($label, ENT_QUOTES) . '</strong>'
            . '<button type="button" class="btn btn-xs btn-default cke5-copy-btn" data-cke5-copy-target="' . $sampleId . '">Kopieren</button>'
            . '</div>'
            . '<pre id="' . $sampleId . '" class="cke5-code-block" data-cke5-copy-source="1"><code' . $codeClass . '>' . htmlspecialchars($code, ENT_QUOTES) . '</code></pre>'
            . '</div>';
    }

    /**
     * @param array<string,mixed> $profile
     */
    public static function getMFormCode(array $profile): string
    {
        if (!rex_addon::exists('mform')) {
            return '';
        }

        $profileName = (string) ($profile['name'] ?? 'default');
        $userLang = (string) Cke5Lang::getUserLang();
        $outputLang = (string) Cke5Lang::getOutputLang();

        $code = '$mform->addTextAreaField(\'1\', [\'class\' => \'cke5-editor\', \'data-lang\' => \''
            . $userLang
            . '\', \'data-content-lang\' => \''
            . $outputLang
            . '\', \'data-profile\' => \''
            . $profileName
            . '\']);';

        return self::renderCodeSample('MForm Beispiel', $code, 'language-php');
    }

    /**
     * @param array<string,mixed> $profile
     */
    public static function getHtmlCode(array $profile): string
    {
        $profileName = (string) ($profile['name'] ?? 'default');
        $userLang = (string) Cke5Lang::getUserLang();
        $outputLang = (string) Cke5Lang::getOutputLang();

        $code = '<textarea class="form-control cke5-editor" data-profile="'
            . $profileName
            . '" data-lang="'
            . $userLang
            . '" data-content-lang="'
            . $outputLang
            . '">REX_VALUE[1]</textarea>';

        return self::renderCodeSample('HTML Beispiel', $code, 'language-html');
    }

    /**
     * @param array<string,mixed> $profile
     */
    public static function getYFormJsonCode(array $profile): string
    {
        $profileName = (string) ($profile['name'] ?? 'default');
        $userLang = (string) Cke5Lang::getUserLang();
        $outputLang = (string) Cke5Lang::getOutputLang();

        $data = [
            'style' => 'max-width: 843',
            'class' => 'form-control cke5-editor',
            'data-profile' => $profileName,
            'data-lang' => $userLang,
            'data-content-lang' => $outputLang,
        ];

        return self::renderCodeSample(
            'YForm JSON Beispiel',
            (string) json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'language-json'
        );
    }

    /**
     * @param array<string,int|string|null> $profile
     */
    public static function getProfileDetails(array $profile): string
    {
        /** @var array<string,string> $profile */
        $result = Cke5ProfilesCreator::mapProfile($profile);
        $json = json_encode($result['profile'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        if (!is_string($json) || $json === '') {
            $json = '{}';
        }

        $profileId = (string) ($profile['id'] ?? '0');

        return '<a href="#profile' . $profile['id'] . '" data-toggle="collapse">' . rex_i18n::msg('cke5_editor_preview_shop_profile_settings') . '</a>
            <div id="profile' . $profile['id'] . '" class="collapse">
                <pre class="cke5-json-preview" data-cke5-json-preview="' . htmlspecialchars($profileId, ENT_QUOTES) . '">' . htmlspecialchars($json, ENT_QUOTES) . '</pre>
            </div>';
    }

    /**
     * @param array<string,int|string|null> $profile
     */
    public static function getProfilePreview(array $profile, bool $html = true, bool $mform = true, bool $details = true): string
    {
        $code = '';
        $profileName = (string) ($profile['name'] ?? 'default');
        $userLang = (string) Cke5Lang::getUserLang();
        $outputLang = (string) Cke5Lang::getOutputLang();

        if ($html || $mform || $details) {
            $code = '<div class="cke5-preview-code">';
            $code .= ($html) ? self::getHtmlCode($profile) : '';
            $code .= self::getYFormJsonCode($profile);
            $code .= ($mform) ? self::getMFormCode($profile) : '';
            $code .= ($details) ? self::getProfileDetails($profile) : '';
            $code .= '</div>';
        }

        return '        <div class="cke5-preview-row">
            <div class="cke5-preview-editor">
                <h4>"<a href="index.php?page=cke5/profiles&func=edit&profile=' . rawurlencode($profileName) . '">' . htmlspecialchars($profileName, ENT_QUOTES) . '</a>" - ' . htmlspecialchars((string) ($profile['description'] ?? ''), ENT_QUOTES) . '</h4>
                <div class="cke5-editor" data-profile="' . htmlspecialchars($profileName, ENT_QUOTES) . '" data-lang="' . htmlspecialchars($userLang, ENT_QUOTES) . '" data-content-lang="' . htmlspecialchars($outputLang, ENT_QUOTES) . '"></div>
                ' . $code . '
            </div>
        </div>';
    }
}
