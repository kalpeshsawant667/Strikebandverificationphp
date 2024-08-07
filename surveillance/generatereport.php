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

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800)) {
  session_unset();     
  session_destroy();  
  echo '<script>alert("You have Been looged out.")</script>';
  header("Location: ../logout.php");
}
$_SESSION['LAST_ACTIVITY'] = time();
$username = $_SESSION["username"];
if($username == null)
{
    echo '<script>alert("You have Been looged out.")</script>';
    header("Location: ../logout.php");
}

$start_date = date("Y-m-d");
$start_time = date("H:i:s");
$end_date = date("Y-m-d");
$end_time = date("H:i:s");
$datetype = "issue_time";
$stmt = null;

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
            $sql = "SELECT * FROM band WHERE issue_time BETWEEN ? AND ?";
            break;
        case "fo_issue_time":
            $sql = "SELECT * FROM band WHERE fo_issue_time BETWEEN ? AND ?";
            break;
        case "used_time":
            $sql = "SELECT * FROM band WHERE used_time BETWEEN ? AND ?";
            break;
        default:
            $sql = "SELECT * FROM band WHERE issue_time BETWEEN ? AND ?";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $start_datetime, $end_datetime);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // Generate CSV file
    if ($result && $result->num_rows > 0) {
        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        
        // Function to convert data to CSV format
        function array_to_csv($array, $filename = "export.csv", $delimiter = ",") {
            $f = fopen('php://output', 'w');
            fputcsv($f, array_keys($array[0]));
            foreach ($array as $row) {
                fputcsv($f, $row);
            }
            fclose($f);
        }
        
        // Output CSV file
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="band_data_report.csv"');
        array_to_csv($data);
        exit;
    } else {
        echo "<p>No records found.</p>";
    }
}
if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
    $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
    $logstmt = $conn->prepare($log);
    if (!$logstmt) {
      die("Prepare failed: " . $conn->error);
  }
    $page = "generatereport";
    $username =  $_SESSION["username"];
    $log_action = "user generated report";
    $user_id = $_SESSION["empid"];
    $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
    $logstmt->execute();
  } else {
    echo "Session variables are not set.";
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

.profile {
  display: flex;
  flex-direction: column;
  position: relative;
  display: flex;
  align-items: center;
  margin-left: 90%;
}

.profile img {
  border-radius: 50%;
  cursor: pointer;
  height: 50px;
  width: 50px;
}

.profile .dropdown {
  display: none;
  position: absolute;
  right: 0;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
}
         .profile .dropdown a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
         .profile .dropdown a:hover {
            background-color: #f1f1f1
        }
         .profile:hover .dropdown {
            display: block;
        }
         .profile .dropdown a:hover {
            background-color: #f1f1f1;
        }
         .profile .dropdown .show {
            display: block;
        }
@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}

    </style>
    <title>Generate Report</title>
</head>
<body>
<div class="sidenav">
  <h1 style="background-color:rgb(231, 239, 240);">Surveillance</h1>
    <a href="datatablesoutput.php">Datatable Output</a>
    <a href="generatereport.php">generatereport</a>
    <a href="changepassword.php">Change Password</a>
    <a href="../logout.php">Logout</a>
  </div>
  <div class="main">
  <script>
    function toggleDropdown() {
        const dropdown = document.getElementById("profileDropdown");
        dropdown.classList.toggle("show");
      }
  </script>
  <div class="profile">
              <img src="../images/user.png" alt="Profile Image" onclick="toggleDropdown()">
              <p><?php echo $username; ?></p>
                <div class="dropdown" id="profileDropdown">
                    <a href="#"><?php echo $username; ?></a>
                    <a href="changepassword.php">Change Password</a>
                    <a href="../logout.php">Logout</a>
                </div>
            </div>
<h2>Generate Report</h2>
    <form id="reportForm" action="generatereport.php" method="post">
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
    <!-- <table>
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
        switch($datetype) {
          case "issue_time":
              $sql = "SELECT * FROM band WHERE issue_time BETWEEN ? AND ?";
              break;
          case "fo_issue_time":
              $sql = "SELECT * FROM band WHERE fo_issue_time BETWEEN ? AND ?";
              break;
          case "used_time":
              $sql = "SELECT * FROM band WHERE used_time BETWEEN ? AND ?";
              break;
          default:
              $sql = "SELECT * FROM band WHERE issue_time BETWEEN ? AND ?";
      }
            $update_sql = "SELECT * FROM `band` WHERE `bar_code` = ? AND fo_issued = TRUE";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $start_datetime, $end_datetime);
            $update_stmt->execute();
            $result = $update_stmt->get_result();
            $row_count = $result->num_rows;
        $row = $result->fetch_assoc();
        if ($row_count > 0) {
            $count = $row["count"];
            $count++;
                echo "<table style='background-color: white; text-align: center;'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>Serial Number</th>";
                echo "<th>Barcode</th>";
                echo "<th>FO Issued</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr style='text-align: center; '>";
                echo "<td style='text-align: center;'>" . $row['serial_number'] . "</td>";
                echo "<td style='text-align: center;'>" . $row['bar_code'] . "</td>";
                echo "<td style='text-align: center;'>" . $row['fo_issue_time'] . "</td>";
                echo "</tr>";
                echo "</tbody>";
                echo "</table>";
           
        }
        $update_stmt->close();
        ?>

<div class="pagination">
   <!-- <?php
    $total_records = mysqli_num_rows($result);
    $total_pages = ceil($total_records / $num_records_per_page);
    for($i=1;$i<=$total_pages;$i++)
    {
        // echo "<a href='?page=".$i."'>".$i."</a>";
    }
    ?> -->
    </div>
    <script>
    document.getElementById("generateReportBtn").addEventListener("click", function(event) {
        event.preventDefault();
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "generatereport.php", true);
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

<?php
if ($stmt) {
    $stmt->close();
}
$conn->close();
?>
