<?php

namespace App\Service;

class Memcached {

    /**
     * The memcached object
     *
     * @var \Memcached
     */
    private $memcached;

    /**
     * Servers configuration
     *
     * @var array $servers
     */
    private $servers;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->memcached = new \Memcached();
    }

    /**
     * Adds a server
     *
     * @param string $host
     * @param integer $port
     * @return void
     */
    public function addServer(string $host, int $port)
    {
        $this->servers[] = [
            'host' => $host,
            'port' => $port
        ];
       
        $this->memcached->addServer($host, $port);
    } 

    /**
     * Get a value from Memcached
     *
     * @param string $key
      * @return any
     */
    public function get(string $key)
    {
        return $this->memcached->get($key);
    }

    /**
     * Set a value in Memcached
     *
     * @param string $key
     * @param [type] $value
     * @param int $expiration
     * @return void
     */
    public function set(string $key, $value, $expiration = 0): void
    {
        $this->memcached->set($key, $value, $expiration);
    }

}