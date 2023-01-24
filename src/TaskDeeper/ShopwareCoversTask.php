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
    private array $log = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->collection = Collection::make($this->readFile('Images'))
            ->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->where('is_uploaded', '=', '1');
    }

    public function check(): void
    {
        $page = 1;
        do {
            echo "Checking page: $page" . PHP_EOL;
            $paginate = $this->shopware->paginateProducts($page);
            foreach ($paginate['response']['data'] as $product) {
                $this->log['products'][] = $product['id'];
            }
            $page++;

        } while (!empty(@$paginate['response']['data']));

        echo 'Products covers' . PHP_EOL;
        foreach (@$this->log['products'] ?: [] as $productId) {
            echo "Product: $productId" . PHP_EOL;
            $image = @$this->collection->where('sw_product_id', '=', $productId)->sortBy('image_position')->first();
            if (!$image) {
                $this->log['missing'][] = ['product' => $productId];
                continue;
            }
            echo "Products cover added. CoverId {$image['sw_product_media_id']}" . PHP_EOL;
            $sync = $this->shopware->updateProductCover($productId, $image['sw_product_media_id']);
            $this->log['covers'][] = ['product' => $productId, 'coverId' => $image['sw_product_media_id'], 'code' => @$sync['code'] ?: 0];
        }
        $this->log['count']['missing'] = count((@$this->log['missing'] ?: []));
        $this->log['count']['covers'] = count((@$this->log['covers'] ?: []));

        $this->saveFile($this->log);
    }
}