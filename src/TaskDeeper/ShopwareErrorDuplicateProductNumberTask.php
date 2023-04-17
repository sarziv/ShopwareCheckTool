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
    public const FILE_NAME = 'ShopwareError';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = Collection::make($this->readFile(self::FILE_NAME))
            ->where('alias', '=', parse_url($this->shopware->configuration->getDomain())['host'])
            ->whereIn('errors.0.code', 'CONTENT__DUPLICATE_PRODUCT_NUMBER')->pluck('errors.0.meta.parameters.number')
            ->unique()
            ->toArray();
        $this->clear();
    }

    public function check(): void
    {
        $this->newGeneralFileLine('Started: ' . self::FILE_NAME);
        foreach ($this->file as $productNumber) {
            $postProductSearch = $this->shopware->postProductSearch($productNumber);
            $this->newFileLineLog("{$productNumber}:{$postProductSearch['code']}");
            if ($postProductSearch['code'] === 200 || $postProductSearch['response']['meta']['total'] <= 0) {
                $this->newFileLineLog("SKIP-CHECK:$productNumber, CODE:{$postProductSearch['code']}, TOTAL: {$postProductSearch['response']['meta']['total']}");
                continue;
            }
            $deleteProductById = $this->shopware->deleteProductById($postProductSearch['response']['data'][0]['id']);
            $this->newFileLineLog("REMOVE:$productNumber, CODE:{$deleteProductById['code']}");
        }
        $this->newGeneralFileLine('Finished ' . self::FILE_NAME);
    }
}