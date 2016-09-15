<?php

class _ConfigureCest
{
    public function saveSettings(AcceptanceTester $I)
    {
        // this needs to happen first. Doh.
        $I->changeDefaultPages($I, 'Events', 'Teachers');
    }
}
