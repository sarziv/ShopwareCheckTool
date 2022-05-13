<?php

namespace ShopwareCheckTool\Requests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Illuminate\Support\Collection;
use JsonException;
use RuntimeException;
use ShopwareCheckTool\Models\Configuration;

class Refresh
{
    public Configuration $configuration;
    private Client $client;

    public function __construct(int $configurationId)
    {
        $this->configuration = $this->getConfigurationFile($configurationId);
        $this->client = new Client(['base_uri' => $this->configuration->getDomain()]);
    }

    protected function authenticate(): void
    {
        if ($this->configuration->getExpiresIn() > time()) {
            return;
        }
        try {
            $token = $this->client->post('/api/oauth/token', [RequestOptions::FORM_PARAMS => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->configuration->getClientId(),
                'client_secret' => $this->configuration->getClientSecret()
            ]]);
        } catch (GuzzleException $e) {
            throw new RuntimeException($e->getMessage());
        }

        if ($token->getStatusCode() !== 200) {
            throw new RuntimeException('Failed to refresh token');
        }
        try {
            $body = json_decode($token->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException($e->getMessage());
        }
        $this->configuration->setAccessToken($body['access_token']);
        $this->configuration->setExpiresIn(($body['expires_in'] + time() - 60));
    }

    private function getConfigurationFile(int $configurationId): Configuration
    {
        try {
            $configurationList = json_decode(file_get_contents(__DIR__ . '/../Logs/Downloaded/Configuration.json'), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new RuntimeException("Configuration file decode error: {$e->getMessage()}");
        }
        $configurationCollection = Collection::make($configurationList)->where('id', '=', $configurationId)->first();
        if (!$configurationCollection) {
            throw new RuntimeException("No configuration found from file: $configurationId");
        }

        $configuration = new Configuration();
        $configuration->setId($configurationCollection['id']);
        $configuration->setDomain($configurationCollection['domain']);
        $configuration->setVersion($configurationCollection['sw_version']);
        $configuration->setClientId($configurationCollection['client_id']);
        $configuration->setClientSecret($configurationCollection['client_secret']);
        $configuration->setAccessToken($configurationCollection['access_token']);
        $configuration->setExpiresIn($configurationCollection['expires_in']);

        return $configuration;
    }
}