<?php

declare(strict_types=1);

namespace SeoFilter\Service;

use modX;
use PDO;

/**
 * Rule Service for SeoFilter
 *
 * Handles rule finding, matching, and field relationship operations.
 * Rules define how filter parameters map to SEO-friendly URLs.
 *
 * @package SeoFilter\Service
 * @since 2.0.0
 */
class RuleService
{
    /**
     * Comparison operators for rule conditions
     */
    public const COMPARE_IN = 1;
    public const COMPARE_NOT_IN = 2;
    public const COMPARE_GREATER = 3;
    public const COMPARE_LESS = 4;
    public const COMPARE_BETWEEN = 5;
    public const COMPARE_CONTAINS = 6;
    public const COMPARE_NOT_CONTAINS = 7;

    /**
     * @var modX MODX instance
     */
    protected modX $modx;

    /**
     * @var ConfigService Configuration service
     */
    protected ConfigService $configService;

    /**
     * RuleService constructor
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
     * Find rule ID by page and parameters
     *
     * Searches for the best matching rule based on filter parameters.
     * Considers field aliases, WHERE conditions, and comparison operators.
     *
     * @param int $pageId Page/Resource ID
     * @param array<string, mixed> $params Filter parameters
     * @param array<string, mixed> $firstParams Base parameters
     * @param array<string, mixed> $otherParams Additional parameters
     * @return int Rule ID (0 if not found)
     */
    public function findRuleId(
        int $pageId = 0,
        array $params = [],
        array $firstParams = [],
        array $otherParams = []
    ): int {
        if (empty($firstParams)) {
            $copyParams = $params;
            $aliases = $this->getFieldAliasesByPage($pageId);

            if (!empty($params)) {
                $otherParams = array_flip(array_diff(array_keys($params), $aliases));
            }

            if (!empty($otherParams)) {
                foreach ($otherParams as $key => $value) {
                    unset($copyParams[$key]);
                }
                $otherParams = array_diff_key($params, $copyParams);
                $firstParams = array_intersect_key($params, $copyParams);
            }
        } else {
            $params = array_merge($firstParams, $otherParams);
        }

        $ruleId = 0;
        $maxFieldCount = 0;
        $paramsKeys = array_keys(array_merge($firstParams, $otherParams));
        $pageAliases = $this->getPageAliases($pageId, array_keys($firstParams));

        // Build structured rule aliases
        $ruleAliases = $this->buildRuleAliasesStructure($pageAliases);

        // Find best matching rule
        foreach ($ruleAliases as $rule => $ruleData) {
            $fields = $ruleData['fields'];
            $isBase = $ruleData['base'];

            // Check if parameters match rule fields
            if (!$this->parametersMatchRuleFields($fields, $paramsKeys, $isBase)) {
                continue;
            }

            // Only consider rules with more fields than current best match
            if (count($fields) <= $maxFieldCount) {
                continue;
            }

            // Check all field conditions
            if ($this->checkFieldConditions($fields, $params, $paramsKeys)) {
                $maxFieldCount = count($fields);
                $ruleId = $rule;
            }
        }

        return $ruleId;
    }

    /**
     * Check if parameters match rule fields
     *
     * @param array<string, array<string, mixed>> $fields Rule fields
     * @param array<int, string> $paramsKeys Parameter keys
     * @param bool $isBase Whether rule is base rule
     * @return bool
     */
    protected function parametersMatchRuleFields(array $fields, array $paramsKeys, bool $isBase): bool
    {
        $fieldKeys = array_keys($fields);

        if ($isBase) {
            // Base rule: all field keys must be in params
            return empty(array_diff($fieldKeys, $paramsKeys));
        }

        // Exact match required
        return count($paramsKeys) === count($fieldKeys)
            && empty(array_diff($fieldKeys, $paramsKeys));
    }

    /**
     * Check all field conditions for a rule
     *
     * @param array<string, array<string, mixed>> $fields Rule fields
     * @param array<string, mixed> $params Request parameters
     * @param array<int, string> $paramsKeys Parameter keys
     * @return bool True if all conditions pass
     */
    protected function checkFieldConditions(array $fields, array $params, array $paramsKeys): bool
    {
        $checkCount = 0;

        foreach ($fields as $alias => $fieldData) {
            if (!in_array($alias, $paramsKeys, true)) {
                continue;
            }

            if ($fieldData['where'] && $fieldData['compare']) {
                if ($this->checkCompareCondition(
                    $params[$alias],
                    $fieldData['value'],
                    (int) $fieldData['compare']
                )) {
                    $checkCount++;
                }
            } else {
                $checkCount++;
            }
        }

        return $checkCount === count($fields);
    }

