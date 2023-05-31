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

?>

<!DOCTYPE html>
<html>

<head>
    <title>Suggested Search Engine</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/home.css">
    <link rel="stylesheet" href="css/suggested.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>

    <?php include('includes/header.php'); ?>
    <br>
    
    <div class="container search-forms">
   <br>
   <h4>Sponsored Suggested Dessert Recipes</h4><br>

    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2" type="text" placeholder="Search by ingredients" name="ingredients"
        id="ingredients">
      <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
    </form>
    <br>
    <br>

    <div class="row">
      <div class="col-md-4">
        <h4>Filter By:</h4>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <input type="number" class="form-control" id="calories" name="calories" min="0" placeholder="Enter max calories">
        </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <input type="number" class="form-control" id="sugar" name="sugar" min="0" placeholder="Enter Max Sugar">
        </div>
      </div>
    </div>
    <br>
    <h2 style="display:none;" id="etitle">Results:</h2>
  </div>
  <br>
  

  <div class="container"><div class="row recipe-card"></div></div>

   
          

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
            <script src="js/api.js"></script>
           
</body>

</html>