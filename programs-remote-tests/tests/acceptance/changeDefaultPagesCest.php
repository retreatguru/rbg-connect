<?php

class changeDefaultPages
{
    public function changePages(AcceptanceTester $I)
    {
        $I->changeDefaultPages($I, 'Retreats', 'Gurus');

        $I->amOnPage('/retreats/');
        $I->see('Multi Person Lodging');
        $I->click('Multi Person Lodging');
        $I->see('REGISTER NOW');

        $I->amOnPage('/gurus/');
        $I->see('Yogi Bear Test');
        $I->click('Yogi Bear Test');
        $I->see('A yogi is a practitioner of yoga.');

        $I->changeDefaultPages($I, 'Events', 'Teachers');
    }
}