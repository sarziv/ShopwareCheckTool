<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class TagTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    private array $log = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = Collection::make($this->readFile('Tag'))->where('configuration_id', '=', $this->shopware->configuration->getId())->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $tag) {
            echo "Reading {$this->name}: {$tag['id']}" . PHP_EOL;
            $resp = $this->shopware->getTagById($tag['sw_tag_id']);
            $this->log[$tag['id']] = (@$resp['code'] ?: $resp['error']);
        }
        $this->saveFile($this->log);
    }
}