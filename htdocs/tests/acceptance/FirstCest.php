<?php

namespace App\Tests;

use Codeception\Util\Fixtures;
use Doctrine\Bundle\FixturesBundle\Fixture;

class FirstCest
{

    public function _before(AcceptanceTester $I)
    {

    }

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
        $I->haveInDatabase(
            'user',
            [
                'email' =>'user@email.com',
                'password' => 'pass',
                'roles' => ''
            ]
        );

        $I->amOnPage('/login');
        $I->seeElement('#submitButton');
        $I->makeScreenshot('loginpage');
        $I->fillField(['name' => '_username'], 'user@email.com');
        $I->fillField(['name' => '_password'], 'pass');
        $I->click('#submitButton');
        $I->see('Logout');
        $I->dontSeeElement('#loginForm');
    }
}
