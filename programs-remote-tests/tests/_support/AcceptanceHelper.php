<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class AcceptanceHelper extends \Codeception\Module
{
    function changeDefaultPages($I, $programPage, $teacherPage)
    {
        $I->amOnPage('/wp-admin/');
        $I->fillField('#user_login', 'admin');
        $I->fillField('#user_pass', 'admin');
        $I->click('Log In');

        // Get page IDs
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click($programPage);
        $event_id = $I->grabFromCurrentUrl('/post=(\d+)/');
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click($teacherPage);
        $teacher_id = $I->grabFromCurrentUrl('/post=(\d+)/');

        // Save settings
        $I->click('Retreat Guru Settings');
        $I->fillField('#rs_domain', 'tests');
        $I->selectOption('#page-programs', $event_id);
        $I->selectOption('#page-teachers', $teacher_id);

        $I->click('Save');
        $I->click('Log Out');
    }
}
