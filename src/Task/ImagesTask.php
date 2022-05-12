<?php


namespace ShopwareCheckTool\Task;


use ReflectionClass;
use ShopwareCheckTool\Requests\Shopware;

class ImagesTask
{
    private Shopware $shopware;
    private string $name;
    private array $file = [];
    private array $log = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $file = __DIR__ . '/../Logs/Downloaded/Images.json';
        if (file_exists($file)) {
            $this->file = json_decode(file_get_contents($file), true);
        }
        if (!$this->file) {
            echo "{$this->name} file is empty. Task skipped." . PHP_EOL;
        }
    }

    public function check(): void
    {
        $id = $this->shopware->configuration->getId();
        foreach ($this->file as $image) {
            echo "Reading {$this->name}: {$image['id']}" . PHP_EOL;
            if ($image['configuration_id'] !== $id) {
                continue;
            }
            if (!$image['is_uploaded']) {
                $this->log[$image['id']] = 'No uploaded';
                continue;
            }
            $getProduct = $this->shopware->getProductById($image['sw_product_id']);
            $this->log[$image['id']]['sw_product_id'] = (@$getProduct['code'] ?: $getProduct['error']);
            $this->log[$image['id']]['coverId'] = @$getProduct['response']['data']['attributes']['coverId'] ?: 'No cover';
            if (array_key_exists('error', $getProduct)) {
                continue;
            }

            $getMedia = $this->shopware->getMediaById($image['sw_media_id']);
            $this->log[$image['id']]['sw_media_id'] = (@$getMedia['code'] ?: $getMedia['error']);
            if (array_key_exists('error', $getMedia)) {
                continue;
            }
            $getProductMedia = $this->shopware->getProductMediaById($image['sw_product_media_id']);
            $this->log[$image['id']]['sw_product_media_id'] = (@$getProductMedia['code'] ?: $getProductMedia['error']);

        }
        $file = __DIR__ . "/../Logs/Completed/{$this->name}_configuration_{$this->shopware->configuration->getId()}.json";
        if (file_exists($file)) {
            unlink($file);
            echo "Generating new file." . PHP_EOL;
        }
        file_put_contents($file, json_encode($this->log, JSON_PRETTY_PRINT));
        echo "{$this->name} completed." . PHP_EOL;
    }
}