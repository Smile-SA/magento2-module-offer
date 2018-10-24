## Smile Offer

This module is a plugin for [ElasticSuite](https://github.com/Smile-SA/elasticsuite).

It allows to add offers on products by sellers.

### Requirements

The module requires :

- [ElasticSuite](https://github.com/Smile-SA/elasticsuite) > 2.1.*
- [Seller](https://github.com/Smile-SA/magento2-module-seller) > 1.2.*

### How to use

1. Install the module via Composer :

``` composer require smile/module-offer ```

2. Enable it

``` bin/magento module:enable Smile_Offer ```

3. Install the module and rebuild the DI cache

``` bin/magento setup:upgrade ```

### How to configure offers

Go to magento backoffice

Menu : Sellers > Retailer Offers
