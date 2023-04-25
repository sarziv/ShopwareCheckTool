<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class CategoryTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'Category';
    public const TABLE = 'CategoryMatch';
    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $offset = $this->clear();
        $this->file = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId())->slice($offset)->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $category) {
            echo "Reading {$this->name}: {$category['id']}" . PHP_EOL;
            $resp = $this->shopware->getCategoryById($category['sw_category_id']);
            $this->newLogLine(($category['id']) . ': ' . (@$resp['error'] ?: $resp['code']));
            if (@$resp['code'] === 404) {
                $this->newInvalidLine($category['id']);
            }
        }
    }
}