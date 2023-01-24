<?php


namespace ShopwareCheckTool\Removers;


use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Models\Marketplace;
use ShopwareCheckTool\Requests\Plentymarket;
use ShopwareCheckTool\Requests\Shopware;
use ShopwareCheckTool\Task\AttributeReworkTask;
use ShopwareCheckTool\Task\AttributeTask;
use ShopwareCheckTool\Task\CategoryTask;
use ShopwareCheckTool\Task\DeliveryTask;
use ShopwareCheckTool\Task\ImagesTask;
use ShopwareCheckTool\Task\ManufacturerTask;
use ShopwareCheckTool\Task\MeasurementTask;
use ShopwareCheckTool\Task\ProductConfiguratorTask;
use ShopwareCheckTool\Task\ProductVisibilityTask;
use ShopwareCheckTool\Task\PropertyTask;
use ShopwareCheckTool\Task\TagTask;

class PluginRemoveTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $log = [];
    private array $tasks;

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->useCompletedFolder();
        $this->tasks = $this->getFiles();
    }

    public function check(Marketplace $marketplace): void
    {
        $plentymarket = new Plentymarket($marketplace);
        foreach ($this->tasks as $file) {
            $table = self::getTable($file);
            if(!$table){
                continue;
            }
            foreach ($this->readFile($file, false)['invalid']['list'] as $id) {
                echo "Reading {$this->name}: $id" . PHP_EOL;
                sleep(1);
                $resp = $plentymarket->deleteFromPlugin($table, $id);
                echo "{$this->name}-$table-$id, CODE:{$resp['code']}" . PHP_EOL;
                $this->log[$table][$id] = 'REMOVED-'.(string)($resp['code']);
            }
        }
        $this->saveFile($this->log);
    }

    private static function getTable($file): string
    {
        $list = [
            'AttributeReworkTask.json' => AttributeReworkTask::TABLE,
            'AttributeTask.json' => AttributeTask::TABLE,
            'CategoryTask.json' => CategoryTask::TABLE,
            'DeliveryTask.json' => DeliveryTask::TABLE,
            'ImagesTask.json' => ImagesTask::TABLE,
            'ManufacturerTask.json' => ManufacturerTask::TABLE,
            'MeasurementTask.json' => MeasurementTask::TABLE,
            'ProductConfiguratorTask.json' => ProductConfiguratorTask::TABLE,
            'ProductVisibilityTask.json' => ProductVisibilityTask::TABLE,
            'PropertyTask.json' => PropertyTask::TABLE,
            'TagTask.json' => TagTask::TABLE
        ];
        return @$list[$file] ?: '';
    }
}