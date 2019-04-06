<?php

namespace App\Service;

use GuzzleHttp\Client;

class Idealista
{
    private const TOKEN_KEY = 'idealista-token';
    private const BASE_URI = 'https://api.idealista.com';
    private const SEARCH_PATH = '/3.5/pt/search';
    private const OAUTH_PATH = '/oauth/token';
    private const TIMEOUT = 60;

    /**
     * Security Token.
     *
     * @var string
     */
    private $bearerCode = null;

    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $secret;

    /**
     * @var string
     */
    private $credentials = null;

    /**
     * @var Memcached
     */
    private $memcached;

    /**
     * @var Client
     */
    private $client;

    /**
     * Constructor.
     *
     * @param Memcached $memcached
     */
    public function __construct(
        Memcached $memcached,
        Client $client
    ) {
        $this->memcached = $memcached;
        $this->client = $client;
    }

    /**
     * Return the headers for the request to the idealista API
     * This is used both for requesting a bearer code or to do actual requests to the API
     * When requesting the bearer code, `$credentials` are the user login creditians and `$isBearer` is `false` or unset.
     * When doing an actual request, `$credentials` is actually the Bearer code and `$isBearer` must be `true`.
     *
     * @param string $credentials
     * @param bool   $isBearer
     *
     * @return array
     */
    public function getHeaders($credentials, $bearer = false): array
    {
        return [
            'Content-Type' => 'application/x-www-form-urlencoded;charset=UTF-8',
            'Authorization' => ($bearer ? 'Bearer' : 'Basic').' '.$credentials,
        ];
    }

    public function getCredentials(): string
    {
        if (!$this->credentials) {
            $this->credentials = base64_encode($this->key.':'.$this->secret);
        }

        return $this->credentials;
    }

    /**
     * Queries Memcached for a cached Bearer code.
     *
     * @return array
     */
    public function getBearerCodeFromMemcached(): ?array
    {
        $memcacheResult = $this->memcached->get(self::TOKEN_KEY);

        return $memcacheResult ? $memcacheResult : null;
    }

    /**
     * Get the Beare code from Idealista using the API.
     *
     * @return array
     *
     * @throws Exception
     */
    public function getBearerCodeFromServer(): array
    {
        $credentials = $this->getCredentials();
        $headers = $this->getHeaders($credentials);

        $url = self::BASE_URI.self::OAUTH_PATH;

        $requestParams = [
            'timeout' => self::TIMEOUT,
            'form_params' => [
                'grant_type' => 'client_credentials',
            ],
            'headers' => $headers,
        ];
        $response = $this->client->request(
            'POST',
            $url,
            $requestParams
        );

        if (!$response || $response->getStatusCode() != 200) {
            throw new \Exception('Error retrieving bearer code.');
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Checks if a security token is valid.
     *
     * @param array $bearerCode
     *
     * @return bool
     */
    public function isBearerCodeValid($bearerCode): bool
    {
        return !empty($bearerCode);
    }

    /**
     * Returns the bearer code.
     *
     * This method checks the bearer code in three distint locations and fallback in the predefined order:
     *  - locally on the Service
     *  - on memcached
     *  - on the idealista oauth endpoint
     *
     * @return array
     */
    public function getBearerCode(): array
    {
        if ($this->isBearerCodeValid($this->bearerCode)) {
            return $this->bearerCode;
        }

        $memcachedBearerCode = $this->getBearerCodeFromMemcached();
        if ($this->isBearerCodeValid($memcachedBearerCode)) {
            $this->bearerCode = $memcachedBearerCode;

            return $memcachedBearerCode;
        }

        $bearerCodeFromServer = $this->getBearerCodeFromServer();
        $this->memcached->set(self::TOKEN_KEY, $bearerCodeFromServer);
        $this->token = $bearerCodeFromServer;

        return $bearerCodeFromServer;
    }

    /**
     * Get the value of secret.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set the value of secret.
     *
     * @param string $secret
     *
     * @return self
     */
    public function setSecret(string $secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get the value of key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set the value of key.
     *
     * @param string $key
     *
     * @return self
     */
    public function setKey(string $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Searches in the Idealista API using the search parameters provided.
     *
     * @param array $searchParameters
     */
    public function search($searchParameters)
    {
        /** @var string $bearerCode */
        /** @var array $headers */
        /** @var string */
        /** @var string */
        $bearerCode = $this->getBearerCode();
        $headers = $this->getHeaders($bearerCode['access_token'], true);
        $searchParametersString = $this->getSearchParametersString($searchParameters);
        $cachedSearchResult = $this->memcached->get($searchParametersString);

        if ((bool) $cachedSearchResult) {
            return $cachedSearchResult;
        }

        $url = self::BASE_URI.self::SEARCH_PATH;

        /** @var $requestParams */
        $requestParams = [
            'timeout' => self::TIMEOUT,
            'form_params' => $searchParameters,
            'headers' => $headers,
        ];
        $response = $this->client->request(
            'POST',
            $url,
            $requestParams
        );

        if (!$response || $response->getStatusCode() != 200) {
            throw new \Exception('Error retrieving bearer code.');
        }

        return json_decode($response->getBody(), true);
    }

    /**
     * Produces a key-string from an array to be used on the body of a search request.
     *
     * @param array $searchParameters
     *
     * @return string
     */
    public static function getSearchParametersString($searchParameters): string
    {
        return join('&', array_map(
            function ($value, string $key) {
                if (gettype($value) == 'boolean') {
                    $value = $value ? 'true' : 'false';
                }

                return $key.'='.$value;
            },
            $searchParameters,
            array_keys($searchParameters)
        ));
    }
}
