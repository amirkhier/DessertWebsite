<?php
session_start();

// Include the necessary files
require_once('includes/db.php');
require_once('includes/functions.php');

// Check if the user is already logged in
if (is_logged_in()) {
    header('Location: home.php');
    exit();
}

// Define variables and set to empty values
$username = $password = '';
$username_err = $password_err = '';

// Process form data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Validate username
    if (empty(trim($_POST['username']))) {
        $username_err = 'Please enter a username.';
    } else {
        $username = trim($_POST['username']);
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter a password.';
    } else {
        $password = trim($_POST['password']);
    }

    // Check for input errors before logging in
    if (empty($username_err) && empty($password_err)) {

        // Check if the username and password are correct
        $sql = 'SELECT id, username, password, name, profile_pic FROM users WHERE username = ?';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $param_username, PDO::PARAM_STR);
        $param_username = $username;
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() == 1) {
            if (password_verify($password, $result['password'])) {
                // Password is correct, log in the user
                $_SESSION['user_id'] = $result['id'];
                $_SESSION['username'] = $result['username'];
                $_SESSION['name'] = $result['name'];
                $_SESSION['profile_pic'] = $result['profile_pic'];
                $coupons = [
                    ['name' => 'Sultam 10%', 'code' => 'CODE1', 'recipes_amount' => 5, "image" => "sultam.jpg"],
                    ['name' => 'Mahsaney hashmal 15%', 'code' => 'CODE2', 'recipes_amount' => 10, "image" => "hashmal.jfif"],
                    ['name' => 'Home center 10%', 'code' => 'CODE3', 'recipes_amount' => 15, "image" => "homecenter.jfif"],
                    ['name' => 'Fox Home 10%', 'code' => 'CODE4', 'recipes_amount' => 20, "image" => "fox-home.jpg"],
                    ['name' => 'Wolt 20%', 'code' => 'CODE5', 'recipes_amount' => 25, "image" => "Wolt-Logo.jpg"]
                ];
                $_SESSION['coupons'] = $coupons;
                session_write_close(); // write session data and close the session
                header('Location: home.php');
                exit();
            } else {
                // Password is not correct, show error message
                $password_err = 'The password you entered is not valid.';
            }
        } else {
            // Username not found, show error message
            $username_err = 'No account found with that username.';
        }

        // Close statement
        $stmt->closeCursor();
    }

    // Close connection
    $conn = null;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Login - Recipe App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <div class="container mt-3">
        <div class="row justify-content-center pt-5">
            <div class="col-md-6 p-4 shadow">
                <h2>Login</h2>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <span class="invalid-feedback">
                            <?php echo $username_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>">
                        <span class="invalid-feedback">
                            <?php echo $password_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <input type="submit" value="Login" class="btn btn-primary">
                        <input type="reset" value="Reset" class="btn btn-secondary ml-2">
                    </div>
                    <p>Don't have an account? <a href="register.php">Sign up now</a>.</p>

                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>