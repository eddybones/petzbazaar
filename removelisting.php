<?php
session_start();
require('functions.php');
$conn = DBConnect();
CheckLogin();


$sql = "SELECT uniqueimage, uniquefilename, stack FROM shop_stock WHERE id = ? and sellerid= ". $_SESSION["userID"];
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $_GET["id"]); 
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    if ($row["stack"] != 1) {
        $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'fileuploads' . DIRECTORY_SEPARATOR . $_SESSION["userID"] . DIRECTORY_SEPARATOR;
        unlink($targetFolder . $row["uniquefilename"] );
        $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'imageuploads' . DIRECTORY_SEPARATOR . $_SESSION["userID"] . DIRECTORY_SEPARATOR;
        unlink($targetFolder . $row["uniqueimage"] );
        $sql="DELETE from shop_stock WHERE id = ? and sellerid= ". $_SESSION["userID"];
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($statement, "i", $_GET["id"]); 
        mysqli_stmt_execute($statement);
        $_SESSION["message"] = "Listing Removed.";
        header('location: /petzbazaar/shop.php');
        die();
    }
else {
        $sql = "UPDATE shop_stock SET purchased = 1 WHERE id = ? and sellerid= " . $_SESSION["userID"];
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($statement, "i", $_GET["id"]); 
        mysqli_stmt_execute($statement);
        $_SESSION["message"] = "Listing Removed.";
        header('location: /petzbazaar/shop.php');
        die();
        }    
}

