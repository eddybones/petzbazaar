<?php

session_start();
require('functions.php');
CheckLogin();

$title="Marketplace";
include("indexheader.php");
$conn = DBConnect();
?>

<h1> Open Shops </h1>
<br>
<div id="content2">
<?php
$usershopviews=[];
$sql = "SELECT sellerid, viewed FROM shop_views WHERE userid=" . $_SESSION["userID"];
$viewresult = mysqli_query($conn, $sql);
if (mysqli_num_rows($viewresult) > 0) {
  while($viewrow = mysqli_fetch_assoc($viewresult)) {
   $usershopviews[$viewrow["sellerid"]] = $viewrow["viewed"]; //seller id is like the label on the drawer, and viewed is the contents inside that we want to see.
  }
}

$mutations = mutations();
$muteIDs = muteids_string($mutations);
$muteCondition = '';
if($muteIDs != '') {
    $muteCondition = "AND users.ID NOT IN ({$muteIDs})";
}

$sql = "SELECT users.id, username, max(creationdate) as newestlisting
    FROM users
    INNER JOIN shop_stock ON shop_stock.sellerid = users.id 
    WHERE shop_stock.purchased = 0
      AND users.open = 1
      {$muteCondition}
    GROUP BY users.id, username
    ORDER BY username ASC";
$openresult = mysqli_query($conn, $sql);
if (mysqli_num_rows($openresult) > 0) {
  while ($openrow = mysqli_fetch_assoc($openresult)) {
    if (array_key_exists($openrow["id"], $usershopviews)) {
      if ($usershopviews[$openrow["id"]] < $openrow["newestlisting"]) {
        echo "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/updatestar.png'/>";
      }
    }
    else {
      if ($openrow["newestlisting"] !=null) {
        echo "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/updatestar.png'/>";
      }
    }
    echo '<a href="/petzbazaar/shop/' . $openrow["username"] . '">' . $openrow["username"] . "</a> <br>";
  }
}


?> 
 </div>     
<br>
<br>
<br>
<img src="http://www.mythicsilence.com/malevolent/Images/bazaar/market.png">	


<br>
<?php
include("indexfooter.php");
?>