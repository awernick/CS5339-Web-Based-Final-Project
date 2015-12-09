<?php
  require_once 'config.php';

  if(isset($_SESSION["email"])) {
    if(isset($_POST["text"]) && isset($_POST["date"])) {
      $query = $conn->query("SELECT id FROM users WHERE email='{$_SESSION["email"]}'");
      $user = $query->fetch_assoc();

      $query = $conn->query("INSERT INTO messages(user_id, text, date) 
                               VALUES('{$user["id"]}', '{$_POST["text"]}', '{$_POST["date"]}')");

    }

    # Fetch messages from DB
    $query = 
      $conn->query("SELECT messages.text, messages.date, users.first_name, users.last_name 
                        FROM messages 
                  INNER JOIN users 
                       WHERE messages.user_id = users.id");
    $messages = array(); 

    # Format messages into JSON-able array
    while($message = $query->fetch_assoc()) {
      $tmp = [
        "date" => "{$message["date"]}",
        "author" => "{$message['last_name']}, {$message['first_name']}",
        "text" => "{$message["text"]}"
      ];
      array_push($messages, $tmp);
    }

    # display JSON
    echo json_encode($messages);
  }
?>
