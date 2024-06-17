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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["bar_code"])) {
        $bar_code = $_POST["bar_code"];

        // Prepare SQL statement
        $sql = "INSERT INTO `band`(`company`, `color`, `color_code`, `batch_code`, `bar_code`, `issue_time`, `issued`) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $company = $_POST['company'];
        $color = $_POST['color'];
        $color_code = $_POST['color_code'];
        $batch_code = $_POST['batch_code'];
        $issue_time = date('Y-m-d H:i:s');
        $issued = 1;
        $count =  $_POST['count'];

        $stmt = $conn->prepare($sql);

        for ($i = 1; $i <= $count; $i++) {
            $bar_code = (int)$bar_code + 1;
            $bar_code = strval($bar_code);
            $stmt->bind_param("ssssssi", $company, $color, $color_code, $batch_code, $bar_code, $issue_time, $issued );
            if ($stmt->execute()) {
                echo "Barcode added successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }
        if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
          $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
          $logstmt = $conn->prepare($log);
          if (!$logstmt) {
            die("Prepare failed: " . $conn->error);
        }
          $page = "addbarcodedirectly";
          $username =  $_SESSION["username"];
          $log_action = "user added barcode directly";
          $user_id = $_SESSION["empid"];
          $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
          $logstmt->execute();
      } else {
          echo "Session variables are not set.";
      }

        $stmt->close();
    }
    $conn->close();
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

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}
</style>
</head>
<body>
<div class="sidenav">
        <h1 style="background-color:rgb(231, 239, 240);">Accounts</h1>
        <a href="addbarcode.php">Add Barcode</a>
          <!-- <a href="addbatch.php">Add Batch</a> -->
          <a href="addbarcodedirectly.php">Add Single Directly</a>
          <!-- <a href="addbatchdirectly.php">Add Batch Directly</a> -->
          <a href="addproduct.php">Add multiple barcode</a>
          <a href="datatablesoutput.php">Datatable Output</a>
          <!-- <a href="deleterecord.php">Delete record</a> -->
          <a href="../logout.php">Logout</a>
        </div>

<div class="main">
  <h2>Add Batch</h2>
  
  <form class="formclass" action="addbatch.php" method="post">
            <label class="label" for="company">Company:</label>
            <input type="text" name="company" id="company" required><br>

            <label class="label" for="color_code">Color Code:</label>
            <input type="text" name="color_code" id="color_code" required><br>

            <label class="label" for="batch_code">Batch Code:</label>
            <input type="text" name="batch_code" id="batch_code" required><br>
            <label class="label" for="barcode">Barcode:</label>
            <input type="text" name="bar_code" id="bar_code" required><br>
            <label for="quantity">Quantity :</label>
            <input type="number" id="count" name="count"><br>

            <input type="submit" value="Add Barcode">
        </form>
  </div>

  
 
</div>

<script>
var dropdown = document.getElementsByClassName("dropdown-btn");
var i;

for (i = 0; i < dropdown.length; i++) {
  dropdown[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var dropdownContent = this.nextElementSibling;
    if (dropdownContent.style.display === "block") {
      dropdownContent.style.display = "none";
    } else {
      dropdownContent.style.display = "block";
    }
  });
}
</script>

</body>
</html> 

