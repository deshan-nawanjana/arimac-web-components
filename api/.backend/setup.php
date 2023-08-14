<?php

    // allow origin headers
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Allow-Methods: *');
    header('Access-Control-Allow-Headers: *');

    // backend setup for local host
    $setup = [
        // base url for external apis
        "baseurl" => "https://teamarimac.blogspot.com/feeds",
        // cache directory
        "cache_dir" => ".backend/cache/",
        // database details
        "database" => [
            // database name
            "name" => "arimac_web",
            // database credentials
            "auth" => [
                "hostname" => "localhost",
                "username" => "root",
                "password" => ""
            ]
        ]
    ];

?>