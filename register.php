<?php 
  require_once 'config.php'; 
  
  if(logged_in($conn)) {
    header("Location: index.php");
  }

  if($_POST["register"]) {
    $email  = $_POST["email"]; 
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $password = $_POST["password_confirmation"];
    $password_confirmation = $_POST["password_confirmation"];

    if(empty($email)) {
      array_push($errors, "E-Mail cannot be blank");
    }

    if(strlen($email) > 254) {
      array_push($errors, "E-Mail cannot be longer than 254 characters");
    } 

    if(empty($first_name)) {
      array_push($errors, "First name cannot be blank");
    }

    if(empty($last_name)) {
      array_push($errors, "Last name cannot be blank");
    }

    if(empty($password)) {
      array_push($errors, "Password cannot be blank");
    }

    if(empty($password_confirmation)) {
      array_push($errors, "Password confirmation cannot be blank");
    }

    if($password != $password_confirmation) {
      array_push($errors, "Password and confirmation do not match.");
    }

    if(count($errors) == 0) {
      $password_digest = encrypt_password($password);
      
      try {
        $stmt = $conn->prepare("INSERT INTO users(first_name, last_name, email, password_digest) 
                                VALUES(:first_name, :last_name, :email, :password_digest)");

        $stmt->bindValue(":first_name",$first_name);
        $stmt->bindValue(":last_name",$last_name);
        $stmt->bindValue(":email", $email);
        $stmt->bindValue(":password_digest", $password_digest);
        $stmt->execute();

        $auth_token = generate_auth_token(); 
        set_auth_token($username, $auth_token, $conn);
      } catch(PDOException $e) {
        array_push($errors, $e->getMessage());
      }
    }
  }

  var_dump($errors);
?>

<!DOCTYPE html>
<html>
  <head>
    <title> Login | UTEP Alumni Store </title>
    <?php require_once '_stylesheets.php'; ?>
  </head>
  <body>
    <?php require_once '_navbar.php'; ?>
    <div class="container">
      <div class="offset-by-three columns six column login-module">
        <div class="row header">
          <h2> Register </h2>
        </div>
        <div class="row form">
          <form action="register.php" method="post">
            <label for="first_name"> First Name </label>
            <input type="text" name="first_name" />
            <br />
            <label for="last_name">Last Name</label>
            <input type="text" name="last_name" />
            <br />
            <label for="email">E-Mail</label>
            <input type="text" name="email" />
            <br />
            <label for="password"> Password </label>
            <input type="password" name="password" />
            <br />
            <label for="password_confirmation">Password Confirmation</label>
            <input type="password" name="password_confirmation" />
            <br />
            <input type="submit" name="register" value="Register"/>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
