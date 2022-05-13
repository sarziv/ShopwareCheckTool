<?php

namespace ShopwareCheckTool\Task;

use RuntimeException;
use ShopwareCheckTool\Requests\Shopware;
use ShopwareCheckTool\Traits\Timable;

class Tasker
{
    use Timable;
    private Shopware $shopware;

    public function __construct(Shopware $shopware)
    {
        $this->shopware = $shopware;
        $this->shopware->configuration->setPath(parse_url($shopware->configuration->getDomain())['host']);
        $dir = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}";
        if (!file_exists($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
    }

    public function attribute(): Tasker
    {
        $this->start(__FUNCTION__);
        (new AttributeTask($this->shopware))->check();
        $this->end(__FUNCTION__);
        return $this;
    }

    public function category(): Tasker
    {
        $this->start(__FUNCTION__);
        (new CategoryTask($this->shopware))->check();
        $this->end(__FUNCTION__);
        return $this;
    }

    public function delivery(): Tasker
    {
        $this->start(__FUNCTION__);
        (new DeliveryTask($this->shopware))->check();
        $this->end(__FUNCTION__);
        return $this;
    }

    public function manufacturer(): Tasker
    {
        $this->start(__FUNCTION__);
        (new ManufacturerTask($this->shopware))->check();
        $this->end(__FUNCTION__);
        return $this;
    }

    public function property(): Tasker
    {
        $this->start(__FUNCTION__);
        (new PropertyTask($this->shopware))->check();
        $this->end(__FUNCTION__);
        return $this;
    }

    public function tag(): Tasker
    {
        $this->start(__FUNCTION__);
        (new TagTask($this->shopware))->check();
        $this->end(__FUNCTION__);
        return $this;
    }

    public function images(): Tasker
    {
        $this->start(__FUNCTION__);
        (new ImagesTask($this->shopware))->check();
        $this->end(__FUNCTION__);
        return $this;
    }

    public function all(): Tasker
    {
        (new AttributeTask($this->shopware))->check();
        (new CategoryTask($this->shopware))->check();
        (new DeliveryTask($this->shopware))->check();
        (new ManufacturerTask($this->shopware))->check();
        (new PropertyTask($this->shopware))->check();
        (new TagTask($this->shopware))->check();
        (new ImagesTask($this->shopware))->check();
        return $this;
    }
}