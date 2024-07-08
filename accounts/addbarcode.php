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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barcode = $_POST["barcode"];
    $sql = "INSERT INTO `band`(`company`, `color`, `color_code`, `batch_code`, `bar_code`, `issue_time`, `issued`) 
    VALUES (?, ?, ?, ?, ?, ?, ?)";


    $company = $_POST['company'];
    $color = $_POST['color'];
    $color_code = $_POST['color_code'];
    $batch_code = $_POST['batch_code'];
    $bar_code=  $_POST['barcode'];
    $issue_time = time();
    $issued = 1;

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $company, $color, $color_code, $batch_code, $barcode, $issue_time, $issued );

    if ($stmt->execute()) {
        echo "Barcode added successfully.";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
      $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
      $logstmt = $conn->prepare($log);
      if (!$logstmt) {
        die("Prepare failed: " . $conn->error);
    }
      $page = "addbarcode";
      $username =  $_SESSION["username"];
      $log_action = "user added barcode";
      $user_id = $_SESSION["empid"];
      $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
      $logstmt->execute();
  } else {
     //echo "Session variables are not set.";
  }
    $stmt->close();
    // $conn->close();
}
?>



<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
  body {
    font-family: Arial, sans-serif;
}

.formclass {
    display: inline-block;
    margin: 20px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    width: 80%;
}

.label {
    display: block;
    margin-bottom: 5px;
}

input[type="text"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
}

input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
}

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

.sidenav a:hover, .dropdown-btn:hover {
  color: #f1f1f1;
}

.main {
  margin-left: 200px; 
  font-size: 20px; 
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
        <h1 style="background-color:rgb(231, 239, 240);">Accounts</h1>
        <!-- <a href="addbarcode.php">Add Barcode</a> -->
          <!-- <a href="addbatch.php">Add Batch</a> -->
          <a href="addbarcodedirectly.php">Add Single Directly</a>
          <!-- <a href="addbatchdirectly.php">Add Batch Directly</a> -->
          <a href="addproductdirectly.php">Add multiple barcode</a>
          <a href="datatablesoutput.php">Datatable Output</a>
          <!-- <a href="deleterecord.php">Delete record</a> -->
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
                    <a href="logout.php">Logout</a>
                </div>
            </div>

<form class="formclass" action="addbarcode.php" method="post">
    <label class="label" for="company">Company:</label>
    <input type="text" name="company" id="company" required><br>

    <label class="label" for="color_code">Color Code:</label>
    <input type="text" name="color_code" id="color_code" required><br>

    <label class="label" for="batch_code">Batch Code:</label>
    <input type="text" name="batch_code" id="batch_code" required><br>

    <label class="label" for="barcode">Barcode:</label>
    <input type="text" name="barcode" id="barcode" required><br>

    <input type="submit" value="Add Barcode">
</form>

<script src="js/jquery.min.js"></script>
<script src="js/popper.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
</div>
</body>
</html>