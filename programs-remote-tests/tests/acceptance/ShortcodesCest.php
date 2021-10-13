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

    public function listEventsWithCustomContent(AcceptanceTester $I)
    {
        $I->wantTo('Set default content for the list page using shortcodes');
        $I->loginAdmin($I);
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click('Shortcode Event List');
        $I->fillField('#wp-content-editor-container textarea', '[rs_programs category="plant-medicine"]');
        $I->click('#publish');

        $I->amOnPage('/shortcode-event-list');
        $I->see('Example Program');
        $I->see('Exhaustive Program');

        $I->wantTo('Set custom content for the list page using shortcodes');
        $I->loginAdmin($I);
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click('Shortcode Event List');
        $I->fillField('#wp-content-editor-container textarea',
            '[rs_programs show_first_teacher_photo show_first_price show_price_details show_more_link="see more info..."'.
            ' show_availability="Spots" show_availability_words="Spots Left" show_register_link="Apply Now"'.
            ' wait_list_text="Join wait list"]'
        );
        $I->click('#publish');

        $I->amOnPage('/shortcode-event-list');
        $I->see('Example Program');
        $I->see('Exhaustive Program');

        $I->see('Exhaustive program', '.rs-program-title');
        $I->see('With Yogi Bear Test and Kumare Test', '.rs-program-with-teachers');
        $I->see('Flexible Dates', '.rs-program-date');
        $I->see('Nelson', '.rs-program-location');
        $I->see('From $100.00', '.rs-program-first-price');
        $I->see('see more info', '.rs-program-see-more-link');
        $I->see('Apply Now', '.rs-program-register-link');
    }

    public function listEventsTable(AcceptanceTester $I)
    {
        $I->wantTo('Set up a default list table');
        $I->loginAdmin($I);
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click('Shortcode Event List Table');
        $I->fillField('#wp-content-editor-container textarea',
            '[rs_programs table show_title show_date show_availability show_availability_words show_teachers show_location show_price_details show_price_first show_more_link show_register_link wait_list_text]'
        );
        $I->click('#publish');

        $I->amOnPage('/shortcode-event-list-table');

        $I->wantTo('Verify that the headers are correct');
        $I->see('Dates', '.rs-program thead .rs-dates');
        $I->see('Events', '.rs-program thead .rs-title');
        $I->see('Price from', '.rs-program thead .rs-price-first');
        $I->see('Details', '.rs-program thead .rs-show-more-link');
        $I->see('Available Spots', '.rs-program thead .rs-availability');
        $I->see('Register', '.rs-program thead .rs-show-register-link');

        $I->wantTo('Verify the table content');
        $I->see('Ongoing Dateless Program', 'tbody tr:nth-child(1) .rs-title'); // ensure order
        $I->see('Flexible Dates', '.rs-program-lodging-hotel-program .rs-dates');
        $I->see('Lodging Hotel Program', '.rs-program-lodging-hotel-program .rs-title');
        $I->see('View Details', '.rs-program-lodging-hotel-program .rs-show-more-link');
        $I->see('Register Now', '.rs-program-lodging-hotel-program .rs-show-register-link');
        $I->see('$800.00', '.rs-program-multi-person-tiered .rs-price-first');
        $I->see('4', '.rs-program-multi-person-tiered .rs-availability');
        $I->see('Open', '.rs-program-multi-person-tiered .rs-availability-words');

        $I->wantTo('Set custom head titles for the table using shortcodes');
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click('Shortcode Event List Table');
        $I->fillField('#wp-content-editor-container textarea',
            '[rs_programs table show_title="Lux Retreats" show_date="Date" show_availability="Spots"'.
            ' show_availability_words="Spots Left" show_teachers="Hosts" show_location="Locale"'.
            ' show_price_details="Cost" show_price_first="Cost from" show_more_link="More info"'.
            ' show_register_link="Apply Now" wait_list_text="Interested"]'
        );
        $I->click('#publish');

        $I->wantTo('Verify that the custom headers are correct');
        $I->amOnPage('/shortcode-event-list-table');
        $I->see('Date', '.rs-program thead .rs-dates');
        $I->see('Lux Retreats', '.rs-program thead .rs-title');
        $I->see('Hosts', '.rs-program thead .rs-teachers');
        $I->see('Cost', '.rs-program thead .rs-price');
        $I->see('Cost from', '.rs-program thead .rs-price-first');
        $I->see('More info', '.rs-program thead .rs-show-more-link');
        $I->see('Spots', '.rs-program thead .rs-availability');
        $I->see('Spots Left', '.rs-program thead .rs-availability-words');
        $I->see('Apply Now', '.rs-program thead .rs-show-register-link');

        $I->see('Flexible Dates', '.rs-program td.rs-dates');
        $I->see('Lodging Hotel Program', '.rs-program td.rs-title');
        $I->see('With Yogi Bear ', '.rs-program td.rs-teachers');
        $I->see('Nelson ', '.rs-program td.rs-location');
        $I->see('Discount Sale – $69.00', '.rs-program td.rs-price');
        $I->see('$50.00', '.rs-program td.rs-price-first');
        $I->see('View Details', '.rs-program td.rs-show-more-link');
        $I->see('10', '.rs-program-hotel-pricing td.rs-availability');
        $I->see('Open', '.rs-program-hotel-pricing td.rs-availability-words');
        $I->see('Apply Now', '.rs-program-hotel-pricing td.rs-show-register-link');
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
        $I->dontSee('Register Now', '.rs-show-register-link');
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
        $I->click('Exhaustive program', '.rs-program-title');
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
