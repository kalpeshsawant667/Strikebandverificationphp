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

$start_date = date("Y-m-d");
$start_time = date("H:i:s");
$end_date = date("Y-m-d");
$end_time = date("H:i:s");
$datetype = "issue_time";
$stmt = null;



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>report</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
</head>
<body>
<div class="sidenav">
  <h1 style="background-color:rgb(231, 239, 240);">Admin</h1>
    <!-- <a href="addbarcode.php">Add Barcode</a> -->
    <!-- <a href="addbatch.php">Add Batch</a> -->
    <!-- <a href="addproduct.php">Add Product</a> -->
    <a href="addbarcodedirectly.php">Add Barcode</a>
    <!-- <a href="addbatchdirectly.php">Add Batch Directly</a> -->
    <a href="addproductdirectly.php">Add barcode Directly</a>
    <a href="foissue.php">Front Office</a>
    <a href="foonboard.php">Band Update onboard</a>
    <a href="reissue.php">Re issue Office</a>
    <a href="voiditem.php">void item</a>
    <a href="security.php">Security</a>
    <a href="datatablesoutput.php">Datatable Output</a>
    <a href="generatereport.php">generatereport</a>
    <a href="deleterecord.php">Delete record</a>
    <a href="useradd.php">Add User</a>
    <a href="resetpassword.php">reset password</a>
    <a href="usermodification.php">user modification</a>
    <a href="deletuser.php">Delete User</a>
    <a href="Userlogs.php">User Logs</a>
    <a href="logout.php">Logout</a>
  </div>
<div class="main">
    
    <h2>Band Details</h2>
    <form id="reportForm" action="report.php" method="post">
    <select type="text" id="datetype" name="datetype" type="text" placeholder="datetype" >
            <option value="issue_time">issuedate</option>
            <option value="fo_issue_time">front office date</option>
            <option value="used_time">used_date</option>
        </select><br><br>
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" required>
        <input type="time" id="start_time" name="start_time" required><br><br>
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" required>
        <input type="time" id="end_time" name="end_time" required><br><br>
        <button type="submit" value="Submit" id="generateReportBtn">Generate Report</button>
</form>
    <a href="excelout.php">Excel Out full data</a>
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
            $sql = "SELECT * FROM band WHERE voiditem != true AND issue_time BETWEEN ? AND ?";
            if(isset($_POST["start_date"]) && isset($_POST["end_date"]) && isset($_POST["datetype"])) {
              $start_date = $_POST["start_date"];
              $start_time = $_POST["start_time"];
              $end_date = $_POST["end_date"];
              $end_time = $_POST["end_time"];
              $datetype = $_POST["datetype"];
              $start_datetime = $start_date . ' ' . $start_time;
              $end_datetime = $end_date . ' ' . $end_time;
              
              switch($datetype) {
                  case "issue_time":
                      $sql = "SELECT * FROM band WHERE voiditem != true AND issue_time BETWEEN ? AND ?";
                      break;
                  case "fo_issue_time":
                      $sql = "SELECT * FROM band WHERE voiditem != true AND fo_issue_time BETWEEN ? AND ?";
                      break;
                  case "used_time":
                      $sql = "SELECT * FROM band WHERE voiditem != true AND used_time BETWEEN ? AND ?";
                      break;
                  default:
                      $sql = "SELECT * FROM band WHERE voiditem != true AND issue_time BETWEEN ? AND ?";
              }
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("ss", $start_datetime, $end_datetime);
              $result = $stmt->get_result();
              if (!$result) {
                  die("Query execution failed: " . $conn->error);
              }
            
            if ($result && $result->num_rows > 0) {
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
        </tbody>
    </table>
    
    <div class="pagination">
    <!-- <?php
    $total_records = mysqli_num_rows($result);
    $total_pages = ceil($total_records / $num_records_per_page);
      for($i=0;$i<$total_pages;$i++)
      {
        echo "<a href='?page=".($i+1)."'>".($i+1)."</a>";
      }
      if ($conn->ping()) {
        $conn->close();
      }
    ?> -->
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
</div>

</body>
</html>

