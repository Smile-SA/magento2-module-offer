# Changelog

All notable changes to this project will be documented in this file.

## [2.0.2] - Unreleased
[2.0.2]: https://github.com/Smile-SA/magento2-module-offer/compare/2.0.1...2.0.2

- Supported platforms: Magento 2.4.4 and 2.4.5 only, PHP 8.1
- `composer.json` now states it: `php ^8.1` and `magento/framework >=103.0.4 <103.0.6` (previously `php ^8.1` and `magento/framework >=103.0.4`)
- Magento 2.4.6 and later and Mage-OS are supported by the 2.1.x line
- Fix `setup:di:compile` on Magento 2.4.4 and 2.4.5: native `mixed` and union types are removed from method signatures and kept in docblocks
- Require Elasticsuite 2.10 (`smile/elasticsuite ^2.10`)
- Require the Smile modules of the same line: `smile/module-seller ~2.0.2`
- CI: static analysis on Magento 2.4.5, `setup:di:compile` on Magento 2.4.4 and 2.4.5

## [2.0.1] - 2024-05-03
[2.0.1]: https://github.com/Smile-SA/magento2-module-offer/compare/2.0.0...2.0.1

- Fix extensionAttribute - Concrete return type must be specified

## [2.0.0] - 2023-09-20
[2.0.0]: https://github.com/Smile-SA/magento2-module-offer/compare/1.4.1...2.0.0

Dataset compatibility ES 2.11.x and PHP 8.2

- Fix Dynamic type declaration
- Fix Type hinting
- Replace `Zend_Date` by `DateTime`
- Remove `MutationObserver` support
- Fix UI Component Retailer Offer editing
- Replace `Zend_Validate` by `Laminas\Validator`
- Fix Retailer Grid Column Action
- Fix Retailer Grid Mass Actions
- Fix some translations
- Remove Temando/Shipping Plugin
