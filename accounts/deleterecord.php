<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$database = "strikebandbarcode";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['barcode']) && !empty($_POST['barcode'])) {
  // Escape the barcode value to prevent SQL injection
  $bar_code = mysqli_real_escape_string($conn, $_POST['barcode']);
  
  // Use single quotes around the barcode value in the SQL query
  $sql = "DELETE FROM `band` WHERE `bar_code` = '$bar_code'";

  if ($conn->query($sql) === TRUE) {
      echo "Record deleted successfully";
  } else {
      echo "Error deleting record: " . $conn->error;
  }
} else {
  echo "Barcode parameter is missing";
}
if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
    $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
    $logstmt = $conn->prepare($log);
    if (!$logstmt) {
      die("Prepare failed: " . $conn->error);
  }
    $page = "datatablesoutput";
    $username =  $_SESSION["username"];
    $log_action = "user viewed data tables";
    $user_id = $_SESSION["empid"];
    $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
    $logstmt->execute();
  } else {
    echo "Session variables are not set.";
  }

// $conn->close();
?>

<?php
session_start();
$connection=mysqli_connect('localhost','root','','bandbarcode');
$error="";
?>

<!DOCTYPE html>
<html>
<style type="text/css">
    .container{
        padding: 0;
        margin: 0;
    }
    .pos-style{
        display: flex;
        height: 500px;
        width: 900px;
        object-fit: fill;
    }
    .form-group{
        padding-top: 100px;;
    }
    .navbar{
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    body {
    font-family: Arial, sans-serif;
}

.formclass {
    display: inline-block;
    margin: 20px;
    padding: 20px;
    border: 1px solid #ccc;
    border-radius: 5px;
    background-color: #f9f9f9;
    width: 80%;
}

.label {
    display: block;
    margin-bottom: 5px;
}

input[type="text"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ccc;
    border-radius: 3px;
}

input[type="submit"] {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

input[type="submit"]:hover {
    background-color: #45a049;
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
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>delete record</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/bootstrap.css">
</head>  

        <body>
        <div class="container"><div class="sidenav">
        <h1 style="background-color:rgb(231, 239, 240);">Accounts</h1>
        <!-- <a href="addbarcode.php">Add Barcode</a> -->
          <!-- <a href="addbatch.php">Add Batch</a> -->
          <a href="addbarcodedirectly.php">Add Single Directly</a>
          <!-- <a href="addbatchdirectly.php">Add Batch Directly</a> -->
          <a href="addproductdirectly.php">Add multiple barcode</a>
          <a href="datatablesoutput.php">Datatable Output</a>
          <!-- <a href="deleterecord.php">Delete record</a> -->
          <a href="../logout.php">Logout</a>
        </div>

        <div class="main">
            <form class="pos-style" name="pos" action="deleterecord.php" method="post">
            <div class="form-group">
                <label> Delete Barcode</label>
                
                <input type="text" name="barcode" class="form-control" placeholder="bar code read" >
                <input type="submit" value="Delete Barcode">
                </div>
                
            </form>
            
            <?php
            if (mysqli_connect_errno())
            ?>
            <h1 style=" color:red;" class="error"><?php echo $error;?></h1>
            <table>
        <thead>
            <tr>
                <th>Serial Number</th>
                <th>Company</th>
                <th>Band</th>
                <th>Color Code</th>
                <th>Batch Code</th>
                <th>Barcode</th>
                <th>Issue Time</th>
                <th>Used Time</th>
                <th>Issued</th>
                <th>Used</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $sql = "SELECT * FROM band";
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['serial_number'] . "</td>";
                    echo "<td>" . $row['company'] . "</td>";
                    echo "<td>" . $row['color_code'] . "</td>";
                    echo "<td>" . $row['batch_code'] . "</td>";
                    echo "<td>" . $row['bar_code'] . "</td>";
                    echo "<td>" . $row['issue_time'] . "</td>";
                    echo "<td>" . $row['used_time'] . "</td>";
                    echo "<td>" . ($row['issued'] ? 'Yes' : 'No') . "</td>";
                    echo "<td>" . ($row['used'] ? 'Yes' : 'No') . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No records found</td></tr>";
            }

            ?>
        </tbody>
    </table>
        </div>
    <?php
        $conn->close();
    ?>
    </div>
    </body>

</html>