<?php

namespace ShopwareCheckTool\Download;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use ShopwareCheckTool\Models\Marketplace;


class DownloadMarketplace extends Download
{
    private const PREFIX = '/rest/PlentymarketsShopwareCore/test?model=';
    private const CONFIGURATION_URL = ['uri' => self::PREFIX . 'Configuration', 'name' => Download::CONFIGURATION];
    private const ATTRIBUTE_URL = ['uri' => self::PREFIX . 'AttributeMatch', 'name' => Download::ATTRIBUTE];
    private const ATTRIBUTE_REWORK_URL = ['uri' => self::PREFIX . 'AttributeReworkMatch', 'name' => Download::ATTRIBUTE_REWORK];
    private const CATEGORY_URL = ['uri' => self::PREFIX . 'CategoryMatch', 'name' => Download::CATEGORY];
    private const MANUFACTURER_URL = ['uri' => self::PREFIX . 'ManufacturerMatch', 'name' => Download::MANUFACTURER];
    private const MEASUREMENT_URL = ['uri' => self::PREFIX . 'MeasurementUnitMatch', 'name' => Download::MEASUREMENT];
    private const DELIVERY_URL = ['uri' => self::PREFIX . 'DeliveryTimeMatch', 'name' => Download::DELIVERY];
    private const PROPERTY_URL = ['uri' => self::PREFIX . 'PropertyMatch', 'name' => Download::PROPERTY];
    private const PROPERTY_DYNAMIC_URL = ['uri' => self::PREFIX . 'PropertyDynamicMatch', 'name' => Download::PROPERTY_DYNAMIC];
    private const VARIATION_IMAGE_QUEUE_URL = ['uri' => self::PREFIX . 'VariationImageQueue', 'name' => Download::IMAGES];
    private const TAG_URL = ['uri' => self::PREFIX . 'TagMatching', 'name' => Download::TAG];
    private const PRODUCT_CONFIGURATION_URL = ['uri' => self::PREFIX . 'ProductConfigurator', 'name' => Download::PRODUCT_CONFIGURATION];
    private const PRODUCT_VISIBILITY_URL = ['uri' => self::PREFIX . 'ProductVisibility', 'name' => Download::PRODUCT_VISIBILITY];
    private const SHOPWARE_ERROR_URL = ['uri' => self::PREFIX . 'ShopwareError', 'name' => Download::SHOPWARE_ERROR];
    private const REFERRERS_URL = ['uri' => self::PREFIX . 'Referrer', 'name' => Download::REFERRER];

    private const LIST = [
        self::CONFIGURATION_URL,
        self::ATTRIBUTE_URL,
        self::ATTRIBUTE_REWORK_URL,
        self::CATEGORY_URL,
        self::MANUFACTURER_URL,
        self::MEASUREMENT_URL,
        self::DELIVERY_URL,
        self::PROPERTY_URL,
        self::PROPERTY_DYNAMIC_URL,
        self::TAG_URL,
        self::PRODUCT_CONFIGURATION_URL,
        self::PRODUCT_VISIBILITY_URL,
        self::SHOPWARE_ERROR_URL,
        self::REFERRERS_URL
    ];
    private const LIST_PAGINATE = [
        self::VARIATION_IMAGE_QUEUE_URL
    ];

    private Marketplace $marketplace;
    private Client $client;
    private bool $download;

    public function __construct(Marketplace $marketplace, bool $download = true)
    {
        $this->marketplace = $marketplace;
        $this->download = $download;

        $this->client = new Client(['base_uri' => $this->marketplace->getDomain(), RequestOptions::HEADERS => [
            'Authorization' => 'Bearer ' . $this->marketplace->getToken(),
            'Accept' => 'application/json',
        ]]);
    }

    /**
     * @return DownloadMarketplace
     */
    public function download(): DownloadMarketplace
    {
        if (!$this->download) {
            $this->newGeneralLine("Downloading disabled.");
            return $this;
        }
        $this->newGeneralLine("Downloading started: {$this->marketplace->getDomain()}");
        $this->downloadList();
        $this->downloadListPaginate();
        $this->newGeneralLine("Downloading finished.");

        return $this;
    }

    private function downloadList(): void
    {
        foreach (self::LIST as $table) {
            $this->newGeneralLine("Downloading: {$table['name']}");
            try {
                $call = $this->client->get($table['uri']);
            } catch (GuzzleException $e) {
                $this->newGeneralLine("GuzzleException: {$e->getMessage()}");
                continue;
            }
            $this->save($table['name'], $call->getBody()->getContents());
        }
    }

    //TODO Improve pagination
    /**
     * @return void
     */
    private function downloadListPaginate(): void
    {
        foreach (self::LIST_PAGINATE as $table) {
            $page = 0;
            $payload = [];
            $this->newGeneralLine("Downloading paginated: {$table['name']}");
            do {
                $this->newGeneralLine("Page: $page");
                try {
                    $call = $this->client->get("{$table['uri']}&page={$page}");
                } catch (GuzzleException $e) {
                    $this->newGeneralLine("GuzzleException: {$e->getMessage()}");
                    continue;
                }
                $content = json_decode($call->getBody()->getContents(), true);
                $payload = array_merge($payload, $content);
                $page++;
            } while (!empty($content));
            $this->save($table['name'], json_encode($payload));
        }
    }
}