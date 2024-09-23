<?php

namespace ShopwareCheckTool\TaskDeeper;

use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class AdvancedPriceDeepTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'AdvancedPrice';
    public const TABLE = 'AdvancedPriceMatch';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $collection = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->groupBy('sw_product_id');
        $offset = $this->clear($collection->count());
        $this->file = $collection->slice($offset)->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $advancedPrice) {
            $advancedPrices = $this->shopware->getProductCrossSellingById($advancedPrice[0]['sw_product_id'])['response']['data'];
            $ci = count($advancedPrice);
            $cs = count($advancedPrices);
            echo 'INTERNAL:' . $ci . 'SHOPWARE:' . $cs . PHP_EOL;
            if ($cs > $ci) {
                echo $advancedPrices[0]['sw_product_id'] . PHP_EOL;
            }
        }
    }
}