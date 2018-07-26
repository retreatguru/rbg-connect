<?php

// Reset the DB before running the tests!

if (getenv('CIRCLE_BUILD_NUM')) {
    $url = 'https://tests.secure.retreat.guru';
} else if (getenv('TEST_HOST')) {
    $test_host = getenv('TEST_HOST');
    $sub_domain_default = getenv('TEST_SUB_DOM');
    $url = "http://$sub_domain_default.$test_host";
}

$ch = curl_init($url. '/?rs_clear_database_and_seed=true');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_exec($ch);