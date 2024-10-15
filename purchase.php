<?php
session_start();
require('functions.php');
$conn = DBConnect();
CheckLogin();

$sql = "SELECT purchased, sellerid FROM shop_stock WHERE id= ?";
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $_POST["id"]);
mysqli_stmt_execute($statement);
$purchasecheck = mysqli_stmt_get_result($statement);
$row = mysqli_fetch_assoc($purchasecheck);
if($row["purchased"] == 1) {
    $_SESSION["error"] = "This item has already been purchased.";
    header('location: /petzbazaar/shop/' . $_POST["seller"]);
    die();
}

$mutations = mutations();
$muteIDs = muteids_array($mutations);
if(in_array($_SESSION['userID'], $muteIDs)) {
    // Don't set a session variable in this case. The user shouldn't be able to purchase from someone who is muting them,
    // but in the event that they send a manual POST request or they were already on the shop page prior to being muted,
    // this will just boot them back to the marketplace.
    header('location: /petzbazaar/marketplace.php');
    die();
}

$sql = "SELECT coinz, transactionz FROM users WHERE id=". $_SESSION["userID"];
$userresult = mysqli_query($conn, $sql);
$sql = "SELECT  id, sellerid, buyerid, itemname, price, purchased, quantity FROM shop_stock WHERE id=?";
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $_POST["id"]);
mysqli_stmt_execute($statement);
$stockresult = mysqli_stmt_get_result($statement);
if (mysqli_num_rows($userresult)==1 && mysqli_num_rows($stockresult)==1) {
    $user = mysqli_fetch_assoc($userresult);
    $stock = mysqli_fetch_assoc($stockresult);
    if ($user["coinz"] >= $stock["price"]) {
        //sets shop stock item to purchased and current time to purchase date
        $sql = "UPDATE shop_stock Set quantity = quantity-1, buyerid=" . $_SESSION["userID"] . " WHERE id=?";
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($statement, "i", $_POST["id"]);
        mysqli_stmt_execute($statement);
        if ($stock["quantity"] -1 ==0) {
            $sql = "UPDATE shop_stock Set purchasedate = utc_date(), purchased =1, buyerid=" . $_SESSION["userID"] . " WHERE id=?";
            $statement = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($statement, "i", $_POST["id"]);
            mysqli_stmt_execute($statement);
        }


        //Buyer id/session user id needs coin cost of listing removed
        $sql = "UPDATE users Set Coinz = coinz - ". $stock["price"] .", transactionz = transactionz+1 Where id=". $_SESSION["userID"];
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_execute($statement);


        //seller id needs coin cost of listing added and sales added
        $sql = "UPDATE users Set Coinz = coinz + ". $stock["price"].", transactionz = transactionz+1, sales = sales + 1 Where id=". $stock["sellerid"]  ; 
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_execute($statement);

        // Transaction Seller Quartz and shop slots
        $sql = "SELECT transactionz, sales, shop_slots FROM users WHERE id=". $stock["sellerid"];
        $sellerresult = mysqli_query($conn, $sql);
        $seller = mysqli_fetch_assoc($sellerresult);
        if ($seller["transactionz"] % 10 ==0) {
            $sql = "UPDATE users Set quartz = quartz + 1 Where id=".$stock["sellerid"];
            $result = mysqli_query($conn, $sql);
        }
        if ($seller["sales"] % 10 ==0) {
            $sql = "UPDATE users Set shop_slots = shop_slots + 1 Where id=".$stock["sellerid"];
            $result = mysqli_query($conn, $sql);
        }

        // Transaction Buyer Quartz
        // Do +1 here because we have not run a new query to get the most current user row after we incremented the transactionz above.
        if (($user["transactionz"] + 1) % 10 ==0) {
            $sql = "UPDATE users Set quartz = quartz + 1 Where id=". $_SESSION["userID"];
            $result = mysqli_query($conn, $sql);
        }
        
        $sql = "INSERT INTO ledger (buyerid, sellerid, price, itemID, purchasedate) Values (?, ?, ?, ?, utc_timestamp())";
        $statement = mysqli_prepare($conn, $sql);
        $price = $stock["price"];
        $buyerid = $_SESSION["userID"];
        $sellerid =  $stock["sellerid"];
        $itemID= $_POST["id"];
        mysqli_stmt_bind_param($statement, "iiii", $buyerid, $sellerid, $price, $itemID);
        mysqli_stmt_execute($statement);

        statCoinzSale($stock['price']);
        
         //sends message to seller to inform them of purchase
         $sender = $_SESSION["userID"];
         $recipient = $stock["sellerid"];
         $message = $stock["itemname"] .  " - Item was purchased!<br>Price: ". $stock['price'] . " coinz";
         $subject = "Your Sale  - " . $stock['itemname'];
         sendMessage(0, $sender, $recipient, $message, $subject) ;
 
          //sends message with link to uploaded file
         $sender = $stock["sellerid"];
         $recipient = $_SESSION["userID"];
         $message = $stock["itemname"] . " - <a href= '/petzbazaar/downloadlink.php?id="  . $stock['id'] . "' >Download your Purchase </a><br>Price: ". $stock['price'] . " coinz";
         $subject = "Your Purchase - " . $stock['itemname'];
         sendMessage(0, $sender, $recipient, $message, $subject) ;


        $_SESSION["message"] = "Transaction Complete!";
        header('location: /petzbazaar/shop/' . $_POST["seller"]);
        die();
        
    }
    else {
        $_SESSION["error"] = "You do not have enough coinz for this purchase.";
        header('location: /petzbazaar/shop/' . $_POST["seller"]);
        die();
    }
}
