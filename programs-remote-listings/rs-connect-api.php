<?php

class RS_Connect_Api
{
    public static $programs = array();

    public static function get_program($id)
    {
        // quick and dirty single request cache because we call this 3 times on a single page load. ugh.
        if (empty(self::$programs[$id])) {
            self::$programs[$id] = self::remote_get('events/' . $id . '?' . self::version());
        }

        return self::$programs[$id];
    }

    public static function get_programs($vars = null)
    {
        return self::remote_get('events/?'.$vars.'&'.self::version());
    }

    public static function get_teachers($vars = null)
    {
        return self::remote_get('teachers/?'.$vars.'&'.self::version());
    }

    public static function get_teacher($id)
    {
        return self::remote_get('teachers/'.$id.'?'.self::version());
    }

    public static function get_base_url()
    {
        $options = get_option('rs_remote_settings');

        $test_host = getenv('TEST_HOST');
        $sub_domain_default = getenv('TEST_SUB_DOM') ?: 'tests';
        $base_domain = 'secure.retreat.guru';
        $http = 'https://';

        if (is_string($test_host)) {
            $base_domain = $test_host;
            $http = 'http://';
        }

        $sub_domain = ! empty($options['rs_domain']) ? $options['rs_domain'] : $sub_domain_default;

        return $http.$sub_domain.'.'.$base_domain;
    }

    /**
     * If the RBG api is down, return cached data, otherwise get fresh from api and save data for later
     * todo: better would be to always serve cached data, then reset cache when new data is available via remote post from RBG.
     *
     * @param $url
     * @return array|bool|mixed|object
     */
    private static function remote_get($url)
    {
        global $rs_api_status;

        $url = self::get_base_url().'/wp-json/'.$url;

        if ($rs_api_status == 'down') {
            // if api is down then return cached version
            return self::get_api_cache($url);
        }

        // ensure api calls are cached
        $versioned_url = add_query_arg(array('rs-rand' => rand()), $url);
        $args = array(
            'timeout' => 10,
        );
        $response = wp_remote_get($versioned_url, $args);

        if (is_wp_error($response) || 200 != wp_remote_retrieve_response_code($response)) {
            $rs_api_status = 'down';

            return self::get_api_cache($url);
        }

        $rs_api_status = 'good';
        $body = json_decode(wp_remote_retrieve_body($response));
        self::save_api_cache($url, $body);

        return $body;
    }

    // occasionally save api data in case RBG is down later, if the value is the same, wp won't update it
    public static function save_api_cache($url, $body)
    {
        if (200 === rand(0, 200)) {
            update_option(self::api_cache_slug($url), serialize($body));
        }
    }

    public static function get_api_cache($url)
    {
        return unserialize(get_option(self::api_cache_slug($url)));
    }

    public static function api_cache_slug($url)
    {
        return 'rs_api_cache_'.preg_replace('/[^a-zA-Z0-9_-]/', '', $url);
    }

    public static function version()
    {
        return 'ver='.RS_Connect::$plugin_version;
    }
}
