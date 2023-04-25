<?php

namespace ShopwareCheckTool\Task;

use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class PropertyTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'Property';
    public const TABLE = 'PropertyMatch';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $offset = $this->clear();
        $this->file = Collection::make($this->readFile('Property'))->where('configuration_id', '=', $this->shopware->configuration->getId())->slice($offset)->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $property) {
            $resp = $this->shopware->getPropertyGroupById($property['sw_property_id']);
            $this->newLogLine(($property['id']) . ': ' . (@$resp['error'] ?: $resp['code']));
            if (@$resp['code'] === 404) {
                $this->newInvalidLine($property['id']);
            }
        }
    }
}