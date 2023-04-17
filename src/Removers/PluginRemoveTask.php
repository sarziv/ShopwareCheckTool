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
use ShopwareCheckTool\Task\PropertyDynamicTask;
use ShopwareCheckTool\Task\PropertyTask;
use ShopwareCheckTool\Task\TagTask;

class PluginRemoveTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $tasks;
    public const FILE_NAME = 'PluginRemoveTask';
    private const LIST = [
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
        'PropertyDynamicTask.json' => PropertyDynamicTask::TABLE,
        'TagTask.json' => TagTask::TABLE
    ];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->useCompletedFolder();
        $this->tasks = $this->getFiles();
    }

    public function check(Marketplace $marketplace): void
    {
        $this->newGeneralFileLine('Started: ' . self::FILE_NAME);
        $pMarketplace = new Plentymarket($marketplace);
        foreach ($this->tasks as $file) {
            $table = self::LIST[$file];
            if (!$table) {
                continue;
            }
            foreach ($this->readFile($file, false)['invalid']['list'] as $id) {
                sleep(2);
                $resp = $pMarketplace->deleteFromPlugin($table, $id);
                $this->newFileLineLog("{$this->name}-$table-$id: " . ($resp['error'] ?: $resp['code']));
            }
        }
        $this->newGeneralFileLine('Started: ' . self::FILE_NAME);
    }
}