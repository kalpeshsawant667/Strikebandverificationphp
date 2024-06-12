<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "bandbarcode";
$conn = new mysqli($servername, $username, $password, $database);

  
if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
  $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
  $logstmt = $conn->prepare($log);
  if (!$logstmt) {
    die("Prepare failed: " . $conn->error);
}
  $page = "logout";
  $username =  $_SESSION["username"];
  $log_action = "user logged out";
  $user_id = $_SESSION["empid"];
  $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
  $logstmt->execute();
  // Clear the session data
  $_SESSION = array();
  // Destroy the session
  session_destroy();
} else {
  echo "Session variables are not set.";
}

// Check if the database connection is set and not null
if (isset($conn) && !is_null($conn)) {
    // Close the database connection
    mysqli_close($conn);
}

header("location: login.php?msg=You have been logged out.");
$conn->close();

exit;

?>
