<?php

    // method to get all posts with summary
    function getAllPosts($page, $limit, $category, $search) {
        // create query object
        $query = [
            // page index
            "start-index" => ($page - 1) * $limit + 1,
            // posts per page
            "max-results" => $limit,
            // response type
            "alt" => "json"
        ];
        // include search into query if valid
        if($search !== "") { $query["q"] = rawurlencode($search); }
        // get request endpoint
        $endpoint = "/posts/summary";
        // include category into endpoint if valid
        if($category !== "") { $endpoint .= "/-/" . rawurlencode($category); }
        // create request path
        $path = $endpoint . QueryString($query);
        // get cache data
        $cache = GetCache($path);
        // check cache availability
        if($cache === false || $cache === null) {
            // request posts
            $data = GET($path);
            // set data on cahce
            SetCache($path, $data);
        } else {
            // get data from cache
            $data = $cache;
        }
        // return data
        return $data;
    }

    // method to create categories string
    function getCategories($post) {
        // check for category node
        if(isset($post['category'])) {
            // return mapped array
            return array_map(function($item) {
                // filter term text
                return $item['term'];
            }, $post['category']);
        } else {
            // return empty array
            return [];
        }
    }

    // method to check and update post summary item in database
    function updatePostItemSummary($post) {
        // get post fields
        $item = [
            "id" => explode("-", $post['id']['$t'])[2],
            "title" => $post['title']['$t'],
            "summary" => $post['summary']['$t'],
            "image" => GetNode($post, ['media$thumbnail', 'url'], null),
            "updated_at" => $post['published']['$t'],
            "category" => json_encode(getCategories($post)),
            "author_name" => GetNode($post, ['author', '0', 'name', '$t'], null),
            "author_image" => GetNode($post, ['author', '0', 'gd$image', 'src'], null),
            "available" => 1
        ];
        // find records from database
        $records = SelectFromTable("posts",
            ["updated_at", "available"],
            ["id" => $item['id']]
        );
        // check if no record
        if(count($records) === 0) {
            // set default read time
            $item['read_time'] = 600;
            // set default view count
            $item['view_count'] = 0;
            // set default share count
            $item['share_count'] = 0;
            // insert item into posts table
            InsertIntoTable("posts", $item);
        } else {
            // get table record
            $record = $records[0];
            // check for updated time or available
            if($record['updated_at'] !== $item['updated_at'] || $record["available"] === "0") {
                // update item on posts table
                UpdateTable("posts", $item, ["id" => $item['id']]);
            }
        }
    }

    // method to get individual post by id
    function getPostById($id, $type = 'default') {
        // create query object
        $query = ["alt" => "json"];
        // get request endpoint
        $endpoint = "/posts/{$type}/{$id}";
        // create request path
        $path = $endpoint . QueryString($query);
        // get cache data
        $cache = GetCache($path);
        // check cache availability
        if($cache === false || $cache === null) {
            // request posts
            $data = GET($path);
            // set data on cahce
            SetCache($path, $data);
        } else {
            // get data from cache
            $data = $cache;
        }
        // check data
        if($data !== null) {
            // return data
            return $data;
        } else {
            // update post on table
            UpdateTable("posts", ["available" => 0], ["id" => $id]);
            // response not found
            Response(404);
        }
    }

    // method to get reading time
    function getReadTime($html) {
        // get text content
        $text = strip_tags($html);
        // get word list from text
        $list = explode(" ", $text);
        // return read time
        return intval(60 * count($list) / 200);
    }

    // method to check and update post item in database
    function updatePostItem($post) {
        // get post fields
        $item = [
            "id" => explode("-", $post['id']['$t'])[2],
            "updated_at" => $post['published']['$t'],
            "read_time" => getReadTime($post['content']['$t']),
            "available" => 1
        ];
        // find records from database
        $records = SelectFromTable("posts",
            ["view_count", "share_count", "updated_at"],
            ["id" => $item['id']]
        );
        // check if no record or updated time
        if(count($records) === 0 || $records[0]['updated_at'] !== $item['updated_at']) {
            // get post summary
            $summary = getPostById($item['id'], 'summary');
            // update posts table
            updatePostItemSummary($summary['entry']);
            // set view count on post
            $post['view_count'] = 0;
            // set share count on post
            $post['share_count'] = 0;
            // set view count on item
            $item['view_count'] = 1;
        } else {
            // get table record
            $record = $records[0];
            // set view count on post
            $post['view_count'] = intval($record['view_count']);
            // set share count on post
            $post['share_count'] = intval($record['share_count']);
            // increase view count on fields
            $item['view_count'] = $record['view_count'] + 1;
        }
        // set read time on post
        $post['read_time'] = $item['read_time'];
        // update post on table
        UpdateTable("posts", $item, ["id" => $item['id']]);
        // return post
        return $post;
    }

?>