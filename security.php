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


/**
 * Parses a date string using multiple formats and a timezone.
 *
 * @param string $dateString The date string to parse.
 * @param array $dateFormats The date formats to try.
 * @param DateTimeZone $timezone The timezone to use.S
 *
 * @return DateTime|false The parsed DateTime object or false on failure.
 */
function parseDate($dateString, $dateFormats, $timezone)
{
    foreach ($dateFormats as $dateFormat) {
        $date = DateTime::createFromFormat($dateFormat, $dateString, $timezone);
        if ($date) {
            return $date;
        }
    }
    return false;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : null;
    if ($barcode !== null) {
        $sql = "SELECT * FROM `band` WHERE `bar_code` = ? AND `fo_issued` = TRUE";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $barcode);
        $stmt->execute();
        $result = $stmt->get_result();
        $row_count = $result->num_rows;

        if ($row_count > 0) {
            $row = $result->fetch_assoc();
            $count = $row["count"];
            $count++;

            $timezone = new DateTimeZone('Asia/Kolkata');
            $fo_issue_time_str = $row['fo_issue_time'];
            $fo_issue_time = parseDate($fo_issue_time_str, ['d/m/Y H:i', 'Y-m-d H:i:s'], $timezone);
            if (!$fo_issue_time) {
                $fo_issue_time = DateTime::createFromFormat('Y-m-d H:i:s', $fo_issue_time_str, $timezone);
                if (!$fo_issue_time) {
                    echo "Error parsing date: " . $fo_issue_time_str;
                    return false;
                }
            }

            $current_time = new DateTime('now', $timezone);  
            $interval = $current_time->diff($fo_issue_time);
            $minutes_elapsed = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

            echo "<div style='text-align: center; font-size: 4rem; color: black'>VALID BAND.</div>";

            if ($minutes_elapsed <= 60) {
                if (!$row["used"]) {
                    $update_sql = "UPDATE `band` SET `used_time` = CURRENT_TIMESTAMP(), `used` = true, `count` = ? WHERE `bar_code` = ?";
                    echo "<div class='tabledisplay'>";
                    echo "<h2>Band Details</h2>";
                    echo "<table>";
                    echo "<thead>";
                    echo "<tr>";
                    echo "<th>FO Issued</th>";
                    echo "<th>Time Elapsed</th>";
                    echo "</tr>";
                    echo "</thead>";
                    echo "<tbody>";
                    echo "<tr>";
                    echo "<td style='font-size:2rem'><b>" . $fo_issue_time->format('d/m/Y H:i') . "</b></td>";
                    echo "<td style='font-size:2rem'><b>";
                    echo " Minutes: " . $interval->format('%I');
                    echo ":" . $interval->format('%S');
                    echo "</b></td>";
                    echo "</tr>";
                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                } else {
                    $used_time = $row['used_time'];
                    $elapsed_seconds = 0;
                    try {
                        if ($used_time !== null) {
                            $used_time = new DateTime($used_time);
                            $usedinterval = $current_time->diff($used_time);
                            $minutes = $usedinterval->format('%I');
                            $seconds = $usedinterval->format('%S');
                            $elapsed_seconds = ($usedinterval->i * 60) + (int)$seconds;
                            echo "Minutes: $minutes, Seconds: $seconds, Elapsed seconds: $elapsed_seconds";
                        } else {
                            throw new Exception("Error: used_time is null.");
                        }
                    } catch (Exception $e) {
                        echo $e->getMessage();
                    }

                    // if ($elapsed_seconds < 10) {
                    //     $backgroundColor = 'green';
                    //     $update_sql = "UPDATE `band` SET `count` = ? WHERE `bar_code` = ?";
                    //     echo "<div style='background-color: red; text-align: center; font-size: 2rem;color: white'>Band has already been used! Count: $count</div>";
                    //     echo "<div class='tabledisplay'>";
                    //     echo "<h2>Band Details</h2>";
                    //     echo "<table>";
                    //     echo "<thead>";
                    //     echo "<tr>";
                    //     echo "<th>FO Issued</th>";
                    //     echo "<th>Used Time</th>";
                    //     echo "<th>Time Elapsed</th>";
                    //     echo "</tr>";
                    //     echo "</thead>";
                    //     echo "<tbody>";
                    //     echo "<tr>";
                    //     echo "<td>" . $fo_issue_time->format('d/m/Y H:i') . "</td>";
                    //     echo "<td>" . $used_time->format('d/m/Y H:i') . "</td>";
                    //     echo "<td>";
                    //     echo " - Minutes: " . $interval->format('%I');
                    //     echo ":" . $interval->format('%S');
                    //     echo "</td>";                    
                    //     echo "</tr>";
                    //     echo "</tbody>";
                    //     echo "</table>";
                    //     echo "</div>";
                    // } else {
                        $backgroundColor = 'yellow';
                        $update_sql = "UPDATE `band` SET `count` = ? WHERE `bar_code` = ?";
                        echo "<div style='background-color: yellow; text-align: center; font-size: 4rem;color: black; padding-left: 100px'>Band has already been used! Count: $count.</div>";
                        echo "<div class='tabledisplay'>";
                        echo "<h2>Band Details</h2>";
                        echo "<table>";
                        echo "<thead>";
                        echo "<tr>";
                        echo "<th>FO Issued</th>";
                        echo "<th>Used Time</th>";
                        echo "<th>Time Elapsed</th>";
                        echo "</tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        echo "<tr>";
                        echo "<td style='font-size:2rem'><b>" . $fo_issue_time->format('d/m/Y H:i') . "</b></td>";
                        echo "<td style='font-size:2rem'><b>" . $row['used_time'] . "</b></td>";
                        echo "<td style='font-size:2rem'><b>";
                        echo " Minutes: " . $interval->format('%I');
                        echo ":" . $interval->format('%S');
                        echo "</b></td>";
                        echo "</tr>";
                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";
                        echo " <script> 
                        var audio = document.createElement('audio');
                        document.body.appendChild(audio);
                        audio.src = 'Audio/beepalert_aeobLVzA.mp3';
                        audio.addEventListener('canplaythrough', function() {
                            audio.play();
                            setTimeout(() => {
                                audio.pause();
                            }, 3000);
                        }, false);
                            </script>";
                    //}
                }
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("is", $count, $barcode);
                $update_stmt->execute();
                $update_stmt->close();
            } else {
                $backgroundColor = 'red';
                echo "<div style='background-color: red; text-align: center; font-size: 2rem;color: white; padding-left: 100px'>FO Issue time has expired! Cannot use this band.</div>";
                echo "<div class='tabledisplay'>";
                echo "<h2>Band Details</h2>";
                echo "<table>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>FO Issued</th>";
                echo "<th>Time Elapsed</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr>";
                echo "<td style='font-size:2rem'><b>" . $fo_issue_time->format('d/m/Y H:i') . "</b></td>";
                echo "<td style='font-size:2rem'><b>";
                echo " Hours: " . $interval->format('%H');
                echo " Minutes: " . $interval->format('%I');
                echo " Seconds: " . $interval->format('%S');
                echo "</b></td>";
                echo "</tr>";
                echo "</tbody>";
                echo "</table>";
                echo "</div>";
                
            echo " <script> var audio = document.createElement('audio');
                    document.body.appendChild(audio);
                    audio.src = 'Audio/afterexplosionbeep_Pcn6DM5v.mp3';
                    audio.addEventListener('canplaythrough', function() { 
                    audio.play();
                    setTimeout(() => {
                        audio.pause();
                    }, 300);
                    }, false);
                </script>";
            }
        } else {
            $backgroundColor = 'red'; 
            echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>BAND NOT FOUND.</div>";
            
            echo " <script>
            var audio = document.createElement('audio');
            document.body.appendChild(audio);
            audio.src = 'Audio/afterexplosionbeep_Pcn6DM5v.mp3';
            audio.addEventListener('canplaythrough', function() {
              audio.play();
              setTimeout(() => {
                audio.pause();
            }, 300);
            }, false);
          </script>
          ";
        }

        $stmt->close();
    } else {
        $backgroundColor = 'red'; 
        echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>No barcode provided.</div>";
    }
}

