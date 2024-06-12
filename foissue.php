<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "bandbarcode";
$conn = new mysqli($servername, $username, $password, $database);
$backgroundColor = 'green'; // Default color is green

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $barcode = isset($_POST['barcode']) ? $_POST['barcode'] : null;
    
    if ($barcode !== null) {
        $username = $_SESSION["username"];

        // Check if the barcode exists in the database
        $checksql = "SELECT * FROM band WHERE `bar_code` = ?";
        $stmt = $conn->prepare($checksql);
        $stmt->bind_param("s", $barcode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (!$row['fo_issued']) {
                    $sql = "UPDATE `band` SET `fo_issue_time` = CURRENT_TIMESTAMP(), `fo_issued` = true, `fo_user` = ? WHERE `bar_code` = ?";
                    $update_stmt = $conn->prepare($sql);

                    if ($update_stmt) {
                        $update_stmt->bind_param("ss", $username, $barcode);
                        $update_stmt->execute();
                        $update_stmt->close();
                        // Change background color to green
                        $backgroundColor = 'green';
                        echo "<div style='background-color: green; text-align: center; font-size: 5rem; color: white'>Band issued successfully</div>";
                    } else {
                        // Change background color to red
                        $backgroundColor = 'red';
                        echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>Failed to issue band</div>";
                    }
                } else {
                    // Change background color to red
                    $backgroundColor = 'red';
                    echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>Band already issued</div>";
                    echo "<script>document.getElementById('barcode').focus();</script>";
                }
            }
        } else {
            // Change background color to red
            $backgroundColor = 'red';
            echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>Barcode not found.</div>";
        }

        // Close the result set
        $result->close();
    } else {
        // Change background color to red
        $backgroundColor = 'red';
        echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>No Barcode</div>";
    }
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
    $log_action = "FO scanned barcode of guest ".$username;
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
    <title>foissue</title>
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
    <div class="container">
        <h2 style="color: white;">Scan Barcode</h2>
        <form action="foissue.php" method="post">
            <label for="barcode">Enter Barcode:</label>
            <input type="text" id="barcode" name="barcode" required><br><br>
            <input type="submit" value="Scan">
        </form>
    </div>
</div>
<script>
    const inputField = document.getElementById("barcode");
    inputField.focus();
</script>
</body>
</html>
