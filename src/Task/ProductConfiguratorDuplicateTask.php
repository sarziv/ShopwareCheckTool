<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\Requests\Shopware;

class ProductConfiguratorDuplicateTask
{
    private int $count = 1;
    private Shopware $shopware;
    private string $name;
    private array $file = [];
    private int $total;

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/ProductConfiguratorTask.json";
        if (file_exists($file)) {
            $this->file = json_decode(file_get_contents($file), true)['invalid'];
        }
        if (!$this->file) {
            echo "{$this->name} file is empty. Task skipped." . PHP_EOL;
        }
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