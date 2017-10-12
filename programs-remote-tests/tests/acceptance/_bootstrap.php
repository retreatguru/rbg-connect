<?php

// Reset the DB before running the tests!
if (getenv('CIRCLE_BUILD_NUM')) {
    $url = 'https://tests.secure.retreat.guru';
} else {
    $url = 'http://tests.programs.dev';
}

$ch = curl_init($url. '/?rs_clear_database_and_seed=true');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_exec($ch);