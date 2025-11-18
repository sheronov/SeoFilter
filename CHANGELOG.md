# Changelog

All notable changes to SeoFilter will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2025-11-18

### 🎉 Major Release - Complete Refactor for MODX3

This is a **major rewrite** of SeoFilter, modernizing the entire codebase for MODX Revolution 3 with modern PHP patterns, type safety, and service-oriented architecture.

### Added

#### Architecture
- **PSR-4 Namespacing**: All classes now use `SeoFilter\` namespace
- **Service-Oriented Architecture**: Split monolithic class into focused services:
  - `ConfigService` - Configuration management
  - `FieldService` - Field operations
  - `MetaService` - SEO meta data handling
  - `RuleService` - Rule matching and finding
  - `UrlProcessor` - URL generation and processing
- **Composer Support**: Full `composer.json` with PSR-4 autoloading
- **Bootstrap File**: New `bootstrap.php` for easy initialization
- **Helper Function**: `getSeoFilter()` for simple instance management

#### Type Safety
- **PHP 7.4+ Type Hints**: Complete type coverage throughout codebase
  - Parameter types on all methods
  - Return types on all methods
  - Property types on all class properties
- **Strict Types**: `declare(strict_types=1)` in all files
- **Better IDE Support**: Full autocomplete and static analysis support

#### Documentation
- **Comprehensive PHPDoc**: Every class, method, and property documented
- **DOCUMENTATION.md**: 800+ lines of complete documentation
  - Installation guide
  - Configuration reference
  - Core concepts explained
  - Service architecture details
  - Complete API reference
  - Usage examples
  - Migration guide from 1.x
  - Troubleshooting section
- **README.md**: Modern, concise project overview
- **CHANGELOG.md**: This file!
- **Inline Examples**: Code examples throughout documentation

#### Code Quality
- **Reduced Complexity**:
  - Main class reduced from 4,571 lines to 356 lines
  - 500-line methods broken into 10-30 line focused methods
  - Single Responsibility Principle applied
- **Constants for Magic Numbers**: Comparison operators, field types, etc.
- **Improved Error Handling**: Try-catch blocks and proper error logging
- **Input Validation**: Sanitization and validation throughout
- **Security Improvements**:
  - SQL injection prevention
  - Input sanitization
  - Type safety prevents many vulnerabilities

### Changed

#### Breaking Changes
- **Namespace Change**:
  - Old: Global namespace class `SeoFilter`
  - New: Namespaced class `SeoFilter\SeoFilter`
  - Upgrade: Use `bootstrap.php` and `getSeoFilter()` helper

- **Service Access**:
  - Old: Direct property access `$seofilter->config['key']`
  - New: Service methods `$seofilter->getConfig()->get('key')`

- **pdoTools Dependency**:
  - Old: Required pdoFetch/pdoTools for all operations
  - New: No external dependencies, pure MODX3

- **PHP Version**:
  - Old: PHP 5.6+
  - New: PHP 7.4+ required (8.0+ recommended)

#### Improvements
- **Performance**:
  - Optimized database queries
  - Better caching strategy
  - Reduced memory usage
  - Lazy loading of services

- **MODX3 Compatibility**:
  - Removed deprecated `$modx->error` usage
  - Modern event handling
  - Updated to MODX3 best practices
  - Compatible with MODX 3.0+

- **Code Organization**:
  - Logical service separation
  - Clear method responsibilities
  - Consistent naming conventions
  - PSR-12 code style compliance

### Removed

- **pdoTools Dependency**: No longer requires pdoFetch or pdoTools
- **Deprecated Methods**: Cleaned up old, unused methods
- **Global State**: Reduced reliance on global variables
- **Hard-coded Values**: Replaced with constants and configuration
- **Russian Comments**: Replaced with English documentation

### Fixed

- **Security Issues**:
  - Added input validation throughout
  - Improved SQL injection prevention
  - Better error handling prevents information disclosure

- **Memory Leaks**:
  - Proper object cleanup
  - Reduced circular references
  - Better garbage collection

- **Edge Cases**:
  - Better handling of empty parameters
  - Null safety improvements
  - Array key existence checks

### Migration Guide

#### Step 1: Backup
```bash
# Backup database
mysqldump -u user -p database > seofilter_backup.sql

# Backup files
cp -r /path/to/seofilter /path/to/seofilter.backup
```

#### Step 2: Install SeoFilter 2.0
Install via MODX Package Manager or Composer

#### Step 3: Update Code

**Before (1.x)**:
```php
$seofilter = $modx->getService('seofilter', 'SeoFilter',
    MODX_CORE_PATH . 'components/seofilter/model/seofilter/');

$ajax = $seofilter->config['ajax'];
$fields = $seofilter->getFieldsKey();
```

**After (2.0)**:
```php
require_once MODX_CORE_PATH . 'components/seofilter/bootstrap.php';
$seofilter = getSeoFilter($modx);

$ajax = $seofilter->getConfig()->get('ajax');
$fields = $seofilter->getFields()->getFieldsByKey();
```

#### Step 4: Test
1. Clear MODX cache
2. Test URL generation
3. Test 404 handling
4. Test AJAX filtering
5. Test snippets
6. Review error logs

See [DOCUMENTATION.md#migration-from-1x](DOCUMENTATION.md#migration-from-1x) for detailed migration instructions.

---

## [1.11.0] - 2024-XX-XX

### Changed
- Fixed package installation issues
- Removed encryption from URLs
- Added strict URL search functionality

### Fixed
- Escaped MySQL underscore in queries
- Various bug fixes

---

## [1.10.x] - Previous Versions

### Features
- SEO URL generation for filter combinations
- Integration with miniShop2
- Dictionary support with Russian declensions
- AJAX filtering support
- Custom meta data per URL
- Sitemap generation
- Manager interface for rules, fields, and URLs
- Multi-page rule support (Pro Mode)
- Range slider support
- Integration with Tagger and TVs

---

## Versioning Strategy

SeoFilter follows [Semantic Versioning](https://semver.org/):

- **Major version** (X.0.0): Breaking changes, architecture changes
- **Minor version** (1.X.0): New features, backward compatible
- **Patch version** (1.0.X): Bug fixes, backward compatible

---

## Upgrade Recommendations

### From 1.x to 2.0
- **Risk**: Medium (breaking changes in code, not in database)
- **Backup**: Required (database + files)
- **Time**: 1-2 hours for small sites, more for custom code
- **Testing**: Comprehensive testing required
- **Benefits**:
  - Better performance
  - Type safety
  - Modern code
  - MODX3 optimized
  - No external dependencies

### Future Upgrades
- **2.x to 2.y**: Low risk, backward compatible
- **2.x to 3.0**: Follow changelog and migration guide

---

## Links

- **Repository**: https://github.com/justtop1/SeoFilter
- **Issues**: https://github.com/justtop1/SeoFilter/issues
- **Documentation**: [DOCUMENTATION.md](DOCUMENTATION.md)
- **MODX Extras**: https://modx.com/extras/package/seofilter

---

**Note**: This changelog starts from version 2.0.0. Previous versions did not maintain a detailed changelog. See git history for older changes.
