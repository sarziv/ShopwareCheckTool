<?php


namespace ShopwareCheckTool\Task;


use ReflectionClass;
use ShopwareCheckTool\Requests\Shopware;

class CategoryTask
{
    private Shopware $shopware;
    private string $name;
    private array $file = [];
    private array $log = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $file = __DIR__ . '/../Logs/Downloaded/Category.json';
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
        foreach ($this->file as $category) {
            echo "Reading {$this->name}: {$category['id']}" . PHP_EOL;
            if ($category['configuration_id'] !== $id) {
                continue;
            }
            $resp = $this->shopware->getCategoryById($category['sw_category_id']);
            $this->log[$category['id']] = (@$resp['code'] ?: $resp['error']);
        }
        $file = __DIR__ . "/../Logs/Completed/{$this->name}_configuration_{$this->shopware->configuration->getId()}.json";
        if (file_exists($file)) {
            unlink($file);
            echo "Generating new file." . PHP_EOL;
        }
        file_put_contents($file, json_encode($this->log,JSON_PRETTY_PRINT));
        echo "{$this->name} completed." . PHP_EOL;
    }
}