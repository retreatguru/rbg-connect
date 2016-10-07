<?php

class shortcodesCest
{
    public function listEventsByCategory(AcceptanceTester $I)
    {
        $I->amOnPage('/shortcode-event-list');
        $I->see('Example Program');
        $I->see('Exhaustive Program');

        $I->dontSee('Lodging Hotel Program');
        $I->dontSee('Lodging Price Program');
        $I->dontSee('Renter Program');
        $I->dontSee('Multi Person Lodging');
        $I->dontSee('Multi Person Tiered');
    }

    public function listEventsTable(AcceptanceTester $I)
    {
        $I->amOnPage('/shortcode-event-list-table');

        $I->wantTo('Verify that the headers are correct');
        $I->see('Dates', '.rs-program thead .rs-dates');
        $I->see('Events', '.rs-program thead .rs-title');
        $I->see('Price from', '.rs-program thead .rs-price-first');
        $I->see('Details', '.rs-program thead .rs-show-more-link');
        $I->see('Available Spots', '.rs-program thead .rs-availability');
        $I->see('Register', '.rs-program thead .rs-show-register-link');

        $I->see('Flexible Dates', 'tbody tr:nth-child(1) .rs-dates');
        $I->see('Lodging Hotel Program', 'tbody tr:nth-child(1) .rs-title');
        $I->see('View Details', 'tbody tr:nth-child(1) .rs-show-more-link');
        $I->see('Register Now', 'tbody tr:nth-child(1) .rs-show-register-link');
    }

    public function eventListHideDescription(AcceptanceTester $I)
    {
        $I->amOnPage('/shortcode-event-list-hide-text/');

        $I->see('Exhaustive Program');
        $I->see('No Price Program');

        $I->wantTo('Verify that I can\'t see description');
        $I->dontSee('Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra');
    }

    public function eventListHideDate(AcceptanceTester $I)
    {
        $I->amOnPage('/shortcode-event-list-hide-location/');
        $I->dontSee('Nelson');
    }

    public function eventsListTableLess(AcceptanceTester $I)
    {
        $I->amOnPage('/shortcode-event-list-table-less/');

        $I->see('Dates', '.rs-program thead .rs-dates');
        $I->see('Events', '.rs-program thead .rs-title');

        $I->dontSee('Price from');
        $I->dontSee('Details');
        $I->dontSee('Available Spots');
        $I->dontSee('Register');
    }

    public function eventListTableCategory(AcceptanceTester $I)
    {
        $I->amOnPage('/shortcode-event-list-table-by-category/');

        $I->see('Exhaustive Program');
        $I->see('Example Program');

        $I->dontSee('Lodging Hotel Program');
        $I->dontSee('Lodging Price Program');
        $I->dontSee('Renter Program');
        $I->dontSee('Multi Person Lodging');
        $I->dontSee('Multi Person Tiered');
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