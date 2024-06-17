
<?php
session_start();
$connection = mysqli_connect('localhost', 'root', '', 'strikebandbarcode');
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the username or ID from the form
    $username = $_POST["username"];

    // Prepare and execute the DELETE query
    $sql = "DELETE FROM users WHERE username = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param("s", $username);

    if ($stmt->execute()) {
        echo "User deleted successfully.";
        echo "<script>alert('User deleted successfully.')</script>";
    } else {
        echo "Error deleting user: " . $connection->error;
        echo "<script>alert('Error deleting user:'. $connection->error)</script>";
    }
    $stmt->close();
}

// Fetch all users
$sql = "SELECT * FROM users";
$result = $connection->query($sql);

if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
  $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
  $logstmt = $connection->prepare($log);
  if (!$logstmt) {
    die("Prepare failed: " . $connection->error);
}
  $page = "deleterecord";
  $username =  $_SESSION["username"];
  $log_action = "user deleted user".$username;
  $user_id = $_SESSION["empid"];
  $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
  $logstmt->execute();
} else {
  echo "Session variables are not set.";
}
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
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete user</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
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
    <h2>Delete User</h2>
    <form action="deletuser.php" method="post">
        <label for="username">Enter username or ID:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        <input type="submit" value="Delete User">
    </form>
    <table>
        <thead>
            <tr>
                <th>Username</th>
                <th>Department</th>
            </tr>
        </thead>
        <tbody>
        <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['department'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='2'>No records found</td></tr>";
                }
                $connection->close();
                ?>
        </tbody>
    </table>
</body>
</html>
