<?php


namespace ShopwareCheckTool\Task;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ImagesTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'Images';
    public const TABLE = 'VariationImageQueue';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = Collection::make($this->readFile(self::FILE_NAME))
            ->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->where('is_uploaded', '=', '1')
            ->groupBy('sw_product_id')
            ->toArray();
        $this->clear();
    }

    public function check(): void
    {
        $this->newLogLine('Started ' . self::FILE_NAME);
        foreach ($this->file as $productId => $imageList) {
            $resp = $this->shopware->getMediaByProductId($productId);
            $this->newLogLine(($productId) . ': ' . (@$resp['error'] ?: $resp['code']));
            if (array_key_exists('error', $resp)) {
                continue;
            }
            $mediaCollection = Collection::make($resp['response']['data']);
            $imageListCollection = Collection::make($imageList);
            foreach ($mediaCollection as $media) {
                $imageFound = $imageListCollection->where('sw_product_media_id', '=', $media['id'])->first();
                if (empty($imageFound['variation_id'])) {
                    $this->newInvalidLine($media['id']);
                }
            }
        }
        $this->newLogLine('Finished ' . self::FILE_NAME);
    }
}