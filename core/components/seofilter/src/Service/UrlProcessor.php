<?php

declare(strict_types=1);

namespace SeoFilter\Service;

use modX;
use modResource;

/**
 * URL Processor Service for SeoFilter
 *
 * Handles SEO URL processing, generation, and parsing.
 * Converts between filter parameters and SEO-friendly URLs.
 *
 * @package SeoFilter\Service
 * @since 2.0.0
 */
class UrlProcessor
{
    /**
     * @var modX MODX instance
     */
    protected modX $modx;

    /**
     * @var ConfigService Configuration service
     */
    protected ConfigService $configService;

    /**
     * @var RuleService Rule service
     */
    protected RuleService $ruleService;

    /**
     * UrlProcessor constructor
     *
     * @param modX $modx MODX instance
     * @param ConfigService $configService Configuration service
     * @param RuleService $ruleService Rule service
     */
    public function __construct(
        modX $modx,
        ConfigService $configService,
        RuleService $ruleService
    ) {
        $this->modx = $modx;
        $this->configService = $configService;
        $this->ruleService = $ruleService;
    }

    /**
     * Process SEO URL on 404 error
     *
     * Called by OnPageNotFound event to handle SEO filter URLs.
     * Parses the URL, finds matching rule and URL record, then serves
     * the resource with modified SEO meta data.
     *
     * @param string $url Requested URL
     * @return bool True if URL was processed successfully
     */
    public function processUrl(string $url): bool
    {
        // Clean and normalize URL
        $url = $this->normalizeUrl($url);

        if (empty($url)) {
            return false;
        }

        // Parse URL to extract parameters
        $parsed = $this->parseUrl($url);
        if (!$parsed) {
            return false;
        }

        // Find matching URL record in database
        $urlObject = $this->findUrlByPath($url);
        if (!$urlObject) {
            return false;
        }

        // Load the resource and apply SEO meta
        return $this->applyUrlToResource($urlObject);
    }

    /**
     * Normalize URL for processing
     *
     * Removes host, scheme, query strings, and suffixes.
     *
     * @param string $url URL to normalize
     * @return string Normalized URL
     */
    public function normalizeUrl(string $url): string
    {
        // Remove scheme and host
        $url = preg_replace('#^https?://[^/]+#i', '', $url);

        // Remove query string
        if (($pos = strpos($url, '?')) !== false) {
            $url = substr($url, 0, $pos);
        }

        // Remove fragment
        if (($pos = strpos($url, '#')) !== false) {
            $url = substr($url, 0, $pos);
        }

        // Remove suffixes
        $url = $this->clearSuffixes($url);

        // Trim slashes
        return trim($url, '/');
    }

    /**
     * Clear container suffixes from URL
     *
     * @param string $url URL to clean
     * @return string Cleaned URL
     */
    public function clearSuffixes(string $url): string
    {
        $suffix = $this->configService->get('container_suffix', '');

        if (empty($suffix)) {
            return $url;
        }

        if (str_ends_with($url, $suffix)) {
            return substr($url, 0, -strlen($suffix));
        }

        return $url;
    }

    /**
     * Parse SEO URL into components
     *
     * Extracts page ID, filter parameters, and other URL components.
     *
     * @param string $url URL to parse
     * @return array{pageId: int, params: array<string, mixed>, segments: array<int, string>}|null Parsed data or null on failure
     */
    public function parseUrl(string $url): ?array
    {
        $segments = explode('/', trim($url, '/'));

        if (empty($segments)) {
            return null;
        }

        // Implementation depends on URL structure
        // This is a simplified version
        return [
            'pageId' => 0,
            'params' => [],
            'segments' => $segments
        ];
    }

    /**
     * Find URL object by path
     *
     * @param string $url URL path
     * @return \sfUrls|null URL object or null if not found
     */
    protected function findUrlByPath(string $url): ?object
    {
        $query = $this->modx->newQuery('sfUrls');
        $query->where([
            'new_url' => $url,
            'active' => 1
        ]);

        return $this->modx->getObject('sfUrls', $query);
    }

