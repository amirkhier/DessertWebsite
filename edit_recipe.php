<?php
// Include the necessary files
require_once('includes/db.php');
require_once('includes/functions.php');

// Check if the user is not logged in
if (!is_logged_in()) {
    header('Location: login.php');
    exit();
}

// Check if the recipe ID is specified in the URL
if (!isset($_GET['id'])) {
    header('Location: home.php');
    exit();
}

// Check if the recipe belongs to the logged-in user
if (!is_recipe_owner($_GET['id'])) {
    header('Location: home.php');
    exit();
}

// Define variables and set to empty values
$name = $description = $instructions = $image = '';
$name_err = $description_err = $instructions_err = $image_err = '';

// Process form data when the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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
    if (empty(trim($_POST['instructions']))) {
        $instructions_err = 'Please enter recipe instructions.';
    } else {
        $instructions = trim($_POST['instructions']);
    }

    // Validate image
    if (!empty($_FILES['image']['name'])) {
        // Check if the file is an image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if (!$check) {
            $image_err = 'File is not an image.';
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

    // Check for input errors before updating the recipe
    if (empty($name_err) && empty($description_err) && empty($instructions_err) && empty($image_err)) {

        // Update the recipe in the database
        $sql = 'UPDATE recipes SET name = ?, description = ?, instructions = ?, image = ?, ingredients = ? WHERE id = ?';
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(1, $param_name, PDO::PARAM_STR);
        $stmt->bindParam(2, $param_description, PDO::PARAM_STR);
        $stmt->bindParam(3, $param_instructions, PDO::PARAM_STR);
        $stmt->bindParam(4, $param_image, PDO::PARAM_STR);
        $stmt->bindParam(5, $param_ingredients, PDO::PARAM_STR);
        $stmt->bindParam(6, $param_id, PDO::PARAM_INT);

        $param_name = $name;
        $param_description = $description;
        $param_instructions = $instructions;
        $param_image = $image;
        $param_ingredients = $ingredients;
        $param_id = $_GET['id'];

        if ($stmt->execute()) {
            // Recipe updated successfully, redirect to the recipe page
            header('Location: recipe.php?id=' . $_GET['id']);
            exit();
        } else {
            echo 'Something went wrong. Please try again later.';
        }

        // Close statement
        unset($stmt);
    }

    // Close connection
    unset($conn);
} else {
    // Retrieve the existing recipe data
    $sql = 'SELECT name, description, instructions, image, ingredients FROM recipes WHERE id = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $param_id, PDO::PARAM_INT);
    $param_id = $_GET['id'];
    $stmt->execute();
    $recipe = $stmt->fetch(PDO::FETCH_ASSOC);

    // Set the existing recipe data to the variables
    $name = $recipe['name'];
    $description = $recipe['description'];
    $instructions = $recipe['instructions'];
    $image = $recipe['image'];
    $ingredients = explode("\n", $recipe['ingredients']);

    // Close statement
    unset($stmt);

    // Close connection
    unset($conn);
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>Edit Recipe - Recipe App</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include('includes/header.php'); ?>

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-6">
                <h2>Edit Recipe</h2>
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']) . '?id=' . $_GET['id']; ?>"
                    method="POST" enctype="multipart/form-data">

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name"
                            class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>"
                            value="<?php echo $name; ?>">
                        <span class="invalid-feedback">
                            <?php echo $name_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description"
                            class="form-control <?php echo (!empty($description_err)) ? 'is-invalid' : ''; ?>"><?php echo $description; ?></textarea>
                        <span class="invalid-feedback">
                            <?php echo $description_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Instructions</label>
                        <textarea name="instructions"
                            class="form-control <?php echo (!empty($instructions_err)) ? 'is-invalid' : ''; ?>"><?php echo $instructions; ?></textarea>
                        <span class="invalid-feedback">
                            <?php echo $instructions_err; ?>
                        </span>
                    </div>

                    <div class="form-group">
                        <label>Image</label>
                        <div class="custom-file">
                            <input type="file" name="image"
                                class="custom-file-input <?php echo (!empty($image_err)) ? 'is-invalid' : ''; ?>">
                            <label class="custom-file-label">
                                <?php echo (!empty($image)) ? $image : 'Choose file'; ?>
                            </label>
                            <span class="invalid-feedback">
                                <?php echo $image_err; ?>
                            </span>
                        </div>
                    </div>

                    <div class="form-group">
                        <input type="submit" value="Save Changes" class="btn btn-primary">
                        <a href="recipe.php?id=<?php echo $_GET['id']; ?>" class="btn btn-secondary ml-2">Cancel</a>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.6.0.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>