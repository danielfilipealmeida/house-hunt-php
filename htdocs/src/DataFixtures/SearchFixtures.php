<?php


namespace App\DataFixtures;


use App\Entity\PropertyType;
use App\Entity\Search;
use App\Repository\PropertyTypeRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class SearchFixtures extends Fixture implements DependentFixtureInterface
{
    /** @var array  */
    public const SEARCHES = [
        'Apartments in Faro' => [
            'latitude' => 37.019356,
            'longitude' => -7.930440,
            'radius' => 10,
            'minPrice' => 50000,
            'maxPrice' => 150000,
            'propertyType' => 'Apartment',
            'configuration' => []
        ]
    ];

    /** @var array $propertyTypes */
    private $propertyTypes = [];

    /** @var PropertyTypeRepository $propertyTypeRepository */
    private $propertyTypeRepository;

    /**
     * SearchFixtures constructor.
     *
     * @param PropertyTypeRepository $propertyTypeRepository
     */
    public function __construct(PropertyTypeRepository $propertyTypeRepository)
    {
        $this->propertyTypeRepository = $propertyTypeRepository;
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            PropertyTypeFixture::class
        ];
    }

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::SEARCHES as $title => $searchData) {


            $propertyType =
            $search = new Search();
            $search->setTitle($title)
                ->setLatitude($searchData['latitude'])
                ->setLongitude($searchData['longitude'])
                ->setRadius($searchData['radius'])
                ->setMinPrice($searchData['minPrice'])
                ->setMaxPrice($searchData['maxPrice'])
                ->setPropertyType($this->getPropertyType($searchData['propertyType']))
                ->setConfiguration(json_encode($searchData['configuration']));

            $manager->persist($search);
        }

        $manager->flush();
    }

    /**
     * Returns a PropertyType defined by its title.
     * @param $propertyType
     *
     * @return PropertyType
     */
    private function getPropertyType($propertyType): PropertyType
    {
        if (!isset($this->propertyTypes[$propertyType])) {
            $this->propertyTypes[$propertyType] = $this->propertyTypeRepository->findOneBy(['title'  => $propertyType]);
        }

        return $this->propertyTypes[$propertyType];
    }
}