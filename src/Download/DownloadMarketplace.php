<?php

namespace ShopwareCheckTool\Download;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use ShopwareCheckTool\Models\Marketplace;


class DownloadMarketplace
{
    private const PREFIX = '/rest/PlentymarketsShopwareCore/test?model=';
    private const CONFIGURATION = ['uri' => self::PREFIX . 'Configuration', 'name' => 'Configuration'];
    private const ATTRIBUTE = ['uri' => self::PREFIX . 'AttributeMatch', 'name' => 'Attribute'];
    private const CATEGORY = ['uri' => self::PREFIX . 'CategoryMatch', 'name' => 'Category'];
    private const MANUFACTURER = ['uri' => self::PREFIX . 'ManufacturerMatch', 'name' => 'Manufacturer'];
    private const MEASUREMENT = ['uri' => self::PREFIX . 'MeasurementUnitMatch', 'name' => 'Measurement'];
    private const DELIVERY = ['uri' => self::PREFIX . 'DeliveryTimeMatch', 'name' => 'Delivery'];
    private const PROPERTY = ['uri' => self::PREFIX . 'PropertyMatch', 'name' => 'Property'];
    private const VARIATION_IMAGE_QUEUE = ['uri' => self::PREFIX . 'VariationImageQueue', 'name' => 'Images'];
    private const TAG = ['uri' => self::PREFIX . 'TagMatching', 'name' => 'Tag'];
    private bool $disable = false;

    private const LIST = [
        self::CONFIGURATION,
        self::ATTRIBUTE,
        self::CATEGORY,
        self::MANUFACTURER,
        self::MEASUREMENT,
        self::DELIVERY,
        self::PROPERTY,
        self::VARIATION_IMAGE_QUEUE,
        self::TAG
    ];

    private Marketplace $marketplace;
    private Client $client;

    public function __construct(Marketplace $marketplace)
    {
        $this->marketplace = $marketplace;

        $this->client = new Client(['base_uri' => $this->marketplace->getDomain(), RequestOptions::HEADERS => [
            'Authorization' => 'Bearer ' . $this->marketplace->getToken(),
            'Accept' => 'application/json',
        ]]);
    }

    /**
     * @return DownloadMarketplace
     */
    public function disable(): DownloadMarketplace
    {
        $this->disable = false;
        return $this;
    }

    /**
     * @return DownloadMarketplace
     */
    public function enable(): DownloadMarketplace
    {
        $this->disable = true;
        return $this;
    }

    /**
     * @return DownloadMarketplace
     */
    public function download(): DownloadMarketplace
    {
        if (!$this->disable) {
            echo 'Downloading disabled.' . PHP_EOL;
            return $this;
        }
        echo "Downloading started: '{$this->marketplace->getDomain()}'" . PHP_EOL;
        foreach (self::LIST as $table) {
            echo "Downloading: {$table['name']}" . PHP_EOL;
            try {
                $call = $this->client->get($table['uri']);
            } catch (GuzzleException $e) {
                echo "Download {$table['name']} error: {$e->getMessage()}" . PHP_EOL;
                continue;
            }
            $this->save($table['name'], $call->getBody()->getContents());
        }
        echo 'Downloading finished.' . PHP_EOL;
        return $this;
    }

    private function save(string $name, string $json): void
    {
        $file = __DIR__ . "/../Logs/Downloaded/$name.json";
        if (file_exists($file)) {
            unlink($file);
        }
        file_put_contents($file, $json);
    }
}