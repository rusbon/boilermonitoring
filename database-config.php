<?php
  $servername = "localhost";
  $username = "casio";
  $password = "Sapiompong.53";
  $dbname = 'plcmonitoring';

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);
  // Check connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }
?> 