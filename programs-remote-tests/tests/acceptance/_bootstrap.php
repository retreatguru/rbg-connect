<?php

//Reset the DB before running the tests!
if (getenv('CIRCLE_BUILD_NUM')) {
    $url = 'https://tests.secure.retreat.guru';
} else {
    $url = 'http://tests.programs.dev';
}
curl_exec(curl_init($url. '/?rs_clear_database_and_seed=true'));