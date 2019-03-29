<?php

if (isset($_POST["id"]) == false) {
  exit();
}

exec("php delete_helper.php " . $_POST["id"] . " > /dev/null 2>&1 &");

?>