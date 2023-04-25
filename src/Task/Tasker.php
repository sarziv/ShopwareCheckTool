<?php

namespace ShopwareCheckTool\Task;

use RuntimeException;
use ShopwareCheckTool\Models\Marketplace;
use ShopwareCheckTool\Removers\PluginRemoveTask;
use ShopwareCheckTool\Requests\Shopware;
use ShopwareCheckTool\TaskDeeper\ImageDeepTask;
use ShopwareCheckTool\TaskDeeper\ShopwareErrorDuplicateProductNumberTask;

class Tasker
{
    private Shopware $shopware;

    public function __construct(Shopware $shopware)
    {
        $this->shopware = $shopware;
        $dir = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/Invalid";
        if (!file_exists($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
    }

    public function attribute(): Tasker
    {
        (new AttributeTask($this->shopware))->check();
        return $this;
    }

    public function attributeRework(): Tasker
    {
        (new AttributeReworkTask($this->shopware))->check();
        return $this;
    }

    public function category(): Tasker
    {
        (new CategoryTask($this->shopware))->check();
        return $this;
    }

    public function delivery(): Tasker
    {
        (new DeliveryTask($this->shopware))->check();
        return $this;
    }

    public function manufacturer(): Tasker
    {
        (new ManufacturerTask($this->shopware))->check();
        return $this;
    }

    public function measurement(): Tasker
    {
        (new MeasurementTask($this->shopware))->check();
        return $this;
    }

    public function property(): Tasker
    {
        (new PropertyTask($this->shopware))->check();
        return $this;
    }

    public function propertyDynamic(): Tasker
    {
        (new PropertyDynamicTask($this->shopware))->check();
        return $this;
    }

    public function tag(): Tasker
    {
        (new TagTask($this->shopware))->check();
        return $this;
    }

    public function allImages(bool $folderIsThumbnails = false): Tasker
    {
        (new ImageDeepTask($this->shopware))->check($folderIsThumbnails);
        return $this;
    }

    public function productVisibility(): Tasker
    {
        (new ProductVisibilityTask($this->shopware))->check();
        return $this;
    }

    public function productConfigurator(): Tasker
    {
        (new ProductConfiguratorTask($this->shopware))->check();
        return $this;
    }

    public function shopwareErrorDuplicateProductNumber(): Tasker
    {
        (new ShopwareErrorDuplicateProductNumberTask($this->shopware))->check();
        return $this;
    }

    public function remove(Marketplace $marketplace): Tasker
    {
        (new PluginRemoveTask($this->shopware))->check($marketplace);
        return $this;
    }

    public function all(): Tasker
    {
        (new AttributeTask($this->shopware))->check();
        (new AttributeReworkTask($this->shopware))->check();
        (new CategoryTask($this->shopware))->check();
        (new DeliveryTask($this->shopware))->check();
        (new ManufacturerTask($this->shopware))->check();
        (new MeasurementTask($this->shopware))->check();
        (new PropertyTask($this->shopware))->check();
        (new PropertyDynamicTask($this->shopware))->check();
        (new TagTask($this->shopware))->check();
        (new ProductVisibilityTask($this->shopware))->check();
        return $this;
    }
}
