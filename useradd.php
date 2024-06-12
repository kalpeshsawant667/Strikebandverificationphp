
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
  $sql = "INSERT INTO users (empid, name, username, password, department, status) VALUES (?, ?, ?, ?, ?, TRUE)";
  
  $stmt = $conn->prepare($sql);
  
  if (!$stmt) {
      die("Prepare failed: " . $conn->error);
  }
  

  $empid = $_POST["empid"];
  $name = $_POST['nameofperson'];
  $username1 = $_POST["username"];
  $password1 = password_hash($_POST["password"], PASSWORD_DEFAULT);
  $department = $_POST["department"];
  
  if (!$stmt->bind_param("sssss", $empid, $name, $username1, $password1, $department)) {
      die("Binding parameters failed: " . $stmt->error);
  }
  
  
  if (!$stmt->execute()) {
      die("Execution failed: " . $stmt->error);
  }
  
  echo "User registered successfully!";
    echo ".";
    echo '<script>alert("User added successfully.!")</script>';

    if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
      $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
      $logstmt = $conn->prepare($log);
      if (!$logstmt) {
        die("Prepare failed: " . $conn->error);
    }
      $page = "useradd";
      $username =  $_SESSION["username"];
      $log_action = "User added successfully".$username1;
      $user_id = $_SESSION["empid"];
      $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
      $logstmt->execute();
    } else {
      echo "Session variables are not set.";
    }
    
    $stmt->close();

}


$conn->close();
?>

<!DOCTYPE html>
<html>
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

@media screen and (max-height: 450px) {
  .sidenav {padding-top: 15px;}
  .sidenav a {font-size: 18px;}
}

    </style>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>useradd</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
</head>  

<body onload="document.pos.barcode.focus();">
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
    <form action="useradd.php" method="post">
        <label for="empid">Employee id:</label><br>
        <input type="text" id="empid" name="empid" required><br><br>
        <label for="department">Department:</label><br>
        <select type="text" id="department" name="department" type="text" placeholder="Department.." >
            <option value="Administrator">Administrator</option>
            <option value="technical">technical</option>
            <option value="accounts">accounts</option>
            <option value="surveillance">surveillance</option>
            <option value="fosuperviser">front office Supervisor</option>
            <option value="fo">front office</option>
            <option value="foonboard">FOonboard</option>
            <option value="security">security</option>
            </select><br><br>
        <label for="name">Name:</label><br>
        <input type="text" id="nameofperson" name="nameofperson" required><br><br>
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Register">
    </form>
    </div>
</body>
</html>
