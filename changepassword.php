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
  header("Location: logout.php");
}
$_SESSION['LAST_ACTIVITY'] = time();
$username = $_SESSION["username"];
if($username == null)
{
    echo '<script>alert("You have Been looged out.")</script>';
    header("Location: logout.php");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
        $oldpassword = $_POST['old_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        // Fetch user data from the database
        $username = $_SESSION["username"];
        $sql = "SELECT password FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if (password_verify($oldpassword, $row["password"])) {
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
        } else {
            $error = "Old password is incorrect!";
        }
    } else {
        $error = "User session is not captured!";
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
  <h1 style="background-color:rgb(231, 239, 240);">Admin</h1>
    <!-- <a href="addbarcode.php">Add Barcode</a> -->
    <!-- <a href="addbatch.php">Add Batch</a> -->
    <!-- <a href="addproduct.php">Add Product</a> -->
    <a href="addbarcodedirectly.php">Add Barcode</a>
    <a href="addbatchdirectly.php">Add Batch Directly</a>
    <a href="addbatchdirectlyall.php">Add Batch All Directly</a>
    <a href="addproductdirectly.php">Add barcode Directly</a>
    <a href="foissue.php">Front Office</a>
    <a href="foonboard.php">Band Update onboard</a>
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
    <a href="changepassword.php">Change Password</a>
    <a href="logout.php">Logout</a>
  </div>
<div class="main">
<script>
    function toggleDropdown() {
        const dropdown = document.getElementById("profileDropdown");
        dropdown.classList.toggle("show");
      }
  </script>
  <div class="profile">
              <img src="images/user.png" alt="Profile Image" onclick="toggleDropdown()">
              <p><?php echo $username; ?></p>
                <div class="dropdown" id="profileDropdown">
                    <a href="#"><?php echo $username; ?></a>
                    <a href="changepassword.php">Change Password</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>

    <h2>Password Reset</h2>
    <?php if(isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>
    <?php if(isset($success)): ?>
        <p style="color: green;"><?php echo $success; ?></p>
    <?php endif; ?>
    <form method="post">
        <label for="new_password">Old Password:</label><br>
        <input type="password" id="old_password" name="old_password" required><br><br>
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