<?php

class ConfigureCest
{
    public function saveSettings(AcceptanceTester $I)
    {
        $I->changeDefaultPages($I, 'Events', 'Teachers');
    }
}
