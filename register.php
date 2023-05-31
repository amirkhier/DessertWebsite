<?php
// Include the necessary files
require_once('includes/db.php');
require_once('includes/functions.php');

// Check if the user is already logged in
if (is_logged_in()) {
    header('Location: home.php');
    exit();
}

// Define variables and set to empty values
$username = $password = $confirm_password = $name = $age = $profile_pic = '';
$username_err = $password_err = $confirm_password_err = $name_err = $age_err = $profile_pic_err = '';
$allowed_types = ['image/jpg', 'image/jpeg', 'image/png'];
$base64_encoded = '';

// Process form data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0 && !empty($_FILES['profile_pic'])) {
        // Check if the file size is less than or equal to 1MB
        if ($_FILES['profile_pic']['size'] <= 1048576) {

            // Check if the file type is allowed
            $file_ext = strtolower($_FILES['profile_pic']['type']);
            if (in_array($file_ext, $allowed_types)) {
                $file_contents = file_get_contents($_FILES['profile_pic']['tmp_name']);
                $profile_pic_err = '';
                $base64_encoded = base64_encode($file_contents);
                // add data:image/png;base64, to the beginning of the base64 string
                $base64_encoded = 'data:' . $file_ext . ';base64,' . $base64_encoded;
            } else {
                $profile_pic_err = 'File type not allowed. Allowed types: ' . implode(', ', $allowed_types) . '.';
            }
        } else {
            $profile_pic_err = 'File size must be less than or equal to 1MB.';
        }
    } else {
        $profile_pic_err = 'Please add a profile picture.';
    }

    // Validate username
    if (empty(trim($_POST['username']))) {
        $username_err = 'Please enter a username.';
    } else {
        // Check if the username is already taken
        $sql = 'SELECT id FROM users WHERE username = ?';
        $stmt = $conn->prepare($sql);
        $param_username = trim($_POST['username']);
        $stmt->bindParam(1, $param_username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result !== false) {
            $username_err = 'This username is already taken.';
        } else {
            $username = trim($_POST['username']);
        }
    }

    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter a password.';
    } elseif (strlen(trim($_POST['password'])) < 6) {
        $password_err = 'Password must have at least 6 characters.';
    } else {
        $password = trim($_POST['password']);
    }

    if (empty(trim($_POST['name']))) {
        $name_err = 'Please enter your name.';
    } else {
        $name = trim($_POST['name']);
    }

    // Validate age
    if (empty(trim($_POST['age']))) {
        $age_err = 'Please enter your age.';
    } else {
        $age = trim($_POST['age']);
    }



    // Validate confirm password
    if (empty(trim($_POST['confirm_password']))) {
        $confirm_password_err = 'Please confirm password.';
    } else {
        $confirm_password = trim($_POST['confirm_password']);
        if ($password != $confirm_password) {
            $confirm_password_err = 'Passwords do not match.';
        }
    }

    // Check for input errors before inserting into database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($name_err) && empty($age_err) && empty($profile_pic_err)) {
        print("woo");;
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user into the database
        $sql = 'INSERT INTO users (username, password, name, age, profile_pic) VALUES (?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $param_username, PDO::PARAM_STR);
        $stmt->bindParam(2, $param_password, PDO::PARAM_STR);
        $stmt->bindParam(3, $param_name, PDO::PARAM_STR);
        $stmt->bindParam(4, $param_age, PDO::PARAM_STR);
        $stmt->bindParam(5, $base64_encoded, PDO::PARAM_STR);

        $param_username = $username;
        $param_password = $hashed_password;
        $param_name = $name;
        $param_age = $age;
        if ($stmt->execute()) {
            // Registration successful, redirect to login page
            header('Location: login.php');
            exit();
        } else {
            echo 'Something went wrong. Please try again later.';
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
    <title>Register - Recipe App</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include('includes/header.php'); ?>

    <div class="container mt-3">
        <div class="row justify-content-center pt-5">
            <div class="col-md-6 p-4 shadow">
                <p>

                    <?php echo $base64_encoded ?>
                </p>
                <h2>Register</h2>
                <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                        <span class="invalid-feedback">
                            <?php echo $name_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Age</label>
                        <input type="number" name="age" class="form-control <?php echo (!empty($age_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $age; ?>">
                        <span class="invalid-feedback">
                            <?php echo $age_err; ?>
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control <?php echo (!empty($username_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $username; ?>">
                        <span class="invalid-feedback">
                            <?php echo $username_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control <?php echo (!empty($password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $password; ?>">
                        <span class="invalid-feedback">
                            <?php echo $password_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control <?php echo (!empty($confirm_password_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $confirm_password; ?>">
                        <span class="invalid-feedback">
                            <?php echo $confirm_password_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Profile Picture</label>
                        <input type="file" name="profile_pic" class="form-control-file">
                        <span class="invalid-feedback d-block">
                            <?php echo $profile_pic_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <input type="submit" value="Register" class="btn btn-primary">
                        <input type="reset" value="Reset" class="btn btn-secondary ml-2">
                    </div>

                    <p>Already have an account? <a href="login.php">Login here</a>.</p>

                </form>
            </div>
        </div>
    </div>

    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>