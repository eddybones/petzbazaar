<?php
session_start();
require('functions.php');
$conn = DBConnect();
CheckLogin();


$sql = "SELECT uniqueimage, uniquefilename FROM auctions WHERE id = ? and userID= ". $_SESSION["userID"];
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $_GET["id"]); 
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);

    $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'fileuploads' . DIRECTORY_SEPARATOR . $_SESSION["userID"] . DIRECTORY_SEPARATOR . 'auctions' . DIRECTORY_SEPARATOR;
    unlink($targetFolder . $row["uniquefilename"] );
    $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'imageuploads' . DIRECTORY_SEPARATOR . $_SESSION["userID"] . DIRECTORY_SEPARATOR . 'auctions' . DIRECTORY_SEPARATOR;
    unlink($targetFolder . $row["uniqueimage"] );
    $sql="DELETE from auctions WHERE id = ? and userID= ". $_SESSION["userID"];
    $statement = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($statement, "i", $_GET["id"]); 
    mysqli_stmt_execute($statement);
    $_SESSION["message"] = "Auction Removed.";
    header('location: /petzbazaar/auctioncreate.php');
    die();
    
}

