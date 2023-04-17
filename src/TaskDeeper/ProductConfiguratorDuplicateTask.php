<?php


namespace ShopwareCheckTool\TaskDeeper;


use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ProductConfiguratorDuplicateTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'ProductConfiguratorTask';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = $this->readFile(self::FILE_NAME)['invalid'];
        $this->clear();
    }

    public function check(): void
    {
        $this->newGeneralFileLine('Started: ' . self::FILE_NAME);
        foreach ($this->file['list'] as $productConfigurator) {
            foreach ($productConfigurator as $productConfiguratorId) {
                $resp = @$this->shopware->deleteProductConfiguratorSettingById($productConfiguratorId)['code'];
                $this->newFileLineLog("$productConfiguratorId :{$resp['code']}");
            }
        }
        $this->newGeneralFileLine('Finished ' . self::FILE_NAME);
    }
}