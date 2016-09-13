<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class AcceptanceHelper extends \Codeception\Module
{
    function changeDefaultPages(\AcceptanceTester $I, $programPage, $teacherPage)
    {
        $I->loginAdmin($I);

        // Get page IDs
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click($programPage);
        $event_id = $I->grabFromCurrentUrl('/post=(\d+)/');
        $I->amOnPage('/wp-admin/edit.php?post_type=page');
        $I->click($teacherPage);
        $teacher_id = $I->grabFromCurrentUrl('/post=(\d+)/');

        // Save settings
        $I->amOnPage('/wp-admin/admin.php?page=options-mbm');
        $I->fillField('#rs_domain', 'tests');
        $I->selectOption('#page-programs', $event_id);
        $I->selectOption('#page-teachers', $teacher_id);

        $I->click('Save');
        $I->click('Log Out');
    }

    public function loginAdmin(\AcceptanceTester $I)
    {
        $I->amOnPage('/wp-admin/');
        $I->fillField('#user_login', 'admin');
        $I->fillField('#user_pass', 'admin');
        $I->click('Log In');
    }
}
