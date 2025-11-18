<?php

declare(strict_types=1);

namespace SeoFilter\Service;

use modX;

/**
 * Configuration Service for SeoFilter
 *
 * Handles all configuration-related operations, including system settings,
 * placeholder replacement, and configuration merging.
 *
 * @package SeoFilter\Service
 * @since 2.0.0
 */
class ConfigService
{
    /**
     * @var modX MODX instance
     */
    protected modX $modx;

    /**
     * @var array<string, mixed> Component configuration
     */
    protected array $config;

    /**
     * Default configuration values
     */
    private const DEFAULT_CONFIG = [
        'namespace' => 'seofilter',
        'modelPath' => '',
        'corePath' => '',
        'assetsPath' => '',
        'assetsUrl' => '',
        'connectorUrl' => '',
        'jsUrl' => '',
        'cssUrl' => '',
        'actionUrl' => '',
        'ajax' => false,
        'page' => 0,
        'params' => [],
        'hash' => '',
        'slider' => 0,
        'crumbsReplace' => false,
        'separator' => '-',
        'redirect' => false,
        'between_urls' => '-',
        'replacebefore' => '',
        'replaceseparator' => '',
        'jtitle' => false,
        'jlink' => false,
        'jdescription' => false,
        'jintrotext' => false,
        'jkeywords' => false,
        'jh1' => false,
        'jh2' => false,
        'jtext' => false,
        'jcontent' => false,
        'values_delimeter' => '||',
        'scheme' => '',
        'replace_host' => false,
        'replace_host_server_key' => 'HTTP_HOST',
        'container_suffix' => '',
        'admin_version' => true,
        'proMode' => false,
    ];

    /**
     * ConfigService constructor
     *
     * @param modX $modx MODX instance
     * @param array<string, mixed> $config Initial configuration
     */
    public function __construct(modX $modx, array $config = [])
    {
        $this->modx = $modx;
        $this->config = $this->prepareConfig($config);
    }

    /**
     * Prepare and merge configuration from various sources
     *
     * Merges default config with system settings and provided config.
     * Automatically resolves paths and URLs.
     *
     * @param array<string, mixed> $config User-provided configuration
     * @return array<string, mixed> Prepared configuration
     */
    protected function prepareConfig(array $config): array
    {
        $namespace = $config['namespace'] ?? self::DEFAULT_CONFIG['namespace'];

        // Core paths
        $corePath = $this->modx->getOption(
            "{$namespace}.core_path",
            $config,
            $this->modx->getOption('core_path') . "components/{$namespace}/"
        );

        $assetsPath = $this->modx->getOption(
            "{$namespace}.assets_path",
            $config,
            $this->modx->getOption('assets_path') . "components/{$namespace}/"
        );

        $assetsUrl = $this->modx->getOption(
            "{$namespace}.assets_url",
            $config,
            $this->modx->getOption('assets_url') . "components/{$namespace}/"
        );

        // Merge with defaults
        $merged = array_merge(self::DEFAULT_CONFIG, [
            'namespace' => $namespace,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'connectorUrl' => $assetsUrl . 'connector.php',
            'actionUrl' => $assetsUrl . 'action.php',
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
        ], $config);

        // Load system settings
        $settings = $this->modx->call('modSystemSetting', 'loadSettingsByNamespace', [$namespace]);
        if (!empty($settings)) {
            $merged = array_merge($merged, $settings);
        }

        return $merged;
    }

    /**
     * Get a configuration value
     *
     * @param string $key Configuration key
     * @param mixed $default Default value if key not found
     * @return mixed Configuration value
     */
    public function get(string $key, $default = null)
    {
        return $this->config[$key] ?? $default;
    }

    /**
     * Set a configuration value
     *
     * @param string $key Configuration key
     * @param mixed $value Configuration value
     * @return self
     */
    public function set(string $key, $value): self
    {
        $this->config[$key] = $value;
        return $this;
    }

    /**
     * Get all configuration
     *
     * @return array<string, mixed> Full configuration array
     */
    public function getAll(): array
    {
        return $this->config;
    }

    /**
     * Merge configuration with new values
     *
     * @param array<string, mixed> $config Configuration to merge
     * @return self
     */
    public function merge(array $config): self
    {
        $this->config = array_merge($this->config, $config);
        return $this;
    }

    /**
     * Create placeholders array from configuration
     *
     * Converts configuration keys to placeholder format for use in templates.
     * Keys are prefixed with '[[+' and suffixed with ']]'.
     *
     * @param array<string, mixed>|null $config Configuration to convert (uses current config if null)
     * @return array{pl: array<string, string>, vl: array<string, mixed>} Placeholders and values
     */
    public function makePlaceholders(?array $config = null): array
    {
        $config = $config ?? $this->config;
        $pl = $vl = [];

        foreach ($config as $k => $v) {
            if (is_string($v) || is_numeric($v)) {
                $pl['[[+' . $k . ']]'] = $k;
                $vl[$k] = $v;
            }
        }

        return ['pl' => $pl, 'vl' => $vl];
    }

    /**
     * Get configuration as JSON
     *
     * Useful for passing configuration to JavaScript.
     *
     * @param array<string>|null $keys Specific keys to include (null = all)
     * @param int $flags JSON encoding flags
     * @return string JSON-encoded configuration
     */
    public function toJson(?array $keys = null, int $flags = JSON_THROW_ON_ERROR): string
    {
        $data = $keys === null
            ? $this->config
            : array_intersect_key($this->config, array_flip($keys));

        return json_encode($data, $flags);
    }

    /**
     * Check if pro mode is enabled
     *
     * @return bool
     */
    public function isProMode(): bool
    {
        return (bool) $this->get('proMode', false);
    }

    /**
     * Check if AJAX mode is enabled
     *
     * @return bool
     */
    public function isAjaxMode(): bool
    {
        return (bool) $this->get('ajax', false);
    }

    /**
     * Get the values delimiter
     *
     * @return string
     */
    public function getValuesDelimiter(): string
    {
        return (string) $this->get('values_delimeter', '||');
    }

    /**
     * Get the URL separator
     *
     * @return string
     */
    public function getUrlSeparator(): string
    {
        return (string) $this->get('separator', '-');
    }
}
