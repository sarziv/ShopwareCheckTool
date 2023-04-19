<?php


namespace ShopwareCheckTool\Requests;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use ShopwareCheckTool\Models\Marketplace;

class Plentymarket
{
    private Client $client;

    public function __construct(Marketplace $marketplace)
    {
        $this->client = new Client(['base_uri' => $marketplace->getDomain(), RequestOptions::HEADERS => [
            'Authorization' => "Bearer {$marketplace->getToken()}",
            'Accept' => '*/*',
            'Content-Type' => 'application/json'
        ]]);
    }

    public function deleteProductVisibilityById(string $id): array
    {
        try {
            $call = $this->client->delete("/rest/PlentymarketsShopwareCore/deleteTableRecords?model=ProductVisibility&whereKey=id&whereValue=$id");
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function updateVariationImageQueueById(string $id, array $payload): array
    {
        try {
            $call = $this->client->post("/rest/PlentymarketsShopwareCore/updateTableRecords?model=VariationImageQueue&whereKey=id&whereValue=$id", [RequestOptions::FORM_PARAMS => $payload]);
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }

    public function deleteFromPlugin(string $table, $id): array
    {
        try {
            $call = $this->client->delete("/rest/PlentymarketsShopwareCore/deleteTableRecords?model=$table&whereKey=id&whereValue=$id");
        } catch (GuzzleException $e) {
            return ['code' => $e->getCode(), 'error' => $e->getMessage()];
        }
        return [
            'code' => $call->getStatusCode(),
            'response' => json_decode($call->getBody()->getContents(), true)
        ];
    }
}