<?php


namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Mapbox
{
    private const MAPBOX_BASE_URL = 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={token}';
    private const MAP_ID = 'mapbox.streets';

    /** @var Memcached */
    private $memcached;

    /**  @var Client */
    private $client;

    /** @var string token */
    private $token;

    /**
     * Constructor.
     *
     * @param Memcached $memcached
     * @param Client $client
     */
    public function __construct(
        Memcached $memcached,
        Client $client
    )
    {
        $this->memcached = $memcached;
        $this->client = $client;
    }

    /**
     * @param string $id
     * @param int $z
     * @param int $x
     * @param int $y
     * @return string
     */
    private function getMapMemcachedKey(
        string $id,
        int $z,
        int $x,
        int $y
    ): string
    {
        return implode('_', func_get_args());
    }

    /**
     * @param string $id
     * @param int $z
     * @param int $x
     * @param int $y
     * @return string
     */
    private function getMapboxUrl(string $id, int $z, int $x, int $y): string
    {
        return str_replace(['{id}', '{z}', '{x}', '{y}', '{token}'],
            [$id, $z, $x, $y, $this->token],
            self::MAPBOX_BASE_URL);
    }

    /**
     * @param int $z
     * @param int $x
     * @param int $y
     * @return string
     * @throws GuzzleException
     * @throws \Exception
     */
    public function getMap(
        int $z,
        int $x,
        int $y
    ): string {
        /** @var string $memcachedKey */
        $memcachedKey = $this->getMapMemcachedKey(self::MAP_ID, $z, $x, $y);

        /** @var  $memcacheResult */
        if ($memcacheResult = $this->memcached->get($memcachedKey)) {
            return $memcacheResult;
        }

        /** @var string $mapboxUrl */
        $mapboxUrl = $this->getMapboxUrl(self::MAP_ID, $z, $x, $y);
        $response = $this->client->request(
            'GET',
            $mapboxUrl,
            []);

        if (!$response || 200 !== $response->getStatusCode()) {
            throw new \Exception('Error retrieving map tile.');
        }

        /** @var string $tile */
        $tile = (string) $response->getBody();

        $this->memcached->set($memcachedKey, $tile);

        return $tile;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
    }


}