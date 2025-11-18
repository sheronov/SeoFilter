<?php

declare(strict_types=1);

namespace SeoFilter;

use modX;
use SeoFilter\Service\ConfigService;
use SeoFilter\Service\FieldService;
use SeoFilter\Service\MetaService;
use SeoFilter\Service\RuleService;
use SeoFilter\Service\UrlProcessor;

/**
 * SeoFilter - Modern SEO URL Filtering Component for MODX3
 *
 * Main component class that provides SEO-friendly URL generation and processing
 * for filtered content (primarily designed for miniShop2 product catalogs).
 *
 * @package SeoFilter
 * @version 2.0.0
 * @author justtop1
 * @copyright Shaun McCormick (original template)
 * @license GPL-2.0-or-later
 *
 * @property-read string $version Component version
 * @property-read modX $modx MODX instance
 * @property-read ConfigService $config Configuration service
 * @property-read FieldService $fields Field service
 * @property-read MetaService $meta Meta service
 * @property-read RuleService $rules Rule service
 * @property-read UrlProcessor $urls URL processor service
 */
class SeoFilter
{
    /**
     * Component version
     */
    public const VERSION = '2.0.0';

    /**
     * @var modX MODX instance
     */
    protected modX $modx;

    /**
     * @var ConfigService Configuration service
     */
    protected ConfigService $config;

    /**
     * @var FieldService Field service
     */
    protected FieldService $fields;

    /**
     * @var MetaService Meta service
     */
    protected MetaService $meta;

    /**
     * @var RuleService Rule service
     */
    protected RuleService $rules;

    /**
     * @var UrlProcessor URL processor service
     */
    protected UrlProcessor $urls;

    /**
     * @var array<string, bool> Initialization tracking by context
     */
    protected array $initialized = [];

    /**
     * @var string Component version (for backward compatibility)
     */
    public string $version = self::VERSION;

    /**
     * SeoFilter constructor
     *
     * Initializes the component with all required services.
     * Automatically loads the seofilter model package and lexicon.
     *
     * @param modX $modx MODX instance
     * @param array<string, mixed> $config Configuration array
     */
    public function __construct(modX $modx, array $config = [])
    {
        $this->modx = $modx;

        // Initialize services
        $this->config = new ConfigService($modx, $config);
        $this->fields = new FieldService($modx, $this->config);
        $this->meta = new MetaService($modx, $this->config);
        $this->rules = new RuleService($modx, $this->config);
        $this->urls = new UrlProcessor($modx, $this->config, $this->rules);

        // Load model package
        $modelPath = $this->config->get('modelPath');
        $this->modx->addPackage('seofilter', $modelPath);

        // Load lexicon
        $this->modx->lexicon->load('seofilter:default');
    }

    /**
     * Initialize component for specific context
     *
     * Sets up frontend JavaScript, registers client scripts,
     * and prepares AJAX configuration if enabled.
     *
     * @param string $ctx Context to initialize (default: 'web')
     * @param array<string, mixed> $scriptProperties Additional properties
     * @return bool Success status
     */
    public function initialize(string $ctx = 'web', array $scriptProperties = []): bool
    {
        // Check if already initialized for this context
        if ($this->initialized[$ctx] ?? false) {
            return true;
        }

        // Merge script properties into configuration
        $this->config->merge($scriptProperties);

        // Initialize AJAX mode if enabled
        if ($this->config->isAjaxMode()) {
            $this->initializeAjaxMode($ctx);
        }

        $this->initialized[$ctx] = true;

        return true;
    }

    /**
     * Initialize AJAX mode
     *
     * Registers JavaScript files and configuration for frontend filtering.
     *
     * @param string $ctx Context
     * @return void
     */
    protected function initializeAjaxMode(string $ctx): void
    {
        // Register frontend JavaScript
        $js = trim($this->modx->getOption(
            'seofilter_frontend_js',
            null,
            $this->config->get('jsUrl') . 'web/default.js',
            false
        ));

        if (!empty($js) && preg_match('/\.js$/i', $js)) {
            $config = $this->config->makePlaceholders();
            $jsFile = str_replace($config['pl'], $config['vl'], $js);

            // Add version parameter if enabled
            if ($this->config->get('admin_version')) {
                $jsFile .= '?v=' . substr(md5(self::VERSION), 0, 10);
            }

            $this->modx->regClientScript($jsFile);
        }

        // Prepare page URL
        $pageId = (int) $this->config->get('page', 0);
        if ($pageId > 0) {
            $pageUrl = $this->preparePageUrl($pageId, $ctx);
            $this->config->set('url', $pageUrl);

            // Count slider fields for this page
            $sliderCount = $this->countSliderFields($pageId);
            $this->config->set('slider', $sliderCount);
        }

        // Register configuration for JavaScript
        $this->registerJavaScriptConfig();
    }

    /**
     * Prepare page URL for AJAX requests
     *
     * @param int $pageId Page ID
     * @param string $ctx Context
     * @return string Prepared page URL
     */
    protected function preparePageUrl(int $pageId, string $ctx): string
    {
        $pageUrl = $this->modx->makeUrl(
            $pageId,
            $ctx,
            '',
            $this->config->get('scheme', '')
        );

        // Replace host if configured
        $replaceHost = $this->config->get('replace_host');
        $serverKey = $this->config->get('replace_host_server_key', 'HTTP_HOST');

        if ($replaceHost && !empty($_SERVER[$serverKey])) {
            $pageUrlHost = parse_url($pageUrl, PHP_URL_HOST);
            if ($pageUrlHost) {
                $pageUrl = str_replace($pageUrlHost, $_SERVER[$serverKey], $pageUrl);
            }
        }

        // Remove container suffix
        $containerSuffix = $this->config->get('container_suffix', '');
        if ($containerSuffix && str_ends_with($pageUrl, $containerSuffix)) {
            $pageUrl = substr($pageUrl, 0, -strlen($containerSuffix));
        }

        return $this->urls->clearSuffixes($pageUrl);
    }

