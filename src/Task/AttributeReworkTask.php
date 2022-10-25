<?php

namespace ShopwareCheckTool\Task;

use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class AttributeReworkTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    private array $log = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = Collection::make($this->readFile('AttributeReworkMatch'))->where('configuration_id', '=', $this->shopware->configuration->getId())->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $attribute) {
            echo "Reading {$this->name}: {$attribute['id']}" . PHP_EOL;
            $resp = $this->shopware->getPropertyGroupOptionById($attribute['sw_property_option_id']);
            $this->log[$attribute['id']] = (@$resp['code'] ?: $resp['error']);
        }
        $this->saveFile($this->log);
    }
}