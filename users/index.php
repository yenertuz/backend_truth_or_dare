<?php

header("Access-Control-Allow-Origin: *");

$database = new SQLite3($_SERVER["DOCUMENT_ROOT"] . "/yenertuz");
$result = $database->query("select * from users");
$result_array = [];
while ($single = $result->fetchArray(SQLITE3_ASSOC)) {
  $result_array[] = $single;
}

$to_echo = json_encode($result_array, JSON_PRETTY_PRINT);
$to_echo = str_replace("\n", "<br>", $to_echo);
echo $to_echo;

$database->close();

?>
