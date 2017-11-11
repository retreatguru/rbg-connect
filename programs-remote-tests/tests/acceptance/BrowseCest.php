<?php

class BrowseCest
{
    public function homePage(AcceptanceTester $I)
    {
        $I->amOnPage('/hello-world/');
        $I->see('Welcome to WordPress');
    }
}