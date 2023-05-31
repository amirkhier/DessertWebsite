<?php
// start session
// Include the database connection file
session_start();
require_once('../includes/db.php');
$api_key = "503b3e8bf94941519b3b7f1f72e9ac6b";
// Get the user's ingredient input from the POST data
$id = $_GET['id'];
$url = 'https://api.spoonacular.com/recipes/' . $id . '/information?includeNutrition=false' . '&apiKey=' . $api_key;
$response = file_get_contents($url);
$data = array();
if ($response === false) {
    // Handle error
    echo 'Error retrieving data from the API. maybe max limit reached';
} else {

    // Process the API response
    $data = json_decode($response, true);
    // open in new tab data.sourceUrl

    echo "<script>window.open('" . $data['sourceUrl'] . "');</script>";
}