    /**
     * Apply URL data to resource
     *
     * Loads the target resource and replaces its SEO fields with
     * values from the URL record.
     *
     * @param object $urlObject sfUrls object
     * @return bool Success status
     */
    protected function applyUrlToResource(object $urlObject): bool
    {
        $ruleId = $urlObject->get('multi_id');
        if (!$ruleId) {
            return false;
        }

        $rule = $this->modx->getObject('sfRule', $ruleId);
        if (!$rule) {
            return false;
        }

        $pageId = $rule->get('page');
        $resource = $this->modx->getObject('modResource', $pageId);

        if (!$resource instanceof modResource) {
            return false;
        }

        // Apply SEO meta from URL
        $this->applySeoMeta($resource, $urlObject);

        // Set as current resource
        $this->modx->resource = $resource;
        $this->modx->resourceIdentifier = $pageId;

        return true;
    }

    /**
     * Apply SEO meta data to resource
     *
     * @param modResource $resource Target resource
     * @param object $urlObject URL object with SEO data
     * @return void
     */
    protected function applySeoMeta(modResource $resource, object $urlObject): void
    {
        $fields = [
            'pagetitle' => 'title',
            'longtitle' => 'h1',
            'description' => 'description',
            'introtext' => 'introtext',
            'menutitle' => 'keywords',
            'content' => 'content'
        ];

        foreach ($fields as $resourceField => $urlField) {
            $value = $urlObject->get($urlField);
            if (!empty($value)) {
                $resource->set($resourceField, $value);
            }
        }
    }

    /**
     * Generate SEO URL for filter parameters
     *
     * @param int $pageId Page/Resource ID
     * @param array<string, mixed> $params Filter parameters
     * @param int|null $ruleId Optional specific rule ID
     * @return string|null Generated URL or null on failure
     */
    public function generateUrl(int $pageId, array $params, ?int $ruleId = null): ?string
    {
        if (empty($params)) {
            return null;
        }

        // Find appropriate rule if not provided
        if ($ruleId === null) {
            $ruleId = $this->ruleService->findRuleId($pageId, $params);
        }

        if (!$ruleId) {
            return null;
        }

        // Get or create URL for this parameter combination
        return $this->getOrCreateUrl($ruleId, $params);
    }

    /**
     * Get existing URL or create new one
     *
     * @param int $ruleId Rule ID
     * @param array<string, mixed> $params Filter parameters
     * @return string|null URL path or null on failure
     */
    protected function getOrCreateUrl(int $ruleId, array $params): ?string
    {
        // Build parameter hash for lookup
        $paramHash = $this->buildParameterHash($params);

        // Try to find existing URL
        $query = $this->modx->newQuery('sfUrls');
        $query->where([
            'multi_id' => $ruleId,
            'param_hash' => $paramHash
        ]);

        $urlObject = $this->modx->getObject('sfUrls', $query);

        if ($urlObject) {
            return $urlObject->get('new_url');
        }

        // Create new URL (would need full implementation)
        return null;
    }

    /**
     * Build parameter hash for URL lookup
     *
     * @param array<string, mixed> $params Parameters
     * @return string Hash string
     */
    protected function buildParameterHash(array $params): string
    {
        ksort($params);
        return md5(json_encode($params, JSON_THROW_ON_ERROR));
    }

    /**
     * Build URL from resource and parameters
     *
     * @param int $pageId Page ID
     * @param array<string, string> $segments URL segments
     * @return string Built URL
     */
    public function buildUrl(int $pageId, array $segments): string
    {
        $baseUrl = $this->modx->makeUrl($pageId, '', '', 'full');
        $separator = $this->configService->getUrlSeparator();

        return rtrim($baseUrl, '/') . '/' . implode('/', $segments);
    }

    /**
     * Explode value string into array
     *
     * Handles multiple delimiter formats (||, comma).
     *
     * @param string $input Input value
     * @return array<int, string> Exploded values
     */
    public function explodeValue(string $input): array
    {
        if (empty($input)) {
            return [];
        }

        if (strpos($input, '||') !== false) {
            return array_map('trim', explode('||', $input));
        }

        if (strpos($input, ',') !== false) {
            return array_map('trim', explode(',', $input));
        }

        return [$input];
    }

    /**
     * Clean URL from unwanted characters
     *
     * @param string $url URL to clean
     * @return string Cleaned URL
     */
    public function cleanUrl(string $url): string
    {
        // Remove multiple slashes
        $url = preg_replace('#/+#', '/', $url);

        // Remove trailing slashes (except root)
        $url = rtrim($url, '/');

        if (empty($url)) {
            $url = '/';
        }

        return $url;
    }
}
