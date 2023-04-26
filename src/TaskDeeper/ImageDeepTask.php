<?php


namespace ShopwareCheckTool\TaskDeeper;


use Illuminate\Support\Collection;
use ReflectionClass;
use ShopwareCheckTool\FileManagement\File;
use ShopwareCheckTool\Requests\Shopware;

class ImageDeepTask extends File
{
    protected string $name;
    protected Shopware $shopware;
    private array $file;
    public const FILE_NAME = 'Images';

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $collection = Collection::make($this->readFile(self::FILE_NAME))->where('configuration_id', '=', $this->shopware->configuration->getId())->where('is_uploaded', '=', '1');
        $offset = $this->clear($collection->count());
        $this->file = $collection->slice($offset)->toArray();
    }

    public function check(bool $folderIsThumbnails): void
    {
        foreach ($this->file as $image) {
            $getProduct = $this->shopware->getProductById($image['sw_product_id']);
            if ($getProduct['code'] === 404) {
                $this->newLogLine("SW-PRODUCT-{$image['id']}: " . (@$getProduct['error'] ?: $getProduct['code']));
                $this->newInvalidLine($image['id']);
                continue;
            }

            $getProductMedia = $this->shopware->getProductMediaById($image['sw_product_media_id']);
            if ($getProductMedia['code'] === 404) {
                $this->newLogLine("SW-PRODUCT-MEDIA-{$image['id']}: " . (@$getProductMedia['error'] ?: $getProductMedia['code']));
                $this->newInvalidLine($image['id']);
                continue;
            }

            $getMedia = $this->shopware->getMediaById($image['sw_media_id']);
            if ($getMedia['code'] === 404) {
                $this->newLogLine("SW-MEDIA-{$image['id']}: " . (@$getMedia['error'] ?: $getMedia['code']));
                $this->newInvalidLine($image['id']);
                continue;
            }

            if ($folderIsThumbnails) {
                $getMediaThumbnailsById = $this->shopware->getMediaThumbnailsById($image['sw_media_id']);
                if (empty($getMediaThumbnailsById['response']['data']) && $getMediaThumbnailsById['code'] === 200) {
                    $this->newLogLine("SW-MEDIA-THUMBNAIL-{$image['id']}: " . (@$getMediaThumbnailsById['error'] ?: count($getMediaThumbnailsById['response']['data'])));
                    $this->newInvalidLine($image['id']);
                    continue;
                }
            }
            $this->newLogLine("SW-PRODUCT-{$image['id']}: " . (@$getProduct['error'] ?: $getProduct['code']));
        }
    }
}