<?php
  require_once "config.php";

  if(!logged_in()) {
    header("Location: index.php");
  }
  
  if(isset($_POST["follow"])) {
    $query = $conn->prepare("INSERT INTO friendships(follower_id, followed_id)
                                  VALUES(:follower_id, :followed_id)");

    $query->bindValue(":follower_id", $current_user["id"]);
    $query->bindValue(":followed_id", $_POST["followed_id"]);
    $query->execute();
  } else {
    $query = $conn->prepare("DELETE FROM friendships 
                              WHERE follower_id=:follower_id 
                                AND followed_id=:followed_id");

    $query->bindValue(":follower_id", $current_user["id"]);
    $query->bindValue(":followed_id", $_POST["followed_id"]);
    $query->execute();
  }

  header("Location: profile.php?id={$_POST["followed_id"]}");
?>
