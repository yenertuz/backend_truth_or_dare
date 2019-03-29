<?php

function get_user_object($user_id, $database) {
    $query = $database->prepare("select * from users where id = :id");
    $query->bindValue(":id", $user_id);
    $result = $query->execute();
    $user_object = $result->fetchArray(SQLITE3_ASSOC);
    return ($user_object);
}

function get_room_object($room_id, $database) {
    $query = $database->prepare("select * from rooms where id = :id");
    $query->bindValue(":id", $room_id);
    $result = $query->execute();
    $room_object = $result->fetchArray(SQLITE3_ASSOC);
    return ($room_object);
}

function delete_user($user_object, $database) {
    $query = $database->prepare("delete from users where id = :id");
    $query->bindValue(":id", $user_object["id"]);
    $query->execute();
}

function delete_room($room_object, $database) {
    $query = $database->prepare("delete from rooms where id = :id");
    $query->bindValue(":id", $room_object["id"]);
    $query->execute();
}

function decrement_room($room_object, $database) {
  $query = $database->prepare("update rooms set member_count = :member_count where id = :id");
  $query->bindValue(":member_count", $room_object["member_count"] - 1);
  $query->bindValue(":id", $room_object["id"]);
  $query->execute();
}

function respin($room_object, $user_object, $database) {
    $query = $database->prepare("select * from users where room_id = :room_id and id != :user_id");
    $query->bindValue(":room_id", $room_object["id"]);
    $query->bindValue(":user_id", $user_object["id"]);
    $result = $query->execute();
    $users_array = [];
    $users_array_length = 0;
    while ($single_user_object = $result->fetchArray(SQLITE3_ASSOC)) {
        $users_array[] = $single_user_object;
        $users_array_length++;
    }
    $index = mt_rand(0, $users_array_length - 1);
    $picked_user_object = $users_array[$index];
    echo "\n" . json_encode($users_array) . "\n";
    echo "\n" . json_encode($picked_user_object) . "\n";
    echo "\n" . $index . "\n";
    $query = $database->prepare("update rooms set asker_user_name = :asker_user_name , status = :status, description = :description where id = :id");
    $query->bindValue(":asker_user_name", $picked_user_object["name"]);
    $query->bindValue(":status", "waiting_for_spin");
    $query->bindValue(":description", "Asker or replier disconnected. Need to spin again");
    $query->bindValue(":id", $room_object["id"]);
    $query->execute();
}

function exit_gracefully($database) {
    $database->close();
    exit();
}

if ($argc != 2) {
    exit();
}
$absolute_path_beginning = dirname(dirname(__FILE__));
$user_id = $argv[1];
$database = new SQLite3($absolute_path_beginning . "/yenertuz");
$user_object = get_user_object($user_id, $database);
if ($user_object["room_id"] == null) {
    delete_user($user_object, $database);
    exit_gracefully($database);
}
$room_object = get_room_object($user_object["room_id"], $database);
if ($room_object["member_count"] <= 1) {
    delete_user($user_object, $database);
    delete_room($room_object, $database);
    exit_gracefully($database);
}
if ($room_object["asker_user_name"] == $user_object["name"] || 
        $room_object["replier_user_name"] == $user_object["name"]) {
    respin($room_object, $user_object, $database);
}
delete_user($user_object, $database);
decrement_room($room_object, $database);
$database->close();


?>