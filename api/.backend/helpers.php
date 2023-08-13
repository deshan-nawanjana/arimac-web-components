<?php

    // method to convert object into query string
    function toQueryString($input) {
        // output string
        $output = "?";
        // for each key and value
        foreach($input as $key => $value) {
            // check for non empty value
            if($value !== "") {
                // set parameter on output
                $output .= $key . "=" . $value . "&";
            }
        }
        // return output
        return $output . "alt=json";
    }

    // method to get cache file path
    function getCacheFile($path) {
        // get cahce folder path
        $cache_path = ".backend/cache/";
        // create cache folder if not exists
        if(file_exists($cache_path) === false) { mkdir($cache_path); }
        // invalid file name characters
        $invalid_chars = [":", "/", " ", "%", "\\", "&", "?", "-", "=", "+", ".", "*", ","];
        // get cache file name
        $cache_name = str_replace($invalid_chars, "_", $path);
        // return cache file path
        return $cache_path . $cache_name . ".json";
    }

    // method to get cache data
    function getCache($path) {
        // get global cache time divisor
        global $CACHE_TIME;
        // get time stamp
        $timestamp = intval(time() / $CACHE_TIME);
        // get cache file path
        $cache_file = getCacheFile($path);
        // check for cache file
        if(file_exists($cache_file)) {
            // read cache content
            $content = json_decode(file_get_contents($cache_file), true);
            // check for time stamp and data availability
            if($content["timestamp"] === $timestamp && $content["data"] !== null) {
                // return cache content
                return $content["data"];
            }
        }
        // return as no available cache
        return null;
    }

    // method to set cache data
    function setCache($path, $data) {
        // get time stamp
        $timestamp = intval(time() / 1000);
        // get cache file path
        $cache_file = getCacheFile($path);
        // create cache data
        $cache_data = ["timestamp" => $timestamp, "data" => $data];
        // save on cahce file
        file_put_contents($cache_file, json_encode($cache_data));
    }

    function GET($endpoint, $query) {
        // get global feed url
        global $FEED_URL;
        // create request path 
        $path = $FEED_URL . $endpoint . toQueryString($query);
        // get cache data
        $cache = getCache($path);
        // check cache data
        if($cache !== null) {
            // return cahce data
            return $cache;
        } else {
            // create request curl
            $curl = curl_init();
            // set request url
            curl_setopt($curl, CURLOPT_URL, $path);
            // set return transfer flag
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            // get response
            $resp = curl_exec($curl);
            // decode response
            $data = json_decode($resp, true);
            // save response on cahce
            setCache($path, $data);
            // return data
            return $data;
        }
    }

?>