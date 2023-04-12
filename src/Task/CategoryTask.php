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
        $this->file = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId())->toArray();
        $this->clear();
    }

    public function check(): void
    {
        $this->newFileLineLog('Started: ' . self::FILE_NAME);
        foreach ($this->file as $category) {
            echo "Reading {$this->name}: {$category['id']}" . PHP_EOL;
            $resp = $this->shopware->getCategoryById($category['sw_category_id']);
            $this->newFileLineLog(($category['id']) . ': ' . (@$resp['code'] ?: $resp['error']));
            if (@$resp['code'] === 404) {
                $this->newFileLine($category['id']);
            }
        }
        $this->newFileLineLog('Finished ' . self::FILE_NAME);
    }
}