<?php

function check_post() {
    if (isset($_POST["room_id"]) == false 
            || isset($_POST["room_name"]) == false) {
        $error = [];
        $error["error"] = "missing_post_keys";
        echo json_encode($error);
        exit();
    }
}

function check_if_room_has_enough_people($database, $room_name) {
    $query = $database->prepare("select member_count from rooms where name = :name ; ");
    $query->bindValue(":name", $room_name);
    $result = $query->execute();
    $count = $result->fetchArray(SQLITE3_NUM)[0];
    if ($count < 2) {
      $error = [];
      $error["error"] = "too_few_people";
      $error["count"] = $count;
      $error["room_name"] = $room_name;
      echo json_encode($error);
      exit();
    }
}

function get_asker_id_and_increment_asked_count($database, $room_id) {
  $query = $database->prepare("select * from users where room_id = :room_id and asked_count = (select asked_count from users where room_id = :room_id order by asked_count asc limit 1)");
  $query->bindValue(":room_id", $room_id);
  $result = $query->execute();
  $result_array = [];
  $result_array_length = 0;
  while ($single = $result->fetchArray()) {
    $result_array[] = $single;
    $result_array_length++;
  }
  $random_index = mt_rand(0, $result_array_length - 1);
  $selected_user = $result_array[$random_index];
  $asked_count_of_selected_user = $selected_user["asked_count"];
  $incremented_asked_count_of_selected_user = $asked_count_of_selected_user + 1;
  $id_of_selected_user = $selected_user["id"];
  $query = $database->prepare("update users set asked_count = :asked_count where id = :id");
  $query->bindValue(":asked_count", $incremented_asked_count_of_selected_user);
  $query->bindValue(":id", $id_of_selected_user);
  $query->execute();
  return $selected_user;
}

function get_replier_id_and_increment_replied_count($database, $room_id, $asker_id) {
  // $echo = [];
  // $echo["asker_id"] = $asker_id;
  // $echo["room_id"] = $room_id;
  // echo json_encode($echo); // ============================================================================>>
  // exit();
  $query = $database->prepare("select * from users where room_id = :room_id and id != :id and replied_count = (select replied_count from users where room_id = :room_id and id != :id order by replied_count asc limit 1)");
  $query->bindValue(":room_id", $room_id);
  $query->bindValue(":id", $asker_id);
  $result = $query->execute();
  $result_array = [];
  $result_array_length = 0;
  while ($single = $result->fetchArray(SQLITE3_ASSOC)) {
    $result_array[] = $single;
    $result_array_length++;
  }
  $random_index = mt_rand(0, $result_array_length - 1);
  $selected_user = $result_array[$random_index];
  $replied_count_of_selected_user = $selected_user["replier_count"];
  $incremented_replied_count_of_selected_user = $replied_count_of_selected_user + 1;
  $id_of_selected_user = $selected_user["id"];
  $query = $database->prepare("update users set replied_count = :replied_count where id = :id");
  $query->bindValue(":replied_count", $incremented_replied_count_of_selected_user);
  $query->bindValue(":id", $id_of_selected_user);
  $query->execute();
  return $selected_user;
}

function update_room_status_and_asker_id_and_replier_id($database, $room_id, $asker_user_name, $replier_user_name) {
  $room_status = "waiting_for_choice";
  $room_description = $asker_user_name . " is asking " . $replier_user_name;
  $query = $database->prepare("update rooms set status = :status, updated_at = current_timestamp,
    asker_user_name = :asker_user_name, replier_user_name = :replier_user_name, description = :description ");
  $query->bindValue(":status", $room_status);
  $query->bindValue(":asker_user_name", $asker_user_name);
  $query->bindValue(":replier_user_name", $replier_user_name);
  $query->bindValue(":description", $room_description);
  $query->execute();
  return $room_description;
}

header("Access-Control-Allow-Origin: *");

$database = new SQLite3($_SERVER["DOCUMENT_ROOT"] . "/yenertuz");

check_post();

check_if_room_has_enough_people($database, $_POST["room_name"]);

$asker_user_object = get_asker_id_and_increment_asked_count($database, $_POST["room_id"]);
$asker_id = $asker_user_object["id"];
$asker_user_name = $asker_user_object["name"];
$replier_user_object = get_replier_id_and_increment_replied_count($database, $_POST["room_id"], $asker_id);
$replier_id = $replier_user_object["id"];
$replier_user_name = $replier_user_object["name"];

$room_description = update_room_status_and_asker_id_and_replier_id($database, $_POST["room_id"], $asker_user_name, $replier_user_name);

$to_echo = [];
$to_echo["asker_user_name"] = $asker_user_name;
$to_echo["replier_user_name"] = $replier_user_name;
$to_echo["room_status"] = "waiting_for_choice";
$to_echo["room_description"] = $room_description;

echo json_encode($to_echo);

$database->close();

?>