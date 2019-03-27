<?php

header("Acces-Control-Allow-Origin: *");

$database = new SQLite3($_SERVER["DOCUMENT_ROOT"] . "/yenertuz");
$query = $database->prepare("select * from rooms");

$result = $query->execute();
$result_array = [];
while ($single_row = $result->fetchArray(SQLITE3_ASSOC)) {
  $result_array[] = $single_row;
}

$to_echo = json_encode($result_array, JSON_PRETTY_PRINT);
$to_echo = str_replace("\n", "<br>", $to_echo);
echo $to_echo;
 

?>
