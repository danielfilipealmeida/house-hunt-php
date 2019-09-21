<?php


namespace App\Tests\unit\Entity;


use App\Entity\Search;
use PHPUnit\Framework\TestCase;

class SearchTest extends TestCase
{

    public function testSetLatitude(): void {
        $search = new Search();
        $this->assertEquals(0, $search->getLatitude());

        $search = new Search();
        $search->setLatitude(1.0);
        $this->assertEquals(1.0, $search->getLatitude());
    }

    public function testSetLongitude(): void {
        $search = new Search();
        $this->assertEquals(0, $search->getLongitude());

        $search = new Search();
        $search->setLongitude(1.0);
        $this->assertEquals(1.0, $search->getLongitude());
    }

    public function testAddNewSearch(): void {

    }

}