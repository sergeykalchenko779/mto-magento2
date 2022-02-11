# maatoo for Magento 2

# Installation

##  Upload extension to Magento

1. Run command: composer require maatoo/mto-magento2:1.0.1 --no-update
2. Run command  composer update

https://devdocs.magento.com/cloud/howtos/install-components.html

## Activate Extension 

2. Run command: php bin/magento maintenance:enable
3. Run command: php bin/magento module:enable Maatoo_Maatoo
4. Run command: php bin/magento setup:upgrade
5. Run command: php bin/magento setup:di:compile
6. Run command: php bin/magento setup:static-content:deploy -f
7. Run command: php bin/magento maintenance:disable
8. Configure plugin in Configuration
11. Enable Website

Also, if a client server has the same configurations as on your stg php commands should be run with parameter -d memory_limit=-1, for example: php -d memory_limit=-1 bin/magento setup:di:compile

## Update Extension

1. Run command: composer require maatoo/mto-magento2:1.0.1 --no-update
2. Run command: php bin/magento maintenance:enable
3. Run command: php bin/magento setup:upgrade
4. Run command: php bin/magento setup:di:compile
5. Run command: php bin/magento setup:static-content:deploy -f
6. Run command: php bin/magento maintenance:disable

# Enable Persisent Carts

1. Go to General -> Web in Magento 2 Admin Panel
2. Go to Customers -> Persisent Shopping Cart
3. Set «Enable Persistence» to yes
4. Save & Clear Caches