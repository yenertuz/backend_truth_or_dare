<?php

function check_post() {
  if (isset($_POST["creator_id"]) == false || 
      isset($_POST["name"]) == false ||
      isset($_POST["nickname"]) == false) {
	  echo json_encode(["error" => "bad_post"]);
      return(-1);
  }
  return (0);
}

function check_if_room_name_exists($database, $room_name) {
  $query = $database->prepare("select count(id) from rooms where name = :name;");
  $query->bindValue(':name', $_POST["name"]);
  $result = $query->execute();
  $count = $result->fetchArray(SQLITE3_NUM)[0];
  if ($count != 0) {
	  $error["error"] = "room_full";
	echo json_encode($error);
    return(-1);
  }
  return (0);
}

function create_room_and_assign_creator_and_member_count_and_replier_id($database, $name, $creator_id, $creator_name) {
  $query = $database->prepare("insert into rooms (name, creator_id, member_count, asker_user_name) values ( :name, :creator_id, 1, :replier_user_name)");
  $query->bindValue(":name", $name);
  $query->bindValue(":creator_id", $creator_id);
  $query->bindValue(":replier_user_name", $creator_name);
  $query->execute();
  return $database->lastInsertRowID(); 
}

function update_user_and_assign_room_and_name($database, $id, $nickname, $room_id) {
  $query = $database->prepare("update users set room_id = :room_id, name = :name where id = :id");
  $query->bindValue(":room_id", $room_id);
  $query->bindValue(":name", $nickname);
  $query->bindValue(":id", $id);
  $query->execute();
}

header("Access-Control-Allow-Origin: *");

$database = new SQLite3($_SERVER["DOCUMENT_ROOT"] . "/yenertuz");

$status = check_post();

file_put_contents("log", $status);

if ($status == 0) {

  file_put_contents("log", "done&&");

  $status = check_if_room_name_exists($database, $_POST["name"]);
  
  file_put_contents("log", "done2");

	if ($status == 0) {

    $room_id = create_room_and_assign_creator_and_member_count_and_replier_id($database, $_POST["name"], $_POST["creator_id"], $_POST["nickname"]);
    
    file_put_contents("log", "done3");

    update_user_and_assign_room_and_name($database, $_POST["creator_id"], $_POST["nickname"], $room_id);

    file_put_contents("log", "done4");
    


		echo $room_id;

	}

}

$database->close();

?>
