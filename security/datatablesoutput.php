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

$sql = "SELECT * FROM band WHERE voiditem != true ORDER BY used_time DESC";
$result = $conn->query($sql);



if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
  $username =  $_SESSION["username"];
  $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
  $logstmt = $conn->prepare($log);
  if (!$logstmt) {
    die("Prepare failed: " . $conn->error);
}
  $page = "datatablesoutput";
  $log_action = "user displayed table";
  $user_id = $_SESSION["empid"];
  $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
  $logstmt->execute();
} else {
 //echo "Session variables are not set.";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tables</title>
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
</head>
<body>
<div class="sidenav">
  <h1 style="background-color:rgb(231, 239, 240);">Security</h1>
        <a href="security.php">Security</a>
        <a href="datatablesoutput.php">Report</a>
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

    <h2>Band Details</h2>
    <table>
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Company</th>
                <th>Color Code</th>
                <th>Batch Code</th>
                <th>Barcode</th>
                <th>Fo Issue Time</th>
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
                    echo "<td>" . $row['fo_issue_time'] . "</td>";
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
    
    <!-- <div class="pagination">
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