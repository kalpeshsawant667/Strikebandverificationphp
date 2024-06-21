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

function csv_to_sql_update($csv_file_path, $table_name, $conn) {
    // Open the CSV file and read its content
    if (($handle = fopen($csv_file_path, "r")) !== FALSE) {
        // Get the column headers
        $headers = fgetcsv($handle, 1000, ",");
        
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $row = array_combine($headers, $data);
            $serial_number = $row['serial_number'];
            $company = $conn->real_escape_string($row['company']);
            $color_code = $conn->real_escape_string($row['color_code']);
            $batch_code = $conn->real_escape_string($row['batch_code']);
            $letter = $conn->real_escape_string($row['letter']);
            $bar_code = $conn->real_escape_string($row['bar_code']);
            $issue_time = $conn->real_escape_string($row['issue_time']);
            $issued = $conn->real_escape_string($row['issued']);
            $fo_issue_time = $conn->real_escape_string($row['fo_issue_time']);
            $fo_issued = $conn->real_escape_string($row['fo_issued']);
            $fo_user = $conn->real_escape_string($row['fo_user']);
            $used_time = $conn->real_escape_string($row['used_time']);
            $used = $conn->real_escape_string($row['used']);
            $count = $conn->real_escape_string($row['count']);
            $voiditem = $conn->real_escape_string($row['voiditem']);
            $voidtime = !empty($row['voidtime']) ? "'" . $conn->real_escape_string($row['voidtime']) . "'" : 'NULL';
            $upgradedfrombarcode = !empty($row['upgradedfrombarcode']) ? "'" . $conn->real_escape_string($row['upgradedfrombarcode']) . "'" : 'NULL';
            $remarks = !empty($row['remarks']) ? "'" . $conn->real_escape_string($row['remarks']) . "'" : 'NULL';

            // Properly format the SQL UPDATE query
            $update_query = "
                UPDATE $table_name
                SET
                    company = '$company',
                    color_code = '$color_code',
                    batch_code = '$batch_code',
                    letter = '$letter',
                    bar_code = '$bar_code',
                    issue_time = '$issue_time',
                    issued = '$issued',
                    fo_issue_time = '$fo_issue_time',
                    fo_issued = '$fo_issued',
                    fo_user = '$fo_user',
                    used_time = '$used_time',
                    used = '$used',
                    count = '$count',
                    voiditem = '$voiditem',
                    voidtime = $voidtime,
                    upgradedfrombarcode = $upgradedfrombarcode,
                    remarks = $remarks
                WHERE
                    serial_number = '$serial_number'
            ";

            if (!$conn->query($update_query)) {
                echo "Error updating record: " . $conn->error . "\n";
                echo "Query: $update_query\n";
            }
        }
        fclose($handle);
    } else {
        echo "Error opening CSV file.\n";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csv_file"])) {
    $csv_file_path = $_FILES["csv_file"]["tmp_name"];
    $table_name = 'band';
    csv_to_sql_update($csv_file_path, $table_name, $conn);
}

$conn->close();
?>

<!doctype html>
<html lang="en">
<head>
    <title>Multiple All Barcode</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
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
        .formclass input[type="text"], .formclass input[type="file"] {
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
        @media screen and (max-height: 450px) {
            .sidenav {padding-top: 15px;}
            .sidenav a {font-size: 18px;}
        }
    </style>
</head>
<body>
<div class="sidenav">
    <h1 style="background-color:rgb(231, 239, 240);">Admin</h1>
    <a href="addbarcodedirectly.php">Add Barcode</a>
    <a href="addproductdirectly.php">Add barcode Directly</a>
    <a href="addproductdirectlyall.php">Add barcode All Directly</a>
    <a href="foissue.php">Front Office</a>
    <a href="foonboard.php">Band Update onboard</a>
    <a href="reissue.php">Re issue Office</a>
    <a href="voiditem.php">void item</a>
    <a href="security.php">Security</a>
    <a href="datatablesoutput.php">Datatable Output</a>
    <a href="generatereport.php">Generate Report</a>
    <a href="useradd.php">Add User</a>
    <a href="updatealldatausingcsv.php">Upload CSV All</a>
    <a href="resetpassword.php">Reset Password</a>
    <a href="deletuser.php">Delete User</a>
    <a href="Userlogs.php">User Logs</a>
    <a href="changepassword.php">Change Password</a>
    <a href="logout.php">Logout</a>
</div>
<div class="main">
    <form class="formclass" action="addproductdirectlyall.php" method="post" enctype="multipart/form-data">
        <label class="label" for="csv_file">Upload CSV file:</label>
        <input type="file" name="csv_file" id="csv_file" accept=".csv" required><br>
        <input type="submit" value="Upload Barcode">
    </form>
</div>
</body>
</html>
