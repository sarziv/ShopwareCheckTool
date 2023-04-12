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
    private array $log = [];
    private array $invalid = [];

    public function __construct(Shopware $shopware)
    {
        $this->name = (new ReflectionClass($this))->getShortName();
        $this->shopware = $shopware;
        $this->file = Collection::make($this->readFile('Images'))
            ->where('configuration_id', '=', $this->shopware->configuration->getId())
            ->where('is_uploaded', '=', '1')
            ->toArray();
    }

    public function check(): void
    {
        foreach ($this->file as $image) {
            echo "Reading {$this->name}: {$image['id']}" . PHP_EOL;
            $getProduct = $this->shopware->getProductById($image['sw_product_id']);
            if (@$getProduct['code'] !== 200) {
                $this->invalid[] = $image['id'];
                continue;
            }
            $this->log[$image['id']]['sw_product_id'] = (@$getProduct['code'] ?: $getProduct['error']);
            if (array_key_exists('error', $getProduct)) {
                $this->invalid[] = $image['id'];
                continue;
            }

            $getMedia = $this->shopware->getMediaById($image['sw_media_id']);
            $this->log[$image['id']]['sw_media_id'] = (@$getMedia['code'] ?: $getMedia['error']);
            if (array_key_exists('error', $getMedia)) {
                $this->invalid[] = $image['id'];
                continue;
            }

            $this->log[$image['id']]['hasFile'] = ($getMedia['response']['data']['attributes']['hasFile'] ?: false);
            if (!$getMedia['response']['data']['attributes']['hasFile']) {
                $this->invalid[] = $image['id'];
                continue;
            }

            $getMediaThumbnailsById = $this->shopware->getMediaThumbnailsById($image['sw_media_id']);
            if (empty($getMediaThumbnailsById['response']['data'])) {
                $this->log[$image['id']]['sw_media_thumbnails'] = (@$getMediaThumbnailsById['response'] ?: $getMediaThumbnailsById['error']);
                $this->invalid[] = $image['id'];
                continue;
            }

            $getProductMedia = $this->shopware->getProductMediaById($image['sw_product_media_id']);
            $this->log[$image['id']]['sw_product_media_id'] = (@$getProductMedia['code'] ?: $getProductMedia['error']);
        }
        $this->log['invalid']['count'] = count($this->invalid);
        $this->log['invalid']['list'] = $this->invalid;
        $this->saveFile($this->log);
    }
}