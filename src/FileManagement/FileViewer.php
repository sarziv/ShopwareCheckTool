<?php

namespace ShopwareCheckTool\FileManagement;

use ShopwareCheckTool\Requests\Shopware;

class FileViewer extends File
{

    protected Shopware $shopware;

    public function __construct(Shopware $shopware)
    {

        $this->shopware = $shopware;
    }

}