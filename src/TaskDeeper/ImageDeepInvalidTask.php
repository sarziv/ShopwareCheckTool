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
        $this->useCompletedInvalidFolder();
        $this->file = $this->readInvalidFile(self::FILE_NAME) ?: [];
    }

    public function check(Marketplace $marketplace): void
    {
        $this->newGeneralLine('Started: ' . self::FILE_NAME);
        $pMarketplace = new Plentymarket($marketplace);
        foreach ($this->file as $id) {
            $resp = $pMarketplace->updateVariationImageQueueById($id, self::CLEAN_PAYLOAD);
            $this->newLogLine("$id-CODE:{$resp['code']}");
            sleep(1);
        }
        $this->newGeneralLine('Finished: ' . self::FILE_NAME);
    }
}