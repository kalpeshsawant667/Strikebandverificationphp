<?php
session_start();
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "strikebandbarcode";
$conn = new mysqli($servername, $dbusername, $dbpassword, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM `band`";
$result = $conn->query($sql);

$output = fopen('php://output', 'w');

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="band_data.csv"');

echo "\xEF\xBB\xBF";

fputcsv($output, array('Serial Number', 'Company', 'Color', 'Color Code', 'Batch Code', 'Bar Code', 'upload Time', 'uploaded', 'Issue Time', 'Issued','User','SecurityScannedtime','SecurityScanned','Count', 'void', 'voidtime', 'upgraded from', 'remarks'));

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);

$conn->close();
?>
