<?php
use \AcceptanceTester;

class listProgramsCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function listPrograms(AcceptanceTester $I)
    {
        $I->amOnPage('/programs/');
        $I->see('Donation Payment Program');
        $I->see('Example Program');
        //todo: you can't click into a program due to permalinks not being setup properly in circle.yml

    }
}