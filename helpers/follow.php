<?php
session_start();
require_once(__DIR__ . '/../includes/db.php');

$user_id = $_GET['id'];
$my_id = $_SESSION['user_id'];
$unfollow = isset($_GET['unfollow']) ? $_GET['unfollow'] : false;

// follow
if (!$unfollow) {
    $sql = "INSERT INTO followers (follower_id, followee_id) VALUES (?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $my_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script> alert('followed successfully.'); window.location.href='../profile.php?id=$user_id'; </script>";
    } else {
        echo "Error occurred while following the user.";
    }
}
// unfollow
else {
    $sql = "DELETE FROM followers WHERE follower_id = ? AND followee_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $my_id, PDO::PARAM_INT);
    $stmt->bindParam(2, $user_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "<script> alert('unfollowed successfully.'); window.location.href='../profile.php?id=$user_id'; </script>";
    } else {
        echo "Error occurred while unfollowing the user.";
    }
}
