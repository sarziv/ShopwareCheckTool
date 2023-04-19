<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ProductVisibilityTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'ProductVisibility';
    public const TABLE = 'ProductVisibility';
    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = Collection::make($this->readFile('ProductVisibility'))
            ->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->toArray();
        $this->clear();
    }

    public function check(): void
    {
        $this->newLogLine('Started ' . self::FILE_NAME);
        foreach ($this->file as $productVisibility) {
            $resp = $this->shopware->getProductVisibilityById($productVisibility['sw_visibility_id']);
            $this->newLogLine(($productVisibility['id']) . ': ' . (@$resp['error'] ?: $resp['code']));
            if (@$resp['code'] === 404) {
                $this->newInvalidLine($productVisibility['id']);
            }
        }
        $this->newLogLine('Finished ' . self::FILE_NAME);
    }
}