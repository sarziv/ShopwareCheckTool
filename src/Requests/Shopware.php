<?php

namespace ShopwareCheckTool\Requests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

class Shopware extends Refresh
{
    private Client $client;

    public function __construct(int $configurationId)
    {
        parent::__construct($configurationId);
        $this->client = new Client(['base_uri' => "{$this->configuration->getDomain()}{$this->configuration->getVersion()}"]);
    }

    public function getPropertyGroupById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("property-group/{$id}?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getPropertyGroupOptionById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("property-group-option/{$id}?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getCategoryById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("category/{$id}?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getDeliveryById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("delivery-time/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getManufacturerById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("product-manufacturer/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getUnitById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("unit/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getProductById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("product/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getMediaById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("media/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getMediaThumbnailsById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("media/$id/thumbnails?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getProductMediaById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("product-media/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getMediaByProductId(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("product/$id/media?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getConfigurationSettingByProductId(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("product/$id/configurator-settings?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getTagById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("tag/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getProductVisibilityById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("product-visibility/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getProductConfiguratorSettingById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("product-configurator-setting/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function getProductOptionsById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->get("product/$id/options?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function deleteProductConfiguratorSettingById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->delete("product-configurator-setting/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function deleteProductMediaById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->delete("product-media/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function postProductSearch(string $productNumber): array
    {
        $this->authenticate();
        try {
            $call = $this->client->post("search/product", [
                RequestOptions::HEADERS => [
                    'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                    'Accept' => '*/*',
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::JSON => [
                    'filter' => [
                        [
                            'type' => 'equals',
                            'field' => 'product.productNumber',
                            'value' => $productNumber
                        ]
                    ]
                ]
            ]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function postProductCloneSearch(int $page): array
    {
        $this->authenticate();
        try {
            $call = $this->client->post("search/product", [
                RequestOptions::HEADERS => [
                    'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                    'Accept' => '*/*',
                    'Content-Type' => 'application/json'
                ],
                RequestOptions::JSON => [
                    'page' => 1,
                    'limit' => 100,
                    'filter' => [
                        [
                            'type' => 'contains',
                            'field' => 'product.productNumber',
                            'value' => '_COPY'
                        ]
                    ]
                ]
            ]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function deleteProductById(string $id): array
    {
        $this->authenticate();
        try {
            $call = $this->client->delete("product/$id?_response=true", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ]]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function paginateProducts(int $page): array
    {
        $this->authenticate();
        try {
            $call = $this->client->post("search/product", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ],
                RequestOptions::JSON => [
                    'page' => $page,
                    'limit' => 100,
                    'filter' => [
                        [
                            'type' => 'equals',
                            'field' => 'product.coverId',
                            'value' => null
                        ]
                    ]
                ]
            ]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function updateProductCover(string $productId, string $coverId): array
    {
        $this->authenticate();
        try {
            $call = $this->client->post("_action/sync", [RequestOptions::HEADERS => [
                'Authorization' => "Bearer {$this->configuration->getAccessToken()}",
                'Accept' => '*/*',
                'Content-Type' => 'application/json'
            ],
                RequestOptions::JSON => [
                    'write-product' => [
                        'entity' => 'product',
                        'action' => 'upsert',
                        'payload' => [
                            [
                                'id' => $productId,
                                'coverId' => $coverId,
                            ]
                        ]
                    ]

                ]
            ]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }
}