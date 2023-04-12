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
        $this->file = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId())->toArray();
        $this->clear();
    }

    public function check(): void
    {
        $this->newFileLineLog('Started: ' . self::FILE_NAME);
        foreach ($this->file as $dynamicProperty) {
            if($dynamicProperty['sw_property_option_id'] === 'null'){
                $resp = $this->shopware->getPropertyGroupById($dynamicProperty['sw_property_id']);
                $this->newFileLineLog($dynamicProperty['sw_property_id'] . ': ' . (@$resp['code'] ?: $resp['error']));
                if (@$resp['code'] === 404) {
                    $this->newFileLine($dynamicProperty['id']);
                }
                continue;
            }
            $resp = $this->shopware->getPropertyGroupOptionById($dynamicProperty['sw_property_option_id']);
            $this->newFileLineLog($dynamicProperty['sw_property_id'] . ': ' . (@$resp['code'] ?: $resp['error']));
            if (@$resp['code'] === 404) {
                $this->newFileLine($dynamicProperty['id']);
            }
        }
        $this->newFileLineLog('Finished ' . self::FILE_NAME);
    }
}