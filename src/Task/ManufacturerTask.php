<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ManufacturerTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    private array $log = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = Collection::make($this->readFile('Manufacturer'))->where('configuration_id', '=', $this->shopware->configuration->getId())->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $delivery) {
            echo "Reading {$this->name}: {$delivery['id']}" . PHP_EOL;
            $resp = $this->shopware->getManufacturerById($delivery['sw_manufacturer_id']);
            $this->log[$delivery['id']] = (@$resp['code'] ?: $resp['error']);
        }
        $this->saveFile($this->log);
    }
}