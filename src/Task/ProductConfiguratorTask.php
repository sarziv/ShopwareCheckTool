<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ProductConfiguratorTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    private array $attribute;
    private array $log = [];
    private array $invalid = [];
    private int $total;
    private int $count = 1;

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;

        $this->file = Collection::make($this->readFile('ProductConfigurator'))
            ->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->groupBy('sw_product_id')
            ->toArray();

        $this->attribute = Collection::make($this->readFile('AttributeReworkMatch'))
            ->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->pluck('sw_property_option_id')
            ->toArray();

        $this->total = count($this->file);
    }

    public function check(): void
    {
        foreach ($this->file as $productId => $productConfigurator) {
            echo $this->name . ':' . $this->count++ . '/' . $this->total . PHP_EOL;
            $getProductConfigurator = $this->shopware->getConfigurationSettingByProductId($productId);
            $this->log[$productId]['product'] = (@$getProductConfigurator['code'] ?: $getProductConfigurator['error']);
            if (array_key_exists('error', $getProductConfigurator)) {
                continue;
            }
            $configurators = Collection::make($getProductConfigurator['response']['data']);
            $collection = Collection::make($productConfigurator);
            foreach ($configurators as $configurator) {
                $found = $collection->where('sw_product_configurator_id', '=', $configurator['id'])->first();
                $this->log[$productId]['configurators'][$configurator['id']] = (@(int)$found['id'] ? 'Found' : 'Invalid');
                if (empty($found['id'])) {
                    $this->invalid[$productId]['configurator'][] = $configurator['id'];
                }
            }
            $getProductOptions = $this->shopware->getProductOptionsById($productId);
            $this->log[$productId]['product'] = (@$getProductOptions['code'] ?: $getProductOptions['error']);
            if (array_key_exists('error', $getProductOptions)) {
                continue;
            }
            foreach ($getProductOptions['response']['data'] as $productOption) {
                if (!in_array($productOption['id'], $this->attribute, false)) {
                    $this->log[$productId]['options'][$productOption['id']] = 'Invalid';
                }
                $this->log[$productId]['options'][$productOption['id']] = 'Found';
                if (empty($found['id'])) {
                    $this->invalid[$productId]['option'][] = $productOption['id'];
                }
            }
        }
        $this->log['invalid']['count'] = count($this->invalid);
        $this->log['invalid']['configuration'] = $this->invalid;
        $this->saveFile($this->log);
    }
}