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

        $I->click('Multi Person Lodging');
        $I->see('REGISTER NOW');
    }

    public function viewProgramCategory(AcceptanceTester $I)
    {
        $I->amOnPage('/events/category/plant-medicine');
        $I->see('Example Program');
    }
}
