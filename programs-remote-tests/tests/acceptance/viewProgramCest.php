<?php
use \AcceptanceTester;

class viewProgramCest
{
    public function viewProgram(AcceptanceTester $I)
    {
        $I->amOnPage('/?programs=true&program=4');
        $I->see('Example Program');
        $I->see('Register Now');
        $I->see('$100.00');
        //todo: Create or Modify seeder to include a program with more details
    }
}