<?php
use \AcceptanceTester;

class BrowseCest
{
    public function homePage(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->see('Just another WordPress site');
    }
}