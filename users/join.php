<?php

function check_if_post_is_correct() {
  if (isset($_POST["id"]) == false ||
       isset($_POST["room_name"]) == false ||
       isset($_POST["user_name"]) == false)
  {
	  $error["error"] = "incorrect_post";
	  echo json_encode($error);
    exit();
  }
}

function increment_room_member_count($database, $room_id, $member_count) {
  $query = $database->prepare("update rooms set member_count = :member_count where id = :id ;");
  $query->bindValue(":member_count", $member_count + 1);
  $query->bindValue(":id", $room_id);
  $query->execute();
}

function get_room_id_and_check_if_over_20($database, $room_name) {
  $query = $database->prepare("select * from rooms where name = :name");
  $query->bindValue(":name", $room_name);
  $result = $query->execute();
  $room_object = $result->fetchArray(SQLITE3_ASSOC);
  if ($room_object == false) {
	  $error["error"] = "no_room";
	  echo json_encode($error);
	  exit();
  }
  if ($room_object["member_count"] >= 20) {
    $error["error"] = "room_full";
    echo json_encode($error);
    exit();
  }
  increment_room_member_count($database, $room_object["id"], $room_object["member_count"]);
  return ($room_object);
}

function check_if_user_name_already_exists($database, $room_id, $user_name) {
  $query = $database->prepare("select count(*) from users where room_id = :room_id and name = :name ;");
  $query->bindValue(":room_id", $room_id);
  $query->bindValue(":name", $user_name);
  $result = $query->execute();
  $result_array = $result->fetchArray(SQLITE3_NUM);
  if ($result_array[0] != 0) {
    $error["error"] = "user_name_taken";
    echo json_encode($eror);
    exit();
  }
}

function update_user_room_id_and_name($database, $user_id, $room_id, $user_name) {
  $query = $database->prepare("update users set room_id = :room_id, name = :name where id = :id");
  $query->bindValue(":room_id", $room_id);
  $query->bindValue(":name", $user_name);
  $query->bindValue(":id", $user_id);
  $query->execute();
}


header("Access-Control-Allow-Origin: *");

$database = new SQLite3($_SERVER["DOCUMENT_ROOT"] . "/yenertuz");

check_if_post_is_correct();

$room_object = get_room_id_and_check_if_over_20($database, $_POST["room_name"]);

check_if_user_name_already_exists($database, $room_object["id"], $_POST["user_name"]);

update_user_room_id_and_name($database, $_POST["id"], $room_object["id"], $_POST["user_name"]);

$database->close();

echo $room_object["id"];


?>
