<?php

    // import methods
    require ".backend/methods.php";

    // page index of posts array
    $page = (isset($_GET["page"]) ? intval($_GET["page"]) : 1);
    // posts per page limitation
    $limit = (isset($_GET["limit"]) ? intval($_GET["limit"]) : 10);
    // category filter
    $category = (isset($_GET["category"]) ? $_GET["category"] : "");
    // search keyword
    $search = (isset($_GET["search"]) ? $_GET["search"] : "");

    // get all posts summary
    $data = getAllPosts($page, $limit, $category, $search);

    // echo post feed
    echo json_encode($data["feed"]);

?>