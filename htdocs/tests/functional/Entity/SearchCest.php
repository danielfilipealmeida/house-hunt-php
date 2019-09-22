<?php

namespace App\Tests\functional\Entity;


use App\Tests\FunctionalTester;
use App\Tests\Page\Functional\Login;
use App\Tests\Page\Functional\Search;
use App\Entity\Search as SearchEntity;

class SearchCest
{
    private const FARO_LATITUDE = 37.019356;
    private const FARO_LONGITUDE = -7.930440;
    private const LOULE_LATITUDE = 37.1557513;
    private const LOULE_LONGITUDE = -8.0985633;

    /**
     * @param FunctionalTester $I
     */
    public function tryAddNewSearch(
        FunctionalTester $I
    ) {

    }

    /**
     * @param FunctionalTester $I
     * @param Login            $loginPage
     * @param Search           $searchPage
     */
    public function tryAddNewSearchInSite(
        FunctionalTester $I,
        Login $loginPage,
        Search $searchPage
    ): void {
        $minPrice = 50000;
        $maxPrice = 100000;
        $radius   = 10000;

        $loginPage->login('user@email.com', 'pass');

        $searchPage->addNewSearch([
            'title'     => 'Test Search',
            'radius'    => $radius,
            'minPrice'  => $minPrice,
            'maxPrice'  => $maxPrice,
            'latitude'  => self::FARO_LATITUDE,
            'longitude' => self::FARO_LONGITUDE
        ]);

        $I->see('Search: Test Search');
        $I->seeElement('table.table');
        $I->seeInRepository(
            SearchEntity::class,
            [
                'title'         => 'Test Search',
                'radius'        => $radius,
                'minPrice'      => $minPrice,
                'maxPrice'      => $maxPrice,
                'coordinates'   => serialize([
                    'latitude'  => (string)self::FARO_LATITUDE,
                    'longitude' => (string)self::FARO_LONGITUDE
                ]),
                'configuration' => json_encode([
                    'country'      => 'pt',
                    'operation'    => 'sale',
                    'propertyType' => 'homes',
                    'center'       => implode(', ', [self::FARO_LATITUDE, self::FARO_LONGITUDE]),
                    'distance'     => $radius,
                    'chalet'       => true,
                    'maxPrice'     => $maxPrice,
                    'minPrice'     => $minPrice,
                    'order'        => 'price',
                    'sort'         => 'asc'
                ])
            ]
        );
    }

    public function tryUpdateASearch(
        FunctionalTester $I,
        Login $loginPage,
        Search $searchPage
    ): void {
        $minPrice = 50000;
        $maxPrice = 100000;
        $radius   = 10000;

        $loginPage->login('user@email.com', 'pass');

        $searchPage->addNewSearch([
            'title'     => 'Test Search that will be updated',
            'radius'    => 50000,
            'minPrice'  => 50000,
            'maxPrice'  => 100000,
            'latitude'  => self::FARO_LATITUDE,
            'longitude' => self::FARO_LONGITUDE
        ]);

        /** @var SearchEntity $entity */
        $entity = $I->grabEntityFromRepository(SearchEntity::class,
            [
                'title' => 'Test Search that will be updated'
            ]
        );

        $title     = 'Updated Entity';
        $radius    = 1000;
        $minPrice  = 10;
        $maxPrice  = 1000;
        $latitude  = self::LOULE_LATITUDE;
        $longitude = self::LOULE_LONGITUDE;
        $entity->setTitle($title)
            ->setLatitude($latitude)
            ->setLongitude($longitude)
            ->setRadius($radius)
            ->setMinPrice($minPrice)
            ->setMaxPrice($maxPrice);
        $I->persistEntity($entity);

        $entity2 = $I->grabEntityFromRepository(SearchEntity::class,
            [
                'title' => $title
            ]
        );

        $I->seeInRepository(
            SearchEntity::class,
            [
                'title'         => $title,
                'radius'        => $radius,
                'minPrice'      => $minPrice,
                'maxPrice'      => $maxPrice,
                'coordinates'   => serialize([
                    'latitude'  => $latitude,
                    'longitude' => $longitude
                ]),
                'configuration' => json_encode([
                    'country'      => 'pt',
                    'operation'    => 'sale',
                    'propertyType' => 'homes',
                    'center'       => implode(', ', [$latitude, $longitude]),
                    'distance'     => $radius,
                    'chalet'       => true,
                    'maxPrice'     => $maxPrice,
                    'minPrice'     => $minPrice,
                    'order'        => 'price',
                    'sort'         => 'asc'
                ])
            ]);
    }

}