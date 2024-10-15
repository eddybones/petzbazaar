<?php
session_start();
require('functions.php');
$conn = DBConnect();
CheckLogin();

$sql = "SELECT purchased, sellerid FROM auctions WHERE id= ?";
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $_POST["id"]);
mysqli_stmt_execute($statement);
$purchasecheck = mysqli_stmt_get_result($statement);
$row = mysqli_fetch_assoc($purchasecheck);
if($row["purchased"] == 1) {
    $_SESSION["error"] = "This item has already been purchased.";
    header('location: /petzbazaar/auctionhouse/' . $_POST["seller"]);
    die();
}

$sql = "SELECT * from bid_history Where auctionid= ". $_POST["id"] ." ORDER BY bid_amount DESC";
$bid_historyresult = mysqli_query($conn, $sql);
$bidhistory = mysqli_fetch_all($bid_historyresult, MSQLI_ASSOC);

//$mutations = mutations();
//$muteIDs = muteids_array($mutations);
//if(in_array($_SESSION['userID'], $muteIDs)) {
    // Don't set a session variable in this case. The user shouldn't be able to purchase from someone who is muting them,
    // but in the event that they send a manual POST request or they were already on the shop page prior to being muted,
    // this will just boot them back to the mauction house.
//    header('location: /petzbazaar/auctionhouse.php');
//    die();
//}

