<?php

// Check if a user is logged in
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

/**
 * Retrieves all recipes from the database.
 *
 * @return array An array of recipe objects.
 */
function get_all_recipes()
{
    global $conn;

    $recipes = array();

    $sql = "SELECT * FROM recipes";
    $result = $conn->query($sql);

    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $recipes[] = array(
                'id' => $row['id'],
                'name' => $row['name'],
                'instructions' => $row['instructions']
            );
        }
    }

    return $recipes;
}

function is_recipe_owner($recipe_id)
{
    global $conn;

    $sql = "SELECT user_id FROM recipes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $recipe_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['user_id'] == $_SESSION['user_id'];
}

?>