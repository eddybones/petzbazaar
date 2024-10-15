<?php

session_start();
require('functions.php');
CheckLogin();

$title="Auction House";
include("indexheader.php");
$conn = DBConnect();


if(array_key_exists('message', $_SESSION)) {
  echo "<div class='messagestyle'>" . $_SESSION["message"] . "</div>";
  unset($_SESSION["message"]);
}

if(array_key_exists('error', $_SESSION)) {
  echo "<div class='errorstyle'>" . $_SESSION["error"] . "</div>";
  unset($_SESSION["error"]);
}
?>
<img src="https://www.mythicsilence.com/malevolent/Images/bazaar/boutique.png"/><br><br>
The auction house features standard and silent auctions from shop owners on the bazaar. Browse current listings or add your own.<br><br>
<a href ="auctioncreate.php" class="button"> Create or Manage Auctions</a><br><br>

<?php


//could just order by listing ending soonest for simplicity. //would number of bids be in the table with bid history as well?
$sql = "SELECT id, uniqueimage, buyoutprice, reserveprice, itemname, description, startingbid, standardauction, auctionend, sellerid FROM auctions Where purchased=0 and standardauction=1 ORDER BY auctionend";
//  currentbid needs to be pulled from other table
$result = mysqli_query($conn, $sql);
$lastitemtype = 0;
if (mysqli_num_rows($result) > 0) {
  echo '<div id="grid">';  //needs new output grid that is more compact
  // output shop stock data of each row
  while($row = mysqli_fetch_assoc($result)) {
    echo "<div class='item'>";
    echo "<div class='image'> <img src='imageuploads/" . $row["sellerid"] . "/auctions/" . $row["uniqueimage"] . "'/> </div>";
    echo "<h1>". $row["itemname"] . "</h1>";
    echo  "Current Bid: " . $row["startingbid"] . " <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/coinz.png'><br>"; //this could be the starting bid or current bid. Starting needs to show if no bids. Current otherwise
    if ($row["buyoutprice"] > 0 && $row["buyoutprice"] > $row["startingbid"]) { //this would be the current bid from bid history, not the starting. It will always be equal to or greater than starting bid.
    echo "Buyout: " . $row["buyoutprice"] . " <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/coinz.png'>"; 
    }
    echo "<br>";
    echo "Auction End: ". date_format(date_create($row["auctionend"]), "m/d/Y") . "<br><br>";
    echo "<a href ='auctionlisting.php?id=" . $row["id"] . "' class='button'> View </a>";   //routes to GET page
    echo "</div>"; // End class="item"
    }
    echo '</div>'; // End id="grid"
} else {
  echo "There are no active auction listings. Check back soon!";
}  
?>

<br>
<?php
include("indexfooter.php");
?>