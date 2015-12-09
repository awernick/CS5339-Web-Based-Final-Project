<?php
  require_once 'config.php';

  if(!logged_in($conn)) {
    header("Location: index.php");
  }
?>
<!DOCTYPE html>
<html>
  <head>
    <title> Message Board | UTEP Alumni Store </title>
    <?php require_once '_stylesheets.php'; ?>
  </head>
  <body>
    <?php require_once '_navbar.php'; ?>
    <div class="container">
      <div class="twelve columns"> 
        <div id="messages"></div>
      </div>
    </div>
    <?php require_once '_javascripts.php';?>
  </body>
</html>