if (isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
    $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
    $logstmt = $conn->prepare($log);
    if (!$logstmt) {
        die("Prepare failed: " . $conn->error);
    }
    $page = "security";
    $username = $_SESSION["username"];
    $log_action = "security scanned barcode of guest";
    $user_id = $_SESSION["empid"];
    $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
    $logstmt->execute();
    $logstmt->close();
} else {
    echo "User not logged in.";
}

$conn->close();
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security</title>
    <style>
        body {
            font-family: "Lato", sans-serif;
            background-color: <?php echo $backgroundColor; ?>;
        }
        .container {
            padding: 0;
            margin: 5%;
            text-align: center;
        }
        .pos-style {
            display: flex;
            height: 500px;
            width: 900px;
            object-fit: fill;
        }
        .form-group {
            padding-top: 100px;
        }
        .navbar {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
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
        .barcode{
            border: 1px solid black;
            height: 100px;
            width: 750px;
            font-size:75px;
        }
        .barcodelabel{
            height: 100px;
            width: 500px;
            font-size:50px;
        }
        .button{
            width: 20%;
            height: 15%;
            font-size: 60px;
        }
        .tabledisplay {
            text-align: center;
            font-size: 20px;
            color: black;
            width: 100%;
            padding-left: 5%;
            padding-right: 5%;
        }
        table {
            text-align: center;
            font-size: 20px;
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        thead {
            background-color: #f2f2f2;
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
    <!-- <script>
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
            </div> -->

        <div class="container">
            <form action="security.php" method="post">
                <label  class="barcodelabel" for="barcode">Enter Barcode:</label>
                <input type="text" id="barcode" name="barcode" class="barcode" required><br><br>
                <input class="button" type="submit" value="Check">
            </form>
        </div>
    </div>
    <script>
        const inputField = document.getElementById("barcode");
        inputField.focus();
    </script>
</body>

