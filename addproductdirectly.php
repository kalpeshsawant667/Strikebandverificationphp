<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$database = "bandbarcode";
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csv_file"])) {
    $file_name = $_FILES["csv_file"]["name"];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    if ($file_ext !== "csv") {
        die("Please upload a CSV file.");
    }

    $file_tmp = $_FILES["csv_file"]["tmp_name"];

    if (($handle = fopen($file_tmp, "r")) !== FALSE) {
      fgetcsv($handle, 1000, ",");
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $company = substr($data[0], 0, 2);
            $color_code = substr($data[0], 2, 3);
            $batch_code = substr($data[0], 5, 4);
            $letter = substr($data[0], 9, 1);
            $bar_code = $data[0];
            $issue_time = date('Y-m-d H:i:s');
            $issued = 1;

            $sql = "INSERT INTO `band`(`company`, `color_code`, `batch_code`, `letter`, `bar_code`, `issued`) 
            VALUES (?, ?, ?, ?, ?, ?)";

            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssi", $company, $color_code, $batch_code, $letter, $bar_code, $issued);

            if ($stmt->execute()) {
                // echo "<script>alert('Barcode added successfully.');</script>";
            } else {
                echo "<script>alert('Error');</script>" . $sql . "<br>" . $conn->error;
                break;
            }
        }
        echo "<script>alert('Barcode added successfully.');</script>";
        fclose($handle);
    } else {
        echo "Error opening file.";
    }


    // if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
    //   $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
    //   $logstmt = $conn->prepare($log);
    //   if (!$logstmt) {
    //     die("Prepare failed: " . $conn->error);
    // }
    //   $page = "addproductdirectlybarcode";
    //   $username =  $_SESSION["username"];
    //   $log_action = "user added barcode csv file directly";
    //   $user_id = $_SESSION["empid"];
    //   $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
    //   $logstmt->execute();
    // } else {
    //   echo "Session variables are not set.";
    // }
    // Close database connection
    $conn->close();
}
?>

<!doctype html>
<html lang="en">
<head>
    <title>multiple barcode</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Your CSS and other HTML head content -->

</head>
<body>
    
<!doctype html>
<html lang="en">
<head>
    <title>Add Barcode</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style type="text/css">
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
    <!-- <a href="deleterecord.php">Delete record</a> -->
    <a href="useradd.php">Add User</a>
    <a href="resetpassword.php">reset password</a>
    <a href="usermodification.php">user modification</a>
    <a href="deletuser.php">Delete User</a>
    <a href="Userlogs.php">User Logs</a>
    <a href="changepassword.php">Change Password</a>
    <a href="logout.php">Logout</a>
  </div>
    <div class="main">
        <form class="formclass" action="addproductdirectly.php" method="post" enctype="multipart/form-data">
            <label class="label" for="csv_file">Upload CSV file:</label>
            <input type="file" name="csv_file" id="csv_file" accept=".csv" required><br>

            <input type="submit" value="Upload Barcode">
        </form>
    </div>
</body>
</html>
