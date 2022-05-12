<?php

namespace ShopwareCheckTool;
require_once('../vendor/autoload.php');

use ShopwareCheckTool\Download\DownloadMarketplace;
use ShopwareCheckTool\Models\Marketplace;
use ShopwareCheckTool\Requests\Shopware;
use ShopwareCheckTool\Task\AttributeTask;
use ShopwareCheckTool\Task\CategoryTask;
use ShopwareCheckTool\Task\DeliveryTask;
use ShopwareCheckTool\Task\ImagesTask;
use ShopwareCheckTool\Task\ManufacturerTask;
use ShopwareCheckTool\Task\MeasurementTask;
use ShopwareCheckTool\Task\PropertyTask;

class Tasker
{
    public function downloadMarketplace(string $domain, string $token, bool $skip = false): void
    {
        if (!$skip) {
            $marketplace = new Marketplace();
            $marketplace->setDomain($domain);
            $marketplace->setToken($token);
            (new DownloadMarketplace($marketplace))->download();
        }
    }

    public function start(int $configurationId): void
    {
        $shopware = new Shopware($configurationId);
        (new AttributeTask($shopware))->check();
        (new CategoryTask($shopware))->check();
        (new DeliveryTask($shopware))->check();
        (new ManufacturerTask($shopware))->check();
        (new MeasurementTask($shopware))->check();
        (new PropertyTask($shopware))->check();
        (new ImagesTask($shopware))->check();
    }
}

$tasker = new Tasker();
$tasker->downloadMarketplace(
    "PM_DOMAIN",
    "PM_TOKEN",
    true
);
$tasker->start(1);
