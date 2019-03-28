<?php

function check_post() {
  if (isset($_POST["room_name"]) == false ||
        isset($_POST["replier_user_name"]) == false || 
        isset($_POST["choice"]) == false ) {
          $error = [];
          $error["error"] = "missing_post_keys";
          echo json_encode($error);
          exit();
        }
}

header("Access-Control-Allow-Origin: *");

$database = new SQLite3($_SERVER["DOCUMENT_ROOT"] . "/yenertuz");

check_post();

$new_description = $_POST["replier_user_name"] . " picked " . $_POST["choice"];
$query = $database->prepare("update rooms set status = 'waiting_for_spin', description = :description, 
  updated_at = current_timestamp");
$query->bindValue(":description", $new_description);
$query->execute();

echo $new_description;

$database->close();

?>