<?php
// Here you can initialize variables that will be available to your tests

// Minimal WordPress stand-ins so RS_Connect_Api can be tested without loading WordPress.
// Tests control the api response through $GLOBALS['wp_test_response'] and inspect $GLOBALS['wp_test_requests'].

class WP_Error
{
}

class RS_Connect
{
    public static $plugin_version = 'wp-test';
}

function is_wp_error($thing)
{
    return $thing instanceof WP_Error;
}

function add_query_arg($args, $url)
{
    return $url.(strpos($url, '?') === false ? '?' : '&').http_build_query($args);
}

function wp_remote_get($url, $args = array())
{
    $GLOBALS['wp_test_requests'][] = $url;

    return $GLOBALS['wp_test_response'];
}

function wp_remote_retrieve_response_code($response)
{
    return $response['response']['code'] ?? '';
}

function wp_remote_retrieve_body($response)
{
    return $response['body'] ?? '';
}

function get_option($option, $default = false)
{
    return $GLOBALS['wp_test_options'][$option] ?? $default;
}

function update_option($option, $value)
{
    $GLOBALS['wp_test_options'][$option] = $value;

    return true;
}

function delete_option($option)
{
    unset($GLOBALS['wp_test_options'][$option]);

    return true;
}

require_once dirname(__DIR__, 3).'/programs-remote-listings/rs-connect-api.php';
