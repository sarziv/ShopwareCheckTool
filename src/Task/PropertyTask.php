<?php

namespace ShopwareCheckTool\Task;

use ReflectionClass;
use ShopwareCheckTool\Requests\Shopware;

class PropertyTask
{
    private Shopware $shopware;
    private string $name;
    private array $file = [];
    private array $log = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $file = __DIR__ . '/../Logs/Downloaded/Property.json';
        if (file_exists($file)) {
            $this->file = json_decode(file_get_contents($file), true);
        }
        if (!$this->file) {
            echo "{$this->name} file is empty. Task skipped." . PHP_EOL;
        }
    }

    public function check(): void
    {
        $id = $this->shopware->configuration->getId();
        foreach ($this->file as $attribute) {
            $temp = [];
            echo "Reading {$this->name}: {$attribute['id']}" . PHP_EOL;
            if ($attribute['configuration_id'] !== $id) {
                continue;
            }
            $resp = $this->shopware->getPropertyGroupById($attribute['sw_property_id']);
            $temp[$attribute['sw_property_id']] = @$resp['code'] ?: $resp['error'];
            foreach ($attribute['sw_property_options'] as $sw_property_option) {
                $resp = $this->shopware->getPropertyGroupOptionById($sw_property_option);
                $temp['sw_property_options'][$sw_property_option] = (@$resp['code'] ?: $resp['error']);
            }
            $this->log[$attribute['id']] = $temp;
        }
        $file = __DIR__ . "/../Logs/Completed/{$this->name}_configuration_{$this->shopware->configuration->getId()}.json";
        if (file_exists($file)) {
            unlink($file);
            echo "Generating new file." . PHP_EOL;
        }
        file_put_contents($file, json_encode($this->log, JSON_PRETTY_PRINT));
        echo "{$this->name} completed." . PHP_EOL;
    }
}