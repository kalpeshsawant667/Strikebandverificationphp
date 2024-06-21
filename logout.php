<?php
session_start();
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "strikebandbarcode";
$conn = new mysqli($servername, $dbusername, $dbpassword, $database);

if (isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
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

    $_SESSION = array();
    session_destroy();
} else {
    echo "Session variables are not set.";
}

if (isset($conn)) {
    mysqli_close($conn);
}

header('Location: login.php ');
exit;
?>
