<?php

    // import config
    require "config.php";
    // import helpers
    require "helpers.php";

    // method to get all posts summary
    function getAllPosts($page, $limit, $category, $search) {
        // get request path
        $path = "/posts/summary";
        // include category if valid
        if($category !== "") { $path .= "/-/" . rawurlencode($category); }
        // get posts summary
        $data = GET($path, [
            // page index of posts array
            "start-index" => ($page - 1) * $limit + 1,
            // posts per page limitation
            "max-results" => $limit,
            // search keyword
            "q" => $search,
            // sort by updated date
            "orderby" => "updated"
        ]);
        // check for entry node
        if(isset($data["feed"]["entry"]) === false) {
            // set empty array
            $data["feed"]["entry"] = [];
        }
        // return data
        return $data;
    }

?>