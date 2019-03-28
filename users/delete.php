<?php

function reassign_spin($room_object, $user_object, $database) {
  $query = $database->prepare("select * from users where room_id = :room_id and id != :id");
  $query->bindValue(":room_id", $room_object["room_id"]);
  $query->bindValue(":id", $user_object["id"]);
  $result = $query->execute();
  $result_array = [];
  $result_array_length = 0;
  while ($single = $result->fetchArray()) {
    $result_array[] = $single;
    $result_array_length++;
  }
  $index_to_choose = mt_rand(0, $result_array_length - 1);
  $new_asker_user_name = $result_array[$index_to_choose]["name"];
  $query = $database->prepare("update rooms set asker_user_name = :new_asker_user_name, status = 'waiting_for_spin', description = :descripton where id = :room_id");
  $query->bindValue(":new_asker_name", $new_asker_user_name);
  $query->bindValue(":description", $user_object["name"] . " dropped out. Need a re-spin");
  $query->bindValue(":room_id", $room_object["id"]);
  $query->execute();
}

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

$query = $database->prepare("select * from users where id = :id");
$query->bindValue(":id", $_POST["id"]);
$result = $query->execute();
$user_object = $result->fetchArray(SQLITE_ASSOC);
$room_id = $user_object["room_id"];

$result = $database->query("select * from rooms where id = " . $room_id . " ;");
$room_object = $result->fetchArray(SQLITE3_ASSOC);

// Reassign asker_user_name if quitting user is the asker_user

if ($user_name != null && $room_object["member_count"] > 1) {
  $asker_user_name = $room_object["asker_user_name"];
  $replier_user_name = $room_object["replier_user_name"];
  if ($asker_user_name != null && $user_name == $asker_user_name) {
    reassign_spin($room_object, $user_object, $database);
  } else if ($replier_user_name != null && $user_name == $replier_user_name) {
    reassign_spin($room_object, $user_object, $database);
  }
}

// Subtract 1 from room or delete the room if the deleted user is its last user

if ($room_id != null) {
  $member_count = $room_object["member_count"];
  $asker_user_name = $room_object["asker_user_name"];
  $replier_user_name = $room_object["replier_user_name"];
  if ($member_count <= 1) {
    delete_room($room_id, $database);
  } else {
    subtract_1($room_id, $database, $member_count);
  }
}

// Delete the user

$query = $database->prepare("delete from users where id = :id");
$query->bindValue(":id", $_POST["id"]);

$result = $query->execute();

$database->close();

?>
