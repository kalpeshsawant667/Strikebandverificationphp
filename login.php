<?php
session_start();
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$database = "strikebandbarcode";
$conn = new mysqli($servername, $dbusername, $dbpassword, $database);

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: logout.php");
    exit;
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    if (!empty($username) && !empty($password)) {
        $sql = "SELECT * FROM users WHERE username=? AND status=TRUE";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row["password"])) {
            // if($password===$row["password"]){
                $_SESSION["loggedin"] = true;
                $_SESSION["empid"] = $row["empid"];
                $_SESSION["username"] = $row["username"];
                $_SESSION["department"] = $row["department"];
                $_SESSION["status"] = $row["status"];

                if(isset($_SESSION["username"]) && isset($_SESSION["empid"])) {
                    $log = "INSERT INTO user_log (page, username, log_action, user_id) VALUES (?, ?, ?, ?)";
                    $logstmt = $conn->prepare($log);

                    if (!$logstmt) {
                      die("Prepare failed: " . $conn->error);
                    }
                    $page = "login";
                    $username =  $_SESSION["username"];
                    $log_action = "user logged in";
                    $user_id = $_SESSION["empid"];
                    $logstmt->bind_param("sssi", $page, $username, $log_action, $user_id);
                    $logstmt->execute();
                }

                switch ($row["department"]) {
                    case 'technical':
                        header("location: addbarcodedirectly.php");
                        break;
                    case 'Administrator':
                        header("location: addbarcodedirectly.php");
                        break;
                    case 'accounts':
                        header("location: accounts/addbarcodedirectly.php");
                        break;
                    case 'security':
                        header("location: security/security.php");
                        break;
                    case 'surveillance':
                        header("location: surveillance/generatereport.php");
                        break;
                    case 'fo':
                        header("location: fo/foissue.php");
                        break;
                    case 'fosuperviser':
                        header("location: fosuperviser/generatereport.php");
                        break;
                    case 'foonboard':
                        header("location: FOonboard/foonboard.php");
                        break;
                    default:
                        echo "Invalid department.";
                }
                exit;
            } else {
                $login_err = "Invalid password.";
                echo "<div style='background-color: red;'>Invalid password.</div>";
            }
        } else {
            $login_err = "Invalid username or password.";
            echo "<div style='background-color: red;'>Invalid username or password.</div>";
        }
    } else {
        $login_err = "Username and password are required.";
        echo "Username and password are required.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .bodyclass {
            background-color: #000000;
        }
        .img {
            padding-left: 500px;
        }
    </style>
</head>
<body class="bodyclass">

<script>
function togglePassword() {
    var passwordField = document.getElementById("password");
    if (passwordField.type === "password") {
        passwordField.type = "text";
        document.querySelector(".toggle-password").textContent = "";
    } else {
        passwordField.type = "password";
        document.querySelector(".toggle-password").textContent = "";
    }
}
</script>

<form class="ftco-section" name="pos" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 text-center mb-5">
                <div>
                    <img src="./images/logo.png" width="100px" height="100px" class="img" style="padding-left: 500px;">
                </div>
                <h2 class="heading-section">Strike By Big Daddy</h2>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="login-wrap p-0">
                    <h3 class="mb-4 text-center">Have an account?</h3>
                    <div class="form-group">
                        <input type="text" name="username" id="username" class="form-control" placeholder="Username" autocomplete="username" required>
                    </div>
                    <div class="form-group">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Password" autocomplete="current-password" required>
                        <span toggle="password" class="fa fa-fw fa-eye field-icon toggle-password" onclick="togglePassword()"></span>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="form-control btn btn-primary submit px-3">Sign In</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script src="js/jquery.min.js"></script>
<script src="js/popper.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>
</body>
<?php
$conn->close();
?>
</html>
