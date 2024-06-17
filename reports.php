
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

$start_date = date("Y-m-d");
$end_date = date("Y-m-d");
$datetype = "issue_date";

if(isset($_POST["start_date"]) && isset($_POST["end_date"])&& isset($_POST["datetype"])) {
        $start_date = $_POST["start_date"];
        $end_date = $_POST["end_date"];
        $datetype = $_POST["datetype"];
} 
// else {
//     echo "Please select both start and end dates.";
//     exit;
// }

$sql = "SELECT * FROM band WHERE ? BETWEEN ? AND ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $datetype, $start_date, $end_date);

$stmt->execute();
$result = $stmt->get_result();

// Set CSV filename
$filename = 'report_' . date('Ymd') . '.csv';

// Set header for CSV file
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

// Open file handle to write CSV data
$output = fopen('php://output', 'w');

// Write CSV header
fputcsv($output, array('ID', 'Name', 'Email'));

// Write CSV data
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

// Close file handle
fclose($output);


$stmt->close();
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .bodyclass {
            background-color: #000000;
        }
        .img {
            padding-left: 500px;
        }
    </style>
    <title>Generate Report</title>
</head>
<body>
    <h2>Generate Report</h2>
    <form action="reports.php" method="post">
    <select type="text" id="datetype" name="datetype" type="text" placeholder="datetype" >
            <option value="issue_date">issuedate</option>
            <option value="fo_date">front office date</option>
            <option value="used_date">used_date</option>
        </select><br><br>
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>
        <button type="submit" value="Submit">Generate Report</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Company</th>
                <th>Color Code</th>
                <th>Batch Code</th>
                <th>Barcode</th>
                <th>Issue Time</th>
                <th>Used Time</th>
                <th>Issued</th>
                <th>Used</th>
                <th>Times Scanned</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['serial_number'] . "</td>";
                    echo "<td>" . $row['company'] . "</td>";
                    echo "<td>" . $row['color_code'] . "</td>";
                    echo "<td>" . $row['batch_code'] . "</td>";
                    echo "<td>" . $row['bar_code'] . "</td>";
                    echo "<td>" . $row['issue_time'] . "</td>";
                    echo "<td>" . $row['used_time'] . "</td>";
                    echo "<td>" . ($row['issued'] ? 'Yes' : 'No') . "</td>";
                    echo "<td>" . ($row['used'] ? 'Yes' : 'No') . "</td>";
                    echo "<td>" . $row['count'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
    
    <div class="pagination">
    <?php
    $total_records = mysqli_num_rows($result);
    $total_pages = ceil($total_records / $num_records_per_page);
      for($i=0;$i<$total_pages;$i++)
      {
        echo "<a href='?page=".($i+1)."'>".($i+1)."</a>";
      }
      if ($conn->ping()) {
        $conn->close();
      }
    ?>
    </div>
    <script>
        document.getElementById("exportButton").onclick = function() {
            var table = document.getElementById("dataTable");
            var html = table.outerHTML;
            var url = 'data:application/vnd.ms-excel,' + escape(html); // Set MIME type
            var link = document.createElement("a");
            link.href = url;
            link.download = "band_details.xls"; // Set file name
            link.click();
        }
    </script>
</body>
</html>
