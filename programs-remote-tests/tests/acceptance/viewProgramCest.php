<?php
use \AcceptanceTester;

class viewProgramCest
{
    public function viewProgram(AcceptanceTester $I)
    {
        $I->amOnPage('/events/');
        $I->see('Multi Person Lodging');
        $I->click('Multi Person Lodging');
        $I->see('REGISTER NOW');
    }
}
