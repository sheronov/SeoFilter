<?php

declare(strict_types=1);

namespace SeoFilter\Service;

use modX;
use modResource;

/**
 * Meta Service for SeoFilter
 *
 * Handles SEO meta data generation, placeholder parsing,
 * and meta field management for filter URLs.
 *
 * @package SeoFilter\Service
 * @since 2.0.0
 */
class MetaService
{
    /**
     * SEO meta field names
     */
    public const FIELD_TITLE = 'title';
    public const FIELD_H1 = 'h1';
    public const FIELD_H2 = 'h2';
    public const FIELD_DESCRIPTION = 'description';
    public const FIELD_INTROTEXT = 'introtext';
    public const FIELD_KEYWORDS = 'keywords';
    public const FIELD_TEXT = 'text';
    public const FIELD_CONTENT = 'content';
    public const FIELD_LINK = 'link';

    /**
     * @var modX MODX instance
     */
    protected modX $modx;

    /**
     * @var ConfigService Configuration service
     */
    protected ConfigService $configService;

    /**
     * MetaService constructor
     *
     * @param modX $modx MODX instance
     * @param ConfigService $configService Configuration service
     */
    public function __construct(modX $modx, ConfigService $configService)
    {
        $this->modx = $modx;
        $this->configService = $configService;
    }

    /**
     * Get all SEO meta field names
     *
     * @return array<int, string> SEO field names
     */
    public function getSeoFields(): array
    {
        return [
            self::FIELD_TITLE,
            self::FIELD_H1,
            self::FIELD_H2,
            self::FIELD_DESCRIPTION,
            self::FIELD_INTROTEXT,
            self::FIELD_KEYWORDS,
            self::FIELD_TEXT,
            self::FIELD_CONTENT,
            self::FIELD_LINK,
        ];
    }

    /**
     * Build meta data from rule and parameters
     *
     * Generates SEO meta content by processing rule templates with
     * filter parameters and dictionary words.
     *
     * @param object $rule sfRule object
     * @param array<string, mixed> $params Filter parameters
     * @param array<string, mixed> $words Dictionary words for parameters
     * @return array<string, string> Generated meta data
     */
    public function buildMetaFromRule(object $rule, array $params, array $words = []): array
    {
        $meta = [];

        foreach ($this->getSeoFields() as $field) {
            $template = $rule->get($field);

            if (empty($template)) {
                continue;
            }

            // Process placeholders in template
            $processed = $this->processPlaceholders($template, $params, $words);
            $meta[$field] = $processed;
        }

        return $meta;
    }

    /**
     * Process placeholders in template string
     *
     * Replaces placeholders like [[+field_alias]] with actual values.
     * Supports dictionary words, parameters, and custom placeholders.
     *
     * @param string $template Template string with placeholders
     * @param array<string, mixed> $params Filter parameters
     * @param array<string, mixed> $words Dictionary words
     * @return string Processed template
     */
    public function processPlaceholders(string $template, array $params, array $words = []): string
    {
        // Build placeholder array
        $placeholders = array_merge(
            $this->buildParamPlaceholders($params),
            $this->buildWordPlaceholders($words)
        );

        // Replace placeholders
        $result = str_replace(
            array_keys($placeholders),
            array_values($placeholders),
            $template
        );

        // Clean up any remaining unreplaced placeholders
        $result = preg_replace('/\[\[.*?\]\]/', '', $result);

        return trim($result);
    }

    /**
     * Build placeholders from parameters
     *
     * @param array<string, mixed> $params Parameters
     * @return array<string, string> Placeholder => value map
     */
    protected function buildParamPlaceholders(array $params): array
    {
        $placeholders = [];

        foreach ($params as $key => $value) {
            if (is_scalar($value)) {
                $placeholders["[[+{$key}]]"] = (string) $value;
                $placeholders["[[+param.{$key}]]"] = (string) $value;
            }
        }

        return $placeholders;
    }

    /**
     * Build placeholders from dictionary words
     *
     * @param array<string, mixed> $words Dictionary words
     * @return array<string, string> Placeholder => value map
     */
    protected function buildWordPlaceholders(array $words): array
    {
        $placeholders = [];

        foreach ($words as $key => $word) {
            if (is_array($word)) {
                // Support different grammatical cases (Russian declensions)
                $cases = ['nominative', 'genitive', 'dative', 'accusative', 'instrumental', 'prepositional'];

                foreach ($cases as $case) {
                    if (isset($word[$case])) {
                        $placeholders["[[+{$key}.{$case}]]"] = $word[$case];
                    }
                }

                // Default to nominative or first available
                $default = $word['nominative'] ?? $word['value'] ?? reset($word);
                $placeholders["[[+{$key}]]"] = (string) $default;
            } else {
                $placeholders["[[+{$key}]]"] = (string) $word;
                $placeholders["[[+word.{$key}]]"] = (string) $word;
            }
        }

        return $placeholders;
    }

