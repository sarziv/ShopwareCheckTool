<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ProductVisibilityTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    private array $invalid = [];
    private array $log = [];
    private int $total;
    private int $count = 1;

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = Collection::make($this->readFile('ProductVisibility'))->where('configuration_id', '=', $this->shopware->configuration->getId())->toArray();
        $this->total = count($this->file);
    }

    public function check(): void
    {
        foreach ($this->file as $productVisibility) {
            echo $this->name . ':' . $this->count++ . '/' . $this->total . PHP_EOL;
            $getProductVisibility = $this->shopware->getProductVisibilityById($productVisibility['sw_visibility_id']);
            $this->log[$productVisibility['id']]['visibility'] = (@$getProductVisibility['code'] ?: $getProductVisibility['error']);
            if (@$getProductVisibility['code'] != 200) {
                $this->invalid[] = $productVisibility['id'];
            }
        }
        $this->log['invalid']['count'] = count($this->invalid);
        $this->log['invalid']['visibility'] = $this->invalid;
        $this->saveFile($this->log);
    }
}