# SeoFilter 2.0 - Complete Documentation

## Table of Contents

1. [Overview](#overview)
2. [What's New in 2.0](#whats-new-in-20)
3. [System Requirements](#system-requirements)
4. [Installation](#installation)
5. [Configuration](#configuration)
6. [Core Concepts](#core-concepts)
7. [Service Architecture](#service-architecture)
8. [Usage Examples](#usage-examples)
9. [API Reference](#api-reference)
10. [Migration from 1.x](#migration-from-1x)
11. [Troubleshooting](#troubleshooting)

---

## Overview

**SeoFilter** is a powerful SEO-focused URL filtering system for MODX Revolution 3 that transforms standard filter query parameters into beautiful, SEO-friendly URLs. Originally designed for miniShop2 product catalogs, it can work with any filterable content.

### The Problem It Solves

Standard filtering URLs like this:
```
/products?color=red&size=large&brand=nike
```

Become SEO-friendly URLs like this:
```
/products/krasnyy-tsvet/bolshoy-razmer/nike
```

Each filter combination gets:
- ✅ Unique, human-readable URL
- ✅ Custom SEO meta data (title, description, H1, etc.)
- ✅ Proper indexing by search engines
- ✅ Automatic result counting
- ✅ Breadcrumb support
- ✅ Sitemap generation

---

## What's New in 2.0

### Major Improvements

#### 🏗️ Modern Architecture
- **PSR-4 Namespacing**: All classes now use `SeoFilter\` namespace
- **Service-Oriented Design**: Monolithic class split into focused services
- **Dependency Injection**: Proper DI patterns throughout
- **Composer Support**: Full Composer integration with autoloading

#### 🔒 Type Safety
- **PHP 7.4+ Type Hints**: Complete type coverage (parameters, returns, properties)
- **Strict Types**: `declare(strict_types=1)` throughout
- **Better IDE Support**: Full autocomplete and type checking

#### 🎯 MODX3 Optimized
- **Removed pdoTools Dependency**: No longer requires pdoFetch/pdoTools
- **Modern MODX Patterns**: Uses current best practices
- **Improved Error Handling**: Comprehensive error management
- **Better Performance**: Optimized queries and caching

#### 📚 Documentation
- **Comprehensive PHPDoc**: Every method documented
- **Usage Examples**: Real-world code samples
- **Migration Guide**: Step-by-step upgrade path
- **API Reference**: Complete API documentation

#### 🧪 Code Quality
- **Single Responsibility**: Each class has one clear purpose
- **SOLID Principles**: Proper OOP design
- **Reduced Complexity**: 500-line methods broken into manageable pieces
- **Security Improvements**: Input validation and sanitization

---

## System Requirements

### Minimum Requirements
- **PHP**: 7.4.0 or higher
- **MODX Revolution**: 3.0.0 or higher
- **MySQL**: 5.7.0 or higher (or MariaDB 10.2+)
- **PHP Extensions**:
  - PDO
  - pdo_mysql
  - mbstring
  - json

### Recommended
- **PHP**: 8.0+ for better performance
- **Composer**: For dependency management
- **Memory**: 128MB minimum, 256MB recommended

### Optional Dependencies
- **miniShop2**: 2.4+ (for e-commerce filtering)
- **Tagger**: For tag-based filtering
- **TvSuperSelect**: For advanced TV filtering

---

## Installation

### Via Package Manager (Recommended)

```bash
# Download and install via MODX Package Manager
# Search for "SeoFilter" and install version 2.0+
```

### Via Composer

```bash
composer require justtop1/seofilter
```

### Manual Installation

1. Download the package
2. Extract to `/core/components/seofilter/`
3. Extract assets to `/assets/components/seofilter/`
4. Run the package installer
5. Clear MODX cache

### Post-Installation

1. **Create System Settings** (optional):
   ```
   seofilter.core_path = {core_path}components/seofilter/
   seofilter.assets_url = {assets_url}components/seofilter/
   ```

2. **Run Database Setup**:
   - Tables are created automatically during installation
   - Verify tables exist: `modx_sf_field`, `modx_sf_rule`, `modx_sf_urls`, etc.

3. **Enable the Plugin**:
   - Go to Elements → Plugins
   - Find "SeoFilter" plugin
   - Ensure it's enabled and attached to these events:
     - `OnPageNotFound`
     - `OnLoadWebDocument`
     - `OnDocFormSave`
     - `OnBeforeDocFormSave`

---

## Configuration

### System Settings

Configure SeoFilter via System Settings (Namespace: `seofilter`):

| Setting | Type | Default | Description |
|---------|------|---------|-------------|
| `seofilter_frontend_js` | Text | `{assets_url}js/web/default.js` | Frontend JavaScript file |
| `seofilter_pro_mode` | Bool | `0` | Enable multi-page rules |
| `seofilter_url_suffix` | Text | Empty | URL suffix (e.g., `.html`) |
| `seofilter_between_urls` | Text | `/` | Separator between page and filter URL |
| `seofilter_main_alias` | Bool | `0` | Use main page alias in URLs |
| `seofilter_values_delimeter` | Text | `\|\|` | Delimiter for multiple values |
| `seofilter_container_suffix` | Text | `/` | Container suffix to remove |

### Component Initialization

#### In Template
```php
<?php
// Load bootstrap
require_once MODX_CORE_PATH . 'components/seofilter/bootstrap.php';

// Get SeoFilter instance
$seofilter = getSeoFilter($modx, [
    'ajax' => true,
    'page' => $modx->resource->id,
    'separator' => '-',
]);

// Initialize for current context
$seofilter->initialize('web', [
    'params' => $_GET,
]);
?>
```

#### In Snippet
```php
// Bootstrap is auto-loaded in snippets
$seofilter = getSeoFilter($modx, $scriptProperties);
return $seofilter->generateUrl($pageId, $params);
```

---

## Core Concepts

### 1. Fields (sfField)

Fields define what can be filtered. Examples:
- Product color (TV)
- Product size (TV)
- Product brand (Resource field)
- Tags (Tagger)

**Field Configuration**:
```php
[
    'name' => 'Color',           // Display name
    'alias' => 'color',          // URL alias
    'class' => 'modTemplateVar', // Class type
    'key' => 'color',            // Field key
    'slider' => 0,               // Is range slider?
    'exact' => 1,                // Exact match?
]
```

### 2. Rules (sfRule)

Rules define how fields combine to create URLs.

**Rule Configuration**:
```php
[
    'page' => 5,                              // Target page ID
    'base' => 0,                              // Allow extra parameters?
    'title' => 'Products: [[+color.nominative]] [[+size.nominative]]',
    'h1' => '[[+color.nominative]] products in [[+size.nominative]]',
    'description' => 'Buy [[+color.accusative]] products in [[+size.prepositional]]',
]
```

### 3. URLs (sfUrls)

Generated SEO URLs with their meta data.

**URL Record**:
```php
[
    'multi_id' => 1,                    // Rule ID
    'page_id' => 5,                     // Page ID
    'old_url' => 'color-red-size-large', // Generated URL
    'new_url' => 'krasnye-bolshie',      // Custom URL (optional)
    'title' => 'Red Large Products',     // SEO title
    'h1' => 'Red Products in Large Size', // H1
    'description' => '...',              // Meta description
    'total' => 42,                       // Result count
]
```

### 4. Dictionary (sfDictionary)

Translates filter values into SEO-friendly words.

**Dictionary Entry**:
```php
[
    'field_id' => 1,
    'input' => 'red',
    'value' => 'krasniy',         // Base form
    'nominative' => 'krasniy',    // Именительный
    'genitive' => 'krasnogo',     // Родительный
    'dative' => 'krasnomu',       // Дательный
    'accusative' => 'krasniy',    // Винительный
    'instrumental' => 'krasnym',  // Творительный
    'prepositional' => 'krasnom', // Предложный
]
```

---

## Service Architecture

SeoFilter 2.0 uses a service-oriented architecture:

### Service Classes

#### ConfigService
Manages all configuration and system settings.

```php
$config = $seofilter->getConfig();
$config->get('ajax');           // Get setting
$config->set('page', 5);        // Set setting
$config->isProMode();           // Check pro mode
$config->getValuesDelimiter();  // Get delimiter
```

#### FieldService
Handles field operations and queries.

```php
$fields = $seofilter->getFields();
$fields->getFieldsByKey('alias');      // Get all fields
$fields->getFieldByAlias('color');     // Get specific field
$fields->isSliderField($fieldId);      // Check if slider
$fields->getSliderFieldsByPage($pageId); // Get sliders
```

#### RuleService
Manages rule finding and matching.

```php
$rules = $seofilter->getRules();
$ruleId = $rules->findRuleId($pageId, $params);  // Find rule
$count = $rules->countRuleFields($ruleId);       // Count fields
$rules->updateUrlTotal($urlId, $total);          // Update count
```

#### MetaService
Generates and processes SEO meta data.

```php
$meta = $seofilter->getMeta();
$meta->buildMetaFromRule($rule, $params, $words); // Build meta
$meta->processPlaceholders($template, $params);   // Process
$meta->applyMetaToResource($resource, $meta);     // Apply to resource
$meta->truncateMetaField('title', $value);        // Truncate
```

#### UrlProcessor
Handles URL processing and generation.

```php
$urls = $seofilter->getUrls();
$urls->processUrl($url);              // Process 404 URL
$urls->generateUrl($pageId, $params); // Generate URL
$urls->normalizeUrl($url);            // Normalize URL
$urls->explodeValue($input);          // Parse values
```

---

## Usage Examples

### Example 1: Basic Product Filtering

#### Step 1: Create Fields
```sql
INSERT INTO modx_sf_field (name, alias, class, key) VALUES
('Color', 'color', 'modTemplateVar', 'color'),
('Size', 'size', 'modTemplateVar', 'size');
```

#### Step 2: Create Rule
```sql
INSERT INTO modx_sf_rule (page, title, h1, description) VALUES
(5,
 'Products: [[+color.nominative]] [[+size.nominative]]',
 '[[+color.nominative]] products',
 'Buy [[+color.accusative]] products in [[+size.prepositional]] size');
```

#### Step 3: Link Fields to Rule
```sql
INSERT INTO modx_sf_field_ids (multi_id, field_id, rank) VALUES
(1, 1, 0),  -- Color
(1, 2, 1);  -- Size
```

#### Step 4: Add Dictionary Words
```sql
INSERT INTO modx_sf_dictionary (field_id, input, value, nominative, accusative) VALUES
(1, 'red', 'krasniy', 'krasniy', 'krasniye'),
(2, 'large', 'bolshoy', 'bolshoy', 'bolshie');
```

#### Step 5: Generate URLs

URLs are generated automatically when:
- Products are created/updated
- Dictionary entries are added
- Manual recount is triggered

Result:
```
/products/krasniy-bolshoy
Title: Products: krasniy bolshoy
H1: krasniy products
Description: Buy krasniye products in bolshie size
```

### Example 2: Using Snippets

#### sfLink - Generate Filter Link
```html
[[!sfLink?
    &pages=`5`
    &color=`red`
    &size=`large`
    &tpl=`@INLINE <a href="[[+url]]" class="filter-link">[[+name]]</a>`
]]
```

#### sfMenu - Generate Filter Menu
```html
[[!sfMenu?
    &rules=`1,2,3`
    &tpl=`filterMenuTpl`
    &showInactive=`0`
]]
```

#### sfWord - Get Dictionary Word
```html
[[!sfWord?
    &alias=`color`
    &input=`red`
    &case=`nominative`
]]
```

### Example 3: Programmatic Usage

```php
<?php
// Get SeoFilter instance
$seofilter = getSeoFilter($modx);

// Find rule for current parameters
$params = ['color' => 'red', 'size' => 'large'];
$ruleId = $seofilter->findRuleId($pageId, $params);

// Generate SEO URL
$url = $seofilter->generateUrl($pageId, $params, $ruleId);

// Get meta data
$rule = $modx->getObject('sfRule', $ruleId);
$meta = $seofilter->getMeta()->buildMetaFromRule($rule, $params);

// Apply to resource
$seofilter->getMeta()->applyMetaToResource($modx->resource, $meta);
```

### Example 4: AJAX Filtering

```html
<!-- Template -->
<div id="products-container">
    [[!msProducts]]
</div>

<div id="filters">
    <select name="color" data-sf-filter>
        <option value="">All Colors</option>
        <option value="red">Red</option>
        <option value="blue">Blue</option>
    </select>

    <select name="size" data-sf-filter>
        <option value="">All Sizes</option>
        <option value="small">Small</option>
        <option value="large">Large</option>
    </select>
</div>

<script>
// SeoFilter automatically handles AJAX filtering if initialized
</script>
```

---

## API Reference

### SeoFilter Main Class

```php
class SeoFilter\SeoFilter
```

#### Methods

##### __construct(modX $modx, array $config = [])
Initialize SeoFilter with configuration.

**Parameters**:
- `$modx` - MODX instance
- `$config` - Configuration array

**Returns**: `SeoFilter` instance

---

##### initialize(string $ctx = 'web', array $scriptProperties = []): bool
Initialize for specific context.

**Parameters**:
- `$ctx` - Context name (default: 'web')
- `$scriptProperties` - Additional properties

**Returns**: `bool` - Success status

---

##### processUrl(string $url): bool
Process SEO URL on 404 error.

**Parameters**:
- `$url` - Requested URL

**Returns**: `bool` - True if URL was processed

---

##### generateUrl(int $pageId, array $params, ?int $ruleId = null): ?string
Generate SEO URL for parameters.

**Parameters**:
- `$pageId` - Page/Resource ID
- `$params` - Filter parameters
- `$ruleId` - Optional rule ID

**Returns**: `string|null` - Generated URL or null

---

##### findRuleId(int $pageId, array $params): int
Find matching rule for page and parameters.

**Parameters**:
- `$pageId` - Page/Resource ID
- `$params` - Filter parameters

**Returns**: `int` - Rule ID (0 if not found)

---

### Service Getters

```php
$seofilter->getConfig(): ConfigService
$seofilter->getFields(): FieldService
$seofilter->getMeta(): MetaService
$seofilter->getRules(): RuleService
$seofilter->getUrls(): UrlProcessor
```

---

## Migration from 1.x

### Breaking Changes

1. **Namespace Change**: Classes now use `SeoFilter\` namespace
2. **Service Architecture**: Direct property access replaced with service methods
3. **Type Hints**: All methods now have strict types
4. **Removed pdoTools**: No longer depends on pdoFetch/pdoTools

### Migration Steps

#### Step 1: Update Installation
```bash
# Backup your database first!
mysqldump -u user -p modx_database > seofilter_backup.sql

# Install SeoFilter 2.0 via Package Manager
# Or via Composer: composer require justtop1/seofilter
```

#### Step 2: Update Custom Code

**Old Code (1.x)**:
```php
$seofilter = $modx->getService('seofilter', 'SeoFilter',
    MODX_CORE_PATH . 'components/seofilter/model/seofilter/');

$config = $seofilter->config['ajax'];
$fields = $seofilter->getFieldsKey();
```

**New Code (2.0)**:
```php
require_once MODX_CORE_PATH . 'components/seofilter/bootstrap.php';
$seofilter = getSeoFilter($modx);

$config = $seofilter->getConfig()->get('ajax');
$fields = $seofilter->getFields()->getFieldsByKey();
```

#### Step 3: Update Snippets

**Old Snippet Code**:
```php
$modx->addPackage('seofilter', ...);
$pdo = $modx->getService('pdoTools');
// ... pdoTools-dependent code
```

**New Snippet Code**:
```php
require_once MODX_CORE_PATH . 'components/seofilter/bootstrap.php';
$seofilter = getSeoFilter($modx, $scriptProperties);
// ... use $seofilter services
```

#### Step 4: Test Thoroughly

1. Test URL generation
2. Test 404 handling
3. Test AJAX filtering
4. Test snippet output
5. Check manager interfaces

### Backward Compatibility

SeoFilter 2.0 maintains backward compatibility for:
- Database schema (no changes required)
- Plugin events (same events supported)
- System settings (all preserved)
- Chunk templates (same placeholders)

---

## Troubleshooting

### URLs Not Working

**Problem**: SEO URLs return 404

**Solutions**:
1. Verify plugin is enabled and attached to `OnPageNotFound`
2. Check that URLs exist in `modx_sf_urls` table
3. Clear MODX cache
4. Check .htaccess is configured correctly

### No URLs Generated

**Problem**: sfUrls table is empty

**Solutions**:
1. Verify fields are created and active
2. Verify rules are created and active
3. Verify field-rule relationships exist in `sf_field_ids`
4. Run manual recount via manager
5. Check error log for issues

### AJAX Filtering Not Working

**Problem**: AJAX requests don't filter products

**Solutions**:
1. Verify `ajax` config is set to `true`
2. Check JavaScript console for errors
3. Verify `seoFilterConfig` object exists
4. Check frontend JS file is loaded
5. Verify action.php is accessible

### Performance Issues

**Problem**: Slow URL generation or filtering

**Solutions**:
1. Add database indexes:
   ```sql
   CREATE INDEX idx_sf_urls_lookup ON modx_sf_urls(multi_id, page_id, active);
   CREATE INDEX idx_sf_field_ids ON modx_sf_field_ids(multi_id, field_id);
   ```

2. Enable caching in configuration
3. Optimize rule complexity
4. Use pro mode only when needed

### Memory Issues

**Problem**: PHP memory exhausted

**Solutions**:
1. Increase PHP memory: `memory_limit = 256M`
2. Reduce batch size for URL generation
3. Process URLs in chunks
4. Clear old/inactive URLs

---

## Support

### Getting Help

1. **Documentation**: Read this file thoroughly
2. **GitHub Issues**: https://github.com/justtop1/SeoFilter/issues
3. **MODX Forums**: https://community.modx.com

### Reporting Bugs

When reporting bugs, include:
- MODX version
- PHP version
- SeoFilter version
- Error messages from MODX error log
- Steps to reproduce
- Database structure (if relevant)

### Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Write tests for new features
4. Follow PSR-12 coding standards
5. Submit a pull request

---

## License

SeoFilter is distributed under the GPL-2.0-or-later license.

Original template by Shaun McCormick.
SeoFilter component by justtop1.

---

## Changelog

### Version 2.0.0 (2025-11-18)

#### Added
- PSR-4 namespacing with `SeoFilter\` namespace
- Service-oriented architecture (ConfigService, FieldService, MetaService, RuleService, UrlProcessor)
- PHP 7.4+ type hints throughout codebase
- Comprehensive PHPDoc documentation
- Composer support with autoloading
- Modern MODX3 patterns
- Backward compatibility layer
- Complete API documentation
- Migration guide from 1.x

#### Changed
- Refactored monolithic SeoFilter class into focused services
- Removed hard dependency on pdoTools/pdoFetch
- Improved error handling and logging
- Optimized database queries
- Better code organization and structure

#### Fixed
- Security improvements with input validation
- Performance optimizations
- Memory usage improvements
- Code quality issues

### Version 1.x
- Original MODX2-compatible version
- Single large class architecture
- pdoTools dependency
- Basic functionality for SEO URL filtering
