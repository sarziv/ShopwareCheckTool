<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ManufacturerTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'Manufacturer';
    public const TABLE = 'ManufacturerMatch';
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
        foreach ($this->file as $manufacturer) {
            $resp = $this->shopware->getManufacturerById($manufacturer['sw_manufacturer_id']);
            $this->newLogLine(($manufacturer['id']) . ': ' . (@$resp['error'] ?: $resp['code']));
            if (@$resp['code'] === 404) {
                $this->newInvalidLine($manufacturer['id']);
            }
        }
        $this->newLogLine('Finished ' . self::FILE_NAME);
    }
}