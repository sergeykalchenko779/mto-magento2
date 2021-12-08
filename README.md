# maatoo for Magento 2

# Installation

1. Unpack module to the directory <magento_folder>/app/code, it should be as app/code/Maatoo/Maatoo/
2. Run command: php bin/magento maintenance:enable (this command needs to be run only for production mode)
3. Run command: php bin/magento module:enable Maatoo_Maatoo
4. Run command: php bin/magento setup:upgrade
5. Run command: php bin/magento setup:di:compile
6. Run command: php bin/magento setup:static-content:deploy -f (this command needs to be run only for production mode)
7. Run command: php bin/magento maintenance:disable (this command needs to be run only for production mode)
8. Also, if a client server has the same configurations as on your stg php commands should be run with parameter -d memory_limit=-1, for example: php -d memory_limit=-1 bin/magento setup:di:compile
