<?php

declare(strict_types=1);

namespace SeoFilter\Service;

use modX;
use PDO;

/**
 * Field Service for SeoFilter
 *
 * Handles field operations including retrieving field configurations,
 * checking field types (TVs, Tagger, etc.), and field value processing.
 *
 * @package SeoFilter\Service
 * @since 2.0.0
 */
class FieldService
{
    /**
     * Field type constants
     */
    public const TYPE_TV = 'tvs';
    public const TYPE_TV_SUPER_SELECT = 'tvss';
    public const TYPE_TAGGER = 'tagger';
    public const TYPE_DATA = 'data';

    /**
     * @var modX MODX instance
     */
    protected modX $modx;

    /**
     * @var ConfigService Configuration service
     */
    protected ConfigService $configService;

    /**
     * @var array<string, array<string, array<string, mixed>>> Cached fields by key
     */
    protected array $fieldsCache = [];

    /**
     * FieldService constructor
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
     * Get fields organized by key and type
     *
     * Retrieves all sfField records and organizes them by type (TVs, Tagger, Data, etc.)
     * and key (id, alias, etc.).
     *
     * @param string $key Key field to use (id, alias, key, etc.)
     * @param int $resourceId Optional resource ID for condition checking
     * @return array<string, array<string, array<string, mixed>>> Organized fields array
     */
    public function getFieldsByKey(string $key = 'key', int $resourceId = 0): array
    {
        $cacheKey = $key . '_' . $resourceId;

        if (isset($this->fieldsCache[$cacheKey])) {
            return $this->fieldsCache[$cacheKey];
        }

        $fields = [
            self::TYPE_TV => [],
            self::TYPE_TV_SUPER_SELECT => [],
            self::TYPE_TAGGER => [],
            self::TYPE_DATA => []
        ];

        $query = $this->modx->newQuery('sfField');
        $query->select(['sfField.*']);

        if ($query->prepare() && $query->stmt->execute()) {
            while ($row = $query->stmt->fetch(PDO::FETCH_ASSOC)) {
                // Check resource-specific conditions if provided
                if ($resourceId && !empty($row['xpdo_where'])) {
                    if (!$this->checkResourceCondition($resourceId, $row['xpdo_where'])) {
                        continue;
                    }
                }

                $type = $this->determineFieldType($row);
                $keyValue = $row[$key] ?? null;

                if ($keyValue !== null) {
                    $fields[$type][$keyValue] = $row;
                }
            }
        }

        $this->fieldsCache[$cacheKey] = $fields;

        return $fields;
    }

    /**
     * Determine field type from row data
     *
     * @param array<string, mixed> $row Field row data
     * @return string Field type constant
     */
    protected function determineFieldType(array $row): string
    {
        $class = mb_strtolower($row['class'] ?? '');
        $package = mb_strtolower($row['xpdo_package'] ?? '');

        if ($class === 'modtemplatevar') {
            return $package === 'tvsuperselect'
                ? self::TYPE_TV_SUPER_SELECT
                : self::TYPE_TV;
        }

        if ($class === 'tagger') {
            return self::TYPE_TAGGER;
        }

        return self::TYPE_DATA;
    }

    /**
     * Get flat array of all fields by key
     *
     * @param string $key Key field to use
     * @param int $resourceId Optional resource ID
     * @return array<string, array<string, mixed>> Flat fields array
     */
    public function getFlatFieldsByKey(string $key = 'key', int $resourceId = 0): array
    {
        $organized = $this->getFieldsByKey($key, $resourceId);
        $flat = [];

        foreach ($organized as $type => $typeFields) {
            foreach ($typeFields as $keyValue => $field) {
                $flat[$keyValue] = $field;
            }
        }

        return $flat;
    }

    /**
     * Get field aliases for a page
     *
     * @param int $pageId Page ID
     * @param bool $activeOnly Only return active fields
     * @return array<int, string> Array of field aliases
     */
    public function getFieldAliasesByPage(int $pageId, bool $activeOnly = true): array
    {
        $query = $this->modx->newQuery('sfFieldIds');
        $query->innerJoin('sfField', 'Field', 'sfFieldIds.field_id = Field.id');
        $query->innerJoin('sfRule', 'Rule', 'sfFieldIds.multi_id = Rule.id');

        $where = [];
        if ($this->configService->isProMode()) {
            $where[] = "FIND_IN_SET({$pageId}, REPLACE(IFNULL(NULLIF(Rule.pages,''), Rule.page),' ',''))";
        } else {
            $where['Rule.page'] = $pageId;
        }

        if ($activeOnly) {
            $where['Rule.active'] = 1;
            $where['Field.active'] = 1;
        }

        $query->where($where);
        $query->select(['Field.alias']);
        $query->groupby('Field.alias');

        $aliases = [];
        if ($query->prepare() && $query->stmt->execute()) {
            while ($row = $query->stmt->fetch(PDO::FETCH_ASSOC)) {
                $aliases[] = $row['alias'];
            }
        }

        return $aliases;
    }

