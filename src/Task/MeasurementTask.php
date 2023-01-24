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
    }

    public function check(): void
    {
        foreach ($this->file as $delivery) {
            echo "Reading {$this->name}: {$delivery['id']}" . PHP_EOL;
            $resp = $this->shopware->getUnitById($delivery['sw_unit_id']);
            $this->log[$delivery['id']] = (@$resp['code'] ?: $resp['error']);
            if (@$resp['code'] !== 200) {
                $this->invalid[] = $delivery['id'];
            }
        }
        $this->log['invalid']['count'] = count($this->invalid);
        $this->log['invalid']['list'] = $this->invalid;
        $this->saveFile($this->log);
    }
}