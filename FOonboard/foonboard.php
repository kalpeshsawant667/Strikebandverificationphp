<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "strikebandbarcode";
$conn = new mysqli($servername, $username, $password, $database);
$backgroundColor = 'green'; 

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
                        $sql = "UPDATE `band` SET `fo_issue_time` = CURRENT_TIMESTAMP(), `used_time`=  CURRENT_TIMESTAMP(), `remarks` = 'From $barcode', count=count+1, `fo_issued` = true, `used` = true, `fo_user` = ?, `upgradedfrombarcode`= ? WHERE `bar_code` = ?";
                        $update_stmt = $conn->prepare($sql);
                        $update_stmt->bind_param("sss", $username, $barcode, $upgradebarcode);
                        $update_stmt->execute();
                        $update_stmt->close();
                        $update_sql = "UPDATE `band` SET `voiditem` = 1, `remarks` = 'Upgraded $upgradebarcode' WHERE `bar_code` = ?";
                        $update_st = $conn->prepare($update_sql);
                        $update_st->bind_param("s", $barcode);
                        $update_st->execute();
                        $update_st->close();
                        $backgroundColor = 'green';
                        echo "<div style='background-color: green; text-align: center; font-size: 5rem; color: white'>Band issued successfully `$upgradebarcode`</div>";
                    }
                } else {
                    $backgroundColor = 'red';
                    echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>Barcode not found.</div>";
                }

                // Close the result set
                $upgraderesult->close();
            } else {
                $backgroundColor = 'red';
                echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>Error </div>";
            }
        } else {
            $backgroundColor = 'red';
            echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>Same Barcode</div>";
        }
    }
} else {
    $backgroundColor = 'red';
    echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>No Barcode</div>";
}



// Log user action
if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
    $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
    $logstmt = $conn->prepare($log);
    if (!$logstmt) {
        die("Prepare failed: " . $conn->error);
    }
    $page = "foissue";
    $username = $_SESSION["username"];
    $log_action = "FO upgraded barcode of guest ".$username;
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
        @media screen and (max-height: 450px) {
            .sidenav {padding-top: 15px;}
            .sidenav a {font-size: 18px;}
        }
    </style>
</head>  
<body onload="document.pos.barcode.focus();">
<div class="sidenav">
  <h1 style="background-color:rgb(231, 239, 240);">FO onboard</h1>
    <a href="foissue.php">Front Office</a>
    <a href="foonboard.php">FO onboard</a>
    <a href="datatablesoutput.php">Datatable Output</a>
    <a href="voiditem.php">void band</a>
    <a href="generatereport.php">generate report</a>
    <a href="../logout.php">Logout</a>
  </div>
<div class="main">
    <div class="container">
        <h2 style="color: white;">Scan Barcodes</h2>
        <form action="foonboard.php" method="post">
            <label  class="oldband" for="oldbarcode">Old Band:</label>
            <input type="text" id="oldbarcode" name="oldbarcode" required><br><br>
            <label  class="newband" for="upgradebarcode">Upgrade Band to:</label>
            <input type="text" id="upgradebarcode" name="upgradebarcode" required><br><br>
            <input type="submit" value="Upgrade band">
        </form>
    </div>
</div>
</body>
</html>
