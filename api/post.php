<?php

    require ".backend/backend.php";
    require ".backend/methods.php";

    Endpoints([
        "GET" => [
            "validate" => [
                "params" => ["id"]
            ],
            "callback" => function($request) {
                // get post data
                $data = getPostById($request['params']['id']);
                // check feed node
                if(isset($data["entry"])) {
                    // get post entry
                    $post = $data["entry"];
                    // update post
                    $post = updatePostItem($post);
                    // return post
                    Response(200, $post);
                } else {
                    // response error
                    Response(500);
                }
            }
        ]
    ])

?>