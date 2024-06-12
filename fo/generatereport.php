<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "bandbarcode";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style type="text/css">
    .container{
        padding: 0;
        margin: 0;
    }
    .pos-style{
        display: flex;
        height: 500px;
        width: 900px;
        object-fit: fill;
    }
    .form-group{
        padding-top: 100px;;
    }
    .navbar{
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    body {
  font-family: "Lato", sans-serif;
}
.formclass {
  border: 1px solid #ccc;
  padding: 10px;
  width: 300px;
  margin: 0 auto;
  margin-top: 50px;
}

.label {
  display: block;
  margin-bottom: 5px;
  color: black;
}

.formclass input[type="text"] {
  width: 100%;
  padding: 5px;
  margin-bottom: 10px;
  border: 1px solid #ccc;
  border-radius: 3px;
  box-sizing: border-box;
}

.formclass input[type="submit"] {
  background-color: #4CAF50;
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: 3px;
  cursor: pointer;
}
/* Fixed sidenav, full height */
.sidenav {
  height: 100%;
  width: 200px;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  background-color: #111;
  overflow-x: hidden;
  padding-top: 20px;
}

/* Style the sidenav links and the dropdown button */
.sidenav a, .dropdown-btn {
  padding: 6px 8px 6px 16px;
  text-decoration: none;
  font-size: 20px;
  color: #818181;
  display: block;
  border: none;
  background: none;
  width: 100%;
  text-align: left;
  cursor: pointer;
  outline: none;
}

/* On mouse-over */
.sidenav a:hover, .dropdown-btn:hover {
  color: #f1f1f1;
}

/* Main content */
.main {
  margin-left: 200px; /* Same as the width of the sidenav */
  font-size: 20px; /* Increased text to enable scrolling */
  padding: 0px 10px;
}

/* Add an active class to the active dropdown button */
.active {
  background-color: green;
  color: white;
}

/* Dropdown container (hidden by default). Optional: add a lighter background color and some left padding to change the design of the dropdown content */
.dropdown-container {
  display: none;
  background-color: #262626;
  padding-left: 8px;
}

/* Optional: Style the caret down icon */
.fa-caret-down {
  float: right;
  padding-right: 8px;
}

/* Some media queries for responsiveness */
@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}

    </style>
    <title>Generate Report</title>
</head>
<body>
<div class="sidenav">
  <h1 style="background-color:rgb(231, 239, 240);">FO</h1>
    <a href="foissue.php">Front Office</a>
    <a href="datatablesoutput.php">Datatable Output</a>
    <a href="voiditem.php">void band</a>
    <a href="reissue.php">reissue band</a>
    <!-- <a href="generatereport.php">generate report</a> -->
    <a href="../logout.php">Logout</a>
  </div>
  <div class="main">
<h2>Generate Report</h2>
    <form id="reportForm" action="generatereport.php" method="post">
    <select type="text" id="datetype" name="datetype" type="text" placeholder="datetype" >
            <option value="issue_time">issuedate</option>
            <option value="fo_issue_time">front office date</option>
            <option value="used_time">used_date</option>
        </select><br><br>
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>
        <button type="submit" value="Submit" id="generateReportBtn">Generate Report</button>
    </form>
<!-- 
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
        <tbody> -->
    <?php

$start_date = date("Y-m-d");
$end_date = date("Y-m-d");
$datetype = "issue_time";
$stmt = null; 

if(isset($_POST["start_date"]) && isset($_POST["end_date"])&& isset($_POST["datetype"])) {
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    $datetype = $_POST["datetype"];
    $sql = "SELECT * FROM band WHERE $datetype BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start_date, $end_date);
    $stmt->execute();
    $result = $stmt->get_result();
    
// $num_records_per_page = 10;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
// $offset = ($current_page - 1) * $num_records_per_page;

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
}


    ?>

<!-- </tbody>
    </table> -->
    <div class="pagination">
    <!-- <?php
    $total_records = mysqli_num_rows($result);
    $total_pages = ceil($total_records / $num_records_per_page);
    for($i=1;$i<=$total_pages;$i++)
    {
        echo "<a href='?page=".$i."'>".$i."</a>";
    }
    ?> -->
    </div>

    <script>
        document.getElementById("generateReportBtn").addEventListener("click", function(event) {
            event.preventDefault();
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "excelout.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.responseType = "blob"; // Set response type to blob

            xhr.onload = function() {
                if (this.status === 200) {
                    var a = document.createElement("a");
                    var url = window.URL.createObjectURL(this.response);
                    a.href = url;
                    a.download = "band_data_report.csv"; // Set filename
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }
            };

            // Serialize form data
            var formData = new FormData(document.getElementById("reportForm"));
            var serialized = "";
            for (var [key, value] of formData.entries()) {
                serialized += key + "=" + value + "&";
            }

            // Remove the last "&" character
            serialized = serialized.slice(0, -1);

            // Send AJAX request with form data
            xhr.send(serialized);
        });
    </script>
</body>
</html>
