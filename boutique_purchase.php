<?php

ini_set('display_errors', '1');

session_start();
require('functions.php');
$conn = DBConnect();
CheckLogin();

$sql = "SELECT quartz FROM users WHERE id=". $_SESSION["userID"];
$userresult = mysqli_query($conn, $sql);
$sql = "SELECT  id, sellerid, buyerid, itemname, price, purchased, quantity FROM boutique_stock WHERE id=?";
$statement = mysqli_prepare($conn, $sql);
$id = $_POST["id"];
mysqli_stmt_bind_param($statement, "i", $id);
mysqli_stmt_execute($statement);
$stockresult = mysqli_stmt_get_result($statement);
if (mysqli_num_rows($userresult)==1 && mysqli_num_rows($stockresult)==1) {
    $user = mysqli_fetch_assoc($userresult);
    $stock = mysqli_fetch_assoc($stockresult);
    if ($user["quartz"] >= $stock["price"]) {
       //sets boutique stock item to purchased and sets purchase date
       $sql = "UPDATE boutique_stock Set quantity = quantity-1, buyerid=" . $_SESSION["userID"] . " WHERE id=?";
       $statement = mysqli_prepare($conn, $sql);
       mysqli_stmt_bind_param($statement, "i", $_POST["id"]);
       mysqli_stmt_execute($statement);
       if ($stock["quantity"] -1 ==0) {
           $sql = "UPDATE boutique_stock Set purchasedate = utc_date(), purchased =1, buyerid=" . $_SESSION["userID"] . " WHERE id=?";
           $statement = mysqli_prepare($conn, $sql);
           $id = $_POST["id"];
           mysqli_stmt_bind_param($statement, "i", $id);
           mysqli_stmt_execute($statement);
       }    

       //Buyer id/session user id needs quartz cost of listing removed
       $sql = "UPDATE users Set quartz = quartz - ". $stock["price"] ." Where id=". $_SESSION["userID"];
       $statement = mysqli_prepare($conn, $sql);
       mysqli_stmt_execute($statement);

       $sql = "INSERT INTO boutique_ledger (buyerid, sellerid, price, itemID, purchasedate) Values (?, ?, ?, ?, utc_timestamp())";
       $statement = mysqli_prepare($conn, $sql);
       $price = $stock["price"];
       $buyerid = $_SESSION["userID"];
       $sellerid =  $stock["sellerid"];
       $itemID= $_POST["id"];
       mysqli_stmt_bind_param($statement, "iiii", $buyerid, $sellerid, $price, $itemID);
       mysqli_stmt_execute($statement);

       statBoutiqueSale($stock['price']);

        //sends message to me to inform me of purchase
        $sender = $_SESSION["userID"];
        $recipient = $stock["sellerid"]; //me
        $message = $stock["itemname"] .  " - Item was purchased!";
        $subject = "Your Sale";
        sendMessage(0, $sender, $recipient, $message, $subject);

         //sends message with link to uploaded file
        $sender = $stock["sellerid"]; //me
        $recipient = $_SESSION["userID"];
        $message = $stock["itemname"] . " - <a href='/petzbazaar/boutique_downloadlink.php?id=" . $stock['id'] . "'>Download your Purchase </a>";
        $subject = "Your Purchase";
        sendMessage(0, $sender, $recipient, $message, $subject);

 

        $_SESSION["message"] = "Transaction Complete!";
        header('location: /petzbazaar/boutique.php');
        die();
    }
    else {
        $_SESSION["error"] = "You do not have enough quartz for this purchase.";
        header('location: /petzbazaar/boutique.php');
        die();
    }
}
