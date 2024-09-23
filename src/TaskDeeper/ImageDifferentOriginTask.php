<?php

namespace ShopwareCheckTool\TaskDeeper;

use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ImageDifferentOriginTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'Images';
    public const TABLE = 'VariationDifferentOrigin';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $collection = Collection::make($this->readFile(self::FILE_NAME))
            ->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->where('is_uploaded', '=', '1')->groupBy('sw_product_id');
        $offset = $this->clear($collection->count());
        $this->file = $collection->slice($offset)->toArray();
    }

    public function check(): void
    {
        $cp = $cm = 0;
        foreach ($this->file as $group) {
            $getProductMedia = $this->shopware->getMediaByProductId($group[0]['sw_product_id']);
            if ($getProductMedia['code'] != 200) {
                continue;
            }
            $images = array_column($group, 'sw_product_media_id');
            $ids = Collection::make($getProductMedia['response']['data'])->where('type', '=', 'product_media');
            if (count($group) === $ids->count()) {
                continue;
            }
            $cp++;
            $invalid = $ids->whereNotIn('id', $images)->pluck('id');
            foreach ($invalid as $id) {
                echo "Media for product will be deleted: {$group[0]['sw_product_id']} - $id" . PHP_EOL;
                $this->shopware->deleteMediaByProductId($group[0]['sw_product_id'], $id);
                $cm++;
            }
        }
        echo "Images unassigned: Products: $cp Media: $cm" . PHP_EOL;
        echo 'Finished' . PHP_EOL;
    }
}