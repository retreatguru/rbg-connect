<?php

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

    public function ifShortCodeIsOnCorePageDontAutoAdd(AcceptanceTester $I)
    {
        $I->loginAdmin($I);
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click('Events');
        $I->fillField('.wp-editor-area', '[rs_programs category="plant-medicine"]');
        $I->click('#publish');

        $I->wantTo('see that because we added our own custom shortcode above, the default shortcode output is not added.');
        $I->amOnPage('/events');
        $I->dontSee('Multi Person Lodging');
        $I->see('Example program');
        $I->see('Exhaustive program');
        $I->click('Exhaustive program', '.rs-program-title');
        $I->dontSee('Example program');
        $I->see('Register Now');

        // put back to normal
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click('Events');
        $I->fillField('.wp-editor-area', '');
        $I->click('#publish');
    }
}