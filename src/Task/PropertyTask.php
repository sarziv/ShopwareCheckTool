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
        $this->file = Collection::make($this->readFile('Property'))->where('configuration_id', '=', $this->shopware->configuration->getId())->toArray();
        $this->clear();
    }

    public function check(): void
    {
        $this->newFileLineLog('Started: ' . self::FILE_NAME);
        foreach ($this->file as $property) {
            $resp = $this->shopware->getPropertyGroupById($property['sw_property_id']);
            $this->newFileLineLog(($property['id']) . ': ' . (@$resp['code'] ?: $resp['error']));
            if (@$resp['code'] === 404) {
                $this->newFileLine($property['id']);
            }
            foreach ($property['sw_property_options'] as $sw_property_option) {
                $resp = $this->shopware->getPropertyGroupOptionById($sw_property_option);
                $this->newFileLineLog(("{$property['id']}-$sw_property_option: " . (@$resp['code'] ?: $resp['error'])));
            }
        }
        $this->newFileLineLog('Finished ' . self::FILE_NAME);
    }
}