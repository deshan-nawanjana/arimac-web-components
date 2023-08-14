<?php

    require "../backend.php";

    // get tables schema
    $tables = json_decode(file_get_contents("tables.json"));

    // for each table
    foreach($tables as $name => $data) {
        // drop current table
        $connection->query("DROP TABLE " . $name);
    }

    // create tables
    Migrate($tables);

?>