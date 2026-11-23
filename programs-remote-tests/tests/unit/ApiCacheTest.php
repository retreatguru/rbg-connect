<?php

class ApiCacheTest extends \Codeception\Test\Unit
{
    protected function _before()
    {
        $GLOBALS['rs_api_status'] = null;
        $GLOBALS['wp_test_options'] = array();
        $GLOBALS['wp_test_requests'] = array();
        RS_Connect_Api::$programs = array();
    }

    /**
     * @dataProvider validStatusCodes
     */
    public function testValidStatusCodeDoesNotFallBackToCache($status)
    {
        $this->cacheTeacher();
        $this->respondWith($status, '[{"code":"json_post_invalid_id","message":"Teacher not found."}]');

        $this->assertNull(RS_Connect_Api::get_teacher(123));
        $this->assertSame('good', $GLOBALS['rs_api_status']);
        $this->assertFalse(get_option($this->teacherCacheSlug()), 'Stale cache should be cleared');
    }

    public function validStatusCodes()
    {
        return array(array(400), array(401), array(403), array(404), array(410));
    }

    /**
     * @dataProvider unavailableStatusCodes
     */
    public function testUnavailableApiFallsBackToCache($status)
    {
        $this->cacheTeacher();
        $this->respondWith($status);

        $this->assertSame('Cached Teacher', RS_Connect_Api::get_teacher(123)->name);
        $this->assertSame('down', $GLOBALS['rs_api_status']);
    }

    public function unavailableStatusCodes()
    {
        return array(array(408), array(429), array(500), array(502), array(503), array(504));
    }

    public function testConnectionErrorFallsBackToCache()
    {
        $this->cacheTeacher();
        $GLOBALS['wp_test_response'] = new WP_Error();

        $this->assertSame('Cached Teacher', RS_Connect_Api::get_teacher(123)->name);
        $this->assertSame('down', $GLOBALS['rs_api_status']);
    }

    public function testSuccessfulResponseReturnsFreshData()
    {
        $this->cacheTeacher();
        $this->respondWith(200, '{"ID":123,"name":"Fresh Teacher"}');

        $this->assertSame('Fresh Teacher', RS_Connect_Api::get_teacher(123)->name);
        $this->assertSame('good', $GLOBALS['rs_api_status']);
    }

    public function testMissingProgramIsOnlyRequestedOncePerPageLoad()
    {
        $this->respondWith(404);

        $this->assertNull(RS_Connect_Api::get_program(123));
        $this->assertNull(RS_Connect_Api::get_program(123));
        $this->assertCount(1, $GLOBALS['wp_test_requests']);
    }

    private function respondWith($status, $body = '')
    {
        $GLOBALS['wp_test_response'] = array('response' => array('code' => $status), 'body' => $body);
    }

    private function cacheTeacher()
    {
        update_option($this->teacherCacheSlug(), serialize((object) array('ID' => 123, 'name' => 'Cached Teacher')));
    }

    private function teacherCacheSlug()
    {
        return RS_Connect_Api::api_cache_slug(RS_Connect_Api::get_base_url().'/wp-json/teachers/123?'.RS_Connect_Api::version());
    }
}
