<?php


namespace ShopwareCheckTool\Task;


use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Models\Marketplace;
use ShopwareCheckTool\Requests\Plentymarket;
use ShopwareCheckTool\Requests\Shopware;

class CustomPluginRemoveTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $log = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->useCompletedFolder();
    }

    public function check(array $payload, Marketplace $marketplace, string $table, string $whereKey = 'id'): void
    {
        $plentymarket = new Plentymarket($marketplace);
        foreach ($payload as $value) {
            echo "Reading {$this->name}: $value" . PHP_EOL;
            sleep(1);
            $resp = $plentymarket->deleteFromPlugin($table, $whereKey, $value);
            echo "{$this->name}-ID:$value, CODE:{$resp['code']}" . PHP_EOL;
            $this->log[$value] = (string)($resp['code']);
        }
        $this->saveFile($this->log);
    }
}