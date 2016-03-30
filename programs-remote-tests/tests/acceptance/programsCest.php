<?php
use \AcceptanceTester;

class programsCest
{
    public function listPrograms(AcceptanceTester $I)
    {
        // todo: To be more thorough we need our test seeder to add more content to our programs
        $I->amOnPage('/events/');
        $I->see('Multi Person Lodging');
        $I->see('Price Options Program');
    }
    public function viewProgram(AcceptanceTester $I)
    {
        $I->amOnPage('/events/');
        $I->see('Multi Person Lodging');
        $I->click('Multi Person Lodging');
        $I->see('REGISTER NOW');
    }
}
