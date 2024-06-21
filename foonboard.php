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
    $barcode = isset($_POST['oldbarcode']) ? $_POST['oldbarcode'] : null;
    $upgradebarcode = isset($_POST['upgradebarcode']) ? $_POST['upgradebarcode'] : null;
    if ($barcode !== null && $upgradebarcode !== null) {
        if ($barcode != $upgradebarcode) {
            $username = $_SESSION["username"];
                $checksql = "SELECT * FROM band WHERE `bar_code` = ? && `fo_issued` = true && voiditem != true";
                $stmt = $conn->prepare($checksql);
                $stmt->bind_param("s", $barcode);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    $checkupdatesql = "SELECT * FROM band WHERE `bar_code` = ? AND voiditem != true && `fo_issued` != true";
                    $updatestmt = $conn->prepare($checkupdatesql);
                    $updatestmt->bind_param("s", $upgradebarcode);
                    $updatestmt->execute();
                    $upgraderesult = $updatestmt->get_result();
                    if ($upgraderesult->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $sql = "UPDATE `band` SET `fo_issue_time` = CURRENT_TIMESTAMP(), `used_time`=  CURRENT_TIMESTAMP(), `remarks` = 'From $barcode .by .$username', count=count+1, `fo_issued` = true, `used` = true, `fo_user` = ?, `upgradedfrombarcode`= ? WHERE `bar_code` = ?";
                        $update_stmt = $conn->prepare($sql);
                        $update_stmt->bind_param("sss", $username, $barcode, $upgradebarcode);
                        $update_stmt->execute();
                        $update_stmt->close();
                        $update_sql = "UPDATE `band` SET `voiditem` = 1,'voidtime'= CURRENT_TIMESTAMP(), `remarks` = 'Upgraded $upgradebarcode' WHERE `bar_code` = ?";
                        $update_st = $conn->prepare($update_sql);
                        $update_st->bind_param("s", $barcode);
                        $update_st->execute();
                        $update_st->close();
                        $backgroundColor = 'green';
                        echo "<div style='background-color: green; text-align: center; font-size: 5rem; color: white'>Band issued successfully `$upgradebarcode`</div>";
                    }
                } else {
                    $backgroundColor = 'red';
                    echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>Upgrading Band not found.</div>";
                }
                $upgraderesult->close();
            } else {
                $backgroundColor = 'red';
                echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'> FO issued Band Not Found</div>";
            }
        } else {
            $backgroundColor = 'red';
            // echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>Same Barcode</div>";
        }
    }
} else {
    $backgroundColor = 'green';
    echo "<div style='text-align: center; font-size: 5rem; color: black'>Please Scan Band</div>";
}


if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
    $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
    $logstmt = $conn->prepare($log);
    if (!$logstmt) {
        die("Prepare failed: " . $conn->error);
    }
    $page = "foissue";
    $username = $_SESSION["username"];
    $log_action = "FO upgraded barcode of guest ";
    if (isset($barcode)) {
        $log_action .= $barcode;
    }
    if (isset($upgradebarcode)) {
        $log_action .= " to " . $upgradebarcode;
    }
    $log_action .= " by " . $username;
    
    $user_id = $_SESSION["empid"];
    $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
    $logstmt->execute();
} else {
    echo "Session variables are not set.";
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>upgrade band</title>
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <style type="text/css">
        body {
            font-family: "Lato", sans-serif;
            background-color: <?php echo $backgroundColor; ?>;
        }
        .container{
            text-align: center;
        }
        .upgradebutton{
            background-color: rgb(0, 0, 255);
            color: white;
            font-size: 2rem;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .upgradebutton:hover {
            background-color: #45a049;
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
            margin-left: 200px; /* Same as the width of the sidenav */
            font-size: 20px; /* Increased text to enable scrolling */
            padding: 0px 10px;
        }
        .active {
            background-color: green;
            color: white;
        }
        .dropdown-container {
            display: flex;
            flex-direction: row;
            background-color: #262626;
            padding-left: 8px;
        }
        .fa-caret-down {
            float: right;
            padding-right: 8px;
        }
        .oldband{
            padding-right: 80px;
        }
        .newband{
            padding-right: 1%;
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
<body onload="document.pos.barcode.focus();">
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
    <div class="container">
        <h2 style="color: white;">Scan Barcodes</h2>
        <form action="foonboard.php" method="post">
            <label  class="oldband" for="oldbarcode">Old Band:</label>
            <input type="text" id="oldbarcode" name="oldbarcode" required><br><br>
            <label  class="newband" for="upgradebarcode">Upgrade Band to:</label>
            <input type="text" id="upgradebarcode" name="upgradebarcode" required><br><br>
            <input class="upgradebutton" type="submit" value="Upgrade band">
        </form>
    </div>
</div>
</body>
</html>
