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
    $username = $_POST['username'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword != $confirmPassword) {
        $error = "New password and confirm password do not match!";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $sql = "UPDATE users SET password = ? WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashedPassword, $username);
        
        if ($stmt->execute()) {
            $success = "Password reset successfully!";
        } else {
            $error = "Error resetting password: " . $conn->error;
        }
        
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>reset password</title>
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

<body>
    <h2>Password Reset</h2>
    <?php if(isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if(isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <label for="new_password">New Password:</label><br>
        <input type="password" id="new_password" name="new_password" required><br><br>
        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>
        <input type="submit" value="Reset Password">
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