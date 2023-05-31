<?php
session_start();
require_once('includes/db.php');
require_once('includes/functions.php');
if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit();
}
if (isset($_POST['login'])) {
    header('Location: login.php');
    exit();
} elseif (isset($_POST['signup'])) {
    header('Location: register.php');
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dessfits</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>

    </style>
</head>

<body>

    <!-- 
    <div class="container">
        <img src="./images/logo.svg" alt="Your App Logo">
        <h1>Welcome to</h1>
        <h2 class="name">Dessfits</h2>
        <div>
            <button class="btn btn-primary" id="login">Login</button>
        </div>
        <div class="or">
            OR
        </div>
        <div>

            <button class="btn btn-primary" id="register">Register</button>
        </div>
    </div> -->
    <div class="container d-flex align-items-center justify-content-center h-100">
        <div class="box ">
            <img src="./images/logo.svg" alt="Logo" class="logo">
            <h2 class="text-center">Welcome</h2>
            <div>

                <a href="login.php" class="btn btn-custom shadow btn-login w-100">Login</a>
            </div>
            <div>

                <a href="register.php" class="btn btn-custom shadow btn-signup w-100">Sign up</a>
            </div>
            <h2 class="text-center mt-4 name">Dessfit</h2>
        </div>
    </div>
    <link rel="stylesheet" href="./css/landing-page.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>