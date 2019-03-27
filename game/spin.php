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

function check_if_the_room_has_enough_people($database, $room_name) {
    $query = $database->prepare("select count(*) from member")
}

header("Access-Control-Allow-Origin: *");

$database = new SQLite3($_SERVER["DOCUMENT_ROOT"] . "/yenertuz");

check_post();

check_if_room_has_enough_people($database, $_POST["room_id"]);

$asker_user_object = get_asker_id_and_increment_asked_count($database, $_POST["room_id"]);
$asker_id = $asker_user_object["id"];
$asker_user_name = $asker_user_object["name"];
$replier_user_object = get_replier_id_and_increment_replied_count($database, $_POST["room_id"], $asker_id, $asker_user_name);
$replier_id = $replier_user_object["id"];
$replier_user_name = $replier_user_object["name"];

$room_description = update_room_status_and_asker_id_and_replier_id($database, $_POST["room_id"], $asker_id, $replier_id);

$to_echo = [];
$to_echo["asker_user_name"] = $asker_user_name;
$to_echo["replier_user_name"] = $replier_user_name;
$to_echo["room_status"] = "waiting_for_choice";
$to_echo["room_description"] = $room_description;

echo json_encode($to_echo);

$database->close

?>