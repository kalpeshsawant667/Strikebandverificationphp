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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["bar_code"])) {
        $bar_code = $_POST["bar_code"];

        $sql = "INSERT INTO `band`(`company`, `color`, `color_code`, `batch_code`, `bar_code`, `issue_time`, `issued`) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";

        $company = $_POST['company'];
        $color = $_POST['color'];
        $color_code = $_POST['color_code'];
        $batch_code = $_POST['batch_code'];
        $issue_time = date('Y-m-d H:i:s');
        $issued = 1;
        $count =  $_POST['count'];

        $stmt = $conn->prepare($sql);

        for ($i = 1; $i <= $count; $i++) {
            echo $bar_code;
            $bar_code = (int)$bar_code + 1;
            $bar_code = strval($bar_code);
            $stmt->bind_param("ssssssi", $company, $color, $color_code, $batch_code, $bar_code, $issue_time, $issued );
            if ($stmt->execute()) {
                echo "Barcode added successfully.";
            } else {
                echo "Error: " . $sql . "<br>" . $conn->error;
            }
        }

        $stmt->close();
    }
    $conn->close();
}
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
<body>
<div class="sidenav">
  <h1 style="background-color:rgb(231, 239, 240);">Admin</h1>
    <a href="useradd.php">Add User</a>
    <a href="usermodification.php">Update User</a>
    <a href="addbarcode.php">Add Barcode</a>
    <!-- <a href="addbatch.php">Add Batch</a> -->
    <a href="addproduct.php">Add Product</a>
    <a href="addbarcodedirectly.php">Add Directly</a>
    <a href="addbatchdirectly.php">Add Batch Directly</a>
    <a href="addproductdirectly.php">Add Product Directly</a>
    <a href="foissue.php">Front Office</a>
    <a href="voiditem.php">void item</a>
    <a href="security.php">Security</a>
    <a href="datatablesoutput.php">Datatable Output</a>
    <a href="generatereport.php">generatereport</a>
    <a href="deleterecord.php">Delete record</a>
    <a href="deletuser.php">Delete User</a>
    <a href="logout.php">Logout</a>
  </div>
<div class="main">
        <form class="formclass" action="addbatch.php" method="post">
            <label class="label" for="company">Company:</label>
            <input type="text" name="company" id="company" required><br>

            <label class="label" for="color_code">Color Code:</label>
            <input type="text" name="color_code" id="color_code" required><br>

            <label class="label" for="batch_code">Batch Code:</label>
            <input type="text" name="batch_code" id="batch_code" required><br>
            <label class="label" for="barcode">Barcode:</label>
            <input type="text" name="bar_code" id="bar_code" required><br>
            <label for="quantity">Quantity :</label>
            <input type="number" id="count" name="count"><br>

            <input type="submit" value="Add Barcode">
        </form>

    <script src="js/jquery.min.js"></script>
    <script src="js/popper.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</div>
</body>
</html>

