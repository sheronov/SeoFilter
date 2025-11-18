# SeoFilter 2.0 API Reference

Complete API documentation for all public classes and methods.

## Table of Contents

1. [SeoFilter Main Class](#seofilter-main-class)
2. [ConfigService](#configservice)
3. [FieldService](#fieldservice)
4. [MetaService](#metaservice)
5. [RuleService](#ruleservice)
6. [UrlProcessor](#urlprocessor)
7. [Helper Functions](#helper-functions)

---

## SeoFilter Main Class

```php
namespace SeoFilter;

class SeoFilter
```

Main component class that provides a facade to all services.

### Constants

```php
const VERSION = '2.0.0';
```

### Constructor

```php
public function __construct(modX $modx, array $config = [])
```

**Parameters:**
- `$modx` (modX) - MODX instance
- `$config` (array) - Optional configuration array

**Example:**
```php
$seofilter = new SeoFilter\SeoFilter($modx, [
    'ajax' => true,
    'page' => 5,
]);
```

---

### initialize()

```php
public function initialize(string $ctx = 'web', array $scriptProperties = []): bool
```

Initialize component for specific context.

**Parameters:**
- `$ctx` (string) - Context name (default: 'web')
- `$scriptProperties` (array) - Additional script properties

**Returns:** `bool` - Success status

**Example:**
```php
$seofilter->initialize('web', [
    'ajax' => true,
    'params' => $_GET,
]);
```

---

### processUrl()

```php
public function processUrl(string $url): bool
```

Process SEO URL on 404 error (OnPageNotFound handler).

**Parameters:**
- `$url` (string) - Requested URL

**Returns:** `bool` - True if URL was processed successfully

**Example:**
```php
// Called by plugin on OnPageNotFound
$seofilter->processUrl($_SERVER['REQUEST_URI']);
```

---

### generateUrl()

```php
public function generateUrl(int $pageId, array $params, ?int $ruleId = null): ?string
```

Generate SEO URL for filter parameters.

**Parameters:**
- `$pageId` (int) - Page/Resource ID
- `$params` (array) - Filter parameters
- `$ruleId` (int|null) - Optional specific rule ID

**Returns:** `string|null` - Generated URL or null on failure

**Example:**
```php
$url = $seofilter->generateUrl(5, [
    'color' => 'red',
    'size' => 'large'
]);
// Returns: "krasniy-tsvet/bolshoy-razmer"
```

---

### findRuleId()

```php
public function findRuleId(int $pageId, array $params): int
```

Find matching rule for page and parameters.

**Parameters:**
- `$pageId` (int) - Page/Resource ID
- `$params` (array) - Filter parameters

**Returns:** `int` - Rule ID (0 if not found)

**Example:**
```php
$ruleId = $seofilter->findRuleId(5, [
    'color' => 'red',
    'size' => 'large'
]);
```

---

### Service Getters

```php
public function getConfig(): ConfigService
public function getFields(): FieldService
public function getMeta(): MetaService
public function getRules(): RuleService
public function getUrls(): UrlProcessor
public function getModx(): modX
```

Get service instances.

**Example:**
```php
$config = $seofilter->getConfig();
$fields = $seofilter->getFields();
```

---

## ConfigService

```php
namespace SeoFilter\Service;

class ConfigService
```

Handles all configuration and settings.

### get()

```php
public function get(string $key, $default = null)
```

Get configuration value.

**Parameters:**
- `$key` (string) - Configuration key
- `$default` (mixed) - Default value if not found

**Returns:** `mixed` - Configuration value

**Example:**
```php
$ajax = $config->get('ajax', false);
$pageId = $config->get('page', 0);
```

---

### set()

```php
public function set(string $key, $value): self
```

Set configuration value.

**Parameters:**
- `$key` (string) - Configuration key
- `$value` (mixed) - Configuration value

**Returns:** `self` - For method chaining

**Example:**
```php
$config->set('ajax', true)->set('page', 5);
```

---

### getAll()

```php
public function getAll(): array
```

Get all configuration.

**Returns:** `array` - Full configuration array

---

### merge()

```php
public function merge(array $config): self
```

Merge configuration with new values.

**Parameters:**
- `$config` (array) - Configuration to merge

**Returns:** `self` - For method chaining

---

### makePlaceholders()

```php
public function makePlaceholders(?array $config = null): array
```

Create placeholders from configuration.

**Returns:** `array` - ['pl' => placeholders, 'vl' => values]

---

### Convenience Methods

```php
public function isProMode(): bool
public function isAjaxMode(): bool
public function getValuesDelimiter(): string
public function getUrlSeparator(): string
public function toJson(?array $keys = null, int $flags = JSON_THROW_ON_ERROR): string
```

---

## FieldService

```php
namespace SeoFilter\Service;

class FieldService
```

Handles field operations and queries.

### Constants

```php
const TYPE_TV = 'tvs';
const TYPE_TV_SUPER_SELECT = 'tvss';
const TYPE_TAGGER = 'tagger';
const TYPE_DATA = 'data';
```

---

### getFieldsByKey()

```php
public function getFieldsByKey(string $key = 'key', int $resourceId = 0): array
```

Get fields organized by key and type.

**Parameters:**
- `$key` (string) - Key field (id, alias, key, etc.)
- `$resourceId` (int) - Optional resource ID for condition checking

**Returns:** `array` - Organized fields array

**Example:**
```php
$fields = $service->getFieldsByKey('alias');
// Returns: [
//     'tvs' => [...],
//     'tagger' => [...],
//     'data' => [...],
// ]
```

---

### getFlatFieldsByKey()

```php
public function getFlatFieldsByKey(string $key = 'key', int $resourceId = 0): array
```

Get flat array of all fields.

**Returns:** `array` - Flat fields array

---

### getFieldById()

```php
public function getFieldById(int $fieldId): ?object
```

Get field object by ID.

**Parameters:**
- `$fieldId` (int) - Field ID

**Returns:** `sfField|null` - Field object or null

---

### getFieldByAlias()

```php
public function getFieldByAlias(string $alias): ?object
```

Get field object by alias.

**Parameters:**
- `$alias` (string) - Field alias

**Returns:** `sfField|null` - Field object or null

---

### isSliderField()

```php
public function isSliderField(int $fieldId): bool
```

Check if field is a slider type.

---

### getSliderFieldsByPage()

```php
public function getSliderFieldsByPage(int $pageId): array
```

Get all slider fields for a page.

---

### detectFieldChanges()

```php
public function detectFieldChanges(array $after, array $before, string $type = ''): array
```

Detect changes between before/after field values.

**Returns:** `array` - ['added' => [...], 'removed' => [...], 'unchanged' => [...]]

---

## MetaService

```php
namespace SeoFilter\Service;

class MetaService
```

Handles SEO meta data generation and processing.

### Constants

```php
const FIELD_TITLE = 'title';
const FIELD_H1 = 'h1';
const FIELD_H2 = 'h2';
const FIELD_DESCRIPTION = 'description';
const FIELD_INTROTEXT = 'introtext';
const FIELD_KEYWORDS = 'keywords';
const FIELD_TEXT = 'text';
const FIELD_CONTENT = 'content';
const FIELD_LINK = 'link';
```

---

### buildMetaFromRule()

```php
public function buildMetaFromRule(object $rule, array $params, array $words = []): array
```

Build meta data from rule and parameters.

**Parameters:**
- `$rule` (object) - sfRule object
- `$params` (array) - Filter parameters
- `$words` (array) - Dictionary words

**Returns:** `array` - Generated meta data

**Example:**
```php
$rule = $modx->getObject('sfRule', 1);
$meta = $service->buildMetaFromRule($rule, [
    'color' => 'red'
], [
    'color' => ['nominative' => 'krasniy', 'accusative' => 'krasniye']
]);
```

---

### processPlaceholders()

```php
public function processPlaceholders(string $template, array $params, array $words = []): string
```

Process placeholders in template string.

**Example:**
```php
$result = $service->processPlaceholders(
    'Products: [[+color.nominative]]',
    ['color' => 'red'],
    ['color' => ['nominative' => 'krasniy']]
);
// Returns: "Products: krasniy"
```

---

### applyMetaToResource()

```php
public function applyMetaToResource(modResource $resource, array $meta): void
```

Apply meta data to resource.

---

### validateMetaField()

```php
public function validateMetaField(string $field, string $value): bool
```

Validate meta field value.

---

### truncateMetaField()

```php
public function truncateMetaField(string $field, string $value, string $suffix = '...'): string
```

Truncate meta field to optimal length.

---

### generateBreadcrumb()

```php
public function generateBreadcrumb(array $params, array $words = []): string
```

Generate breadcrumb text for filter URL.

---

## RuleService

```php
namespace SeoFilter\Service;

class RuleService
```

Handles rule finding and matching.

### Constants

```php
const COMPARE_IN = 1;
const COMPARE_NOT_IN = 2;
const COMPARE_GREATER = 3;
const COMPARE_LESS = 4;
const COMPARE_BETWEEN = 5;
const COMPARE_CONTAINS = 6;
const COMPARE_NOT_CONTAINS = 7;
```

---

### findRuleId()

```php
public function findRuleId(
    int $pageId = 0,
    array $params = [],
    array $firstParams = [],
    array $otherParams = []
): int
```

Find rule ID by page and parameters.

**Parameters:**
- `$pageId` (int) - Page/Resource ID
- `$params` (array) - Filter parameters
- `$firstParams` (array) - Base parameters
- `$otherParams` (array) - Additional parameters

**Returns:** `int` - Rule ID (0 if not found)

---

### countRuleFields()

```php
public function countRuleFields(int $ruleId): int
```

Count fields for a specific rule.

---

### checkResourceCondition()

```php
public function checkResourceCondition(int $resourceId, string $condition): bool
```

Check if resource matches a condition.

---

### updateUrlTotal()

```php
public function updateUrlTotal(int $urlId, int $total): bool
```

Update total count for a URL.

---

## UrlProcessor

```php
namespace SeoFilter\Service;

class UrlProcessor
```

Handles URL processing and generation.

### processUrl()

```php
public function processUrl(string $url): bool
```

Process SEO URL on 404 error.

---

### generateUrl()

```php
public function generateUrl(int $pageId, array $params, ?int $ruleId = null): ?string
```

Generate SEO URL for filter parameters.

---

### normalizeUrl()

```php
public function normalizeUrl(string $url): string
```

Normalize URL for processing.

**Example:**
```php
$normalized = $service->normalizeUrl('https://example.com/path?query#hash');
// Returns: "path"
```

---

### parseUrl()

```php
public function parseUrl(string $url): ?array
```

Parse SEO URL into components.

**Returns:** `array|null` - ['pageId' => int, 'params' => array, 'segments' => array]

---

### clearSuffixes()

```php
public function clearSuffixes(string $url): string
```

Clear container suffixes from URL.

---

### explodeValue()

```php
public function explodeValue(string $input): array
```

Explode value string into array.

**Example:**
```php
$values = $service->explodeValue('red||blue||green');
// Returns: ['red', 'blue', 'green']
```

---

### cleanUrl()

```php
public function cleanUrl(string $url): string
```

Clean URL from unwanted characters.

---

## Helper Functions

### getSeoFilter()

```php
function getSeoFilter(modX $modx, array $config = []): SeoFilter\SeoFilter
```

Get SeoFilter instance (singleton pattern).

**Parameters:**
- `$modx` (modX) - MODX instance
- `$config` (array) - Optional configuration

**Returns:** `SeoFilter` - SeoFilter instance

**Example:**
```php
require_once MODX_CORE_PATH . 'components/seofilter/bootstrap.php';
$seofilter = getSeoFilter($modx, ['ajax' => true]);
```

---

## Usage Examples

### Example 1: Complete Workflow

```php
<?php
require_once MODX_CORE_PATH . 'components/seofilter/bootstrap.php';

// Get instance
$seofilter = getSeoFilter($modx);

// Initialize
$seofilter->initialize('web', [
    'ajax' => true,
    'page' => 5,
]);

// Find rule
$params = ['color' => 'red', 'size' => 'large'];
$ruleId = $seofilter->findRuleId(5, $params);

if ($ruleId) {
    // Generate URL
    $url = $seofilter->generateUrl(5, $params, $ruleId);

    // Get rule and build meta
    $rule = $modx->getObject('sfRule', $ruleId);
    $meta = $seofilter->getMeta()->buildMetaFromRule($rule, $params);

    // Apply to resource
    $seofilter->getMeta()->applyMetaToResource($modx->resource, $meta);
}
?>
```

### Example 2: Service Usage

```php
<?php
$seofilter = getSeoFilter($modx);

// Configuration
$config = $seofilter->getConfig();
$config->set('ajax', true);
$isProMode = $config->isProMode();

// Fields
$fields = $seofilter->getFields();
$colorField = $fields->getFieldByAlias('color');
$allFields = $fields->getFieldsByKey('alias');

// Meta
$meta = $seofilter->getMeta();
$processed = $meta->processPlaceholders(
    '[[+color.nominative]] products',
    ['color' => 'red'],
    ['color' => ['nominative' => 'krasniy']]
);

// URLs
$urls = $seofilter->getUrls();
$normalized = $urls->normalizeUrl($_SERVER['REQUEST_URI']);
?>
```

---

## Type Reference

### Configuration Array

```php
[
    'namespace' => 'seofilter',
    'ajax' => false,
    'page' => 0,
    'params' => [],
    'separator' => '-',
    'values_delimeter' => '||',
    'proMode' => false,
    // ... see ConfigService for full list
]
```

### Meta Data Array

```php
[
    'title' => 'Page Title',
    'h1' => 'Main Heading',
    'description' => 'Meta description',
    'keywords' => 'keyword1, keyword2',
    'content' => 'Page content',
    // ... see MetaService constants for full list
]
```

### Field Array

```php
[
    'id' => 1,
    'name' => 'Color',
    'alias' => 'color',
    'class' => 'modTemplateVar',
    'key' => 'color',
    'slider' => 0,
    'exact' => 1,
    'active' => 1,
]
```

---

## Error Handling

All services log errors to MODX error log:

```php
try {
    $url = $seofilter->generateUrl($pageId, $params);
} catch (Exception $e) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'SeoFilter: ' . $e->getMessage());
}
```

---

## Performance Tips

1. **Cache Field Queries**:
   ```php
   $fields = $seofilter->getFields();
   $allFields = $fields->getFieldsByKey('alias'); // Cached after first call
   ```

2. **Reuse SeoFilter Instance**:
   ```php
   $seofilter = getSeoFilter($modx); // Returns same instance
   ```

3. **Use Specific Methods**:
   ```php
   // Good
   $field = $fields->getFieldByAlias('color');

   // Avoid
   $allFields = $fields->getFlatFieldsByKey();
   $field = $allFields['color'];
   ```

---

For more information, see [DOCUMENTATION.md](DOCUMENTATION.md)
