<?php
// start session
// Include the database connection file
session_start();
require_once('includes/db.php');
// Get the user's ingredient input from the POST data
$user_id;
$mine = false;
$params = $_GET;
if (!empty($params)) {
    $user_id = $params['id'];
    if ($user_id == $_SESSION['user_id']) {
        $mine = true;
    }
}

// Extract recipe IDs from the likes table
$sql1 = "SELECT recipe_id FROM likes WHERE user_id=$user_id";
$stmt1 = $conn->prepare($sql1);
$stmt1->execute();
$likes_result = $stmt1->fetchAll(PDO::FETCH_COLUMN);

// Format the liked recipe IDs as a comma-separated string
$likedRecipeIds = implode(',', $likes_result);

// Build the SQL query to retrieve the matching recipes
$sql = "SELECT r.*, COUNT(likes.id) AS likes_count, users.name AS user_name, users.id AS user_id, users.profile_pic AS user_profile_pic
        FROM recipes AS r
        LEFT JOIN likes ON r.id = likes.recipe_id
        LEFT JOIN users ON r.user_id = users.id
        WHERE r.id IN ($likedRecipeIds)
        GROUP BY r.id";

// Prepare the SQL query
$stmt = $conn->prepare($sql);
$stmt->execute();

// Execute the SQL query and retrieve the matching recipes
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Close the database connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php if ($mine) { ?>
            My Liked Recipes
        <?php } else { ?>
            <?php echo $recipes[0]['user_name'] ?> Liked Recipes
        <?php } ?>
    </title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/recipes.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <?php include_once('includes/header.php'); ?>
    <div class="container">
        <h1 class="text-center my-5">
            <?php if ($mine) {
            ?>
                My Liked Recipes
            <?php } else { ?>
                <?php echo $recipes[0]['user_name']
                ?> Liked Recipes
            <?php } ?>
        </h1>

        <?php if ($result && count($result) > 0) { ?>
            <div class="row">
                <?php foreach ($result as $recipe) { ?>
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
                                        <p class="small text-muted">Likes <?php echo isset($recipe['likes_count']) && $recipe['likes_count'] !== null ? $recipe['likes_count'] : '0'; ?></p>
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
            <p>No matching recipes found. Please try again.</p>
        <?php } ?>


    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>
