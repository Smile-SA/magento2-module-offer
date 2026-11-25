## Smile Offer

This module is a plugin for [ElasticSuite](https://github.com/Smile-SA/elasticsuite).

It allows to add offers on products by sellers.

### Requirements

The module requires:

- PHP ^8.1
- [ElasticSuite](https://github.com/Smile-SA/elasticsuite) >= 2.10.* (2.10.* for Magento 2.4.4 and 2.4.5, 2.11.* or later for Magento 2.4.6 and later)
- [Seller](https://github.com/Smile-SA/magento2-module-seller) > 2.0.*

### How to use

1. Install the module via Composer:

``` composer require smile/module-offer ```

2. Enable it:

``` bin/magento module:enable Smile_Offer ```

3. Install the module and rebuild the DI cache:

``` bin/magento setup:upgrade ```

### How to configure offers

Go to magento backoffice

Menu : Sellers > Retailer Offers
