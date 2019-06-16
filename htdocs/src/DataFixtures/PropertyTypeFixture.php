<?php


namespace App\DataFixtures;


use App\Entity\PropertyType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PropertyTypeFixture extends Fixture
{
    private const PROPERTY_TYPES = [
        'Apartment',
        'House',
        'Building',
        'Farm',
        'Plot'
    ];

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::PROPERTY_TYPES as $propertyTypeTitle) {
            $propertyType = new PropertyType();
            $propertyType->setTitle($propertyTypeTitle);

            $manager->persist($propertyType);
        }

        $manager->flush();
    }
}