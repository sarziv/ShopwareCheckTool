<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class MeasurementTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'Measurement';
    public const TABLE = 'MeasurementUnitMatch';
    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $collection = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId());
        $offset = $this->clear($collection->count());
        $this->file = $collection->slice($offset)->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $measurement) {
            $resp = $this->shopware->getUnitById($measurement['sw_unit_id']);
            $this->newLogLine(($measurement['id']) . ': ' . $resp['code']);
            if (@$resp['code'] === 404) {
                $this->newInvalidLine($measurement['id']);
            }
        }
    }
}