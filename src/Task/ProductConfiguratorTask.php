<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\Requests\Shopware;

class ProductConfiguratorTask
{
    private int $count = 1;
    private int $total;
    private Shopware $shopware;
    private string $name;
    private array $file = [];
    private array $attribute = [];
    private array $log = [];
    private array $invalid = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $file = __DIR__ . '/../Logs/Downloaded/ProductConfigurator.json';
        if (file_exists($file)) {
            $this->file = Collection::make(json_decode(file_get_contents($file), true))
                ->where('configuration_id', '=', $this->shopware->configuration->getId())
                ->groupBy('sw_product_id')
                ->toArray();
        }
        $attribute = __DIR__ . '/../Logs/Downloaded/AttributeReworkMatch.json';
        if (file_exists($attribute)) {
            $this->attribute = Collection::make(json_decode(file_get_contents($attribute), true))
                ->where('configuration_id', '=', $this->shopware->configuration->getId())
                ->pluck('sw_property_option_id')
                ->toArray();
        }
        if (!$this->file) {
            echo "{$this->name} file is empty. Task skipped." . PHP_EOL;
        }
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