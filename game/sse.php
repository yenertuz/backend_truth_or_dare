<?php

header("Access-Control-Allow-Origin: *");
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

if (isset($_GET["room_id"]) == false) {
  exit();
}

$room_id = $_GET["room_id"];
$database = new SQLite3($_SERVER["DOCUMENT_ROOT"] . "/yenertuz");
$query = $database->prepare("select * from rooms where id = :id");
$query->bindValue(":id", $room_id);
$result = $query->execute();
$result_object = $result->fetchArray(SQLITE3_ASSOC);

echo "data: " . json_encode($result_object) . "\n\n";

flush();

?>