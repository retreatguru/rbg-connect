<?php

class SingleProgramTemplateTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        $GLOBALS['wp_test_options'] = array();
        $GLOBALS['shortcode_atts'] = array();
    }

    public function testSummaryPanelRowsReplaceTheLegacyBlocks()
    {
        // program-edit-v3 sites send the rows and keep filling the legacy fields for older plugin versions.
        $xpath = $this->render(array(
            'summary_panel' => array(
                $this->row('When', '<p>Arrive 3pm</p>', 'date'),
                $this->row('Level:', 'All levels', null),
                $this->row('', 'Bring a mat', null),
                $this->row('Price', '<ul><li>$100</li></ul>', 'price'),
                $this->row('Where', 'Nelson', 'location'),
                $this->row('Getting there', 'Take the ferry', null),
            ),
        ));

        $this->assertSame(array(
            'rs-program-early-bird-discount rs-highlight',
            'rs-program-datetime',
            'rs-program-custom-wrap',
            'rs-program-price',
            'rs-program-location',
            'rs-program-custom-wrap',
            'rs-button',
            'rs-regsitration-wrap',
        ), $this->sidebarClasses($xpath));

        $this->assertSame(array('When:', 'Level:', 'Price', 'Where:', 'Getting there:'), $this->texts($xpath, '//span[@class="rs-program-label"]'));
        $this->assertSame(array('When: Arrive 3pm', 'Level: All levels', 'Bring a mat', 'Price $100', 'Where: Nelson', 'Getting there: Take the ferry'), $this->texts($xpath, '//div[@class="rs-program-datetime" or @class="program-custom" or @class="rs-program-price" or @class="rs-program-location"]'));
        $this->assertSame(1, $xpath->query('//div[@class="rs-program-custom-wrap"][1]/div[@class="program-custom"][2]')->length, 'Consecutive untagged rows share one wrap');
        $this->assertSame(0, $xpath->query('//*[contains(text(), "Legacy")]')->length, 'The legacy fields are not printed next to the rows');
    }

    public function testEmptySummaryPanelPrintsNoRowsAndNoLegacyBlocks()
    {
        // A program-edit-v3 program with no row that shows sends an empty list. That's the admin's panel, not a reason to
        // fall back.
        $xpath = $this->render(array('summary_panel' => array()));

        $this->assertSame(array(
            'rs-program-early-bird-discount rs-highlight',
            'rs-button',
            'rs-regsitration-wrap',
        ), $this->sidebarClasses($xpath));
    }

    public function testLegacyBlocksWithoutSummaryPanel()
    {
        // Sites without program-edit-v3 and older RetreatGuru versions don't send the field.
        $xpath = $this->render(array());

        $this->assertSame(array(
            'rs-program-early-bird-discount rs-highlight',
            'rs-program-price',
            'rs-program-datetime',
            'rs-program-location',
            'rs-program-address',
            'rs-program-contact',
            'rs-program-custom-wrap',
            'rs-button',
            'rs-regsitration-wrap',
        ), $this->sidebarClasses($xpath));
        $this->assertSame(array('Date & Time Details: Legacy date', 'Location: Legacy location', 'Address: Legacy address'), $this->texts($xpath, '//p[@class="rs-program-datetime" or @class="rs-program-location" or @class="rs-program-address"]'));
    }

    private function row($label, $html, $source)
    {
        return (object) array('label' => $label, 'html' => $html, 'source' => $source);
    }

    private function render(array $fields)
    {
        $GLOBALS['rs_the_program'] = (object) ($fields + array(
            'ID' => 33,
            'title' => 'Summary Panel Program',
            'early_bird_discount' => 'Early bird',
            'price_details' => 'Legacy price',
            'date_time' => 'Legacy date',
            'location' => 'Legacy location',
            'address' => 'Legacy address',
            'contact' => 'Legacy contact',
            'custom' => 'Legacy custom',
            'email' => 'info@example.com',
            'registration_action' => 'Register Now',
        ));

        ob_start();
        include dirname(__DIR__, 3).'/programs-remote-listings/templates/shortcode-programs-single.php';
        $dom = new DOMDocument();
        $dom->loadHTML('<?xml encoding="UTF-8">'.ob_get_clean(), LIBXML_NOERROR);

        return new DOMXPath($dom);
    }

    private function sidebarClasses(DOMXPath $xpath)
    {
        $classes = array();
        foreach ($xpath->query('//div[@class="rs-meta-content-container"]/*') as $element) {
            $classes[] = $element->getAttribute('class');
        }

        return $classes;
    }

    private function texts(DOMXPath $xpath, $query)
    {
        $texts = array();
        foreach ($xpath->query($query) as $element) {
            $texts[] = trim(preg_replace('/\s+/', ' ', $element->textContent));
        }

        return $texts;
    }
}
