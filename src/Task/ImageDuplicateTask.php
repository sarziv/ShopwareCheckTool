<?php


namespace ShopwareCheckTool\Task;


use ReflectionClass;
use ShopwareCheckTool\Requests\Shopware;

class ImageDuplicateTask
{
    private int $count = 1;
    private Shopware $shopware;
    private string $name;
    private array $file = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/$this->name.json";
        if (file_exists($file)) {
            $this->file = json_decode(file_get_contents($file), true)['invalid'];
        }
        if (!$this->file) {
            echo "{$this->name} file is empty. Task skipped." . PHP_EOL;
        }
        $this->total = count($this->file['media']);
    }

    public function check(): void
    {
        foreach ($this->file['media'] as $productId => $productMedia) {
            echo $this->name . ': ' . $this->count++ . '/' . $this->total . PHP_EOL;
            foreach ($productMedia as $mediaId) {
                $resp = $this->shopware->deleteProductMediaById($mediaId);
                echo "$mediaId :{$resp['code']}" . PHP_EOL;
            }
        }
    }
}