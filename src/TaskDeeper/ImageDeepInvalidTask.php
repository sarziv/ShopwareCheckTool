<?php


namespace ShopwareCheckTool\TaskDeeper;


use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Models\Marketplace;
use ShopwareCheckTool\Requests\Plentymarket;
use ShopwareCheckTool\Requests\Shopware;

class ImageDeepInvalidTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'ImageDeepTask';
    private const CLEAN_PAYLOAD = [
        'sw_media_id' => '',
        'sw_product_media_id' => '',
        'is_uploaded' => false,
        'need_to_delete' => false,
        'need_to_update' => false
    ];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->useCompletedFolder();
        $this->file = $this->readFile(self::FILE_NAME)['invalid'];
        $this->clear();
    }

    public function check(Marketplace $marketplace): void
    {
        $this->newGeneralFileLine('Started: ' . self::FILE_NAME);
        $pMarketplace = new Plentymarket($marketplace);
        foreach ($this->file['list'] as $id) {
            echo "Reading {$this->name}: $id" . PHP_EOL;
            sleep(2);
            $resp = $pMarketplace->updateVariationImageQueueById($id, self::CLEAN_PAYLOAD);
            $this->newFileLineLog("$id: CODE:{$resp['code']}");
        }
        $this->newGeneralFileLine('Finished: ' . self::FILE_NAME);
    }
}