<?php

namespace App\Tests;

use App\Entity\User;
use Codeception\Util\Fixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;
use Symfony\Component\Security\Core\Encoder\Argon2iPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;


class FirstCest
{
    /** @var UserPasswordEncoderInterface  */
    private $passwordEncoder;

    //https://codeception.com/docs/07-AdvancedUsage

    /**
     * @param UserPasswordEncoder $passwordEncoder
     */
//    protected function _inject(Symfony\Component\Security\Core\Encoder\UserPasswordEncoder $passwordEncoder): void
//    {
//        $this->passwordEncoder = $passwordEncoder;
//    }

    public function _before(AcceptanceTester $I)
    {}

    // tests
    public function tryToTest(AcceptanceTester $I)
    {
    }

    public function canSeeLoginPage(AcceptanceTester $I)
    {
        $I->amOnPage('/login');
        $I->see('Username:');
        $I->see('Password:');
        $I->see('Login');
    }

    public function canLogin(AcceptanceTester $I) {

        $email = 'user@email.com';
        $password = 'pass';

//        $I->haveInDatabase(
//            'user',
//            [
//                'email' => $email,
//                'password' => $password,
//                'roles' => ''
//            ]
//        );

        $I->amOnPage('/login');
        $I->seeElement('#submitButton');
        $I->makeScreenshot('loginpage');
        $I->fillField(['name' => '_username'], $email);
        $I->fillField(['name' => '_password'], $password);
        $I->click('#submitButton');
        $I->see('Logout');
        $I->dontSeeElement('#loginForm');
    }
}
