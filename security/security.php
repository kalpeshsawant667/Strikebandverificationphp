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

function checkBarcodeExists($conn, $barcode) {
    $sql = "SELECT * FROM `band` WHERE `bar_code` = ? AND fo_issued = TRUE";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $barcode);
    $stmt->execute();
    $result = $stmt->get_result();
    $row_count = $result->num_rows;

    if ($row_count > 0) {
        $row = $result->fetch_assoc();
        $count = $row["count"];
        $count++;
        if (!$row["used"]) {
            $update_sql = "UPDATE `band` SET `used_time` = CURRENT_TIMESTAMP(), `used` = true, `count` = ? WHERE `bar_code` = ?";
           
            echo "<tr style='text-align: center;'>";
            echo "<td style='text-align: center;'>" . $row['serial_number'] . "</td>";
            echo "<td style='text-align: center;'>" . $row['bar_code'] . "</td>";
            echo "<td style='text-align: center;'>" . $row['fo_issue_time'] . "</td>";
            echo "<td style='text-align: center;'>" . $row['used_time'] . "</td>";
            echo "<td style='text-align: center;'>" . ($row['used'] ? 'Yes' : 'No') . "</td>";
            echo "<td style='text-align: center;'>" . $row['count'] . "</td>";
            echo "</tr>";
        } else {
            $update_sql = "UPDATE `band` SET `count` = ? WHERE `bar_code` = ?";
            echo "<div style='background-color: red; text-align: center; font-size: 5rem;color: white'>Band has already been used! Count: $count</div>";
        }
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("is", $count, $barcode);
        $update_stmt->execute();
        $update_stmt->close();
    }
    
    $stmt->close();

    return $row_count > 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : null;
    if ($barcode !== null) {
        if (checkBarcodeExists($conn, $barcode)) {
            $backgroundColor = 'green'; // Change background color to green
            echo "<div style='background-color: green; text-align: center; font-size: 5rem; color: white'>VALID BAND.</div>";
        } else {
            $backgroundColor = 'red'; // Change background color to red
            echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>BAND NOT FOUND.</div>";
        }
    } else {
        $backgroundColor = 'red'; // Change background color to red
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
    $username =  $_SESSION["username"];
    $log_action = "security scanned barcode of guest" . $username;
    $user_id = $_SESSION["empid"];
    $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
    $logstmt->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Pos</title>
    <style>
        body {
            font-family: "Lato", sans-serif;
            background-color: <?php echo $backgroundColor; ?>;
        }
        .container {
            padding: 0;
            margin: 0;
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
            height: 50px;
            width: 300px;
            font-size:25px;
        }
        @media screen and (max-height: 450px) {
            .sidenav {padding-top: 15px;}
            .sidenav a {font-size: 18px;}
        }
    </style>
</head>  
<body>
<div class="sidenav">
  <h1 style="background-color:rgb(231, 239, 240);">Security</h1>
        <a href="security.php">Security</a>
        <a href="datatablesoutput.php">Report</a>
        <a href="../logout.php">Logout</a>
  </div>
    <div class="main">
        <div class="container">
            <h2 style="color: white;">Check Band</h2>
            <form action="security.php" method="post">
                <label for="barcode">Enter Barcode:</label>
                <input type="text" id="barcode" name="barcode" class="barcode" required><br><br>
                <input type="submit" value="Check">
            </form>
        </div>
        <?php
            $update_sql = "SELECT * FROM `band` WHERE `bar_code` = ? AND fo_issued = TRUE";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("s", $barcode);
            $update_stmt->execute();
            $result = $update_stmt->get_result();
            $row_count = $result->num_rows;
        $row = $result->fetch_assoc();
        if ($row_count > 0) {
            $count = $row["count"];
            $count++;
            if (!$row["used"]) {
                echo "<h2>Band Details</h2>";
                echo "<table>";
                echo "<thead>";
                echo "<tr>";
                echo "<th>Serial Number</th>";
                echo "<th>Barcode</th>";
                echo "<th>FO Issued</th>";
                echo "<th>Used</th>";
                echo "<th>Times Scanned</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                echo "<tr style='text-align: center;'>";
                echo "<td style='text-align: center;'>" . $row['serial_number'] . "</td>";
                echo "<td style='text-align: center;'>" . $row['bar_code'] . "</td>";
                echo "<td style='text-align: center;'>" . $row['fo_issue_time'] . "</td>";
                echo "<td style='text-align: center;'>" . $row['used_time'] . "</td>";
                echo "<td style='text-align: center;'>" . ($row['used'] ? 'Yes' : 'No') . "</td>";
                echo "<td style='text-align: center;'>" . $row['count'] . "</td>";
                echo "</tr>";
                echo "</tbody>";
                echo "</table>";
            }
            $update_stmt->close();
        }
        ?>
    </div>
    <script>
        const inputField = document.getElementById("barcode");
        inputField.focus();
    </script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></
