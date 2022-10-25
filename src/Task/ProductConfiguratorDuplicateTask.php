<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ProductConfiguratorDuplicateTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private int $count = 1;
    private array $file;
    private int $total;

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = $this->readFile('ProductConfiguratorTask')['invalid'];
        $this->total = count($this->file['configuration']);
    }

    public function check(): void
    {
        foreach ($this->file['configuration'] as $productConfigurator) {
            echo $this->name . ': ' . $this->count++ . '/' . $this->total . PHP_EOL;
            foreach ($productConfigurator as $productConfiguratorId) {
                $resp = @$this->shopware->deleteProductConfiguratorSettingById($productConfiguratorId)['code'] ?: 'error';
                echo "$productConfiguratorId :{$resp}" . PHP_EOL;
            }
            die();
        }
    }
}