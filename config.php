<?php
  require_once 'functions.php';

  session_start();

  $username = "cs5339team12fa15";
  $password = "cs5339!cs5339team12fa15";
  $database = "cs5339team12fa15";
  $host = "earth.cs.utep.edu";

  try {
    // Attempt to connect to the database
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch(PDOException $e) {
    // Display error message if connection could not be established
    echo "Connection failed: " . $e->getMessage();
    die();
  }
  
  # Setup errors array for global use
  $errors = array();
  $current_user = NULL;
  //current_user();
?>
