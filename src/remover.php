<?php

namespace ShopwareCheckTool;
require_once('../vendor/autoload.php');

use ShopwareCheckTool\Download\DownloadMarketplace;
use ShopwareCheckTool\Models\Marketplace;
use ShopwareCheckTool\Requests\Shopware;
use ShopwareCheckTool\Task\Tasker;

$credentials = include __DIR__ . '/credentials.php'; //files for credentials

$marketplace = new Marketplace();
$marketplace->setDomain($credentials['domain']);
$marketplace->setToken($credentials['token']);

$downloadMarketplace = new DownloadMarketplace($marketplace);
$downloadMarketplace->disable();
$downloadMarketplace->download();
$shopware = new Shopware($credentials['configurationId']);
$tasker = new Tasker($shopware);

$table = 'CategoryMatch';
$whereKey = 'id';
$payload = [
    "3580",
    "3582"
];
//$json = (json_decode(file_get_contents('./Logs/Completed/onlineshop.modehaus-heinze.de/ManufacturerTask.json')));
//
//foreach ($json as $key => $log) {
//    if (str_contains($log, 'FRAMEWORK__RESOURCE_NOT_FOUND')) {
//        $payload[] = (string)$key;
//    }
//}

$tasker->customRemoveTable($payload, $marketplace, $table, $whereKey);