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
    public const FILE_NAME = 'Tag';
    public const TABLE = 'TagMatching';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $collection = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId());
        $offset = $this->clear($collection->count());
        $this->file = $collection->slice($offset)->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $tag) {
            $resp = $this->shopware->getTagById($tag['sw_tag_id']);
            $this->newLogLine(($tag['id']) . ': ' . (@$resp['error'] ?: $resp['code']));
            if (@$resp['code'] === 404) {
                $this->newInvalidLine($tag['id']);
            }
        }
    }
}