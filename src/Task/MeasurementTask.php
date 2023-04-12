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
    private array $invalid = [];
    private array $file;
    private array $log = [];
    public const FILE_NAME = 'Measurement';
    public const TABLE = 'MeasurementUnitMatch';
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
        foreach ($this->file as $measurement) {
            $resp = $this->shopware->getUnitById($measurement['sw_unit_id']);
            $this->newFileLineLog(($measurement['id']) . ': ' . (@$resp['code'] ?: $resp['error']));
            if (@$resp['code'] === 404) {
                $this->newFileLine($measurement['id']);
            }
        }
        $this->newFileLineLog('Finished ' . self::FILE_NAME);
    }
}