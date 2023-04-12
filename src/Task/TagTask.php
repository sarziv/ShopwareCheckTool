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
        $this->file = Collection::make($this->readFile('Tag'))->where('configuration_id', '=', $this->shopware->configuration->getId())->toArray();
        $this->clear();
    }

    public function check(): void
    {
        $this->newFileLineLog('Started: ' . self::FILE_NAME);
        foreach ($this->file as $tag) {
            $resp = $this->shopware->getTagById($tag['sw_tag_id']);
            $this->newFileLineLog(($tag['id']) . ': ' . (@$resp['code'] ?: $resp['error']));
            if (@$resp['code'] === 404) {
                $this->newFileLine($tag['id']);
            }
        }
        $this->newFileLineLog('Finished ' . self::FILE_NAME);
    }
}