    /**
     * Count slider fields for a page
     *
     * @param int $pageId Page ID
     * @return int Number of slider fields
     */
    protected function countSliderFields(int $pageId): int
    {
        $query = $this->modx->newQuery('sfFieldIds');
        $query->rightJoin('sfRule', 'sfRule', 'sfRule.id = sfFieldIds.multi_id');
        $query->rightJoin('sfField', 'sfField', 'sfField.id = sfFieldIds.field_id');
        $query->where(['sfField.slider' => 1]);

        if ($this->config->isProMode()) {
            $query->where(
                "FIND_IN_SET({$pageId}, REPLACE(IFNULL(NULLIF(sfRule.pages,''), sfRule.page),' ',''))"
            );
        } else {
            $query->where(['sfRule.page' => $pageId]);
        }

        return $this->modx->getCount('sfFieldIds', $query);
    }

    /**
     * Register JavaScript configuration
     *
     * Outputs component configuration as JavaScript object.
     *
     * @return void
     */
    protected function registerJavaScriptConfig(): void
    {
        $jsConfig = [
            'jsUrl' => $this->config->get('jsUrl') . 'web/',
            'actionUrl' => $this->config->get('actionUrl'),
            'ctx' => $this->modx->context->key,
            'page' => $this->config->get('page'),
            'params' => $this->config->get('params'),
            'hash' => $this->config->get('hash'),
            'slider' => $this->config->get('slider'),
            'crumbs' => $this->config->get('crumbsReplace'),
            'separator' => $this->config->get('separator'),
            'redirect' => $this->config->get('redirect'),
            'url' => $this->config->get('url'),
            'between' => $this->config->get('between_urls'),
            'replacebefore' => $this->config->get('replacebefore'),
            'replaceseparator' => $this->config->get('replaceseparator'),
            'jtitle' => $this->config->get('jtitle'),
            'jlink' => $this->config->get('jlink'),
            'jdescription' => $this->config->get('jdescription'),
            'jintrotext' => $this->config->get('jintrotext'),
            'jkeywords' => $this->config->get('jkeywords'),
            'jh1' => $this->config->get('jh1'),
            'jh2' => $this->config->get('jh2'),
            'jtext' => $this->config->get('jtext'),
            'jcontent' => $this->config->get('jcontent'),
            'delimeter' => $this->config->get('values_delimeter'),
        ];

        $json = json_encode($jsConfig, JSON_THROW_ON_ERROR);
        $script = sprintf('<script type="text/javascript">seoFilterConfig = %s;</script>', $json);

        $this->modx->regClientStartupScript($script, true);
    }

    /**
     * Process SEO URL (OnPageNotFound handler)
     *
     * Main entry point for processing SEO filter URLs.
     * Called by the plugin on 404 errors.
     *
     * @param string $url Requested URL
     * @return bool True if URL was processed successfully
     */
    public function processUrl(string $url): bool
    {
        return $this->urls->processUrl($url);
    }

    /**
     * Generate SEO URL for filter parameters
     *
     * @param int $pageId Page ID
     * @param array<string, mixed> $params Filter parameters
     * @param int|null $ruleId Optional rule ID
     * @return string|null Generated URL or null on failure
     */
    public function generateUrl(int $pageId, array $params, ?int $ruleId = null): ?string
    {
        return $this->urls->generateUrl($pageId, $params, $ruleId);
    }

    /**
     * Find rule ID for page and parameters
     *
     * @param int $pageId Page ID
     * @param array<string, mixed> $params Filter parameters
     * @return int Rule ID (0 if not found)
     */
    public function findRuleId(int $pageId, array $params): int
    {
        return $this->rules->findRuleId($pageId, $params);
    }

    /**
     * Get configuration service
     *
     * @return ConfigService
     */
    public function getConfig(): ConfigService
    {
        return $this->config;
    }

    /**
     * Get field service
     *
     * @return FieldService
     */
    public function getFields(): FieldService
    {
        return $this->fields;
    }

    /**
     * Get meta service
     *
     * @return MetaService
     */
    public function getMeta(): MetaService
    {
        return $this->meta;
    }

    /**
     * Get rule service
     *
     * @return RuleService
     */
    public function getRules(): RuleService
    {
        return $this->rules;
    }

    /**
     * Get URL processor service
     *
     * @return UrlProcessor
     */
    public function getUrls(): UrlProcessor
    {
        return $this->urls;
    }

    /**
     * Get MODX instance
     *
     * @return modX
     */
    public function getModx(): modX
    {
        return $this->modx;
    }

    /**
     * Magic getter for backward compatibility
     *
     * Allows accessing services as properties: $seofilter->config
     *
     * @param string $name Property name
     * @return mixed Property value
     */
    public function __get(string $name)
    {
        return match ($name) {
            'modx' => $this->modx,
            'config' => $this->config,
            'fields' => $this->fields,
            'meta' => $this->meta,
            'rules' => $this->rules,
            'urls' => $this->urls,
            'version' => $this->version,
            default => null,
        };
    }

    /**
     * Check if property exists (for backward compatibility)
     *
     * @param string $name Property name
     * @return bool
     */
    public function __isset(string $name): bool
    {
        return in_array($name, ['modx', 'config', 'fields', 'meta', 'rules', 'urls', 'version'], true);
    }
}
