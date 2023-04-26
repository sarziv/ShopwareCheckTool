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
        $collection = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId());
        $offset = $this->clear($collection->count());
        $this->file = $collection->slice($offset)->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $configurator) {
            $resp = $this->shopware->getProductConfiguratorSettingById($configurator['sw_product_configurator_id']);
            $this->newLogLine("{$configurator['id']}: ".(@$resp['code'] ?: $resp['error']));
            if (@$resp['code'] === 404) {
                $this->newInvalidLine($configurator['id']);
            }
        }
    }
}