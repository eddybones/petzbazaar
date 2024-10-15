<?php

session_start();
require('functions.php');
CheckLogin();
$conn = DBConnect();
$id= $_GET["id"];

//add mute stuff so someone with a direct link can't see an auction when muted

$title="Auction Listing";  //make this customizable
include("indexheader.php");

?>
<table align="center" style="width:66%" cellspacing="0">

<?php
$sql = "SELECT auctions.id, uniqueimage, buyoutprice, reserveprice, itemname, description, startingbid, standardauction, auctionend, sellerid, users.username FROM auctions INNER JOIN users ON auctions.sellerid= users.id Where purchased=0 and auctions.id=?";
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $id); 
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
$row = mysqli_fetch_assoc($result); 
  echo "<tr><th colspan='2'>" . $row["itemname"] . "</th></tr>";
  echo "<tr>";
  echo "<td style='width:32%' align='left'><center><img src='imageuploads/" . $row["sellerid"] . "/auctions/" . $row["uniqueimage"] . "'/><br>"; //image scaling might be an issue here?
  echo "<center>" . $row["description"] . "</td>";

  
  echo "<td style='width:32%' align='left'>";
  echo "Seller: " . $row["username"] . "<br>";
  echo "Current Bid: " . $row["startingbid"] . " <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/coinz.png'> <br>"; //current or starting
  if ($row["buyoutprice"] > 0 && $row["buyoutprice"] > $row["startingbid"]) { //this would be the current bid from bid history, not the starting. It will always be equal to or greater than starting bid.
    echo "Buyout: " . $row["buyoutprice"] . " <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/coinz.png'><br>";
    } 
 
  echo "Number of Bids: Placeholder<br>"; //needs to come from bid history. Bid numbers only, not usernames
  echo "Auction End: ". date_format(date_create($row["auctionend"]), "m/d/Y") . "<br><br>";
//if auction has not ended show buttons
    echo "<center><form action='/petzbazaar/auctionbid.php' method='POST'>"; //this button only appears if the auction has not ended
    echo "<input type='hidden' name='id' value=' ". $row["id"] . "'>";
    echo "<input class ='button' type='submit' value='Bid' onclick=\"return confirmation('Do you really want to bid on this listing?')\">"; //entering the bid to post from here would be ideal. Could be a field they enter coinz into like shop listing price set
    echo "</form>";
    echo "<br>";
  if ($row["buyoutprice"] > 0 && $row["buyoutprice"] > $row["startingbid"]) { //this would be the current bid from bid history, not the starting. It will always be equal to or greater than starting bid.
    echo "<center><form action='/petzbazaar/auctionbid.php' method='POST'>"; 
    echo "<input type='hidden' name='id' value=' ". $row["id"] . "'>";
    echo "<input class ='button' type='submit' value='Buyout' onclick=\"return confirmation('Do you really want to purchase this listing?')\">"; 
    echo "</form>";
    } 


  echo "</tr>"; 
  
