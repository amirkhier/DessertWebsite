<?php

$host = 'localhost';
$dbname = 'amirkh_recipe_app';
$username = 'amirkh_recipeapp';
$password = 'password';


try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);




    try {
        $conn->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            points INT(11) UNSIGNED NOT NULL DEFAULT 0,
            username VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            name VARCHAR(255) NOT NULL,
            age INT(11) UNSIGNED NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            profile_pic MEDIUMTEXT
            
        );
    ");

        $conn->exec("
        CREATE TABLE IF NOT EXISTS followers  (
            follower_id INT(11) UNSIGNED NOT NULL,
            followee_id INT(11) UNSIGNED NOT NULL,
            FOREIGN KEY (followee_id) REFERENCES users(id),
            FOREIGN KEY (follower_id) REFERENCES users(id)
        );
    ");

        $conn->exec("
        CREATE TABLE IF NOT EXISTS likes  (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            recipe_id INT(11) UNSIGNED NOT NULL,
            user_id INT(11) UNSIGNED NOT NULL,
            FOREIGN KEY (recipe_id) REFERENCES recipes(id),
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
    ");

        $conn->exec("
        CREATE TABLE IF NOT EXISTS coupons (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255),
            user_id INT(11) UNSIGNED NOT NULL,
            code VARCHAR(255),
            recipes_amount INT(11) UNSIGNED NOT NULL,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
    ");




        $conn->exec("
        CREATE TABLE IF NOT EXISTS recipes (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL,
            description TEXT NOT NULL,
            instructions TEXT NOT NULL,
            image MEDIUMTEXT,
            video VARCHAR(255),
            ingredients VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id)
        );
    ");

        $conn->exec("
        CREATE TABLE IF NOT EXISTS ingredients (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL UNIQUE
        );
    ");
    } catch (PDOException $e) {
        echo "Error creating tables: " . $e->getMessage();
        die();
    }
} catch (PDOException $e) {
    // echo "Connection failed: " . $e->getMessage();
    die();
}

