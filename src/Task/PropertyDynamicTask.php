<?php

namespace ShopwareCheckTool\Task;

use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class PropertyDynamicTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $invalid = [];
    private array $file;
    private array $log = [];
    public const FILE_NAME = 'PropertyDynamic';
    public const TABLE = 'PropertyDynamicMatch';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $offset = $this->clear();
        $this->file = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId())->slice($offset)->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $dynamicProperty) {
            if($dynamicProperty['sw_property_option_id'] === 'null'){
                $resp = $this->shopware->getPropertyGroupById($dynamicProperty['sw_property_id']);
                $this->newLogLine($dynamicProperty['sw_property_id'] . ': ' . (@$resp['error'] ?: $resp['code']));
                if (@$resp['code'] === 404) {
                    $this->newInvalidLine($dynamicProperty['id']);
                }
                continue;
            }
            $resp = $this->shopware->getPropertyGroupOptionById($dynamicProperty['sw_property_option_id']);
            $this->newLogLine($dynamicProperty['sw_property_id'] . ': ' . (@$resp['error'] ?: $resp['code']));
            if (@$resp['code'] === 404) {
                $this->newInvalidLine($dynamicProperty['id']);
            }
        }
    }
}