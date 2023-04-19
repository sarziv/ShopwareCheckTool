<?php

namespace ShopwareCheckTool;
require_once('../../vendor/autoload.php');

use ShopwareCheckTool\Download\Download;
use ShopwareCheckTool\Download\DownloadPush;

$downloadMarketplace = new DownloadPush();
$downloadMarketplace->pushFile(Download::ATTRIBUTE, '{}');