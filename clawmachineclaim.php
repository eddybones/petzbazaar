<?php

session_start();
require("functions.php");

$conn = DBConnect();
CheckLogin();

if(!array_key_exists("clawclaim", $_SESSION)) {
    die('Missing id...');
}

/*
 * Check if item is still there or not - refund coin if not
 */
$sql = "SELECT * FROM clawmachine WHERE claimed = 0 and id = " . $_SESSION["clawclaim"];
$result = mysqli_query($conn, $sql);
if(mysqli_num_rows($result) == 0) {
    $_SESSION['error'] = 'Oops, this item magically disappeared! Sorry! We\'ve refunded your coinz.';

    $sql = "UPDATE users Set Coinz = coinz+1 Where id = " . $_SESSION["userID"];
    $result = mysqli_query($conn, $sql);

    statClawRefund();

    header('location: /petzbazaar/clawmachine.php');
    die();
}

/*
 * Update item as claimed and stream the item to the user
 */
$sql = "UPDATE clawmachine SET claimed = 1, claimdate = utc_timestamp(), claimid = " . $_SESSION["userID"] . " WHERE id = " . $_SESSION["clawclaim"]; 
$result = mysqli_query($conn, $sql);
$statement = mysqli_prepare($conn, "SELECT originalfilename, uniquefilename FROM clawmachine WHERE id = ?");
$id = $_SESSION["clawclaim"];
mysqli_stmt_bind_param($statement, 'i', $id);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);

if(mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $originalFilename = $row['originalfilename'];
    $uniqueFilename = $row['uniquefilename'];

    $folder = __DIR__ . DIRECTORY_SEPARATOR . 'clawmachine' . DIRECTORY_SEPARATOR;
    $filePath = $folder . $uniqueFilename;

    if(!file_exists($filePath)) {
        die('Could not find file to download...');
    }
    unset($_SESSION["clawclaim"]);

    // Stream the file to the user
    set_time_limit(0);
    header("Content-Disposition: attachment; filename=\"{$originalFilename}\"");
    ob_start();
    $chunkSize = 1024 * 8;
    $fileResource = @fopen($filePath, 'r');
    while(!feof($fileResource)) {
        print(@fread($fileResource, $chunkSize));
        ob_flush();
        flush();
    }
} else {
    die('Could not find purchased item...');
}