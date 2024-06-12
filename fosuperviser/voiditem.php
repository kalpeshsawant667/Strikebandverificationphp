<?php
// Start session
session_start();

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barcode = $_POST["barcode"];
    $remark = $_POST["remark"];


    $sql = "SELECT * FROM `band` WHERE `bar_code` = ? && voiditem != true";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $barcode);
    if ($stmt->execute()) {
        
        $result = $stmt->get_result();
        $row_count = $result->num_rows;

        if ($row_count > 0) {
            $row = $result->fetch_assoc();
            if (!$row["voiditem"]) {
              $update_sql = "UPDATE `band` SET `voiditem` = true,`voidtime` = CURRENT_TIMESTAMP(), `remarks` = ? WHERE `bar_code` = ?";
              $update_stmt = $conn->prepare($update_sql);
              
              $update_stmt->bind_param("ss", $remark, $barcode);
              
              $update_stmt->execute();
              
            echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>Barcode voided successfully`$barcode`</div>";

            $update_stmt->close();
            } else {
                echo "<script>alert('band has already been void.')</script>";
            }
           
        }
        else {
            echo '<script>alert("Barcode not issued.")</script>';

        }
    } else {
        echo "<script>alert('Error: " . $sql . "\\n" . $conn->error . "');</script>";
    }
    
    // Check if session variables are set
    if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
        $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
        $logstmt = $conn->prepare($log);
        if (!$logstmt) {
          die("Prepare failed: " . $conn->error);
      }
        $page = "voiditem";
        $username =  $_SESSION["username"];
        $log_action = "user void barcode".$barcode;
        $user_id = $_SESSION["empid"];
        $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
        $logstmt->execute();
    } else {
        echo "Session variables are not set.";
    }


    // Close statement and database connection
    $stmt->close();
    $conn->close();
}
?>


<!doctype html>
<html lang="en">
<head>
    <title>void band</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style type="text/css">
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

.active {
  background-color: green;
  color: white;
}

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
  <h1 style="background-color:rgb(231, 239, 240);">FOsupervisor</h1>
  <a href="foissue.php">FO issue</a>
  <a href="reissue.php">reissue band</a>
    <a href="datatablesoutput.php">Datatable Output</a>
    <a href="voiditem.php">void band</a>
    <a href="generatereport.php">generate report</a>
    <a href="../logout.php">Logout</a>
  </div>
  <div class="main">
        <div class="container">
        <h2 style="color: white;">Void Band</h2>
            <form action="voiditem.php" method="post">
                <label for="barcode" style="color: black;">Enter Barcode:</label>
                <input type="text" id="barcode" name="barcode" required><br><br>
                <label for="remark" style="color: black;">Remark:</label>
                <input type="text" style="margin-left: 50px"id="remark" name="remark" required><br><br>
                <input type="submit" style="width: 100px" value="Void">
            </form>
        </div>
</body>
</html>

