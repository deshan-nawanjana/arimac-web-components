<?php

    // allow origin headers
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: *');

    // set content type header
    header("Content-Type: application/json");

    // blogger feed url
    $FEED_URL = "https://teamarimac.blogspot.com/feeds";
    // cache time divisor
    $CACHE_TIME = 1000;

?>