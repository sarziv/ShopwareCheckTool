<?php

namespace ShopwareCheckTool;
require_once('../vendor/autoload.php');

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