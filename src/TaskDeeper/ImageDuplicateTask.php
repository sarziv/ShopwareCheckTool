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
    private array $log = [];
    private int $count = 1;
    private int $total;

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->useCompletedFolder();
        $this->file = $this->readFile('ImagesTask')['invalid'];
        $this->total = count($this->file['list']);
    }

    public function check(): void
    {
        foreach ($this->file['list'] as $productMedia) {
            echo $this->name . ': ' . $this->count++ . '/' . $this->total . PHP_EOL;
            foreach ($productMedia as $mediaId) {
                $resp = @$this->shopware->deleteProductMediaById($mediaId)['code'] ?: 'error';
                echo "$mediaId :{$resp}" . PHP_EOL;
                $this->log[] = ["$mediaId :{$resp}"];
            }
        }
        $this->saveFile($this->log);
        echo "{$this->name} completed." . PHP_EOL;
    }
}