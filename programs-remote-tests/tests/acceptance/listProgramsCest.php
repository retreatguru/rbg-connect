<?php
use \AcceptanceTester;

class listProgramsCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function listPrograms(AcceptanceTester $I)
    {
        $I->amOnPage('/?programs=true');
        $I->see('Donation Payment Program');
        $I->see('Example Program');
        $I->click('Example Program');
        $I->see('Register Now');

        //todo: Check to see that hidden programs aren't visible
    }
}