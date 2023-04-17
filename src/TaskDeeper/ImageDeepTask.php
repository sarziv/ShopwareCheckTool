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
        $this->file = Collection::make($this->readFile('Images'))
            ->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->where('is_uploaded', '=', '1')
            ->toArray();
        $this->clear();
    }

    public function check(): void
    {
        $this->newGeneralFileLine('Started: ' . self::FILE_NAME);
        foreach ($this->file as $image) {
            $getProduct = $this->shopware->getProductById($image['sw_product_id']);
            $this->newFileLineLog("SW-PRODUCT-{$image['id']}: " . ($getProduct['error'] ?: $getProduct['code']));
            if ($getProduct['code'] === 404) {
                $this->newFileLine($image['id']);
                continue;
            }

            $getProductMedia = $this->shopware->getProductMediaById($image['sw_product_media_id']);
            $this->newFileLineLog("SW-PRODUCT-MEDIA-{$image['id']}: " . ($getProductMedia['error'] ?: $getProductMedia['code']));
            if ($getProductMedia['code'] === 404) {
                $this->newFileLine($image['id']);
                continue;
            }

            $getMedia = $this->shopware->getMediaById($image['sw_media_id']);
            $this->newFileLineLog("SW-MEDIA-{$image['id']}: " . ($getMedia['error'] ?: $getMedia['code']));
            if ($getMedia['code'] === 404) {
                $this->newFileLine($image['id']);
                continue;
            }

            $isFile = ($getMedia['response']['data']['attributes']['hasFile'] ?: false);
            $this->newFileLineLog("SW-FILE-{$image['id']}: " . ($getProduct['error'] ?: $getProduct['code']));
            if (!$isFile) {
                $this->newFileLine($image['id']);
                continue;
            }
            $getMediaThumbnailsById = $this->shopware->getMediaThumbnailsById($image['sw_media_id']);
            $this->newFileLineLog("SW-MEDIA-THUMBNAIL-{$image['id']}: " . ($getMediaThumbnailsById['error'] ?: $getMediaThumbnailsById['code']));
            if (empty($getMediaThumbnailsById['response']['data'])) {
                $this->newFileLine($image['id']);
            }
        }
        $this->newGeneralFileLine('Finished: ' . self::FILE_NAME);
    }
}