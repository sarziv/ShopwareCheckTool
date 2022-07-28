<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\Requests\Shopware;

class ImageDeepTask
{
    private Shopware $shopware;
    private string $name;
    private array $file = [];
    private array $log = [];
    private array $invalid = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $file = __DIR__ . '/../Logs/Downloaded/Images.json';
        if (file_exists($file)) {
            $this->file = Collection::make(json_decode(file_get_contents($file), true))
                ->where('configuration_id', '=', $this->shopware->configuration->getId())
                ->where('is_uploaded', '=', '1')
                ->toArray();
        }
        if (!$this->file) {
            echo "{$this->name} file is empty. Task skipped." . PHP_EOL;
        }
    }

    public function check(): void
    {
        foreach ($this->file as $image) {
            echo "Reading {$this->name}: {$image['id']}" . PHP_EOL;
            $getProduct = $this->shopware->getProductById($image['sw_product_id']);
            if ($getProduct['code'] !== 200) {
                continue;
            }
            $this->log[$image['id']]['sw_product_id'] = (@$getProduct['code'] ?: $getProduct['error']);
            if (array_key_exists('error', $getProduct)) {
                $this->invalid[] = $image['id'];
                continue;
            }

            $getMedia = $this->shopware->getMediaById($image['sw_media_id']);
            $this->log[$image['id']]['sw_media_id'] = (@$getMedia['code'] ?: $getMedia['error']);
            if (array_key_exists('error', $getMedia)) {
                $this->invalid[] = $image['id'];
                continue;
            }
            $getProductMedia = $this->shopware->getProductMediaById($image['sw_product_media_id']);
            $this->log[$image['id']]['sw_product_media_id'] = (@$getProductMedia['code'] ?: $getProductMedia['error']);
        }
        $this->log['invalid']['count'] = count($this->invalid);
        $this->log['invalid']['media'] = $this->invalid;
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/$this->name.json";
        if (file_exists($file)) {
            unlink($file);
            echo "Generating new file." . PHP_EOL;
        }
        file_put_contents($file, json_encode($this->log, JSON_PRETTY_PRINT));
        echo "{$this->name} completed." . PHP_EOL;
    }
}