//This is for buyout. This counts as a won auction.
if(array_key_exists('buyout', $_POST)){
$sql = "SELECT coinz, auctiontransactionz FROM users WHERE id=". $_SESSION["userID"]; 
$userresult = mysqli_query($conn, $sql);
$sql = "SELECT  id, sellerid, itemname, buyoutprice, purchased,  FROM auctions WHERE id=? and purchased=0"; //select what you need from auctions. Do I need column for winner or join bidder from history
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $_POST["id"]);
mysqli_stmt_execute($statement);
$stockresult = mysqli_stmt_get_result($statement);
if (mysqli_num_rows($userresult)==1 && mysqli_num_rows($stockresult)==1) {
    $user = mysqli_fetch_assoc($userresult);
    $stock = mysqli_fetch_assoc($stockresult);
    if ($user["coinz"] >= $stock["buyoutprice"]) {
        //sets auction item to purchased and make auction end current date
        $sql = "UPDATE auctions Set auctionend = utc_date(), purchased =1, buyerid=" . $_SESSION["userID"] . " WHERE id=?";
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($statement, "i", $_POST["id"]);
        mysqli_stmt_execute($statement);
        }


        //Buyer id/session user id needs coin cost of listing removed
        $sql = "UPDATE users Set Coinz = coinz - ". $stock["buyoutprice"] .", auctiontransactionz = auctiontransactionz+1 Where id=". $_SESSION["userID"];
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_execute($statement);
        //add to bid history
        $sql = "INSERT into bid_history Set bidder = ". $_SESSION["userID"] .", auctionid = ". $_POST["id"] .", bid_amount = ". $stock["buyoutprice"] .", bid_time = utc_timestamp()";
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_execute($statement);
        //add to auction stats
        $sql = "UPDATE auction_stats Set userID = ". $_SESSION["userID"] .", unique_bids = unique_bids+1, auction_wins = auction_wins+1";
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_execute($statement);


        //seller id needs coin cost of listing added and sales added
        $sql = "UPDATE users Set Coinz = coinz + ". $stock["buyoutprice"].", auctiontransactionz = auctiontransactionz+1 Where id=". $stock["sellerid"]; 
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_execute($statement);
        //add to auction stats
        $sql = "UPDATE auction_stats Set userID = ". $stock["sellerid"] .", sold_auctions = sold_auctions+1";
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_execute($statement);

        // Transaction Seller Quartz and auction slots
        $sql = "SELECT auctiontransactionz, auction_slots FROM users WHERE id=". $stock["sellerid"];
        $sellerresult = mysqli_query($conn, $sql);
        $seller = mysqli_fetch_assoc($sellerresult);
        if ($seller["auctiontransactionz"] % 10 ==0) {
            $sql = "UPDATE users Set quartz = quartz + 1 Where id=".$stock["sellerid"];
            $result = mysqli_query($conn, $sql);
        }
        //new query to check auction stats
        $sql = "SELECT auction_wins FROM auction_stats WHERE id=". $stock["sellerid"];
        $winresult = mysqli_query($conn, $sql);
        if ($winresult["auction_wins"] % 10 ==0) {
            $sql = "UPDATE users Set auction_slots = auction_slots + 1 Where id=".$stock["sellerid"];
            $result = mysqli_query($conn, $sql);
        }

        // Transaction Buyer Quartz
        // Do +1 here because we have not run a new query to get the most current user row after we incremented the transactionz above.
        if (($user["auctiontransactionz"] + 1) % 10 ==0) {
            $sql = "UPDATE users Set quartz = quartz + 1 Where id=". $_SESSION["userID"];
            $result = mysqli_query($conn, $sql);
        }
        
         //sends message to seller to inform them of purchase
         $sender = $_SESSION["userID"];
         $recipient = $stock["sellerid"];
         $message = $stock["itemname"] .  " - Item was purchased!<br>Price: ". $stock['buyoutprice'] . " coinz";
         $subject = "Your Sale  - " . $stock['itemname'];
         sendMessage(0, $sender, $recipient, $message, $subject) ;
 
          //sends message with link to uploaded file
         $sender = $stock["sellerid"];
         $recipient = $_SESSION["userID"];
         //link likely needs updated to auction path
         $message = $stock["itemname"] . " - <a href= '/petzbazaar/downloadlink.php?id="  . $stock['id'] . "' >Download your Purchase </a><br>Price: ". $stock['buyoutprice'] . " coinz";
         $subject = "Your Purchase - " . $stock['itemname'];
         sendMessage(0, $sender, $recipient, $message, $subject) ;


        $_SESSION["message"] = "Transaction Complete!";
        header('location: /petzbazaar/auctionlisting.php?id=' . $_GET["id"]); //auction listing page
        die();
        
    }
    else {
        $_SESSION["error"] = "You do not have enough coinz for this transaction.";
        header('location: /petzbazaar/auctionlisting.php?id=' . $_GET["id"]); //auction listing page
        die();
    }
}
//This is for a bid. So this works for reserve or standard auction types. 
//check that bid is higher than current price
if(array_key_exists('bid', $_POST)){
    $sql = "SELECT coinz, auctiontransactionz FROM users WHERE id=". $_SESSION["userID"]; 
    $userresult = mysqli_query($conn, $sql);
    $sql = "SELECT  id, sellerid, startingbid, itemname, reserveprice, standardauction, auctionend, IF(utc_date() >= auctionend, 1, 0) as auction_closed  FROM auctions WHERE id=?"; 
    $statement = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($statement, "i", $_POST["id"]);
    mysqli_stmt_execute($statement);
    $stockresult = mysqli_stmt_get_result($statement);
    if (mysqli_num_rows($userresult)==1 && mysqli_num_rows($stockresult)==1) {
        $user = mysqli_fetch_assoc($userresult);
        $stock = mysqli_fetch_assoc($stockresult);
        //might need to check auction end time here.
        if ($stock["auction_closed"] == 1) {
            $_SESSION["error"] = "This auction has ended.";
            header('location: /petzbazaar/auctionlisting.php?id=' . $_GET["id"]); //auction listing page
            die();  
        }
        /*
        $sql = "WITH HighBid AS (
            SELECT bid_amount, auctionid FROM bid_history WHERE auctionid=? ORDER BY bid_amount DESC limit 1
        )
        SELECT CASE WHEN ISNULL(bid_amount) THEN startingbid
            WHEN bid_amount > startingbid THEN bid_amount
            ELSE startingbid
        END AS current_high_bid
        FROM auctions a
        INNER JOIN HighBid hb ON a.id = hb.auctionid
        WHERE id=?";

        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($statement, "ii", $_POST["id"], $_POST["id"]);
        mysqli_stmt_execute($statement);
        $highbidresult = mysqli_stmt_get_result($statement);
        $highbid = mysqli_fetch_assoc($highbidresult);
        */
        if ($user["coinz"] >= $_POST["bid"] && (count($bidhistory) == 0 || $_POST["bid"] > $bid_history[0]["bid_amount"]) && $_POST["bid"] > $stock["startingbid"]) { //this checks that the person's bid doesn't exceed their coinz, and that it is higher than current/strating bid
            $sql = "INSERT into bid_history (auctionid, bidder, bid_amount, bid_time) VALUES (?, ?, ?, utc_date())";
            $statement = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($statement, "iii", $_POST["id"], $_SESSION["userID"], $_POST["bid"]);
            mysqli_stmt_execute($statement);
            }

            //bidder id/session user id needs bid coinz removed
            $sql = "UPDATE users Set Coinz = coinz - ". $_POST["bid"] ." Where id=". $_SESSION["userID"];
            $statement = mysqli_prepare($conn, $sql);
            mysqli_stmt_execute($statement);

            //if this isn't the first bid, send message to person outbid and refund their coinz
            if (count($bidhistory) > 0) {
                $previousbidder= $bid_history[0]["bidder"];
                $previousbid= $bid_history[0]["bid_amount"]; 
                $sql = "UPDATE users SET coinz = coinz+ $previousbid WHERE id= $previousbidder";
                $result = mysqli_query($conn, $sql);
                //sends message to bidder to say they are outbid
                $sender = $stock["sellerid"];
                $recipient = $previousbidder;
                $message = $stock["itemname"] .  " - You have been outbid. <br>Refund: ". $previousbid . " coinz";
                $subject = "Outbid on auction  - " . $stock['itemname'];
                sendMessage(0, $sender, $recipient, $message, $subject) ;

            } 
            
        
            //if this is a unique bid - the bidder's first time bidding on this item - it needs to be added to auction stats table.
            //if $bid_historyresult["bidder"] does not have the session uder ID?
            $foundbidder = false;
            foreach ($bidhistory as $bid) {
                if($bid["bidder"]== $_SESSION["userID"]) {
                    $foundbidder=true;
                    break;
                }
            }
            if (count($bidhistory) == 0 || $foundbidder==false) {
                $sql = "INSERT INTO auction_stats (userID, unique_bids) Values (?, uniquebids=unique_bids+1)"; 
                $statement = mysqli_prepare($conn, $sql);
                $userID = $_SESSION["userID"];
                mysqli_stmt_bind_param($statement, "i", $userID);
                mysqli_stmt_execute($statement);
                }
            //statCoinzSale($stock['buyoutprice']);   
    
    
            $_SESSION["message"] = "Bid Accepted!";
            header('location: /petzbazaar/auctionlisting.php?id=' . $_GET["id"]); //auction listing page
            die();
            
        }
        else {
            $_SESSION["error"] = "You do not have enough coinz for this bid, or posted bid does not exceed current bid.";
            header('location: /petzbazaar/auctionlisting.php?id=' . $_GET["id"]); //auction listing page
            die();
        }
    }

