<?php
session_start();
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "strikebandbarcode";
$conn = new mysqli($servername, $dbusername, $dbpassword, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csvFile"])) {
    $file = $_FILES["csvFile"]["tmp_name"];
    if (($handle = fopen($file, "r")) !== FALSE) {
        fgetcsv($handle); // Skip the header line
    }

    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        $serial_number = $data[0];
        $company = $data[1];
        $color_code = $data[3];
        $batch_code = $data[4];
        $bar_code = $data[5];
        $issue_time = date('Y-m-d H:i:s');
        $issued = $data[7];
        $used_time = $data[8];
        $used = $data[9];

        // Prepare SQL statement
        $sql = "INSERT INTO `band`(`serial_number`, `company`, `color_code`, `batch_code`, `bar_code`, `issue_time`, `issued`, `used_time`, `used`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssss", $serial_number, $company, $color_code, $batch_code, $bar_code, $issue_time, $issued, $used_time, $used);
        $stmt->execute();
    }

    fclose($handle);
    echo '<script>alert("Barcode added successfully!")</script>';
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    echo '<script>alert("No files uploaded!")</script>';
}
if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
  $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
  $logstmt = $conn->prepare($log);
  if (!$logstmt) {
    die("Prepare failed: " . $conn->error);
}
  $page = "addproductbarcode";
  $username =  $_SESSION["username"];
  $log_action = "user added barcode csv file";
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload CSV to Database</title>
    <style>
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
<body >
<div class="sidenav">
  <h1 style="background-color:rgb(231, 239, 240);">Admin</h1>
    <!-- <a href="addbarcode.php">Add Barcode</a> -->
    <!-- <a href="addbatch.php">Add Batch</a> -->
    <!-- <a href="addproduct.php">Add Product</a> -->
    <a href="addbarcodedirectly.php">Add Barcode</a>
    <!-- <a href="addbatchdirectly.php">Add Batch Directly</a> -->
    <a href="addproductdirectly.php">Add barcode Directly</a>
    <a href="foissue.php">Front Office</a>
    <a href="foonboard.php">Front Office</a>
    <a href="reissue.php">Re issue Office</a>
    <a href="voiditem.php">void item</a>
    <a href="security.php">Security</a>
    <a href="datatablesoutput.php">Datatable Output</a>
    <a href="generatereport.php">generatereport</a>
    <!-- <a href="deleterecord.php">Delete record</a> -->
    <a href="useradd.php">Add User</a>
    <a href="resetpassword.php">reset password</a>
    <a href="usermodification.php">user modification</a>
    <a href="deletuser.php">Delete User</a>
    <a href="logout.php">Logout</a>
  </div>
<div class="main">
    <h2>Upload CSV to Database</h2>
    <form action="addproduct.php" method="post" enctype="multipart/form-data">
        <label for="csvFile">Select CSV file:</label>
        <input type="file" id="csvFile" name="csvFile" accept=".csv" required><br><br>
        <input type="submit" value="Upload">
    </form>
</div>
</body>

<?php
    $conn->close();
?>
</html>
