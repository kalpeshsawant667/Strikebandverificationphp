<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "bandbarcode";
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$sql = "SELECT * FROM `band`";
$result = $conn->query($sql);

// Create a file pointer
$output = fopen('php://output', 'w');

// Set headers for the Excel file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="band_data.csv"');

// Add UTF-8 BOM to ensure proper encoding in Excel
echo "\xEF\xBB\xBF";

// Write column headers
fputcsv($output, array('Serial Number', 'Company', 'Color', 'Color Code', 'Batch Code', 'Bar Code', 'Issue Time', 'Issued', 'FO issued Time', 'FO ISSUED','FO UsER Issued', 'Used Time', 'used', 'Count'));

// Write data rows
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
// Close file pointer
fclose($output);

// Close database connection
$conn->close();
?>
