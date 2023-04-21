# Check tool
## Installation
```bash
git clone {repository}
cd {folder}
composer install
cp ./src/Examples/.credentials.php ./src/Examples/credentials.php
php {location}/src/Examples/runLocally.php
```
## Local development
### Credentials
* ConfigurationId - Configuration ID of the assistant
* Domain - PM System domain
* Token - Generated via [AccessToken](https://developers.plentymarkets.com/en-gb/plentymarkets-rest-api/index.html#/Account/post_rest_account_login) or PM Storage

Configuration ID can be found at:
* Network then opening configuration assistant
* GET {{plentymarketHost}}/rest/PlentymarketsShopwareCore/test?model=Configuration

### Checking records
Last line of code is to chain tasks `$tasker->` in the example.
```php
<?php

namespace ShopwareCheckTool;
require_once('../../vendor/autoload.php');

use ShopwareCheckTool\Download\DownloadMarketplace;use ShopwareCheckTool\Models\Marketplace;use ShopwareCheckTool\Requests\Shopware;use ShopwareCheckTool\Task\Tasker;

$credentials = include __DIR__ . '/credentials.php'; //files for credentials

$marketplace = new Marketplace();
$marketplace->setDomain($credentials['domain']);
$marketplace->setToken($credentials['token']);

$downloadMarketplace = new DownloadMarketplace($marketplace, true);
$downloadMarketplace->download();

$shopware = new Shopware($credentials['configurationId']);
$tasker = new Tasker($shopware);

$tasker->all()->allImages();
```
### Removing records
Generally this file should not be changed, as it uses generated files from tasks.
Removing records can only be done after checking, as they generate invalid id's logs.
```php
<?php

namespace ShopwareCheckTool;
require_once('../../vendor/autoload.php');

use ShopwareCheckTool\Models\Marketplace;
use ShopwareCheckTool\Requests\Shopware;
use ShopwareCheckTool\Task\Tasker;

$credentials = include __DIR__ . '/credentials.php'; //files for credentials

$marketplace = new Marketplace();
$marketplace->setDomain($credentials['domain']);
$marketplace->setToken($credentials['token']);

$shopware = new Shopware($credentials['configurationId']);
$tasker = new Tasker($shopware);
$tasker->remove($marketplace);
```
## Tasks
- AttributeTask - Attributes from V1.0.9>
- AttributeReworkTask - Attributes from V1.0.9<
- CategoryTask - Category checks
- DeliveryTask - Units checks
- ManufacturerTask - Manufacturers checks
- MeasurementTask - Measurements checks
- PropertyTask - Property checks
- TagTask - Tags checks
- ImagesTask - Images checks
- ImageDeepTask - Check images, media files, thumbnails
- ImageDeepInvalidTask - Removes invalid media files
- ProductVisibilityTask - Product visibility checks
- ProductConfiguratorTask - Product configuration checks
- ShopwareErrorDuplicateProductNumberTask - Checks Shopware errors file and removes product by product number
## Logs
```
    └── Logs
        ├── Completed
        │   └── {host}
        │         ├── *.log files - Generated with tasks
        │         └── Invalid
        │             └── Inavlid *.log files - Checked with removing tasks
        ├── Downloaded
        │   └── Plugin files for the current scan
        └── *.log - General log
```