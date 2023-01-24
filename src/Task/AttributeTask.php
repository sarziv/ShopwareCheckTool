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
    }

    public function check(): void
    {
        foreach ($this->file as $attribute) {
            $temp = [];
            echo "Reading {$this->name}: {$attribute['id']}" . PHP_EOL;
            $resp = $this->shopware->getPropertyGroupById($attribute['sw_property_id']);
            $temp[$attribute['sw_property_id']] = @$resp['code'] ?: $resp['error'];
            foreach ($attribute['sw_property_options'] as $sw_property_option) {
                $resp = $this->shopware->getPropertyGroupOptionById($sw_property_option);
                $temp['sw_property_options'][$sw_property_option] = (@$resp['code'] ?: $resp['error']);
                if (@$resp['code'] !== 200) {
                    $this->invalid[] = $sw_property_option;
                }
            }
            $this->log[$attribute['id']] = $temp;
        }
        $this->log['invalid']['count'] = count($this->invalid);
        $this->log['invalid']['list'] = $this->invalid;
        $this->saveFile($this->log);
    }
}