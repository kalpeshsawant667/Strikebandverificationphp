<?php
session_start();
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "strikebandbarcode";
$conn = new mysqli($servername, $dbusername, $dbpassword, $database);
$backgroundColor = 'green';

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

    $bar_code = $_POST['barcode'];
    $checksql = 'select * from band where bar_code = ?';
    $checkstmt = $conn->prepare($checksql);
    $checkstmt->bind_param("s", $bar_code);
    $checkstmt->execute();
    $result = $checkstmt->get_result();

    if ($result->num_rows == 0) {
        $sql = "INSERT INTO `band`(`company`, `color_code`, `batch_code`, `letter`, `bar_code`, `issue_time`, `issued`) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";

        $company = substr($bar_code, 0, 3); // BD
        $color_code = substr($bar_code, 3, 3);
        $batch_code = substr($bar_code, 6, 4);
        $letter = substr($bar_code, 10, 1);
        $issue_time = date('Y-m-d H:i:s');
        $issued = 1;

        // Prepare and bind parameters
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $company, $color_code, $batch_code, $letter, $bar_code, $issue_time, $issued);

        if (isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
            $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
            $logstmt = $conn->prepare($log);
            if (!$logstmt) {
                die("Prepare failed: " . $conn->error);
            }
            $page = "addbarcode";
            $username =  $_SESSION["username"];
            $log_action = "user added barcode directly";
            $user_id = $_SESSION["empid"];
            $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
            $logstmt->execute();
        }

        if ($stmt->execute()) {
            echo "<div style='background-color: green; text-align: center; font-size: 5rem; color: white'>Barcode added successfully</div>";
            $backgroundColor = 'green';
        } else {
            echo "<div style='background-color: red; text-align: center;font-size: 5rem; color: white '>Error</div>" . $sql . "<br>" . $conn->error;
            $backgroundColor = 'red';
        }

        if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
          $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
          $logstmt = $conn->prepare($log);
          if (!$logstmt) {
            die("Prepare failed: " . $conn->error);
        }
          $page = "addproductdirectlybarcode";
          $username =  $_SESSION["username"];
          $log_action = "user added single barcode $bar_code";
          $user_id = $_SESSION["empid"];
          $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
          $logstmt->execute();
        } else {
          echo "Session variables are not set.";
        }
        $stmt->close();
    } else {
        echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: white'>Barcode already exist</div>";
        $backgroundColor = 'red';
    }
}
?>



<!doctype html>
<html lang="en">
<head>
    <title>Add single Barcode</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style type="text/css">
body {
  font-family: "Lato", sans-serif;
  background-color: <?php echo $backgroundColor; ?>;
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
    <h1 style="background-color:rgb(231, 239, 240);">Accounts</h1>
    <a href="addbarcodedirectly.php">Add Single Directly</a>
    <a href="addbatchdirectly.php">Add Batch Directly</a>
    <a href="addproductdirectly.php">Add multiple barcode</a>
    <a href="datatablesoutput.php">Datatable Output</a>
    <a href="generatereport.php">Generate report</a>
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
                    <a href="logout.php">Logout</a>
                </div>
            </div>

        <form class="formclass" action="addbarcodedirectly.php" method="post">
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
<script>
        const inputField = document.getElementById("barcode");
        inputField.focus();
    </script>
</html>

