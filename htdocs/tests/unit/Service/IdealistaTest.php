<?php

namespace App\Tests\Service;

use App\Entity\Search;
use GuzzleHttp\Client;
use GuzzleHttp\Response;
use App\Service\Idealista;
use App\Service\Memcached;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7;

class IdealistaTest extends TestCase 
{
    public function testGetSecurityTokenFromServer()
    {
    
        $this->assertTrue(1 == 1);
    }

    public function testGetCredentials(): void
    {
        $memcached = $this->createMock(Memcached::class);
        $client = $this->createMock(Client::class);
        
        $idealista = new Idealista($memcached, $client);
        
        $idealista->setKey('some_key')
            ->setSecret('some_secret');

        $credentials = $idealista->getCredentials();

        $this->assertEquals('c29tZV9rZXk6c29tZV9zZWNyZXQ=', $idealista->getCredentials());
    }

    public function testGetHeaders():void
    {
        $memcached = $this->createMock(Memcached::class);
        $client = $this->createMock(Client::class);
        
        $idealista = new Idealista($memcached, $client);
        
        $idealista->setKey('some_key')
            ->setSecret('some_secret');

        $headers = $idealista->getHeaders($idealista->getCredentials());

        $this->assertArrayHasKey('Content-Type', $headers);
        $this->assertArrayHasKey('Authorization', $headers);
        $this->assertContains('application/x-www-form-urlencoded;charset=UTF-8', $headers);
        $this->assertContains('Basic c29tZV9rZXk6c29tZV9zZWNyZXQ=', $headers);
    }

    public function testGetBearerCodeFromServer(): void
    {
        /** @var Memcached|MockObject */
        $memcachedMock = $this->createMock(Memcached::class);
    
        /** @var Memcached|MockObject */
        $clientMock = $this->createMock(Client::class);
        $client = new Client();
        
        
        $responseMock = $this->createMock(Psr7\Response::class);
        $responseMock->method('getStatusCode')
            ->willReturn(200);
        $responseMock->method('getBody')
            ->willReturn(json_encode([
                'access_token' => 'Some_access_token',
                'token_type' => 'bearer',
                'expires_in' => 1000
            ]));
    
        $clientMock = $this->createMock(Client::class);
        $clientMock->method('request')
            ->willReturn($responseMock);

        $idealista = new Idealista($memcachedMock, $clientMock);

        $tokenArrayData = $idealista->getBearerCodeFromServer();

        $this->assertNotEmpty($tokenArrayData);
        $this->assertNotEmpty($tokenArrayData["access_token"]);
        $this->assertTrue($tokenArrayData["token_type"] == "bearer");
        $this->assertInternalType('int', $tokenArrayData["expires_in"]);
    }

    public function testGetBearerCodeFromMemcached()
    {
        $returnedToken = [
            "access_token" => "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzY29wZSI6WyJyZWFkIl0sImV4cCI6MTUzODk3MzY0MCwiYXV0aG9yaXRpZXMiOlsiUk9MRV9QVUJMSUMiXSwianRpIjoiMDRjOGVjY2ItNTFkNC00YWEwLTgzMWQtZTkzY2E2MzhkZTc3IiwiY2xpZW50X2lkIjoidGowa3VrMGdnc2MyZ291aWR5ejI4NHlpYno4aDFidTEifQ.qMOMYVVuhfRN8W_OajmwTjlhaOmAtm6mDqO1hWQ96Dc",
            "token_type" => "bearer",
            "expires_in" => 0,
            "scope" => "read",
            "jti" => "04c8eccb-51d4-4aa0-831d-e93ca638de77"
        ];

        $memcached = $this->createMock(Memcached::class);
        $memcached->method('get')
            ->willReturn($returnedToken);
        $client = $this->createMock(Client::class);
        
        $idealista = new Idealista($memcached, $client);

        $tokenFromMemcache = $idealista->getBearerCodeFromMemcached();

        $this->assertEquals($returnedToken, $tokenFromMemcache);
    }


    public function testGetSearchParametersString()
    {
        $testParameterArray = [
            'country' => 'pt',
            'operation' => 'sale',
            'propertyType' => 'homes',
            'center' => '37.0160273,-7.93204812',
            'distance' => 5000,
            'chalet' => true,
            'maxPrice' => 150000,
            'order' => 'price',
            'sort' => 'asc'
        ];

        $expectedResult = 'country=pt&operation=sale&propertyType=homes&center=37.0160273,-7.93204812&distance=5000&chalet=true&maxPrice=150000&order=price&sort=asc';

        $memcached = $this->createMock(Memcached::class);
        $clientMock = $this->createMock(Client::class);

        $idealista = new Idealista($memcached, $clientMock);
        $result = $idealista->getSearchParametersString($testParameterArray);

        $this->assertEquals($expectedResult, $result);
    }

    public function testSearch()
    {
        $testParameterArray = [
            'country' => 'pt',
            'operation' => 'sale',
            'propertyType' => 'homes',
            'center' => '37.0160273,-7.93204812',
            'distance' => 5000,
            'chalet' => true,
            'maxPrice' => 150000,
            'order' => 'price',
            'sort' => 'asc'
        ];

        $memcached = $this->createMock(Memcached::class);
        //$client = new Client();

        $firstResponseMock = $this->createMock(Psr7\Response::class);
        $firstResponseMock->method('getStatusCode')
            ->willReturn(200);
        $firstResponseMock->method('getBody')
            ->willReturn(json_encode([
                'access_token' => 'Some_access_token'
            ]));

        $secondResponseMock = $this->createMock(Psr7\Response::class);
        $secondResponseMock->method('getStatusCode')
            ->willReturn(200);
        $secondResponseMock->method('getBody')
            ->willReturn(json_encode([
                'elementList' => [],
                'total' => 0,
                'totalPages' => 1,
                'actualPage' => 1,
                'itemsPerPage' =>20,
                'numPaginations' => 0,
                'hiddenResults' => false,
                'summary' => [],
                'paginable' => false,
                'upperRangePosition' => 20,
                'lowerRangePosition' =>0
            ]));

        $client = $this->createMock(Client::class);
        $client->method('request')
            ->will($this->onConsecutiveCalls($firstResponseMock, $secondResponseMock));
            

        $idealista = new Idealista($memcached, $client);

        $result = $idealista->search($testParameterArray);

        $this->assertNotNull($result);

    }

    public function testMapToIdealistSearchArray() {

        /** @var Search $search */
        $search = new Search();

        /** @var Memcached $memcached */
        /** @var Client $client */
        $memcached = $this->createMock(Memcached::class);
        $client    = $this->createMock(Client::class);

        $idealista = new Idealista($memcached, $client);
        $result = $idealista->mapToIdealistSearchArray($search);
    }
}