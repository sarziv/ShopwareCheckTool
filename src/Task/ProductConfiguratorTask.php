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
    public const FILE_NAME = 'ProductConfigurator';
    public const TABLE = 'ProductConfigurator';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;

        $this->file = Collection::make($this->readFile(self::FILE_NAME))
            ->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->groupBy('sw_product_id')
            ->toArray();

        $this->attribute = Collection::make($this->readFile('AttributeReworkMatch'))
            ->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->pluck('sw_property_option_id')
            ->toArray();
        $this->clear();
    }

    public function check(): void
    {
        $this->newLogLine('Started ' . self::FILE_NAME);
        foreach ($this->file as $productId => $productConfigurator) {
            $getProductConfigurator = $this->shopware->getConfigurationSettingByProductId($productId);
            $this->newLogLine($productId . ': ' . (@$getProductConfigurator['code'] ?: $getProductConfigurator['error']));
            if (array_key_exists('error', $getProductConfigurator)) {
                continue;
            }
            $configurators = Collection::make($getProductConfigurator['response']['data']);
            $collection = Collection::make($productConfigurator);
            foreach ($configurators as $configurator) {
                $found = $collection->where('sw_product_configurator_id', '=', $configurator['id'])->first();
                $this->newLogLine(($productId . '-' . $configurator['id']) . ': ' . ((int)$found['id'] ? 'Found' : 'Invalid'));
                if (empty($found['id'])) {
                    $this->newInvalidLine($configurator['id']);
                }
            }
            $getProductOptions = $this->shopware->getProductOptionsById($productId);
            $this->newLogLine($productConfigurator['id'] . (@$getProductOptions['error'] ?: $getProductOptions['code']));
            if (array_key_exists('error', $getProductOptions)) {
                continue;
            }
            foreach ($getProductOptions['response']['data'] as $productOption) {
                if (!in_array($productOption['id'], $this->attribute, false)) {
                    $this->newLogLine(($productId - $productOption['id']) . ': ' . 'Product configuration attribute missing.');
                }
            }
        }
        $this->newLogLine('Finished ' . self::FILE_NAME);
    }
}