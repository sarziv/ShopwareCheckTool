<?php

namespace ShopwareCheckTool;
use ShopwareCheckTool\Download\Download;
use ShopwareCheckTool\FileManagement\FileViewer;
use ShopwareCheckTool\Requests\Shopware;

require_once('../../vendor/autoload.php');

$shopware = new Shopware(11);
$fileViewer = (new FileViewer($shopware));
$fileViewer->useCompletedFolder();
return $fileViewer->readLogFile(Download::ATTRIBUTE_LOG);
