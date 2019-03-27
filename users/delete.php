<?php

function delete_room($room_id, $database) {
  $query = $database->prepare("delete from rooms where id = :id");
  $query->bindValue(":id", $room_id);
  $query->execute();
  echo "DELETED ROOM";
}

function subtract_1($room_id, $database, $member_count) {
  $query = $database->prepare("update rooms set member_count = :member_count where id = :id");
  $query->bindValue(":id", $room_id);
  $query->bindValue(":member_count", $member_count - 1);
  $query->execute();
  echo "SUBTRACTED ONE";
}

header("Access-Control-Allow-Origin: *");

$database_path = $_SERVER["DOCUMENT_ROOT"] . "/yenertuz";
$database = new SQLite3($database_path);

if (isset($_POST["id"]) == false) {
  exit();
}

// Subtract 1 more the room's member_count (or delete it if no one else is there)

$query = $database->prepare("select room_id from users where id = :id");
$query->bindValue(":id", $_POST["id"]);
$result = $query->execute();

$room_id = $result->fetchArray(SQLITE3_NUM)[0];

if ($room_id != null) {
  $result = $database->query("select member_count from rooms where id = " . $room_id . " ;");
  $member_count = $result->fetchArray(SQLITE3_NUM)[0];
  if ($member_count <= 1) {
    delete_room($room_id, $database);
  } else {
    subtract_1($room_id, $database, $member_count);
  }
}

// Delete the room

$query = $database->prepare("delete from users where id = :id");
$query->bindValue(":id", $_POST["id"]);

$result = $query->execute();

$database->close();

?>
