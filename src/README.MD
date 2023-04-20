# Check tool
## Installation
```bash
composer install
```
## Local development
Runner tasks can be found at `Tasker.php`
### Checking records

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
        └── *.log - General log
```