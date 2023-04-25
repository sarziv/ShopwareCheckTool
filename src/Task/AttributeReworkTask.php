<?php

namespace ShopwareCheckTool\Task;

use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class AttributeReworkTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'AttributeRework';
    public const TABLE = 'AttributeReworkMatch';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $offset = $this->clear();
        $this->file = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId())->slice($offset)->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $attribute) {
            $resp = $this->shopware->getPropertyGroupOptionById($attribute['sw_property_option_id']);
            $this->newLogLine(($attribute['id']) . ': ' . (@$resp['error'] ?: $resp['code']));
            if (@$resp['code'] === 404) {
                $this->newInvalidLine($attribute['id']);
            }
        }
    }
}