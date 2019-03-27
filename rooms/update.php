<?php

header("Access-Control-Allow-Origin: *");

if (isset($_POST["id"]) == false) {
  exit();
}

$database = new SQLite3($_SERVER["DOCUMENT_ROOT"] . "/yenertuz");

$update_parameters = [];
if (isset($_POST["creator_id"])) {
  $update_parameters[] = "creator_id = :creator_id";
}
if (isset($_POST["


?>
