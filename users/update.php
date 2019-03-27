<?php

header("Acces-Control-Allow-Origin: *");

$database_path = $_SERVER["DOCUMENT_ROOT"] . "/yenertuz";
$database = new SQLite3($database_path);

if (isset($_POST["id"]) == false) {
  echo "EXITING, DUNNO WHY";
  exit();
}
isset($_POST["name"]) ? $name = $_POST["name"] : $name = "";
isset($_POST["gender"]) ? $gender = $_POST["gender"] : $gender = "";

$query = $database->prepare("update users set name = :name, gender = :gender, updated_at = current_timestamp where id = :id");
$query->bindValue(":id", $_POST["id"]);
$query->bindValue(":name", $name);
$query->bindValue(":gender", $gender);

$result = $query->execute();
if ($result == true) {
  echo "OK";
} else {
  echo "ERROR";
}

$database->close();

?>
