<?php
session_start();
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "strikebandbarcode";
$conn = new mysqli($servername, $dbusername, $dbpassword, $database);
$backgroundColor = "green";
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$username = $_SESSION["username"];
if($username == null)
{
    echo '<script>alert("You have Been looged out.")</script>';
    header("Location: ../logout.php");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barcode = $_POST["barcode"];
    $remark = $_POST["remark"];
    $reissueremark = `reissued `.$remark;


    $sql = "SELECT * FROM `band` WHERE `bar_code` = ? AND voiditem != true";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $barcode);
    
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $row_count = $result->num_rows;
    
        if ($row_count > 0) {
            $row = $result->fetch_assoc();
            if (!$row["voiditem"]) {
                $update_sql = "UPDATE `band` SET `fo_issue_time` = CURRENT_TIMESTAMP(), `fo_issued` = true, `fo_user` = ?, `remarks` = ? WHERE `bar_code` = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("sss", $username, $reissueremark, $barcode);
                
                if ($update_stmt->execute()) {
                  echo "<div style='text-align: center; font-size: 4rem; color: black'>Reissued successfully: $barcode</div>";
                  $backgroundColor = "green";
                } else {
                    echo "<div style='text-align: center; font-size: 5rem; color: black'>Error updating record.</div>";
                }
    
                $update_stmt->close();
            } else {
                echo "<div style='text-align: center; font-size: 5rem; color: black'>Band has already been void.</div>";
                $backgroundColor = "red";
            }
        } else {
            echo "<div style='text-align: center; font-size: 5rem; color: black'>Barcode not issued.</div>";
            $backgroundColor = "red";
        }
    } else {
        echo "<div style='text-align: center; font-size: 5rem; color: black'>Error executing SQL query.</div>";
        $backgroundColor = "red";
    }
    
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


    $stmt->close();
    $conn->close();
}
?>


<!doctype html>
<html lang="en">
<head>
    <title>reissue</title>
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


.active {
  background-color: green;
  color: white;
}

.container{
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.container .reissuebutton{
  width: 100px;
  height: 25px; 
  text-align: center; 
  margin-left: 44%;
  color: black;
  background-color: white;
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
  <h1 style="background-color:rgb(231, 239, 240);">FOsupervisor</h1>
    <a href="foissue.php">FO issue</>
    <a href="reissue.php">Reissue band</a>
    <a href="datatablesoutput.php">Datatable Output</a>
    <a href="voiditem.php">Void band</a>
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
                    <a href="../logout.php">Logout</a>
                </div>
            </div>

        <div class="container">
        <h2 style="color: white;">Reissue Band</h2>
            <form action="reissue.php" method="post">
                <label for="barcode" style="color: black;">Enter Barcode:</label>
                <input type="text" id="barcode" name="barcode" required><br><br>
                <label for="remark" style="color: black;">Remark:</label>
                <input type="text" style="margin-left: 58px"id="remark" name="remark" required><br><br>
                <input type="submit" class="reissuebutton" value="Re issue">
            </form>
        </div>
</body>
</html>

