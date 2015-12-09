<?php
  require_once 'config.php';

  if(isset($_SESSION["email"])) {
    if(isset($_POST["text"])) {
      $query = $conn->prepare("SELECT id FROM users WHERE email=:email");
      $query->bindValue(":email", $_SESSION["email"]);
      $query->execute();
      $user = $query->fetch(PDO::FETCH_ASSOC);

      $query = $conn->prepare("INSERT INTO messages(user_id, text) 
                               VALUES(:user_id, :text)");

      $query->bindValue(":user_id", $user["id"]);
      $query->bindValue(":text", $_POST["text"]);
      $query->execute();
    }

    # Fetch messages from DB
    $query = 
      $conn->prepare("SELECT messages.text, users.first_name, users.last_name 
                        FROM messages 
                  INNER JOIN users 
                       WHERE messages.user_id = users.id");
    $query->execute();
    $messages = array(); 

    # Format messages into JSON-able array
    while($message = $query->fetch(PDO::FETCH_ASSOC)) {
      $tmp = [
        "author" => "{$message['last_name']}, {$message['first_name']}",
        "text" => "{$message["text"]}"
      ];
      array_push($messages, $tmp);
    }

    # display JSON
    echo json_encode($messages);
  }
?>
