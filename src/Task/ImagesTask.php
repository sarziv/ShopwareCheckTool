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
    private array $log = [];
    private array $invalid = [];
    private int $count = 1;
    private int $total;
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
        $this->total = count($this->file);
    }

    public function check(): void
    {
        foreach ($this->file as $productId => $imageList) {
            echo $this->name . ':' . $this->count++ . '/' . $this->total . PHP_EOL;
            $getProductMedia = $this->shopware->getMediaByProductId($productId);
            $this->log[$productId]['product'] = (@$getProductMedia['code'] ?: $getProductMedia['error']);
            if (array_key_exists('error', $getProductMedia)) {
                continue;
            }
            $mediaCollection = Collection::make($getProductMedia['response']['data']);
            $imageListCollection = Collection::make($imageList);
            foreach ($mediaCollection as $media) {
                $imageFound = $imageListCollection->where('sw_product_media_id', '=', $media['id'])->first();
                $this->log[$productId]['media'][$media['id']] = (@(int)$imageFound['variation_id'] ?: 'Invalid media');
                if (empty($imageFound['variation_id'])) {
                    $this->invalid[$productId][] = $media['id'];
                }
            }
        }
        $this->log['invalid']['count'] = count($this->invalid);
        $this->log['invalid']['list'] = $this->invalid;
        $this->saveFile($this->log);
    }
}