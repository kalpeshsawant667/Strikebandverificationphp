<?php
// Database connection
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
fputcsv($output, array('Serial Number', 'Company', 'Color', 'Color Code', 'Batch Code', 'Bar Code', 'upload Time', 'uploaded', 'Issue Time', 'Issued','User','SecurityScannedtime','SecurityScanned','Count', 'void', 'voidtime', 'upgraded from', 'remarks'));

// Write data rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

// Close file pointer
fclose($output);

// Close database connection
$conn->close();
?>
