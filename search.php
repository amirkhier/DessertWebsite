<?php
// start session
// Include the database connection file
session_start();
require_once('includes/db.php');
// Get the user's ingredient input from the POST data
$ingredients_string = $_POST['ingredients'];
$ingredients = explode(',', $ingredients_string);

$conditions = array();
$params = array();
foreach ($ingredients as $i => $ingredient) {
    $params[":ingredient$i"] = strtolower(trim($ingredient));
    $conditions[] = "LOWER(r.ingredients) LIKE CONCAT('%', :ingredient$i, '%')";
}

$conditions_query = implode(' AND ', $conditions);
// Build the SQL query to retrieve the matching recipes
$sql = "SELECT r.*, 
        (SELECT COUNT(likes.id) FROM likes WHERE r.id = likes.recipe_id) AS likes_count,
        users.name AS user_name, users.id AS user_id, users.profile_pic AS user_profile_pic
        FROM recipes AS r
        LEFT JOIN users ON r.user_id = users.id
        WHERE $conditions_query
        ORDER BY likes_count DESC";
// Prepare the SQL query
$stmt = $conn->prepare($sql);
foreach ($params as $param => $value) {
    $stmt->bindValue($param, $value);
}
// $stmt->debugDumpParams();

$stmt->execute($params);


// Execute the SQL query and retrieve the matching recipes
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Close the database connection
$conn = null;
// Output the matching recipes as a JSON object
$recipes = $result


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe App - Search Results</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include_once('includes/header.php'); ?>
    <div class="container">
        <h1 class="text-center my-5">
            Search results
        </h1>
        
        <?php if ($recipes && count($recipes) > 0) { ?>
            <div class="row">
                <?php foreach ($recipes as $recipe) { ?>
                    <div class="col-md-4 ">
                        <div class="card" style="border-radius: 15px;">
                            <div class="bg-image hover-overlay ripple ripple-surface ripple-surface-light">
                                <img src="<?php echo $recipe['image'] ?>" style="border-top-left-radius: 15px; border-top-right-radius: 15px;" class="img-fluid" alt="image" />

                            </div>
                            <div class="card-body pb-0">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <p><a href="recipe.php?id=<?php echo $recipe['id'] ?>" class="main-name main-color">
                                                <?php echo $recipe['name'] ?>
                                            </a></p>
                                    </div>
                                    <div>
                                        <div class="d-flex flex-row justify-content-end mt-1 mb-4 text-danger">
                                            <i class="fas fa-thumbs-up text-dark"></i>
                                        </div>
                                        <p class="small text-muted">Likes <?php echo isset($recipe['likes_count']) ? $recipe['likes_count'] : '0'; ?></p>
                                    </div>
                                </div>
                            </div>
                            <hr class="my-0" />


                            <hr class="my-0" />
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center pb-2 mb-1">
                                    <a href="profile.php?id=<?php echo $recipe['user_id'] ?>" class="text-dark fw-bold">
                                        By:
                                        <img width="40" height="40" src="<?php echo $recipe['user_profile_pic'] ?>" class="circle object-fit-cover" alt="Profile pic" />
                                        <?php echo $recipe['user_name'] ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>
        <?php } else { ?>
            <p>No matching recipes found.Please <a href="home.php"> try again</a>, Or Checkout to try <a href="suggestedAPI.php">Sponsored Recipes</a></p>
        <?php } ?>
        <script src="js/jquery.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
</body>

</html>