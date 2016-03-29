<?php
use \AcceptanceTester;

class configureCest
{
    public function saveSettings(AcceptanceTester $I)
    {
        $I->amOnPage('/wp-admin/');
        $I->fillField('#user_login', 'admin');
        $I->fillField('#user_pass', 'admin');
        $I->click('Log In');

        // Get page IDs
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click('Events');
        $event_id = $I->grabFromCurrentUrl('/post=(\d+)/');
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click('Teachers');
        $teacher_id = $I->grabFromCurrentUrl('/post=(\d+)/');

        // Save settings
        $I->click('Retreat Guru Settings');
        $I->fillField('#rs_domain', 'tests');
        $I->selectOption('#page-programs', $event_id);
        $I->selectOption('#page-teachers', $teacher_id);

        $I->click('Save');
    }
}