    /**
     * Apply meta data to resource
     *
     * Updates resource SEO fields with provided meta data.
     *
     * @param modResource $resource Target resource
     * @param array<string, string> $meta Meta data to apply
     * @return void
     */
    public function applyMetaToResource(modResource $resource, array $meta): void
    {
        $fieldMap = $this->getResourceFieldMap();

        foreach ($meta as $metaField => $value) {
            if (empty($value)) {
                continue;
            }

            $resourceField = $fieldMap[$metaField] ?? null;
            if ($resourceField) {
                $resource->set($resourceField, $value);
            }
        }
    }

    /**
     * Get mapping between meta fields and resource fields
     *
     * @return array<string, string> Meta field => Resource field map
     */
    protected function getResourceFieldMap(): array
    {
        return [
            self::FIELD_TITLE => 'pagetitle',
            self::FIELD_H1 => 'longtitle',
            self::FIELD_DESCRIPTION => 'description',
            self::FIELD_INTROTEXT => 'introtext',
            self::FIELD_KEYWORDS => 'menutitle',
            self::FIELD_CONTENT => 'content',
        ];
    }

    /**
     * Extract meta data from resource
     *
     * @param modResource $resource Source resource
     * @return array<string, string> Extracted meta data
     */
    public function extractMetaFromResource(modResource $resource): array
    {
        $fieldMap = array_flip($this->getResourceFieldMap());
        $meta = [];

        foreach ($fieldMap as $resourceField => $metaField) {
            $value = $resource->get($resourceField);
            if (!empty($value)) {
                $meta[$metaField] = $value;
            }
        }

        return $meta;
    }

    /**
     * Prepare meta data for AJAX response
     *
     * @param array<string, string> $meta Meta data
     * @param bool $encode Whether to JSON encode
     * @return array<string, string>|string Meta data or JSON string
     */
    public function prepareMetaForAjax(array $meta, bool $encode = false)
    {
        $prepared = [];

        foreach ($this->getSeoFields() as $field) {
            $prepared[$field] = $meta[$field] ?? '';
        }

        if ($encode) {
            return json_encode($prepared, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        }

        return $prepared;
    }

    /**
     * Validate meta field value
     *
     * Checks if meta value meets requirements (length, format, etc.).
     *
     * @param string $field Meta field name
     * @param string $value Meta field value
     * @return bool Whether value is valid
     */
    public function validateMetaField(string $field, string $value): bool
    {
        if (empty($value)) {
            return false;
        }

        switch ($field) {
            case self::FIELD_TITLE:
                // Title should be 30-60 characters for optimal SEO
                $length = mb_strlen($value);
                return $length >= 10 && $length <= 200;

            case self::FIELD_DESCRIPTION:
                // Meta description should be 120-160 characters
                $length = mb_strlen($value);
                return $length >= 50 && $length <= 500;

            case self::FIELD_H1:
            case self::FIELD_H2:
                // Headers should not be empty and reasonable length
                $length = mb_strlen($value);
                return $length >= 5 && $length <= 200;

            default:
                // General validation - not empty
                return mb_strlen($value) > 0;
        }
    }

    /**
     * Truncate meta field to optimal length
     *
     * @param string $field Meta field name
     * @param string $value Meta field value
     * @param string $suffix Suffix to add when truncating
     * @return string Truncated value
     */
    public function truncateMetaField(string $field, string $value, string $suffix = '...'): string
    {
        $limits = [
            self::FIELD_TITLE => 60,
            self::FIELD_DESCRIPTION => 160,
            self::FIELD_H1 => 70,
            self::FIELD_H2 => 70,
            self::FIELD_KEYWORDS => 200,
        ];

        $limit = $limits[$field] ?? null;

        if ($limit === null || mb_strlen($value) <= $limit) {
            return $value;
        }

        // Truncate at word boundary
        $truncated = mb_substr($value, 0, $limit - mb_strlen($suffix));
        $lastSpace = mb_strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = mb_substr($truncated, 0, $lastSpace);
        }

        return $truncated . $suffix;
    }

    /**
     * Generate breadcrumb for filter URL
     *
     * @param array<string, mixed> $params Filter parameters
     * @param array<string, mixed> $words Dictionary words
     * @return string Breadcrumb text
     */
    public function generateBreadcrumb(array $params, array $words = []): string
    {
        $parts = [];

        foreach ($params as $key => $value) {
            $displayValue = $words[$key] ?? $value;

            if (is_array($displayValue)) {
                $displayValue = $displayValue['nominative'] ?? $displayValue['value'] ?? reset($displayValue);
            }

            $parts[] = $displayValue;
        }

        return implode($this->configService->get('separator', ' / '), $parts);
    }
}
