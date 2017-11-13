<?php

class ShortcodesCest
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

        $I->wantTo('Verify the table content');
        $I->see('Lodging Hotel Program', 'tbody tr:nth-child(1) .rs-title'); // ensure order
        $I->see('Flexible Dates', '.rs-program-lodging-hotel-program .rs-dates');
        $I->see('Lodging Hotel Program', '.rs-program-lodging-hotel-program .rs-title');
        $I->see('View Details', '.rs-program-lodging-hotel-program .rs-show-more-link');
        $I->see('Register Now', '.rs-program-lodging-hotel-program .rs-show-register-link');
        $I->see('$800.00', '.rs-program-multi-person-tiered .rs-price-first');
        $I->see('4', '.rs-program-multi-person-tiered .rs-availability');
        $I->see('Open', '.rs-program-multi-person-tiered .rs-availability-words');
    }

    public function eventListHideDescriptionAndLocation(AcceptanceTester $I)
    {
        $I->amOnPage('/shortcode-event-list-hide-stuff/');

        $I->see('Exhaustive Program');
        $I->see('No Price Program');

        $I->wantTo('Verify that I can\'t see description');
        $I->dontSee('Mauris placerat eleifend leo. Quisque sit amet est et sapien ullamcorper pharetra');
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

    public function listTeachersCategory(AcceptanceTester $I)
    {
/*        $I->amOnPage('/shortcode-teachers');
        $I->see('Kumare Test');
        $I->see('Test description');
        $I->dontSee('Yogi Bear Test');*/
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
        $I->click('Exhaustive program', '.rs-title');
        $I->dontSee('Example program');
        $I->see('Register Now');

        // put back to normal
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click('Events');
        $I->fillField('.wp-editor-area', '');
        $I->click('#publish');
    }

    public function testRegisterButtonShortcode(AcceptanceTester $I)
    {
        $I->loginAdmin($I);
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click('Sample Page');
        $I->fillField('.wp-editor-area', '[rs_register_button id=33]');
        $I->click('#publish');
        $I->amOnPage('/sample-page');
        $I->see('Register Now', '.rs-register-link');
    }
}
