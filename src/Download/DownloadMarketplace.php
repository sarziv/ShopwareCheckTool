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
    private const ATTRIBUTE_REWORK = ['uri' => self::PREFIX . 'AttributeReworkMatch', 'name' => 'AttributeRework'];
    private const CATEGORY = ['uri' => self::PREFIX . 'CategoryMatch', 'name' => 'Category'];
    private const MANUFACTURER = ['uri' => self::PREFIX . 'ManufacturerMatch', 'name' => 'Manufacturer'];
    private const MEASUREMENT = ['uri' => self::PREFIX . 'MeasurementUnitMatch', 'name' => 'Measurement'];
    private const DELIVERY = ['uri' => self::PREFIX . 'DeliveryTimeMatch', 'name' => 'Delivery'];
    private const PROPERTY = ['uri' => self::PREFIX . 'PropertyMatch', 'name' => 'Property'];
    private const VARIATION_IMAGE_QUEUE = ['uri' => self::PREFIX . 'VariationImageQueue', 'name' => 'Images'];
    private const TAG = ['uri' => self::PREFIX . 'TagMatching', 'name' => 'Tag'];
    private const PRODUCT_CONFIGURATION = ['uri' => self::PREFIX . 'ProductConfigurator', 'name' => 'ProductConfigurator'];
    private const PRODUCT_VISIBILITY = ['uri' => self::PREFIX . 'ProductVisibility', 'name' => 'ProductVisibility'];
    private const SHOPWARE_ERRORS = ['uri' => self::PREFIX . 'ShopwareError', 'name' => 'ShopwareError'];
    private const REFERRERS = ['uri' => self::PREFIX . 'Referrer', 'name' => 'Referrer'];
    private bool $disable = false;

    private const LIST = [
        self::CONFIGURATION,
        self::ATTRIBUTE,
        self::ATTRIBUTE_REWORK,
        self::CATEGORY,
        self::MANUFACTURER,
        self::MEASUREMENT,
        self::DELIVERY,
        self::PROPERTY,
        self::TAG,
        self::PRODUCT_CONFIGURATION,
        self::PRODUCT_VISIBILITY,
        self::SHOPWARE_ERRORS,
        self::REFERRERS
    ];
    private const LIST_PAGINATE = [
        self::VARIATION_IMAGE_QUEUE
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
        $this->downloadPaginate();
        echo 'Downloading finished.' . PHP_EOL;
        return $this;
    }

    /**
     * @return DownloadMarketplace
     */
    public function downloadPaginate(): DownloadMarketplace
    {
        if (!$this->disable) {
            return $this;
        }
        foreach (self::LIST_PAGINATE as $table) {
            $page = 1;
            $payload = [];
            echo "Downloading: {$table['name']}" . PHP_EOL;
            do {
                echo "Page: $page" . PHP_EOL;
                try {
                    $call = $this->client->get("{$table['uri']}&page={$page}");
                } catch (GuzzleException $e) {
                    echo "Download {$table['name']} error: {$e->getMessage()}" . PHP_EOL;
                    continue;
                }
                $content = json_decode($call->getBody()->getContents(), true);
                $payload = array_merge($payload, $content);
                $page++;
            } while (!empty($content));
            $this->save($table['name'], json_encode($payload));
        }
        return $this;
    }

    private function save(string $name, string $json): void
    {
        $dir = __DIR__ . "/../Logs/Downloaded";
        $file = "$dir/$name.json";

        if (!file_exists($dir) && !mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }

        if (file_exists($file)) {
            unlink($file);
        }
        file_put_contents($file, $json);
    }
}