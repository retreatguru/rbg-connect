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
        $I->see('Donec non enim in');

        $I->wantTo('Verify that listing path to missing teacher throws a 404 error');
        $I->amOnPage('/leaders/123456/not-a-real-teacher');
        $I->see('The page you were looking for could not be found', '.error404-content');
    }

    public function viewTeacherViaProgram(AcceptanceTester $I)
    {
        $I->amOnPage('/events/');
        $I->click('Program w/ Teachers');
        $I->click('Yogi Bear Test');
        $I->see('Yogi Bear Test');
        $I->see('A yogi is a practitioner of yoga');
    }

    // todo: test teacher category via url: ie. /teachers/category/awesome/
}