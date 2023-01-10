<?php


namespace ShopwareCheckTool\Task;


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
    private array $log = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->useCompletedFolder();
        $this->file = $this->readFile('ImageDeepTask')['invalid'];
    }

    public function check(Marketplace $marketplace): void
    {
        $cleanPayload = [
            'sw_media_id' => '',
            'sw_product_media_id' => '',
            'is_uploaded' => false,
            'need_to_delete' => false,
            'need_to_update' => false
        ];
        $plentymarket = new Plentymarket($marketplace);
        foreach ($this->file['media'] as $id) {
            echo "Reading {$this->name}: $id" . PHP_EOL;
            sleep(1);
            $resp = $plentymarket->updateVariationImageQueueById($id, $cleanPayload);
            echo "{$this->name}-ID:$id, CODE:{$resp['code']}" . PHP_EOL;
            $this->log[$id] = (string)($resp['code']);
        }
        $this->saveFile($this->log);
    }
}