<?php


namespace ShopwareCheckTool\TaskDeeper;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ShopwareErrorDuplicateProductNumberTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    private array $log = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = Collection::make($this->readFile('ShopwareError'))
            ->where('alias', '=', parse_url($this->shopware->configuration->getDomain())['host'])
            ->whereIn('errors.0.code', 'CONTENT__DUPLICATE_PRODUCT_NUMBER')->pluck('errors.0.meta.parameters.number')
            ->unique()
            ->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $productNumber) {
            $postProductSearch = $this->shopware->postProductSearch($productNumber);
            $this->log[$productNumber]['check'] = $postProductSearch['code'];
            echo "CHECK:$productNumber" . PHP_EOL;
            if ($postProductSearch['code'] !== 200 || $postProductSearch['response']['meta']['total'] <= 0) {
                $this->log[$productNumber]['check'] = 'Already removed';
                echo "SKIP-CHECK:$productNumber, CODE:{$postProductSearch['code']}, TOTAL: {$postProductSearch['response']['meta']['total']}" . PHP_EOL;
                continue;
            }
            $deleteProductById = $this->shopware->deleteProductById($postProductSearch['response']['data'][0]['id']);
            echo "REMOVE:$productNumber, CODE:{$deleteProductById['code']}" . PHP_EOL;
            $this->log[$productNumber]['removed'] = $deleteProductById['code'];
        }
        $this->saveFile($this->log);
    }
}