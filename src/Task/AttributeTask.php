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
    private array $invalid = [];
    private array $file;
    private array $log = [];
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
        $this->newFileLineLog('Started: ' . self::FILE_NAME);
        foreach ($this->file as $attribute) {
            $resp = $this->shopware->getPropertyGroupById($attribute['sw_property_id']);
            $this->newFileLineLog(($attribute['sw_property_id']) . ': ' . ($resp['code'] ?: $resp['error']));
            foreach ($attribute['sw_property_options'] as $sw_property_option) {
                $resp = $this->shopware->getPropertyGroupOptionById($sw_property_option);
                if ($resp['code'] === 404) {
                    $this->newFileLine($sw_property_option);
                }
            }
        }
        $this->newFileLineLog('Finished: ' . self::FILE_NAME);
    }
}