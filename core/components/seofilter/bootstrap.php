<?php
/**
 * SeoFilter Bootstrap File
 *
 * This file provides backward compatibility and autoloading for the SeoFilter component.
 * It ensures both old and new code can work together during the transition period.
 *
 * @package SeoFilter
 * @since 2.0.0
 */

// Get the core path
$corePath = dirname(__DIR__) . '/';

// Try to use Composer autoloader if available
if (file_exists($corePath . '../../vendor/autoload.php')) {
    require_once $corePath . '../../vendor/autoload.php';
}

// Fallback PSR-4 autoloader for SeoFilter namespace
spl_autoload_register(function ($class) use ($corePath) {
    $prefix = 'SeoFilter\\';
    $baseDir = $corePath . 'seofilter/src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

/**
 * Get SeoFilter instance (backward compatible)
 *
 * @param modX $modx MODX instance
 * @param array $config Configuration array
 * @return SeoFilter\SeoFilter SeoFilter instance
 */
function getSeoFilter(modX $modx, array $config = []): SeoFilter\SeoFilter
{
    static $instance = null;

    if ($instance === null) {
        $instance = new SeoFilter\SeoFilter($modx, $config);
    } else {
        // Merge new configuration if provided
        if (!empty($config)) {
            $instance->getConfig()->merge($config);
        }
    }

    return $instance;
}
