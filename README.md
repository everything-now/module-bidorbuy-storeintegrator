# Magento 2 bidorbuy Store Integrator

### Compatibility

| Product | PHP version  | Platform |
| ------- | --- | --- |
| Store Integrator-1.4.0  | 7.0.26   |✓ Magento 2.0.15, Magento 2.2.3|
| Store Integrator-1.3.0  | 7.0.26   |✓ Magento 2.0.15 |
| Store Integrator-1.2.1  | 7.0.26   |✓ Magento 2.0.15 |
| Store Integrator-1.2.0  | 7.0.26   |✓ Magento 2.0.15 |
| Store Integrator-1.1.1  | 7.0.26   |✓ Magento 2.0.15 |
| Store Integrator-1.1.0  | 7.0.26   |✓ Magento 2.0.15 |
| Store Integrator-1.0.1  | 7.0.26   |✓ Magento 2.0.15 |
| Store Integrator-1.0.0  | 7.0.26   |✓ Magento 2.0.15 |

### Description
The bidorbuy Store Integrator allows you to get products from your online store listed on bidorbuy quickly and easily.
Expose your products to the bidorbuy audience - one of the largest audiences of online shoppers in South Africa Store updates will be fed through to bidorbuy automatically, within 24 hours so you can be sure that your store is in sync within your bidorbuy listings. All products will appear as Buy Now listings. There is no listing fee just a small commission on successful sales. View [fees](https://support.bidorbuy.co.za/index.php?/Knowledgebase/Article/View/22/0/fee-rate-card---what-we-charge). Select as many product categories to list on bidorbuy as you like. No technical requirements necessary.

To make use of this plugin, you'll need to be an advanced seller on bidorbuy.
 * [Register on bidorbuy](https://www.bidorbuy.co.za/jsp/registration/UserRegistration.jsp?action=Modify)
 * [Apply to become an advanced seller](https://www.bidorbuy.co.za/jsp/seller/registration/UserSellersRequest.jsp)
 * Once you integrate with bidorbuy, you will be contacted by a bidorbuy representative to guide you through the process.

#### System requirements

Minimum Supported PHP versions: 7.1+

PHP extensions: curl, mbstring

#### Useful documentation
* Upgrade the Magento application and components [link](http://devdocs.magento.com/guides/v2.2/comp-mgr/extens-man/extensman-main-pg.html)
* Install extensions from the command line [link](http://devdocs.magento.com/guides/v2.2/comp-mgr/install-extensions.html)
* Uninstall modules [link](http://devdocs.magento.com/guides/v2.0/install-gde/install/cli/install-cli-uninstall-mods.html)

## Installation

#### Option 1: via Magento Marketplace
1. Get your authentication keys http://devdocs.magento.com/guides/v2.0/install-gde/prereq/connect-auth.html
2. Go to your MAGENTO_ROOT path, example: cd /var/www/html/magento-2.0.15
3. Update your composer.json by executing command: composer require <component-name> example: composer require extremeidea/module-bidorbuy-storeintegrator
4. Enable the extension and clear static view files: bin/magento module:enable Bidorbuy_StoreIntegrator --clear-static-content 
5. Register the extension: bin/magento setup:upgrade
6. Deploy static view files: bin/magento setup:static-content:deploy 
    
### Enabling module

####  Option 1: via command line      
0. Connect to server via SSH and execute this commands: 
1. Go to your MAGENTO_ROOT path, example: cd /var/www/html/magento-2.0.15
2. Enable module by next command: bin/magento module:enable Bidorbuy_StoreIntegrator --clear-static-content
3. Register the extension: bin/magento setup:upgrade

####  Option 2: via browser (admincp)
0. Check that  Cron is set up correctly, just follow the path in admin panel to initialize the module 
1. Go to the System/ Web Setup Wizard/Component Manager  
2. Enable Bidorbuy_StoreIntegrator module

### Update module
0. Connect to server via SSH and execute this commands:
1. Go to your MAGENTO_ROOT path, example: cd /var/www/html/magento-2.0.15
2. Update module by command: "composer require extremeidea/module-bidorbuy-storeintegrator:NEW_VERSION --update-with-dependencies
    where NEW_VERSION is new package version, for example 1.2.3
### Disabling

####  Option 1: via command line
0. Connect to server via SSH and execute this commands: 
1. Go to your MAGENTO_ROOT path, example: cd /var/www/html/magento-2.0.15
2. Disable module by command: bin/magento module:disable Bidorbuy_StoreIntegrator --clear-static-content 

####  Option 2: via browser (admincp)
0. Check that  Cron is set up correctly, just follow the path in admin panel to initialize the module 
1. Go to the System/ Web Setup Wizard/Component Manager  
2. Disable Bidorbuy_StoreIntegrator module

### Uninstall

####  Option 1: via command line (if module was installed from the command line)
0. Connect to server via SSH and execute this commands: 
1. Go to your MAGENTO_ROOT path, example: cd /var/www/html/magento-2.0.15
2. bin/magento module:uninstall Bidorbuy_StoreIntegrator --clear-static-content 

####  Option 2: via browser (admincp)
0. Check that  Cron is set up correctly, just follow the path in admin panel to initialize the module 
1. Go to the System/ Web Setup Wizard/Component Manager  
2. Disable Bidorbuy_StoreIntegrator module
3. Delete Bidorbuy_StoreIntegrator module

### Configuration

Note: Store Integrator`s Settings page is available only on Store View: 'Default' page.        

1. Log in to control panel as administrator.
2. Navigate to System > Configuration > bidorbuy > bidorbuy Store Integrator.
3. Set the export criteria.
4. Press the `Save` button.
5. Press the `Export` button.
6. Press the `Download` button.
7. Share Export Links with bidorbuy.
8. To display BAA fields on the setting page add '/baa/1' to URL in address bar.