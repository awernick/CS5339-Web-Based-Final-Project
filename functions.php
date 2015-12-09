<?php

function encrypt_password($password) {
  $digest = md5('MXHa0gz2'.$password);
  return $digest;
}

function set_auth_token($email, $auth_token, $conn) {
  $stmt = $conn->prepare("UPDATE users
                          SET auth_token = :auth_token
                          WHERE email= :email");   
  $stmt->bindValue(":email", $email);
  $stmt->bindValue(":auth_token", $auth_token);
  $stmt->execute();
  $_SESSION["auth_token"] = $auth_token;
}

function generate_auth_token() {
  return substr(md5(microtime()),rand(0,26),15);       
}

function is_following($follower_id, $followed_id) {
  global $conn;

  # Check if the follower is following the followed
  $query = $conn->prepare("SELECT * FROM friendships 
                            WHERE follower_id=:follower_id 
                              AND followed_id=:followed_id");
  
  $query->bindValue(":follower_id", $follower_id);
  $query->bindValue(":followed_id", $followed_id);
  $query->execute();

  # Check if association exists
  $result = $query->fetch(PDO::FETCH_ASSOC);
  if(!$result) {
    return false;
  } else {
    return true;
  }
}
function logged_in() {
  global $conn, $current_user;
  
  if(!isset($conn)) {
    return false;
  }

  if(isset($current_user)) {
    return true;
  }

  if(empty($_SESSION['email'])){
    return false;
  }

  if(empty($_SESSION['auth_token'])) {
    return false;
  }

  $email = $_SESSION['email'];
  $stmt =  $conn->prepare("SElECT * FROM users WHERE email=:email");
  $stmt->bindValue(":email", $email); 
  $stmt->execute();
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  if(count($result) < 0) {
    return false;
  }

  if($_SESSION['auth_token'] === $result['auth_token']) {
    $current_user = $result;
    return true;
  } else {
    return false;
  }
}

# function current_user(){
#   global $conn, $current_user;
# 
#   if(logged_in()) {
#     $email = $_SESSION['email'];
#     $stmt =  $conn->prepare("SElECT * FROM users WHERE email=:email");
#     $stmt->bindValue(":email", $email); 
#     $stmt->execute();
#     $result = $stmt->fetch(PDO::FETCH_ASSOC);
#     $current_user = $result;
#     return $current_user;
#   }
# }
