<?php

    require ".backend/backend.php";
    require ".backend/methods.php";

    Endpoints([
        "GET" => [
            "callback" => function($request) {
                // get request params
                $params = $request["params"];
                // page index
                $page = intval(GetNode($params, "page", 1));
                // posts per page
                $limit = intval(GetNode($params, "limit", 10));
                // search category
                $category = GetNode($params, "category", "");
                // search keyword
                $search = GetNode($params, "search", "");
                // get posts summaries
                $data = getAllPosts($page, $limit, $category, $search);
                // check feed node
                if(isset($data["feed"])) {
                    // get feed node
                    $feed = $data["feed"];
                    // check entry node
                    if(isset($feed['entry'])) {
                        // for each post
                        foreach($feed['entry'] as $item) {
                            // update posts table
                            updatePostItemSummary($item);
                        }
                    }
                    // return feed
                    Response(200, $feed);
                } else {
                    // response error
                    Response(500);
                }
            }
        ]
    ])

?>