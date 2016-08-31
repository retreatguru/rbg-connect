<?php
use \AcceptanceTester;

class configureCest
{
    public function saveSettings(AcceptanceTester $I)
    {
        $I->changeDefaultPages($I, 'Events', 'Teachers');
    }
}
