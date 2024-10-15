<?php
session_start();
require("functions.php");
$conn = DBConnect();
CheckLogin();

if(!array_key_exists('id', $_GET)) {
    die('Missing id...');
}


$sql = "SELECT shop_stock.sellerid, originalfilename, uniquefilename FROM shop_stock INNER JOIN ledger on ledger.itemID = shop_stock.id WHERE shop_stock.id = ?  and ledger.buyerid = " . $_SESSION["userID"];
$statement = mysqli_prepare($conn, $sql);
$id = $_GET['id'];
mysqli_stmt_bind_param($statement, 'i', $id);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);

if(mysqli_num_rows($result) >= 1) {
    $row = mysqli_fetch_assoc($result);
    $originalFilename = $row['originalfilename'];
    $uniqueFilename = $row['uniquefilename'];
    $sellerid = $row['sellerid'];

    $folder = __DIR__ . DIRECTORY_SEPARATOR . 'fileuploads' . DIRECTORY_SEPARATOR . $sellerid . DIRECTORY_SEPARATOR;
    $filePath = $folder . $uniqueFilename;

    if(!file_exists($filePath)) {
        die('Could not find file to download...');
    }

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