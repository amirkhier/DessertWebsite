
<?php
session_start();
// Include the necessary files
require_once('includes/db.php');
require_once('includes/functions.php');

// Check if the user is logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}
$user_id = $_SESSION['user_id'];
$coupons = $_SESSION['coupons'];
$sql = "SELECT COUNT(*) AS recipe_count, GROUP_CONCAT(name) AS recipe_names FROM recipes WHERE user_id = $user_id";
$sql1 ="SELECT GROUP_CONCAT(name) AS recipe_names FROM `recipes`";
$stmt1 = $conn->prepare($sql1);
$stmt = $conn->prepare($sql);
$stmt->execute();
$stmt1->execute();
$all = $stmt->fetchAll(PDO::FETCH_ASSOC);
$all1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);
$recipe_count = $all[0]['recipe_count'];
$recipe_names = $all1[0]['recipe_names'];
// Define variables and set to empty values
$name = $description = '';
$name_err = $description_err = $ingredients_err = $image_err = '';
$description = '';
$video = '';
$instructions = '';
$image = '';
$ingredients = '';
$recipe = array();
$allowed_types = ['image/jpg', 'image/jpeg', 'image/png'];
$base64_encoded = '';
// Process form data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0 && !empty($_FILES['image'])) {
        // Check if the file size is less than or equal to 1MB
        if ($_FILES['image']['size'] <= 1048576) {

            // Check if the file type is allowed
            $file_ext = strtolower($_FILES['image']['type']);
            if (in_array($file_ext, $allowed_types)) {
                $file_contents = file_get_contents($_FILES['image']['tmp_name']);
                $image_err = '';
                $base64_encoded = base64_encode($file_contents);
                // add data:image/png;base64, to the beginning of the base64 string
                $base64_encoded = 'data:' . $file_ext . ';base64,' . $base64_encoded;
            } else {
                $image_err = 'File type not allowed. Allowed types: ' . implode(', ', $allowed_types) . '.';
            }
        } else {
            $image_err = 'File size must be less than or equal to 1MB.';
        }
    } else {
        $image_err = 'Please add a profile picture.';
    }
    // Validate name
    if (empty(trim($_POST['name']))) {
        $name_err = 'Please enter a recipe name.';
    } else {
        $name = trim($_POST['name']);
    }

    // Validate description
    if (empty(trim($_POST['description']))) {
        $description_err = 'Please enter a recipe description.';
    } else {
        $description = trim($_POST['description']);
    }

    // Validate instructions
    if (empty(trim($_POST['instructions'] ?: ''))
) {
        $instructions_err = 'Please enter recipe instructions.';
    } else {
        $instructions = trim($_POST['instructions']);
    }

    $video = trim($_POST['video'] ?: '');


    // Validate image
    if (!empty($_FILES['image']['name'])) {
        $max_size = 1 * 1024 * 1024; // 1 MB in bytes
        $file_size = $_FILES['image']['size'];
        $check = getimagesize($_FILES['image']['tmp_name']);
        if (!$check) {
            $image_err = 'File is not an image.';
        } else if ($file_size > $max_size) {
            $image_err = 'File size cannot exceed 1MB.';
        } else {
            // Upload the image
            $target_dir = 'uploads/';
            $target_file = $target_dir . basename($_FILES['image']['name']);
            $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $image_name = $name . '.' . $image_type;
            $target_file = $target_dir . $image_name;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image = $image_name;
            } else {
                $image_err = 'Error uploading image.';
            }
        }
    }

    // Validate ingredients
    $ingredients = '';
    foreach ($_POST['ingredient'] as $ingredient) {
        if (!empty(trim($ingredient))) {
            $ingredients .= trim($ingredient) . "\n";
        }
    }
    if (empty($ingredients)) {
        $ingredients_err = 'Please enter recipe ingredients.';
    }
    $name_exists = false;
    // search for it in recipe_names which is a string with comma seperated
    if (strpos($recipe_names, $name) !== false) {
        $name_exists = true;
    }
    if ($name_exists) {
        header('Location: add_recipe1.php?error=Recipe name already exists,please try to add other recipe name');
    }
    // Check for input errors before updating the recipe
    if (empty($name_err) && empty($description_err) && empty($instructions_err) && empty($image_err) && empty($ingredients_err) && !$name_exists) {
        $param_name = $name;
        $param_description = $description;
        $param_instructions = $instructions;
        $param_image = $image;
        $param_ingredients = $ingredients;
        $param_video = $video;
        // Insert a new recipe into the database
        $sql = 'INSERT INTO recipes (name, description, instructions, image, ingredients,user_id,video) VALUES (?, ?, ?, ?, ?, ?, ?)';
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(1, $param_name, PDO::PARAM_STR);
        $stmt->bindParam(2, $param_description, PDO::PARAM_STR);
        $stmt->bindParam(3, $param_instructions, PDO::PARAM_STR);
        $stmt->bindParam(4, $base64_encoded, PDO::PARAM_STR);
        $stmt->bindParam(5, $param_ingredients, PDO::PARAM_STR);
        $stmt->bindParam(6, $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(7, $param_video, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $recipe_count++;
            $sql = 'UPDATE users SET points = points + 5 WHERE id = ?';
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(1, $_SESSION['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            // find coupon with recipes_amount from $coupon array by recipe_count
            $coupon = array_filter($coupons, function ($coupon) use ($recipe_count) {
                return $coupon['recipes_amount'] == $recipe_count;
            });
            // if coupon exists, add it to the user 
            if (!empty($coupon)) {
                $coupon = array_values($coupon)[0];
                $sql = 'INSERT INTO coupons (user_id, name, code, recipes_amount) VALUES (?, ?, ?, ?)';
                $stmt = $conn->prepare($sql);
                $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 6);
                $stmt->bindParam(1, $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->bindParam(2, $coupon['name'], PDO::PARAM_STR);
                $stmt->bindParam(3, $code, PDO::PARAM_STR);
                $stmt->bindParam(4, $coupon['recipes_amount'], PDO::PARAM_INT);
                $stmt->execute();
                $message =  'You have earned a coupon for ' . $coupon['recipes_amount'] . ' recipes!, check your coupons in the home page.';
                echo "<script type='text/javascript'>alert('$message'); window.location.href='home.php'</script>";
            }
        } else {
            echo 'Something went wrong. Please try again later.';
        }

        unset($stmt);
    }

    unset($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Add Recipe - Recipe App</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include('includes/header.php'); ?>
    <div class="container mt-3">
        <div class="row justify-content-center pt-5">
            <div class="col-md-6 p-4 shadow">
                <h2>Add Recipe</h2>
                <form enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                        <span class="invalid-feedback">
                            <?php echo $name_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                        <span class="invalid-feedback">
                            <?php echo $description_err; ?>
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Instructions</label>
                        <textarea name="instructions" class="form-control <?php echo (!empty($instructions_err)) ? 'is-invalid' : ''; ?>"><?php echo $instructions; ?></textarea>
                        <span class="invalid-feedback">
                            <?php echo $instructions_err; ?>
                        </span>
                    </div>
                    <div class="form-group">
                        <label>Video</label>
                        <textarea placeholder="Youtube link" name="video" class="form-control"></textarea>

                    </div>
                    <div class="form-group">
                        <label>Ingredients</label>

                        <?php
                        // Retrieve the existing ingredients from the form or database

                        // Make sure there is always at least one ingredient field
                        $ingredients = !empty($_POST['ingredient']) ? $_POST['ingredient'] : [];

                        if (count($ingredients) === 0) {
                            $ingredients[] = '';
                        }

                        // Generate the input fields for each ingredient
                        foreach ($ingredients as $key => $value) {
                        ?>
                            <div class="input-group mb-2">
                                <input type="text" name="ingredient[]" class="form-control" value="<?php echo $value; ?>">
                                <div class="input-group-append">
                                    <?php

                                    if (
                                        $key === count($ingredients) - 1
                                    ) { ?>
                                        <button class="btn btn-secondary add-ingredient" type="button">Add</button>
                                    <?php } else { ?>
                                        <button class="btn btn-secondary remove-ingredient" type="button">Remove</button>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>

                        <span class="invalid-feedback d-block">
                            <?php echo $ingredients_err; ?>
                        </span>
                    </div>
                    <!-- <div class="form-group">
                        <label>Ingredients</label>


                        <div class="input-group mb-2">
                            <input type="text" name="ingredient[]" class="form-control" >
                            <div class="input-group-append">

                                <button class="btn btn-secondary add-ingredient" type="button">Add</button>
                            </div>
                        </div>

                        <span class="invalid-feedback d-block">
                            <?php echo $ingredients_err; ?>
                        </span>
                    </div> -->
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control-file">
                        <span class="invalid-feedback d-block">
                            <?php echo $image_err; ?>
                        </span>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Add Recipe" class="btn btn-primary">
                        <input type="reset" onclick="location.href='add_recipe.php';" href="add_recipe.php" value="Reset" class="btn btn-secondary ml-2">
                    </div>

                </form>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        $('form').on('click', '.add-ingredient', function() {
            var inputGroup = $(this).closest('.input-group');
            const newInput = inputGroup.clone();
            inputGroup.append(
                `<button class="btn btn-secondary remove-ingredient" type="button">Remove</button>`
            )
            inputGroup.find('.add-ingredient').remove();
            newInput.find('input').val('');
            inputGroup.after(newInput);
        });
        $('form').on('click', '.remove-ingredient', function() {
            $(this).closest('.input-group').remove();
        });

        //listen to changes in the form, and update the preview
        // Check if the URL contains the 'error' query parameter
        var urlParams = new URLSearchParams(window.location.search);
        var error = urlParams.get('error');
        console .log(urlParams  )
        // Display an alert if the 'error' query parameter is present
        if (error) {

            alert(error);
        }
    </script>

</body>

</html>