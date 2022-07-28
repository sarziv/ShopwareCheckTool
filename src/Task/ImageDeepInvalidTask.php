<?php


namespace ShopwareCheckTool\Task;


use ReflectionClass;
use ShopwareCheckTool\Models\Marketplace;
use ShopwareCheckTool\Requests\Plentymarket;
use ShopwareCheckTool\Requests\Shopware;

class ImageDeepInvalidTask
{
    private string $name;
    private array $file = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $file = __DIR__ . "/../Logs/Completed/{$shopware->configuration->getPath()}/ImageDeepTask.json";
        if (file_exists($file)) {
            $this->file = json_decode(file_get_contents($file), true)['invalid'];
        }
        if (!$this->file) {
            echo "{$this->name} file is empty. Task skipped." . PHP_EOL;
        }
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
            echo "Updated: $id, Status: {$resp['code']}" . PHP_EOL;
        }
        echo "{$this->name} completed." . PHP_EOL;
    }
}