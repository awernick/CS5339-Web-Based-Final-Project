<?php
  require_once 'functions.php';

  session_start();

  $username = "cs5339team12fa15";
  $password = "cs5339!cs5339team12fa15";
  $database = "cs5339team12fa15";
  $host = "earth.cs.utep.edu";

  // Attempt to connect to the database
  $conn = new mysqli($host, $username, $password, $database);
  // Display error message if connection could not be established
  if ($conn->connect_error) {
    echo "Connection failed: " . $conn->connect_error; 
    die();
  }
  
  # Setup errors array for global use
  $errors = array();
  $current_user = NULL;
  //current_user();
?>
