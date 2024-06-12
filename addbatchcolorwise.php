<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "bandbarcode";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

    global $conn;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Prepare SQL statement
    $sql = "INSERT INTO `band`(`company`, `color_code`, `batch_code`, `letter`, `bar_code`, `issue_time`, `issued`) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $bar_code = $_POST['barcode'];
    $count = $_POST['count'];
    echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: black'>Count: $count</div>";

$company_length = 2;
$color_code_length = 3;
$batch_code_length = 4;
$letter_length = 1;
$code_length = 5;

$company = str_pad(substr($bar_code, 0, $company_length), $company_length, "0", STR_PAD_RIGHT);
$color_code = str_pad(substr($bar_code, 2, $color_code_length), $color_code_length, "0", STR_PAD_RIGHT);
$batch_code = str_pad(substr($bar_code, 5, $batch_code_length), $batch_code_length, "0", STR_PAD_RIGHT);
$letter = str_pad(substr($bar_code, 9, $letter_length), $letter_length, "0", STR_PAD_RIGHT);
$code = str_pad(substr($bar_code, 10, $code_length), $code_length, "0", STR_PAD_LEFT); // Assuming this is the code part of the barcode


    // $company = substr($bar_code, 0, 2);     
    // $color_code = substr($bar_code, 2, 3);
    // $batch_code = substr($bar_code, 5, 4);
    // $letter = substr($bar_code, 9, 1);
    // $code = substr($bar_code, 10, 5);  
    $issue_time = date('Y-m-d H:i:s');
    $issued = 1;
    
    // Check barcode length
    if (strlen($bar_code) != 16) {
        echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: white'>Barcode length should be 16 characters.</div>";
    } else {
        $stmt = $conn->prepare($sql);
        for ($i = 1; $i <= $count; $i++) {
            $code = (int)$code + 1;
            $increasedcode = strval($code);
            $barcode = $company . $color_code . $batch_code . $letter . $increasedcode;
            
            // Prepare and bind parameters
            $stmt->bind_param("ssssssi", $company, $color_code, $batch_code, $letter, $barcode, $issue_time, $issued);

            if ($stmt->execute()) {
                // echo "<script>alert('Barcode added successfully.');</script>";    
            } else {
                echo "<div style='background-color: red; text-align: center; font-size: 5rem; color: white'>Error inserting barcode.</div>";
                echo "<script>alert('Error');</script>" . $sql . "<br>" . $conn->error;
                break;
            }
        }
        $stmt->close();
    }
}

$conn->close();
?>
<!doctype html>
<html lang="en">
<head>
    <title>Add Batch</title>
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
    <form class="formclass" action="addbatchcolorwise.php" method="post">
        <label class="label" for="barcode">Barcode:</label>
        <input type="text" name="barcode" id="barcode" required><br>
        <label for="quantity">Quantity :</label>
        <input type="number" id="count" name="count" min="1" required><br>
        <input type="submit" value="Add Barcode">
    </form>
</div>
</body>
</html>
