<?php

namespace ShopwareCheckTool\Task;

use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class AttributeTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'Attribute';
    public const TABLE = 'AttributeMatch';
    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId())->toArray();
        $this->clear();
    }

    public function check(): void
    {
        $this->newLogLine('Started ' . self::FILE_NAME);
        foreach ($this->file as $attribute) {
            $resp = $this->shopware->getPropertyGroupById($attribute['sw_property_id']);
            $this->newLogLine(($attribute['sw_property_id']) . ': ' . (@$resp['error'] ?: $resp['code']));
            foreach ($attribute['sw_property_options'] as $sw_property_option) {
                $resp = $this->shopware->getPropertyGroupOptionById($sw_property_option);
                if (@$resp['code'] === 404) {
                    $this->newInvalidLine($sw_property_option);
                }
            }
        }
        $this->newLogLine('Finished: ' . self::FILE_NAME);
    }
}