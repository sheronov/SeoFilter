# SeoFilter 2.0 for MODX Revolution 3

[![MODX](https://img.shields.io/badge/MODX-3.0+-blue.svg)](https://modx.com/)
[![PHP](https://img.shields.io/badge/PHP-7.4+-purple.svg)](https://php.net/)
[![License](https://img.shields.io/badge/License-GPL--2.0-green.svg)](LICENSE)

**Modern SEO-friendly URL filtering system for MODX Revolution** - Transform standard filter query parameters into beautiful, indexed URLs with custom meta data for each filter combination.

```
Before: /products?color=red&size=large&brand=nike
After:  /products/red-color/large-size/nike-brand
```

## 🚀 Quick Start

### Installation

```bash
# Via MODX Package Manager
Search for "SeoFilter" and install

# Via Composer
composer require justtop1/seofilter
```

### Basic Usage

```php
<?php
// Load SeoFilter
require_once MODX_CORE_PATH . 'components/seofilter/bootstrap.php';
$seofilter = getSeoFilter($modx);

// Initialize for current page
$seofilter->initialize('web', [
    'ajax' => true,
    'page' => $modx->resource->id,
]);

// Generate SEO URL
$url = $seofilter->generateUrl($pageId, [
    'color' => 'red',
    'size' => 'large'
]);
?>
```

## ✨ Features

### SEO-First Design
- ✅ **Unique URLs** for every filter combination
- ✅ **Custom meta data** (title, description, H1, etc.) per URL
- ✅ **Automatic indexing** by search engines
- ✅ **Breadcrumb support** for filtered pages
- ✅ **XML sitemap generation** for all filter URLs

### Modern Architecture
- 🏗️ **PSR-4 Namespacing** - Clean `SeoFilter\` namespace
- 🎯 **Service-Oriented** - Focused, testable service classes
- 🔒 **Type-Safe** - Full PHP 7.4+ type hints
- 📦 **Composer Ready** - Modern dependency management
- 🚫 **No pdoTools** - Pure MODX3, no external dependencies

### Powerful Features
- 🔢 **Result Counting** - Real-time product counts per filter
- 🌐 **Multi-language** - Full Russian declension support via Morpher.ru API
- ⚡ **AJAX Filtering** - Smooth client-side filtering
- 🎛️ **Range Sliders** - Price and numeric range support
- 🔗 **Deep Linking** - Share and bookmark filter states

## 📚 What's New in 2.0

### Breaking Changes
- **Namespace**: Classes moved to `SeoFilter\` namespace
- **Architecture**: Monolithic class split into service classes
- **Dependencies**: Removed pdoTools/pdoFetch dependency
- **PHP Version**: Minimum PHP 7.4 required

### Major Improvements
- **5x smaller** core class (split from 4,571 lines to focused services)
- **100% type coverage** - All methods fully typed
- **Complete PHPDoc** - Every method documented
- **Better performance** - Optimized queries and caching
- **Enhanced security** - Input validation and sanitization
- **MODX3 optimized** - Modern MODX Revolution 3 patterns

See [DOCUMENTATION.md](DOCUMENTATION.md) for complete migration guide.

## 🏗️ Service Architecture

SeoFilter 2.0 uses a clean service-oriented architecture:

```php
// Core services available through the main class
$seofilter->getConfig();  // ConfigService  - Configuration management
$seofilter->getFields();  // FieldService   - Field operations
$seofilter->getMeta();    // MetaService    - SEO meta generation
$seofilter->getRules();   // RuleService    - Rule matching
$seofilter->getUrls();    // UrlProcessor   - URL generation/processing
```

### ConfigService
```php
$config = $seofilter->getConfig();
$config->get('ajax');              // Get setting
$config->set('page', 5);           // Set setting
$config->isProMode();              // Check pro mode
$config->getValuesDelimiter();     // Get delimiter
```

### FieldService
```php
$fields = $seofilter->getFields();
$fields->getFieldsByKey('alias');          // Get all fields
$fields->getFieldByAlias('color');         // Get specific field
$fields->isSliderField($fieldId);          // Check if slider
```

### RuleService
```php
$rules = $seofilter->getRules();
$ruleId = $rules->findRuleId($pageId, $params);  // Find matching rule
$count = $rules->countRuleFields($ruleId);       // Count fields in rule
```

### MetaService
```php
$meta = $seofilter->getMeta();
$data = $meta->buildMetaFromRule($rule, $params, $words);
$meta->applyMetaToResource($resource, $data);
```

### UrlProcessor
```php
$urls = $seofilter->getUrls();
$urls->processUrl($url);               // Process 404 URL
$urls->generateUrl($pageId, $params);  // Generate SEO URL
$urls->normalizeUrl($url);             // Normalize URL
```

## 📖 Core Concepts

### Fields
Define what can be filtered (color, size, brand, etc.)

```php
// Field types supported:
- Template Variables (TVs)
- Resource fields
- Tagger tags
- miniShop2 data fields
- Custom xPDO classes
```

### Rules
Define how fields combine into URLs and what meta data to use

```php
// Rule configuration
[
    'page' => 5,  // Target page
    'title' => 'Products: [[+color]] [[+size]]',
    'h1' => '[[+color]] products in [[+size]]',
    'description' => 'Buy [[+color]] products...',
]
```

### Dictionary
Translates values into SEO-friendly words with grammatical cases

```php
// Dictionary entry
[
    'input' => 'red',
    'value' => 'krasnyy',       // Base form
    'nominative' => 'krasnyy',   // Nominative case
    'genitive' => 'krasnogo',    // Genitive case
    // ... other grammatical cases
]
```

### URLs
Generated SEO URLs with metadata and result counts

```php
[
    'old_url' => 'krasnyy-tsvet/bolshoy-razmer',
    'title' => 'Red Products in Large Size',
    'total' => 42,  // Product count
]
```

## 🎯 Usage Examples

### Generate Filter Link

```html
[[!sfLink?
    &pages=`5`
    &color=`red`
    &size=`large`
    &tpl=`@INLINE <a href="[[+url]]">[[+name]]</a>`
]]
```

### Generate Filter Menu

```html
[[!sfMenu?
    &rules=`1,2,3`
    &tpl=`filterMenuTpl`
    &showInactive=`0`
]]
```

### Get Dictionary Word

```html
[[!sfWord?
    &alias=`color`
    &input=`red`
    &case=`nominative`
]]
```

### Programmatic Usage

```php
<?php
$seofilter = getSeoFilter($modx);

// Find rule
$params = ['color' => 'red', 'size' => 'large'];
$ruleId = $seofilter->findRuleId($pageId, $params);

// Generate URL
$url = $seofilter->generateUrl($pageId, $params, $ruleId);

// Get rule object
$rule = $modx->getObject('sfRule', $ruleId);

// Build meta data
$meta = $seofilter->getMeta()->buildMetaFromRule($rule, $params);

// Apply to resource
$seofilter->getMeta()->applyMetaToResource($modx->resource, $meta);
?>
```

## 📋 System Requirements

### Minimum
- **PHP**: 7.4.0+
- **MODX**: 3.0.0+
- **MySQL**: 5.7+ or MariaDB 10.2+
- **Extensions**: PDO, pdo_mysql, mbstring, json

### Recommended
- **PHP**: 8.0+ for better performance
- **Memory**: 256MB PHP memory limit
- **Composer**: For dependency management

## 📚 Documentation

- **[DOCUMENTATION.md](DOCUMENTATION.md)** - Complete documentation
  - Installation guide
  - Configuration reference
  - Core concepts explained
  - Service architecture details
  - API reference
  - Usage examples
  - Migration guide from 1.x
  - Troubleshooting

## 🔄 Migration from 1.x

### Quick Migration

**Old Code (1.x)**:
```php
$seofilter = $modx->getService('seofilter', 'SeoFilter', ...);
$config = $seofilter->config['ajax'];
```

**New Code (2.0)**:
```php
require_once MODX_CORE_PATH . 'components/seofilter/bootstrap.php';
$seofilter = getSeoFilter($modx);
$config = $seofilter->getConfig()->get('ajax');
```

See [DOCUMENTATION.md#migration-from-1x](DOCUMENTATION.md#migration-from-1x) for complete migration guide.

## 🤝 Contributing

Contributions welcome! Please:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Standards
- Follow PSR-12 coding standards
- Add type hints to all methods
- Document with PHPDoc
- Write tests for new features
- Update documentation

## 📝 License

SeoFilter is distributed under the **GPL-2.0-or-later** license.

- Original template by [Shaun McCormick](https://github.com/splittingred)
- SeoFilter component by [justtop1](https://github.com/justtop1)

## 🐛 Support

### Getting Help
- 📖 Read [DOCUMENTATION.md](DOCUMENTATION.md)
- 🐛 [GitHub Issues](https://github.com/justtop1/SeoFilter/issues)
- 💬 [MODX Community](https://community.modx.com)

### Reporting Bugs

Include:
- MODX version
- PHP version
- SeoFilter version
- Error messages from error log
- Steps to reproduce

## ⭐ Show Your Support

If SeoFilter helps your project, please:
- ⭐ Star this repository
- 🐦 Share on social media
- 💬 Tell the MODX community
- 🤝 Contribute improvements

## 🎉 Acknowledgments

- **Shaun McCormick** - Original component template structure
- **MODX Community** - Feedback and testing
- **Contributors** - All who have contributed code and ideas

---

**Made with ❤️ for the MODX Community**

Transform your filter URLs from ugly to beautiful with SeoFilter 2.0!
