<?php
namespace Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

class AcceptanceHelper extends \Codeception\Module
{
    function changeDefaultPages(\AcceptanceTester $I, $programPage, $teacherPage)
    {
        $I->loginAdmin($I);

        // Save settings
        $I->amOnPage('/wp-admin/admin.php?page=options-mbm');
        $I->fillField('#rs_domain', 'tests');
        $I->selectOption('#page-programs', $programPage);
        $I->selectOption('#page-teachers', $teacherPage);
        $I->click('Save');

        $I->seeOptionIsSelected('#page-programs', $programPage);
        $I->seeOptionIsSelected('#page-teachers', $teacherPage);
    }

    public function loginAdmin(\AcceptanceTester $I, $page = '/wp-login.php')
    {
        $I->amOnPage($page);
        $url = $I->grabFromCurrentUrl();

        // login only if they need to
        if (strpos($url, 'wp-login.php')) {
            $I->fillField('#user_login', 'admin');
            $I->fillField('#user_pass', 'admin');
            $I->click('#wp-submit');
        }
    }
}
