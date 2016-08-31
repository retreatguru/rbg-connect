<?php
use \AcceptanceTester;

class teachersCest
{
    public function listTeachers(AcceptanceTester $I)
    {
        $I->amOnPage('/teachers/');
        $I->see('Yogi Bear Test');
    }
    public function viewTeacher(AcceptanceTester $I)
    {
        $I->amOnPage('/teachers/');
        $I->click('Yogi Bear Test');
        $I->see('A yogi is a practitioner of yoga.');
    }
    public function viewTeacherViaProgram(AcceptanceTester $I)
    {
        $I->amOnPage('/events/');
        $I->click('Program w/ Teachers');
        $I->click('Yogi Bear Test');
    }
}