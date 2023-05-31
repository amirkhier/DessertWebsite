<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        
      
        .navbar a.nav-link {
            font-weight:bold; !important;
            
        }
       
        
        

        
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <div class="container">
            <a class="main-color navbar-brand d-flex align-items-center" href="home.php">
                <img class="logo mr-2" src="./images/logo.svg" />
                Dessfits</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <?php if (isset($_SESSION['user_id'])) { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="add_recipe.php">Add Recipe</a>
                        </li>
                        
                        
                        <li class="nav-item">
                            <a class="nav-link" href="suggestAPI.php">Sponsored Recipes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="recipes.php?<?php
                                                                    echo 'id=' . $_SESSION['user_id'];
                                                                    ?>">My Recipes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="CouponPG.php">My Coupon</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-flex " href="profile.php?<?php
                                                                    echo 'id=' . $_SESSION['user_id'];
                                                                    ?>"><?php echo $_SESSION['name'] ?>
                                <img class="circle object-fit-cover ml-2" <?php echo 'src="' . ($_SESSION['profile_pic']) . '"'; ?> width="25" height="25" alt="">
                            </a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
    <link rel="stylesheet" href="css/style.css">
</body>

</html>
