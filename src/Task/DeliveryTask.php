<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class DeliveryTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'Delivery';
    public const TABLE = 'DeliveryTimeMatch';

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
        foreach ($this->file as $delivery) {
            echo "Reading {$this->name}: {$delivery['id']}" . PHP_EOL;
            $resp = $this->shopware->getDeliveryById($delivery['sw_delivery_date_id']);

            $this->newFileLineLog(($delivery['id']) . ': ' . ($resp['code'] ?: $resp['error']));
            if ($resp['code'] === 404) {
                $this->newFileLine($delivery['id']);
            }
        }
        $this->newFileLineLog('Finished ' . self::FILE_NAME);
    }
}