    /**
     * Check a comparison condition
     *
     * @param mixed $paramValue Value from request parameters
     * @param string $fieldValue Value from field configuration
     * @param int $compareType Comparison operator constant
     * @return bool Whether condition passes
     */
    protected function checkCompareCondition($paramValue, string $fieldValue, int $compareType): bool
    {
        $values = explode($this->configService->getValuesDelimiter(), $fieldValue);

        switch ($compareType) {
            case self::COMPARE_IN:
                return in_array($paramValue, $values, true);

            case self::COMPARE_NOT_IN:
                return !in_array($paramValue, $values, true);

            case self::COMPARE_GREATER:
                return $paramValue > $fieldValue;

            case self::COMPARE_LESS:
                return $paramValue < $fieldValue;

            case self::COMPARE_BETWEEN:
                return isset($values[0], $values[1])
                    && $paramValue > $values[0]
                    && $paramValue < $values[1];

            case self::COMPARE_CONTAINS:
                return mb_strpos((string) $paramValue, $fieldValue) !== false;

            case self::COMPARE_NOT_CONTAINS:
                return mb_strpos((string) $paramValue, $fieldValue) === false;

            default:
                return false;
        }
    }

    /**
     * Build structured rule aliases from page aliases
     *
     * @param array<int, array<string, mixed>> $pageAliases Raw page aliases data
     * @return array<int, array{sort: int, base: bool, fields: array<string, array<string, mixed>>}>
     */
    protected function buildRuleAliasesStructure(array $pageAliases): array
    {
        $ruleAliases = [];

        foreach ($pageAliases as $rule => $ruleAlias) {
            $sort = $ruleAlias['sort'];
            $base = $ruleAlias['base'];
            unset($ruleAlias['sort'], $ruleAlias['base']);

            foreach ($ruleAlias as $aliasData) {
                if (!isset($ruleAliases[$rule])) {
                    $ruleAliases[$rule] = [
                        'sort' => $sort,
                        'base' => $base,
                        'fields' => []
                    ];
                }

                $ruleAliases[$rule]['fields'][$aliasData['alias']] = [
                    'where' => $aliasData['where'],
                    'compare' => $aliasData['compare'],
                    'value' => $aliasData['value']
                ];
            }
        }

        return $ruleAliases;
    }

    /**
     * Get field aliases for a specific page
     *
     * @param int $pageId Page/Resource ID
     * @return array<int, string> Array of field aliases
     */
    protected function getFieldAliasesByPage(int $pageId): array
    {
        // Implementation would query database for field aliases
        // This is a placeholder - actual implementation depends on data structure
        return [];
    }

    /**
     * Get page aliases with rule data
     *
     * @param int $pageId Page ID
     * @param array<int, string> $firstParams First parameters
     * @return array<int, array<string, mixed>> Page aliases data
     */
    protected function getPageAliases(int $pageId, array $firstParams): array
    {
        // Implementation would query database for page aliases
        // This is a placeholder - actual implementation depends on data structure
        return [];
    }

    /**
     * Count fields for a specific rule
     *
     * @param int $ruleId Rule ID
     * @return int Number of fields
     */
    public function countRuleFields(int $ruleId): int
    {
        if ($ruleId <= 0) {
            return 0;
        }

        $query = $this->modx->newQuery('sfFieldIds');
        $query->where(['multi_id' => $ruleId]);

        return $this->modx->getCount('sfFieldIds', $query);
    }

    /**
     * Check if a resource matches a condition
     *
     * @param int $resourceId Resource ID
     * @param string $condition xPDO WHERE condition
     * @return bool Whether resource matches condition
     */
    public function checkResourceCondition(int $resourceId, string $condition): bool
    {
        if (empty($condition) || $resourceId <= 0) {
            return true;
        }

        try {
            $query = $this->modx->newQuery('modResource');
            $query->where(['id' => $resourceId]);
            $query->where($condition);

            return $this->modx->getCount('modResource', $query) > 0;
        } catch (\Exception $e) {
            $this->modx->log(
                modX::LOG_LEVEL_ERROR,
                "SeoFilter: Invalid resource condition: {$condition}. Error: {$e->getMessage()}"
            );
            return false;
        }
    }

    /**
     * Update total count for a URL
     *
     * @param int $urlId URL ID
     * @param int $total New total count
     * @return bool Success status
     */
    public function updateUrlTotal(int $urlId, int $total): bool
    {
        if ($urlId <= 0) {
            return false;
        }

        $url = $this->modx->getObject('sfUrls', $urlId);
        if (!$url) {
            return false;
        }

        $url->set('total', $total);
        $url->set('editedon', time());

        return $url->save();
    }
}
