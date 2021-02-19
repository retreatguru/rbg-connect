<?php

class TeachersCest
{
    public function listTeachers(AcceptanceTester $I)
    {
        $I->amOnPage('/leaders/');
        $I->see('Yogi Bear Test');
        $I->see('A yogi is a practitioner of yoga');
        $I->see('Kumare Test');
        $I->see('Test description');

        $I->click('Yogi Bear Test');
        $I->see('A yogi is a practitioner of yoga.');

        $I->see('Events with Yogi Bear Test');
        $I->see('Program w/ Teachers');
        $I->see('Exhaustive program');
        $I->see('Pellentesque habitant morbi tristique senectus et netus');
    }

    public function viewTeacherViaProgram(AcceptanceTester $I)
    {
        $I->amOnPage('/events/');
        $I->click('Program w/ Teachers');
        $I->click('Yogi Bear Test');
        $I->see('Yogi Bear Test');
        $I->see('A yogi is a practitioner of yoga');
    }

    public function verifyExcerptLength(AcceptanceTester $I)
    {
        $full_length_text = 'A yogi is a practitioner of yoga. The term yogi is also used to refer specifically to Siddhas, and broadly to refer to ascetic practitioners of meditation in a number of Indian religions including Hinduism, Buddhism, and Jainism.';
        $excerpt_text = 'A yogi is a practitioner of yoga.';

        $I->amOnPage('/leaders/');
        $I->see($full_length_text);
        $I->loginAdmin($I);
        $I->amOnPage('/wp-admin/admin.php?page=options-mbm');
        $I->fillField('input[name="rs_remote_settings[rs_template][limit_description]"]', '7');
        $I->click('Save');
        $I->amOnPage('/wp-admin/admin.php?page=options-mbm');
        $I->seeInField('input[name="rs_remote_settings[rs_template][limit_description]"]', '7');

        $I->amOnPage('/leaders/');
        $I->dontSee($full_length_text);
        $I->see($excerpt_text); // 7 word max

        // reset settings
        $I->amOnPage('/wp-admin/admin.php?page=options-mbm');
        $I->fillField('input[name="rs_remote_settings[rs_template][limit_description]"]', '100');
        $I->click('Save');
    }

    public function verify404PageNotFound(AcceptanceTester $I)
    {
        $I->wantTo('Verify that listing path to missing teacher throws a 404 error');
        $I->amOnPage('/leaders/123456/not-a-real-teacher');

        try { // 404 pages are different depending on the theme, and local and ci have different themes :(
            $I->seeElement('.error404-content');
        } catch (Exception $e) {
            $I->seeElement('.error-404');
        }
    }

    // todo: test teacher category via url: ie. /teachers/category/awesome/
}