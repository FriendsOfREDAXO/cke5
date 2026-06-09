<?php

namespace Cke5;

class PluginRegistry
{
    /**
     * @var array<string,array<string,mixed>>
     */
    private static $plugins = [];

    /**
     * @param array<string,mixed> $config
     */
    public static function addPlugin(string $name, string $url, array $config = []): void
    {
        $name = trim($name);
        $url = trim($url);

        if ($name === '' || $url === '') {
            return;
        }

        self::$plugins[$name] = [
            'name' => $name,
            'url' => $url,
            'config' => $config,
        ];
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public static function all(): array
    {
        return self::$plugins;
    }

    /**
     * @return array<string,array<string,mixed>>
     */
    public static function clientConfig(): array
    {
        $result = [];

        foreach (self::$plugins as $name => $plugin) {
            $result[$name] = [
                'name' => $name,
                'config' => $plugin['config'] ?? [],
            ];
        }

        return $result;
    }
}
