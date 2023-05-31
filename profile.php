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
}
if ($user_id == $_SESSION['user_id']) {
    $mine = true;
}
$my_id = $_SESSION['user_id'];

// Build the SQL first query to retrieve the matching recipes
$sql = "SELECT u.*, 
        COUNT(DISTINCT r.id) AS recipe_count, 
        COUNT(DISTINCT f.followee_id) AS followers,
        CASE WHEN EXISTS (SELECT 1 FROM followers WHERE follower_id = $my_id AND followee_id = u.id) THEN 1 ELSE 0 END AS i_follow
        FROM users AS u
        LEFT JOIN recipes AS r ON u.id = r.user_id
        LEFT JOIN followers AS f ON u.id = f.followee_id
        WHERE u.id = $user_id
        GROUP BY u.id";
$sql1 = "SELECT count(*) FROM `likes` WHERE `user_id`=$user_id";

// Prepare the SQL first query
$stmt = $conn->prepare($sql);
$stmt->execute();
$stmt1 = $conn->prepare($sql1);
$stmt1->execute();

// Execute the SQL first query and retrieve the matching recipes
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$countlike = $stmt1->fetchColumn();




// Close the database connection
$conn = null;

// Output the matching recipes and number of likes as a JSON object
$user = $result;








?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $user['name'] ?>'s Profile</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

</head>

<body>
    <?php include('includes/header.php'); ?>

    <section>
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-8 col-xl-8">
                    <div class="card" style="border-radius: 15px;">
                        <div class="bg-image hover-overlay ripple ripple-surface ripple-surface-light">
                            <img src="<?php echo $user['profile_pic'] ?>" style="border-top-left-radius: 15px; border-top-right-radius: 15px;" class="img-fluid" alt="image" />

                        </div>
                        <div class="card-body pb-0">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <p><span href="#!" class="main-name main-color ">
                                            <?php echo $user['name'] ?>
                                        </span></p>
                                </div>
                                <div>
                                    <div class="d-flex flex-row justify-content-end mt-1 mb-4 text-dark">
                                        <p class="small text-muted"> <?php echo $user['followers'] = isset($user['followers']) ? $user['followers'] : '0';?> Followers</p>
                                        <i class="pl-2 fas fa-users"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr class="my-0" />
                        <div class="card-body pb-2">
                            <div class="d-flex justify-content-between">
                                <p>
                                    <span class="text-dark">Uploaded Recipes</span>
                                </p>
                            </div>
                            <a href="recipes.php?id=<?php echo $user_id ?>" class="break-spaces"><?php echo isset($user['recipe_count']) ? $user['recipe_count'] : '0';
 ?></a>
                        </div>
                        <hr class="my-0" />
                        <div class="card-body pb-2">
                            <div class="d-flex justify-content-between">
                                <p>
                                    <span class="text-dark">Points</span>
                                </p>
                            </div>
                            <p class="break-spaces text-success"><?php echo isset($user['points']) ? $user['points'] : '0'; ?></p>
                        </div>
                        <hr class="my-0" />
                        <div class="card-body pb-2">
                            <div class="d-flex justify-content-between">
                                <p>
                                    <span class="text-dark">Liked Recipes</span>
                                </p>
                            </div>
                            <a class="break-spaces" href="likesrecipes.php?id=<?php echo $user_id ?>"><?php echo $countlike; ?></a>
                        </div>
                        <div>
                            <?php if (!$mine) { ?>
                                <hr class="my-0" />
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center pb-2 mb-1">
                                        <!-- follow button if not mine -->
                                        <?php if (!$user['i_follow']) { ?>
                                            <a href="./helpers/follow.php?id=<?php echo $user_id ?>" class="btn btn-success w-100">Follow</a>
                                        <?php } ?>
                                        <?php if ($user['i_follow']) { ?>
                                            <a href="./helpers/follow.php?id=<?php echo $user_id ?>&unfollow=true" class="btn btn-danger w-100">Unfollow</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
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