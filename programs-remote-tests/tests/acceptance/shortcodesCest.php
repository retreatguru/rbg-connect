<?php
use \AcceptanceTester;

class shortcodesCest
{
    public function listEvents(AcceptanceTester $I)
    {
        $I->amOnPage('/shortcode-event-list');
        $I->see('Example Program');
    }
    public function listEventsTable(AcceptanceTester $I)
    {
        $I->amOnPage('/shortcode-event-list-table');
        $I->see('Available Spots');
        $I->see('Register');
        $I->see('Events');
        $I->see('Dates');
    }
    public function listTeachers(AcceptanceTester $I)
    {
        $I->amOnPage('/shortcode-teachers');
        $I->see('Yogi Bear Test');
    }
}