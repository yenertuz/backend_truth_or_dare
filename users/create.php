<?php

// REMEMBER SQLITE3 NEEDS 777 PERMISSIONS FOR DATABASE AND FOLDER

header("Access-Control-Allow-Origin: *");

$database_path = $_SERVER["DOCUMENT_ROOT"] . "/yenertuz";
$database = new SQLite3($database_path);

$database->query("insert into users default values");
echo $database->lastInsertRowID();

$database->close();

?>
