<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\Requests\Shopware;

class ProductVisibilityTask
{
    private int $count = 1;
    private int $total;
    private Shopware $shopware;
    private string $name;
    private array $file = [];
    private array $log = [];
    private array $invalid = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $file = __DIR__ . '/../Logs/Downloaded/ProductVisibility.json';
        if (file_exists($file)) {
            $this->file = Collection::make(json_decode(file_get_contents($file), true))
                ->where('configuration_id', '=', $this->shopware->configuration->getId())
                ->toArray();
        }
        if (!$this->file) {
            echo "{$this->name} file is empty. Task skipped." . PHP_EOL;
        }
        $this->total = count($this->file);
    }

    public function check(): void
    {
        foreach ($this->file as $productVisibility) {
            echo $this->name . ':' . $this->count++ . '/' . $this->total . PHP_EOL;
            $getProductVisibility = $this->shopware->getProductVisibilityById($productVisibility['sw_visibility_id']);
            $this->log[$productVisibility['id']]['visibility'] = (@$getProductVisibility['code'] ?: $getProductVisibility['error']);
            if(@$getProductVisibility['code'] != 200){
                $this->invalid[] = $productVisibility['id'];
            }
        }
        $this->log['invalid']['count'] = count($this->invalid);
        $this->log['invalid']['visibility'] = $this->invalid;
        $this->toFile();
    }

    private function toFile(): void
    {
        $file = __DIR__ . "/../Logs/Completed/{$this->shopware->configuration->getPath()}/$this->name.json";
        if (file_exists($file)) {
            unlink($file);
        }
        file_put_contents($file, json_encode($this->log, JSON_PRETTY_PRINT));
        echo "{$this->name} completed." . PHP_EOL;
    }
}