<?php 
  require_once 'config.php'; 
    
  if(logged_in()) {
    header("Location: index.php");
  }

  if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password_confirmation = $_POST['password_confirmation'];

    # Check for empty email
    if(empty($email)) {
      array_push($errors, "Email cannot be blank");
    }
    
    # Check for empty password
    if(empty($password)) {
      array_push($errors, "Password cannot be blank");
    }

    if(count($errors) == 0) {
      $stmt = $conn->query("SELECT email, password_digest FROM users 
                               WHERE email='$email'");   

      # Bind values to the SQL query
      $result = $stmt->fetch_assoc();

      if(count($result) > 0) {
        # Create digest of password
        $digest = encrypt_password($password);

        # Verify that the digest matches with the one stored in the DB
        if($digest === $result["password_digest"]) {
          # Start a session for the user and store an auth_token
          $_SESSION["email"] = $email;
          $auth_token = substr(md5(microtime()),rand(0,26),15);       
          set_auth_token($email, $auth_token, $conn);
          header("Location: index.php");
        } 
        
        else {
          $incorrect_password = "Incorrect Password provided.";
          array_push($errors, $incorrect_password);
        }
      } 
     
      else {
        $user_not_found_err = "The email that you've entered does not match any account. Please register before continuing.";
        array_push($user_not_found_err);
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
          <h2> Log In </h2>
        </div>
        <div class="row form">
          <form action="login.php" method="post">
            <label for="email">E-Mail:</label>
            <input type="text" name="email" />
            <br/>
            <label for="password"> Password: </label>
            <input type="password" name="password" />
            <input type="submit" name="login"/>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
