<?php

    /*

        Backend Helpers by Deshan Nawanjana
        ===================================
        https://github.com/deshan-nawanjana
    
    */

    require "setup.php";

    // ============================== ERROR HANDLING ==============================

    set_error_handler(function($a, $b) {
        Error(500, "Internal Server Error - " . $b);
    });

    // ================================= HELPERS ==================================

    // method to create rabdom value
    function Random() {
		$today = date('YmdHi');
		$startDate = date('YmdHi', strtotime('2012-03-14 09:06:00'));
		$range = $today - $startDate;
		$rand = rand(0, $range);
		$fold =  $startDate . $rand;
		return $startDate . $rand;
	}

    // method to encode base 64
    function EncodeBase64($text)  {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }

    // method to convert string to valid file name
    function FileName($input) {
        // invalid file name characters
        $invalid_chars = [":", "/", " ", "%", "\\", "&", "?", "-", "=", "+", ".", "*", ","];
        // get valid file name
        return str_replace($invalid_chars, "_", $input);
    }

    // method to create query string
    function QueryString($input) {
        // output string
        $output = "?";
        // for each key and value
        foreach($input as $key => $value) {
            // check for first key
            if($output === "?") {
                // set parameter without ampersand
                $output .= $key . "=" . $value;
            } else {
                // set parameter with ampersand
                $output .= "&" . $key . "=" . $value;
            }
        }
        // return output
        return $output;
    }

    // method to get node from object
    function GetNode($input, $path, $default = null) {
        // check path type
        if(is_string($path)) {
            // check node in input
            if(isset($input[$path])) {
                // return node value
                return $input[$path];
            } else {
                // return default value
                return $default;
            }
        } else {
            // for each node in path
            foreach($path as $node) {
                // check node in input
                if(isset($input[$node])) {
                    // jump to child node
                    $input = $input[$node];
                } else {
                    // return default value
                    return $default;
                }
            }
            // return node value
            return $input;
        }
    }

    // ================================= DATABASE =================================

    // connect to database
    function Connect($auth) {
        // get credentials
        $hostname = $auth['hostname'];
        $username = $auth['username'];
        $password = $auth['password'];
        // return connection
        return new mysqli($hostname, $username, $password);
    }

    // method to run sql command
    function SQLRun($commands) {
        // get global connection
        global $connection;
        // run sql commands
        if ($connection->query($commands) === TRUE) {
            // return as success
            return 1;
        } else {
            // return as error
            Error(500, $connection->error);
        }
    }

    // method to get sql query
    function SQLGet($commands) {
        // get global connection
        global $connection;
        // get query response
        $response = mysqli_query($connection, $commands);
        // check response
        if (!$response) {
            // return as error
            Error(500, mysqli_error($connection));
        } else {
            // create output array
            $output = array();
            // while result available
            while($result = $response->fetch_assoc()) {
                // push result to array
                array_push($output, $result);
            }
            // return output array
            return $output;
        }
    }

    // method to create table
    function CreateTable($name, $schema) {
        // sql command lines
        $lines = array();
        // for each field
        foreach ($schema as $field => $options) {
            // push to lines array
            array_push($lines, $field . " " . $options);
        }
        // join lines with commas
        $lines = join(", ", $lines);
        // table create command
        $command = "CREATE TABLE IF NOT EXISTS $name ($lines)";
        // run on connection
        return SQLRun($command);
    }

    // method to select from table
    function SelectFromTable($name, $fields = "*", $conditions = null) {
        // check fields type
        if (is_array($fields)) {
            // join with commas
            $fields = join(", ", $fields);
        }
        // check conditions
        if ($conditions === null) {
            // return all records
            return SQLGet("SELECT $fields FROM $name");
        } else {
            // sql command lines
            $lines = array();
            // for each condition field
            foreach ($conditions as $field => $value) {
                // push to lines array
                array_push($lines, $field . " = " . "'" . addslashes($value) . "'");
            }
            // join fields with and operator
            $lines = join(" AND ", $lines);
            // return records by conditions
            return SQLGet("SELECT $fields FROM $name WHERE $lines");
        }
    }

    // method to insert table data
    function InsertIntoTable($name, $record) {
        // fields array
        $fields = array();
        // values array
        $values = array();
        // for each column
        foreach ($record as $field => $value) {
            // push to fields array
            array_push($fields, $field);
            // push to values array
            array_push($values, "'" . addslashes($value) . "'");
        }
        // join fields with commas
        $fields = join(", ", $fields);
        // join values with commas
        $values = join(", ", $values);
        // table create command
        $command = "INSERT INTO $name($fields) VALUES($values)";
        // run on connection
        return SQLRun($command);
    }

    // method to update table data
    function UpdateTable($name, $data, $conditions) {
        // update command lines
        $updates = array();
        // for each field
        foreach ($data as $field => $value) {
            // push to updates array
            array_push($updates, $field . " = " . "'" . addslashes($value) . "'");
        }
        // join updates with commas
        $updates = join(", ", $updates);
        // sql command lines
        $lines = array();
        // for each condition field
        foreach ($conditions as $field => $value) {
            // push to lines array
            array_push($lines, $field . " = " . "'" . addslashes($value) . "'");
        }
        // join lines with commas
        $lines = join(", ", $lines);
        // update table command
        $command = "UPDATE $name SET $updates WHERE $lines";
        // run on connection
        return SQLRun($command);
    }

    // method to insert table data
    function DeleteFromTable($name, $conditions) {
        // sql command lines
        $lines = array();
        // for each condition field
        foreach ($conditions as $field => $value) {
            // push to lines array
            array_push($lines, $field . " = " . "'" . addslashes($value) . "'");
        }
        // join fields with and operator
        $lines = join(" AND ", $lines);
        // delete from table command
        $command = "DELETE FROM $name WHERE $lines";
        // run on connection
        return SQLRun($command);
    }

    // method to empty table
    function EmptyTable($name) {
        // delete all records of table command
        $command = "DELETE FROM $name";
        // run on connection
        return SQLRun($command);
    }
    
    // method to drop table
    function DropTable($name) {
        // delete table by name
        $command = "DROP TABLE $name";
        // run on connection
        return SQLRun($command);
    }

    // method to migrate
    function Migrate($schema) {
        // for each table
        foreach ($schema as $name => $data) {
            // create table
            CreateTable($name, $data);
        }
    }

    // method to seed
    function Seed($data) {
        // for each table
        foreach ($data as $table_name => $table_data) {
            // for each table record
            foreach ($table_data as $record) {
                // insert into table
                InsertIntoTable($table_name, $record);
            }
        }
    }

    // check database in setup
    if(isset($setup['database'])) {
        // create mysqli connection
        $connection = Connect($setup['database']['auth']);
        // use database
        SQLRun("USE " . $setup['database']['name']);
    }

    // =============================== AUTHENTICATION ==============================

    function JWTGenerate($object, $time = 86400, $payload = []) {
        // get global setup
        global $setup;
        // header data
        $header = [ 'alg' => 'HS256', 'type' => 'JWT' ];
        // payload data
        $payload['iat'] = time();
        $payload['exp'] = time() + $time;
        $payload['obj'] = $object;
        // create encoded header
        $header64 = EncodeBase64(json_encode($header));
        // create encoded payload
        $payload64 = EncodeBase64(json_encode($payload));
        // create signature
        $signature = hash_hmac(
            'sha256',
            $header64 . "." . $payload64,
            $setup['authorization']['secret'],
            true
        );
        // encode signature
        $signature64 = EncodeBase64($signature);
        // return token
        return $header64 . '.' . $payload64 . '.' . $signature64;
    }

    function JWTValidate($token) {
        // get global setup
        global $setup;
        // get token string and split
        if (strpos($token, "Bearer ") === 0) { $token = substr($token, 7); }
        $parts = explode('.', $token);
        // define three parts
        $header64 = $parts[0];
        $payload64 = $parts[1];
        $signature64 = $parts[2];
        // create signature again from received header and payload
        $check = hash_hmac(
            'sha256',
            $header64 . "." . $payload64,
            $setup['authorization']['secret'],
            true
        );
        // check if token decodable
        if (EncodeBase64($check) !== $signature64) {
            return 'TOKEN_INVALID';
        }
        // get payload data if token decoded successfully
        $payload = json_decode(base64_decode($payload64), true);
        // check token values
        if (isset($payload['iat']) === false) {
            // no issued time
            return 'TOKEN_INVALID';
        } else if (isset($payload['exp']) === false) {
            // no expiration time
            return 'TOKEN_INVALID';
        } else if (isset($payload['obj']) === false) {
            // no data object
            return 'TOKEN_INVALID';
        } else if ($payload['iat'] > time() || $payload['exp'] < time()) {
            // expired token
            return 'TOKEN_EXPIRED';
        } else {
            // return valid token
            return $payload['obj'];
        }
    }

    // ================================= ENDPOINTS =================================

    // endpoints definer
    function Endpoints($inputs) {
        // request object
        $request = [
            // request method
            "method" => $_SERVER['REQUEST_METHOD'],
            // request headers
            "headers" => getallheaders(),
            // request query params
            "params" => json_decode(json_encode($_GET), true),
            // request body
            "body" => json_decode(file_get_contents('php://input'), true)
        ];
        // for each method inputs
        foreach ($inputs as $method => $input) {
            // check request method
            if ($_SERVER['REQUEST_METHOD'] === $method) {
                // check for validate
                if (isset($input['validate'])) {
                    // for each validate inputs
                    foreach ($input['validate'] as $type => $props) {
                        // check for type
                        if (isset($request[$type])) {
                            // current object
                            $object = $request[$type];
                            // missing props array
                            $missing = array();
                            // for each prop
                            foreach ($props as $prop) {
                                // check for current prop
                                if (isset($object[$prop]) === false) {
                                    // push to missing array
                                    array_push($missing, $prop);
                                }
                            }
                            // check missing props
                            if (count($missing) !== 0) {
                                // exit as bad request
                                Error(400, "Missing parameters - " . join(", ", $missing));
                            }
                        } else {
                            // exit as bad request
                            Error(400, "Missing parameters - " . join(", ", $props));
                        }
                    }
                }
                // check for callback
                if (isset($input['callback'])) {
                    // callback method
                    echo $input['callback']($request);
                }
                // exit as method found
                exit();
            }
        }
        // check if not options method
        if($request['method'] !== "OPTIONS") {
            // method not allowed
            Error(405, $request['method'] . " method is not allowed");
        }
    }

    // response sender
    function Response($code = 200, $data = "", $headers = []) {
        // set response header
        header("HTTP/1.1 " . $code);
        // set content type on header
        header('Content-Type: application/json; charset=utf-8');
        // for each header
        foreach ($headers as $key => $value) {
            // set header
            header($key . ": " . $value);
        }
        // return response if available
        if ($data !== "") { echo json_encode($data); }
        // exit as response
        exit();
    }

    // method to authenticate request
    function Authenticate($request) {
        // check authorization header
        if (isset($request['headers']['Authorization'])) {
            // validate token
            $result = JWTValidate($request['headers']['Authorization']);
            // check result type
            if (is_string($result)) {
                // error as invalid token
                Error(401, "Authorization error - " . $result);
            } else {
                // return payload
                return $result;
            }
        } else {
            // error as no token
            Error(401, "Authorization token is missing");
        }
    }

    // error thrower
    function Error($code = 500, $message = null) {
        // check for response message
        if ($message !== null) {
            // response with message
            Response($code, [
                "error" => [
                    "code" => $code,
                    "message" => $message
                ]
            ]);
        } else {
            // response with no message
            Response($code);
        }
        // exit as request error
        exit();
    }

    // =================================== CACHE ===================================

    // method to set data on cahce
    function SetCache($path, $data, $duration = 3600) {
        // get global setup
        global $setup;
        // create cache directory if not exists
        if(file_exists($setup['cache_dir']) === false) { mkdir($setup['cache_dir']); }
        // get cache file name
        $file = $setup['cache_dir'] . FileName($path) . ".json";
        // get expiry time
        $time = time() + $duration;
        // create content with timestamp
        $content = json_encode(["time" => $time, "data" => $data]);
        // save cache file
        file_put_contents($file, $content);
    }

    // method to get data from cache
    function GetCache($path) {
        // get global setup
        global $setup;
        // create cache directory if not exists
        if(file_exists($setup['cache_dir']) === false) { mkdir($setup['cache_dir']); }
        // get cache file name
        $file = $setup['cache_dir'] . FileName($path) . ".json";
        // get current timestamp
        $time = time();
        // check for cache file
        if(file_exists($file)) {
            // read cache from file
            $content = json_decode(file_get_contents($file), true);
            // check for cache duration
            if($content["time"] > time()) {
                // return cache data
                return $content["data"];
            } else {
                // return invalid cache
                return false;
            }
        } else {
            // return no cache
            return false;
        }
    }

    // ============================== REQUEST METHODS ==============================

    // method to perform get requests
    function GET($endpoint, $query = null) {
        // get global setup
        global $setup;
        // get path with base url
        $path = isset($setup['baseurl']) ? $setup['baseurl'] : "";
        // add request query 
        $path .= $endpoint . ($query !== null ? QueryString($query) : "");
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
        // return data
        return $data;
    }

?>