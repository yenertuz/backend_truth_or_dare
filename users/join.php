<?php

function check_if_post_is_correct() {
  if (isset($_POST["id"]) == false ||
       isset($_POST["room_name"]) == false ||
       isset($_POST["user_name"]) == false)
  {
    exit();
  }
}

function get_room_id_and_check_if_over_20($database, $room_name) {
  $query = $database->prepare("select * from rooms where name = :name");
  $query->bindValue(":room_name", $room_name);
}


header("Acces-Control-Allow-Origin: *");

$database = new SQLite3($_SERVER["DOCUMENT_ROOT"] . "/yenertuz");

check_if_post_is_correct();

$room_id = get_room_id_and_check_if_over_20($database, $_POST["room_name"]);

check_if_name_already_exists($database, $room_id, $user_name);

increment_room_member_count($database, $room_id);

update_user_room_id_and_name($database, $_POST["id"], $room_id, $_POST["user_name"]);

$database->close();


?>
