<?php
use \AcceptanceTester;

class BrowseCest
{
    public function homePage(AcceptanceTester $I)
    {
        $I->amOnPage('/hello-world/');
        $I->see('Welcome to WordPress. This is your first post. Edit or delete it, then start blogging!');
    }
}