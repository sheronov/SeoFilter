<?php
/**
 * Backward Compatibility Wrapper for SeoFilter 1.x
 *
 * This class extends the modern SeoFilter and provides backward compatibility
 * with the old 1.x API. It delegates to the new service-oriented implementation
 * but maintains the old method signatures and return values.
 *
 * @deprecated Use SeoFilter\SeoFilter directly via bootstrap.php
 * @package SeoFilter
 * @since 2.0.0
 */

// Load new implementation
require_once dirname(dirname(__DIR__)) . '/bootstrap.php';

class SeoFilter
{
    public $version = '2.0.0';
    public $modx;
    public $config = [];
    public $initialized = [];
    public $pdo = null;
    public $countHandler = null;
    protected $object = null;

    /**
     * @var SeoFilter\SeoFilter Modern implementation
     */
    protected $modern;

    /**
     * SeoFilter constructor - backward compatible
     *
     * @param modX $modx
     * @param array $config
     */
    public function __construct(modX $modx, array $config = [])
    {
        $this->modx =& $modx;

        // Initialize modern implementation
        $this->modern = getSeoFilter($modx, $config);

        // Get config for backward compatibility
        $this->config = $this->modern->getConfig()->getAll();

        // Legacy pdoFetch support (soft fail)
        try {
            $this->pdo = $this->modx->getService('pdoFetch');
        } catch (Exception $e) {
            // pdoFetch not available, that's OK in MODX3
        }
    }

    /**
     * Initialize - delegates to modern implementation
     *
     * @param string $ctx
     * @param array $scriptProperties
     * @return bool
     */
    public function initialize($ctx = 'web', $scriptProperties = [])
    {
        $result = $this->modern->initialize($ctx, $scriptProperties);
        $this->config = $this->modern->getConfig()->getAll();
        $this->initialized[$ctx] = $result;
        return $result;
    }

    /**
     * Get fields by key - OPTIMIZED delegation
     *
     * @param string $key
     * @param int $resourceId
     * @return array
     */
    public function getFieldsKey($key = 'key', $resourceId = 0)
    {
        return $this->modern->getFields()->getFieldsByKey($key, $resourceId);
    }

    /**
     * Explode value string - delegates to UrlProcessor
     *
     * @param string $input
     * @return array
     */
    public function explodeValue($input = '')
    {
        return $this->modern->getUrls()->explodeValue($input);
    }

    /**
     * Check resource condition - delegates to RuleService
     *
     * @param int $resource_id
     * @param string $where
     * @return bool
     */
    public function checkResourceCondition($resource_id = 0, $where = '')
    {
        return $this->modern->getRules()->checkResourceCondition($resource_id, $where);
    }

    /**
     * Get page aliases - OPTIMIZED delegation (was N+1 queries, now single query)
     *
     * @param int $page_id
     * @param array $first_params
     * @return array
     */
    public function pageAliases($page_id = 0, $first_params = [])
    {
        return $this->modern->getRules()->getPageAliases($page_id, $first_params);
    }

    /**
     * Get field aliases - OPTIMIZED delegation
     *
     * @param int $page
     * @param int $firstAliases
     * @param int $count
     * @return array
     */
    public function fieldsAliases($page = 0, $firstAliases = 0, $count = 0)
    {
        return $this->modern->getFields()->getFieldAliasesByPage($page, (bool) $firstAliases);
    }

    /**
     * Make placeholders - delegates to ConfigService
     *
     * @param array $array
     * @param string $prefix
     * @return array
     */
    public function makePlaceholders(array $array = [], $prefix = '')
    {
        if (empty($array)) {
            return $this->modern->getConfig()->makePlaceholders();
        }

        // Create placeholders from array
        $pl = $vl = [];
        foreach ($array as $k => $v) {
            if (is_string($v) || is_numeric($v)) {
                $pl['[[+' . $prefix . $k . ']]'] = $prefix . $k;
                $vl[$prefix . $k] = $v;
            }
        }

        return ['pl' => $pl, 'vl' => $vl];
    }

    /**
     * Clear suffixes - delegates to UrlProcessor
     *
     * @param string $url
     * @return string
     */
    public function clearSuffixes($url = '')
    {
        return $this->modern->getUrls()->clearSuffixes($url);
    }

    /**
     * Process URL on 404 - delegates to UrlProcessor
     *
     * @param string $requestUrl
     * @return bool|void
     */
    public function processUrl($requestUrl)
    {
        return $this->modern->processUrl($requestUrl);
    }

    /**
     * Find rule ID - OPTIMIZED delegation
     *
     * @param int $page_id
     * @param array $params
     * @param array $first_params
     * @param array $other_params
     * @return int
     */
    public function findRuleId($page_id = 0, $params = [], $first_params = [], $other_params = [])
    {
        return $this->modern->findRuleId($page_id, $params, $first_params, $other_params);
    }

    /**
     * Count rule fields - delegates to RuleService
     *
     * @param int $rule_id
     * @return int
     */
    public function countRuleFields($rule_id = 0)
    {
        return $this->modern->getRules()->countRuleFields($rule_id);
    }

    /**
     * Update URL total - delegates to RuleService
     *
     * @param int $url_id
     * @param int $total
     * @return bool|void
     */
    public function updateUrlTotal($url_id = 0, $total = 0)
    {
        return $this->modern->getRules()->updateUrlTotal($url_id, $total);
    }

    /**
     * Prepare configuration - delegates to ConfigService
     *
     * @param array $config
     * @return array
     */
    public function prepareConfig(array $config = [])
    {
        // Already handled by modern implementation
        return $this->config;
    }

    /**
     * Get option with default
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption($key, $default = null)
    {
        return $this->modern->getConfig()->get($key, $default);
    }

    /**
     * Set configuration value
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function setOption($key, $value)
    {
        $this->modern->getConfig()->set($key, $value);
        $this->config[$key] = $value;
    }

    /**
     * Magic getter for backward compatibility
     *
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        // Try to get from config first
        if (isset($this->config[$name])) {
            return $this->config[$name];
        }

        // Properties
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        return null;
    }

    /**
     * Magic isset for backward compatibility
     *
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->config[$name]) || property_exists($this, $name);
    }

    /**
     * Magic setter for backward compatibility
     *
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
            $this->modern->getConfig()->set($name, $value);
        } else {
            $this->$name = $value;
        }
    }

    /**
     * Call method on modern implementation if not found
     *
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // Try modern implementation
        if (method_exists($this->modern, $name)) {
            return call_user_func_array([$this->modern, $name], $arguments);
        }

        // Try services
        foreach (['getConfig', 'getFields', 'getMeta', 'getRules', 'getUrls'] as $serviceGetter) {
            $service = $this->modern->$serviceGetter();
            if (method_exists($service, $name)) {
                return call_user_func_array([$service, $name], $arguments);
            }
        }

        $this->modx->log(modX::LOG_LEVEL_ERROR, "SeoFilter: Method '{$name}' not found");
        return null;
    }
}
