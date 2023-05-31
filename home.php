<?php
// Include the necessary files
session_start();
require_once('includes/db.php');
require_once('includes/functions.php');
if (!isset($_SESSION['user_id'])) {
    // Redirect to auth.php
    header('Location: landing-page.php');
    exit(); // Make sure to exit the script after the redirect
}
$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>

<head>
    <title>Dessfits</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>

    <?php include('includes/header.php'); ?>
    

    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
            
            
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img class="d-block w-100" src="https://t3.ftcdn.net/jpg/01/76/33/14/360_F_176331484_nLHY9EoW0ETwPZaS9OBXPGbCJhT70GZe.jpg" height="320px" alt="First slide">
                <div class="carousel-caption d-none d-md-block">
                  <h2 style="color:#00FFFF"><b>Welcome to Dessfits</b></h2>
                    <p style="color:#00FFFF"><b>If you're craving a delectable dessert, look no further than this comprehensive recipe website brimming with tantalizing sweet treats.</b></p>
                    </div>
            </div>
            <div class="carousel-item">
                <img class="d-block w-100" src="https://insanelygoodrecipes.com/wp-content/uploads/2021/05/Cherry-Cheesecake-with-Berry-Sauce.png" alt="Second slide" height="320px">
                <div class="carousel-caption d-none d-md-block">
                  <h1 style="color:black"><b>Search by ingridients from our recipes</b></h1>
                  <p style="color:black"><b>Easily search our recipe website by ingredients to find the perfect dessert for your cravings.</b></p>
                  
                    
            </div>
            
            
           
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div class="container mt-3 d-flex justify-content-center"> 
        <form action="search.php" method="POST" class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="text" placeholder="Search recipes by ingredients" name="ingredients">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>
