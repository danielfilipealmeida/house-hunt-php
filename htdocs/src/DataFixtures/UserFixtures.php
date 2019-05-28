<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    /** @var UserPasswordEncoderInterface  */
    private $passwordEncoder;

    /** @var array USERS */
    private const USERS = [
        [
            'email' => 'user@email.com',
            'password' => 'pass'
        ]
    ];


    /**
     * UserFixtures constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        foreach (self::USERS as $userData) {
            /** @var User $user */
            $user = new User();

            /** @var string $encodePassword */
            $encodePassword = $this->passwordEncoder->encodePassword(
                $user,
                $userData['password']
            );

            $user->setEmail($userData['email'])
                ->setPassword($encodePassword);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
