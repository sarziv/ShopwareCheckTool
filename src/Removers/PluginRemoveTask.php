<?php


namespace ShopwareCheckTool\Removers;


use Exception;
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
use ShopwareCheckTool\Task\PropertyDynamicTask;
use ShopwareCheckTool\Task\PropertyTask;
use ShopwareCheckTool\Task\TagTask;

class PluginRemoveTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $tasks;

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->useCompletedInvalidFolder();
        $this->tasks = $this->getFiles();
    }

    public function check(Marketplace $marketplace): void
    {
        $this->newGeneralLine('Started local removing task.');
        $pMarketplace = new Plentymarket($marketplace);
        foreach ($this->tasks as $file) {
            $table = self::getTable($file);
            if (!$table) {
                continue;
            }

            foreach ($this->readInvalidFile($file) ?: [] as $id) {
                $id = (int)$id;
                if (!$id) {
                    continue;
                }
                $resp = $pMarketplace->deleteFromPlugin($table, $id);
                $this->newLogLine("Removing:$table-$id:{$resp['code']}");
                sleep(1);
            }
        }
        $this->newGeneralLine('Finished local removing task.');
    }

    private static function getTable(string $file): string
    {
        $list = [
            'AttributeReworkTask.log' => AttributeReworkTask::TABLE,
            'AttributeTask.log' => AttributeTask::TABLE,
            'CategoryTask.log' => CategoryTask::TABLE,
            'DeliveryTask.log' => DeliveryTask::TABLE,
            'ImagesTask.log' => ImagesTask::TABLE,
            'ImageDeepTask.log' => ImagesTask::TABLE,
            'ManufacturerTask.log' => ManufacturerTask::TABLE,
            'MeasurementTask.log' => MeasurementTask::TABLE,
            'ProductConfiguratorTask.log' => ProductConfiguratorTask::TABLE,
            'ProductVisibilityTask.log' => ProductVisibilityTask::TABLE,
            'PropertyTask.log' => PropertyTask::TABLE,
            'PropertyDynamicTask.log' => PropertyDynamicTask::TABLE,
            'TagTask.log' => TagTask::TABLE
        ];
        return @$list[$file] ?: '';
    }
}