<?php
// start session
// Include the database connection file
session_start();
require_once('includes/db.php');
// Get the user's ingredient input from the POST data
$recipe_id;
$mine = false;
$params = $_GET;
if (!empty($params)) {
    $recipe_id = $params['id'];
}
$current_user_id = $_SESSION['user_id'];

// Build the SQL query to retrieve the matching recipes
$sql = "SELECT r.*, COUNT(likes.id) AS likes_count, users.name AS user_name, users.id AS user_id, users.profile_pic AS user_profile_pic,
        (CASE WHEN EXISTS (SELECT 1 FROM likes WHERE recipe_id = r.id AND user_id = $current_user_id) THEN 1 ELSE 0 END) AS like_by_me
        FROM recipes AS r
        LEFT JOIN likes ON r.id = likes.recipe_id
        LEFT JOIN users ON r.user_id = users.id
        WHERE r.id = $recipe_id
        GROUP BY r.id";

// Prepare the SQL query
$stmt = $conn->prepare($sql);
$stmt->execute();


// Execute the SQL query and retrieve the matching recipes
$result = $stmt->fetch(PDO::FETCH_ASSOC);

// Close the database connection
$conn = null;
// Output the matching recipes as a JSON object
$recipe = $result;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $recipe['name'] ?></title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/recipe.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>

<body>
    <?php include('includes/header.php'); ?>

    <section style="background-color: #eee;">
        <h1 class="text-center"><?php echo $recipe['name'] ?></h1>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-8 col-xl-8">
                    <div class="card" style="border-radius: 15px;">
                        <div class="bg-image hover-overlay ripple ripple-surface ripple-surface-light">
                            <img src="<?php echo $recipe['image'] ?>" style="border-top-left-radius: 15px; border-top-right-radius: 15px;" class="img-fluid" alt="image" />

                        </div>
                        <div class="card-body pb-0">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p><span href="#!" class="main-name main-color">
                                            <?php echo $recipe['name'] ?>
                                        </span></p>
                                </div>
                                <div>
                                    <div class="d-flex flex-row justify-content-end mt-1 mb-4 text-danger">
                                        <?php if ($recipe['like_by_me']) : ?>
                                            <a href="helpers/like.php?id=<?php echo $recipe['id'] ?>&unlike=true" class="text-danger">
                                                <i class="fas fa-thumbs-up text-success"></i>
                                            </a>
                                        <?php else : ?>
                                            <a href="helpers/like.php?id=<?php echo $recipe['id'] ?>" class="text-success">
                                                <i class="far fa-thumbs-up text-success"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <p class="small text-muted">Likes <?php echo isset($recipe['likes_count']) && $recipe['likes_count'] !== null ? $recipe['likes_count'] : '0'; ?></p>
                                </div>
                            </div>
                        </div>
                        <hr class="my-0" />
                        <div class="card-body pb-0">
                            <div class="d-flex justify-content-between">
                                <p>
                                    <spam class="text-dark">Ingredients</spam>
                                </p>
                            </div>
                            <ul class="pl-3">
                                <?php foreach (explode("\n", trim($recipe['ingredients'])) as $ingredient) : ?>
                                    <li><?php echo $ingredient ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <hr class="my-0" />
                        <div class="card-body pb-0">
                            <div class="d-flex justify-content-between">
                                <p><span class="text-dark">Instructions</span></p>
                            </div>
                            <p class="break-spaces"><?php echo $recipe['instructions'] ?></p>
                            <?php if ($recipe['video']) : ?>
                                <div class="embed-responsive embed-responsive-16by9">
                                    <iframe class="embed-responsive-item" src="<?php echo str_replace("watch?v=", "embed/", $recipe['video']); ?>" allowfullscreen></iframe>
                                </div>
                            <?php endif; ?>
                        </div>
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
            </div>
        </div>
    </section>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>

</html>