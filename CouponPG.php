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
$coupons = $_SESSION['coupons'];
$sql = "SELECT * FROM coupons WHERE user_id = :user_id";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$my_coupons = $stmt->fetchAll();
$sql = "SELECT COUNT(DISTINCT id) FROM recipes WHERE user_id = :user_id";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$recipes_amount = $stmt->fetchColumn();

// Get all recipes from the database
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

    <div class="container mt-3">
        <div <?php
                if (isset($_SESSION['user_id'])) { ?> class="row">
            <div>
                <h2>My Coupon : </h2>

                
                </form>

                <?php if ($coupons && count($coupons) > 0) { ?>
                    <div class="row mt-3">
                        <?php foreach ($coupons as $coupon) { ?>
                            <div class="col-md-4 ">
                                <div class="card" style="border-radius: 15px;">
                                    <div class="bg-image hover-overlay ripple ripple-surface ripple-surface-light">
                                        <img src="./images/<?php echo $coupon['image'] ?>" style="border-top-left-radius: 15px; border-top-right-radius: 15px;" class="img-fluid" alt="image" />

                                    </div>
                                    <div class="card-body pb-0">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <p><a class="main-name main-color">
                                                        <?php echo $coupon['name'] ?>
                                                    </a></p>
                                            </div>

                                        </div>
                                    </div>


                                    <hr class="my-0" />
                                    <div class="card-body">

                                        <?php // get coupon from my_coupons with the same name
                                        $my_coupon = null;
                                        foreach ($my_coupons as $cop) {
                                            if ($cop['name'] == $coupon['name']) {
                                                $my_coupon = $cop;
                                            }
                                        }
                                        ?>
                                        <?php if ($my_coupon) {  ?>
                                            <p><?php echo $my_coupon['code'] ?></p>
                                        <?php } else { ?>

                                            <div class="d-flex justify-content-between">
                                                <div>
                                                    <p class="mb-0 text-muted">Add <?php echo $coupon['recipes_amount'] - $recipes_amount ?> Recipes to unlock </p>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>

                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } ?>


                    </div>

            </div>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
            <script src="js/script.js"></script>
</body>

</html>