    /**
     * Check if resource matches a condition
     *
     * @param int $resourceId Resource ID
     * @param string $condition xPDO WHERE condition
     * @return bool Whether resource matches
     */
    protected function checkResourceCondition(int $resourceId, string $condition): bool
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
                "SeoFilter: Invalid resource condition '{$condition}': {$e->getMessage()}"
            );
            return false;
        }
    }

    /**
     * Get field by ID
     *
     * @param int $fieldId Field ID
     * @return \sfField|null Field object or null
     */
    public function getFieldById(int $fieldId): ?object
    {
        if ($fieldId <= 0) {
            return null;
        }

        return $this->modx->getObject('sfField', $fieldId);
    }

    /**
     * Get field by alias
     *
     * @param string $alias Field alias
     * @return \sfField|null Field object or null
     */
    public function getFieldByAlias(string $alias): ?object
    {
        if (empty($alias)) {
            return null;
        }

        return $this->modx->getObject('sfField', ['alias' => $alias]);
    }

    /**
     * Check if field is a slider type
     *
     * @param int $fieldId Field ID
     * @return bool
     */
    public function isSliderField(int $fieldId): bool
    {
        $field = $this->getFieldById($fieldId);
        return $field && (bool) $field->get('slider');
    }

    /**
     * Get slider fields for a page
     *
     * @param int $pageId Page ID
     * @return array<int, array<string, mixed>> Slider fields
     */
    public function getSliderFieldsByPage(int $pageId): array
    {
        $query = $this->modx->newQuery('sfFieldIds');
        $query->rightJoin('sfRule', 'Rule', 'Rule.id = sfFieldIds.multi_id');
        $query->rightJoin('sfField', 'Field', 'Field.id = sfFieldIds.field_id');
        $query->where(['Field.slider' => 1]);

        if ($this->configService->isProMode()) {
            $query->where(
                "FIND_IN_SET({$pageId}, REPLACE(IFNULL(NULLIF(Rule.pages,''), Rule.page),' ',''))"
            );
        } else {
            $query->where(['Rule.page' => $pageId]);
        }

        $query->select(['Field.*']);

        $fields = [];
        if ($query->prepare() && $query->stmt->execute()) {
            while ($row = $query->stmt->fetch(PDO::FETCH_ASSOC)) {
                $fields[] = $row;
            }
        }

        return $fields;
    }

    /**
     * Clear fields cache
     *
     * @return self
     */
    public function clearCache(): self
    {
        $this->fieldsCache = [];
        return $this;
    }

    /**
     * Detect field changes between before/after values
     *
     * Compares field values to detect what changed during resource save.
     *
     * @param array<string, mixed> $after After values
     * @param array<string, mixed> $before Before values
     * @param string $type Field type
     * @return array{added: array<int, mixed>, removed: array<int, mixed>, unchanged: array<int, mixed>} Change data
     */
    public function detectFieldChanges(array $after, array $before, string $type = ''): array
    {
        $changes = [
            'added' => [],
            'removed' => [],
            'unchanged' => []
        ];

        // Normalize arrays
        $afterFlat = $this->flattenFieldValues($after);
        $beforeFlat = $this->flattenFieldValues($before);

        // Find added values
        $changes['added'] = array_diff($afterFlat, $beforeFlat);

        // Find removed values
        $changes['removed'] = array_diff($beforeFlat, $afterFlat);

        // Find unchanged values
        $changes['unchanged'] = array_intersect($afterFlat, $beforeFlat);

        return $changes;
    }

    /**
     * Flatten field values into simple array
     *
     * @param array<string, mixed> $values Values to flatten
     * @return array<int, string> Flattened values
     */
    protected function flattenFieldValues(array $values): array
    {
        $flat = [];

        foreach ($values as $value) {
            if (is_array($value)) {
                foreach ($value as $v) {
                    $parts = $this->explodeValue((string) $v);
                    $flat = array_merge($flat, $parts);
                }
            } else {
                $parts = $this->explodeValue((string) $value);
                $flat = array_merge($flat, $parts);
            }
        }

        return array_unique(array_filter($flat));
    }

    /**
     * Explode value string
     *
     * @param string $input Input value
     * @return array<int, string> Exploded values
     */
    protected function explodeValue(string $input): array
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
}
