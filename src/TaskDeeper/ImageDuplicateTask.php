<?php


namespace ShopwareCheckTool\TaskDeeper;


use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ImageDuplicateTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'ImagesTask';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->useCompletedFolder();
        $this->file = $this->readFile(self::FILE_NAME)['invalid'];
    }

    public function check(): void
    {
        $this->newGeneralFileLine('Started: ' . self::FILE_NAME);
        foreach ($this->file['list'] as $productMedia) {
            foreach ($productMedia as $mediaId) {
                $resp = @$this->shopware->deleteProductMediaById($mediaId);
                $this->newFileLineLog("$mediaId : ".($resp['error'] ?: $resp['code']));
            }
        }
        $this->newGeneralFileLine('Finished ' . self::FILE_NAME);
    }
}