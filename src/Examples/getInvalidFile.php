<?php

namespace ShopwareCheckTool;
use ShopwareCheckTool\Download\Download;
use ShopwareCheckTool\FileManagement\FileViewer;
use ShopwareCheckTool\Requests\Shopware;

require_once('../../vendor/autoload.php');

$shopware = new Shopware(1);
return (new FileViewer($shopware))->getInvalidFile(Download::ATTRIBUTE);