<?php
  require_once "config.php";

  if(!logged_in()) {
    header("Location: index.php");
  }
  
  if(isset($_POST["follow"])) {
    $query = $conn->query("INSERT INTO friendships(follower_id, followed_id)
                                  VALUES('{$current_user["id"]}', '{$_POST["followed_id"]}')");

  } else {
    $query = $conn->query("DELETE FROM friendships 
                            WHERE follower_id='{$current_user["id"]}'
                              AND followed_id='{$_POST["followed_id"]}'");

  }

  header("Location: profile.php?id={$_POST["followed_id"]}");
?>
