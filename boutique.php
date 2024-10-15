<?php

session_start();
require('functions.php');
CheckLogin();

$title="Boutique";
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
The boutique features specialty items that have been generously donated by members of the Petz Community. Please be sure to familiarize yourself with the donor's rules! Learn more about donating to the boutique
<a href ="boutique_donation_info.php"> here</a>.
<?php

$sql = "SELECT id, uniqueimage, price, itemname, description, sellerid, quantity, itemtype FROM boutique_stock Where purchased=0 ORDER BY itemtype";
$result = mysqli_query($conn, $sql);
$lastitemtype = 0;
if (mysqli_num_rows($result) > 0) {
  echo '<div id="grid">';
  // output shop stock data of each row
  while($row = mysqli_fetch_assoc($result)) {
    if($row["itemtype"]!=$lastitemtype){
      echo "</div>";


      switch($row['itemtype']) {
        case 1:
          echo "<h1>Hexed Petz</h1>";
          break;
        case 2:
          echo "<h1>Bred/Brexed Petz</h1>";
          break;
        case 3:
          echo "<h1>Grabbags</h1>";
          break;
        case 4:
          echo "<h1>Toyz</h1>";
          break;
        case 5:
          echo "<h1>Clothes</h1>";
          break;
        case 6:
          echo "<h1> Shop Upgrades </h1>";
          break;
        case 7:
          echo "<h1>Special</h1>";
          break;  

      }        

      echo '<div id="grid">';
      $lastitemtype = $row["itemtype"];
    }
    echo "<div class='item'>";
    echo "<div class='image'> <img src='imageuploads/" . $row["sellerid"] . "/" . $row["uniqueimage"] . "'/> </div>";
    echo "<h1>". $row["itemname"] . "</h1>";
    if($row["quantity"] > 1) {
      echo "Quantity: " . $row["quantity"];
      echo "<br> Price per unit: ";
    }
    echo  $row["price"] . " <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/quartz.png'>";
    echo "<div class='description'><p>" . $row["description"] . "</p> </div>";
    $formaction = "/petzbazaar/boutique_purchase.php";
    switch($row["itemtype"]) {
      case 6:
        $formaction = "/petzbazaar/boutique_upgrade_purchase.php";
        break;
      default:
        $formaction = "/petzbazaar/boutique_purchase.php";
        break;
    }
    echo "<form action='$formaction' method='POST'>";
    echo "<input type='hidden' name='id' value=' ". $row["id"] . "'>";
    echo "<input class ='button' type='submit' value='Purchase' onclick=\"return confirmation('Do you really want to purchase this listing?')\">";
    echo "</form>";
    echo "</div>"; // End class="item"
    }
    echo '</div>'; // End id="grid"
} else {
  echo "There are no active shop listings. Check back soon!";
}  
?>

<br>
<?php
include("indexfooter.php");
?>