<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\Requests\Shopware;

class ImagesTask
{
    private int $count = 1;
    private int $total;
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
                ->groupBy('sw_product_id')
                ->toArray();
        }
        if (!$this->file) {
            echo "{$this->name} file is empty. Task skipped." . PHP_EOL;
        }
        $this->total = count($this->file);
    }

    public function check(): void
    {
        foreach ($this->file as $productId => $imageList) {
            echo $this->name . ':' . $this->count++ . '/' . $this->total . PHP_EOL;
            $getProductMedia = $this->shopware->getMediaByProductId($productId);
            $this->log[$productId]['product'] = (@$getProductMedia['code'] ?: $getProductMedia['error']);
            if (array_key_exists('error', $getProductMedia)) {
                continue;
            }
            $mediaCollection = Collection::make($getProductMedia['response']['data']);
            $imageListCollection = Collection::make($imageList);
            foreach ($mediaCollection as $media) {
                $imageFound = $imageListCollection->where('sw_product_media_id', '=', $media['id'])->first();
                $this->log[$productId]['media'][$media['id']] = (@(int)$imageFound['variation_id'] ?: 'Invalid media');
                if (empty($imageFound['variation_id'])) {
                    $this->invalid[] = $media['id'];
                }
            }
        }
        $this->log['invalid']['count'] = count($this->invalid);
        $this->log['invalid']['media'] = $this->invalid;
        $this->toFile();
    }

    private function toFile(): void
    {
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/$this->name.json";
        if (file_exists($file)) {
            unlink($file);
        }
        file_put_contents($file, json_encode($this->log, JSON_PRETTY_PRINT));
        echo "{$this->name} completed." . PHP_EOL;
    }
}