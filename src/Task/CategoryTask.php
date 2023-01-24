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
    private array $invalid = [];
    private array $file;
    private array $log = [];
    public const FILE_NAME = 'Category';
    public const TABLE = 'CategoryMatch';
    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId())->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $category) {
            echo "Reading {$this->name}: {$category['id']}" . PHP_EOL;
            $resp = $this->shopware->getCategoryById($category['sw_category_id']);
            $this->log[$category['id']] = (@$resp['code'] ?: $resp['error']);
            if (@$resp['code'] !== 200) {
                $this->invalid[] = $category['id'];
            }
        }
        $this->log['invalid']['count'] = count($this->invalid);
        $this->log['invalid']['list'] = $this->invalid;
        $this->saveFile($this->log);
    }
}