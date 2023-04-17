<?php


namespace ShopwareCheckTool\TaskDeeper;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ShopwareCoversTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private Collection $collection;

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->collection = Collection::make($this->readFile('Images'))
            ->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->where('is_uploaded', '=', '1');
        $this->clear();
    }

    public function check(): void
    {
        $page = 1;
        $this->newFileLineLog('Start: ' . date('Y-m-d H:i:s'));
        do {
            echo "Checking page: $page" . PHP_EOL;
            $this->newFileLineLog('Checking page: ' . $page);
            $paginate = $this->shopware->paginateProducts($page);
            foreach ($paginate['response']['data'] as $product) {
                $this->checkProducts($product);
            }
            $page++;
        } while (!empty(@$paginate['response']['data']));
        $this->newFileLineLog('End: ' . date('Y-m-d H:i:s'));
    }

    private function checkProducts(array $products): void
    {
        foreach ($products ?: [] as $productId) {
            $image = @$this->collection->where('sw_product_id', '=', $productId)->sortBy('image_position')->first();
            if (!$image) {
                $this->newFileLineLog($productId . ': ' . 'No images');
                continue;
            }
            $sync = $this->shopware->updateProductCover($productId, $image['sw_product_media_id']);
            $this->newFileLineLog($productId . ': ' . 'Trying to added cover. CODE: ' . $sync['code']);
        }
    }
}