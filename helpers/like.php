<?php
session_start();
require_once(__DIR__ . '/../includes/db.php');

$recipe_id = $_GET['id'];
$my_id = $_SESSION['user_id'];
$unlike = isset($_GET['unlike']) ? $_GET['unlike'] : false;


// like/unlike
if (!$unlike) {
    // Like the recipe
    $sql = "INSERT INTO likes (user_id, recipe_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $my_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $recipe_id, PDO::PARAM_INT);
} else {
    // Unlike the recipe
    $sql = "DELETE FROM likes WHERE user_id = ? AND recipe_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $my_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $recipe_id, PDO::PARAM_INT);
}

if ($stmt->execute()) {
    // Redirect back to the recipe page
    echo "<script>  window.location.href='../recipe.php?id=$recipe_id'; </script>";
} else {
    echo "Error occurred while liking/unliking the recipe.";
}

// Close the database connection
$conn = null;
?>