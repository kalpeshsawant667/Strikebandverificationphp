<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "strikebandbarcode";
$conn = new mysqli($servername, $username, $password, $database);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$sql = "SELECT * FROM `band`";
$result = $conn->query($sql);


$output = fopen('php://output', 'w');


header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="band_data.csv"');


echo "\xEF\xBB\xBF";


fputcsv($output, array('Serial Number', 'Company', 'Color', 'Color Code', 'Batch Code', 'Bar Code', 'Issue Time', 'Issued', 'FO issued Time', 'FO ISSUED','FO UsER Issued', 'Used Time', 'used', 'Count'));


if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
    $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
    $logstmt = $conn->prepare($log);
    if (!$logstmt) {
      die("Prepare failed: " . $conn->error);
  }
    $page = "security";
    $username =  $_SESSION["username"];
    $log_action = "security excel out barcode data";
    $user_id = $_SESSION["empid"];
    $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
    $logstmt->execute();
  }

fclose($output);


$conn->close();
?>
