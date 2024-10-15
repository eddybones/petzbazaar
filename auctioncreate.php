<?php

session_start();
require('functions.php');
CheckLogin();
$conn = DBConnect();
$title = "Auction Creation";

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $sql = "SELECT u.auction_slots, COUNT(*) AS auction_count FROM users u INNER JOIN auctions s ON u.id = s.sellerId WHERE u.id = {$_SESSION['userID']} AND s.purchased = 0"; 
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  if ($row["auction_count"]==$row["auction_slots"]) {
      $_SESSION["error"] = "Your auction block is currently full.";
      header('location: /petzbazaar/auctioncreate.php');
      die();
  }
  if ($_POST["buyoutprice"] !="") {
    if ($_POST["buyoutprice"] < $_POST["startingbid"]) { 
      $_SESSION["error"] = "Buyout price must be equal or greater than the starting bid.";
      header('location: /petzbazaar/auctioncreate.php');
      die();
    }
    if ($_POST["buyoutprice"] > 10000) { 
      $_SESSION["error"] = "Buyout price cannot exceed 10,000 coinz.";
      header('location: /petzbazaar/auctioncreate.php');
      die();
    }
  }
  if ($_POST["auctiontype"] ==1 ) {     
    if ($_POST["startingbid"] < 5) { //it is 0 if silent 1 if standard.
      $_SESSION["error"] = "Starting bid must be at least 5 coinz.";
      header('location: /petzbazaar/auctioncreate.php');
      die();
    }    
    if ($_POST["startingbid"] > 9000) { //it is 0 if silent 1 if standard.
      $_SESSION["error"] = "Starting bid cannot exceed 9,000 coinz.";
      header('location: /petzbazaar/auctioncreate.php');
      die();
    }
  }
  if ($_POST["auctiontype"] ==0 ) {       
    if ($_POST["reserveprice"] < 5) { 
      $_SESSION["error"] = "Reserve price must be at least 5 coinz.";
      header('location: /petzbazaar/auctioncreate.php');
      die();
      }
    if ($_POST["reserveprice"] > 10000) { 
      $_SESSION["error"] = "Reserve price cannot exceed 10,000 coinz.";
      header('location: /petzbazaar/auctioncreate.php');
      die();
      }  
    
  }
  if(array_key_exists('imageToUpload', $_FILES)) {
    $userId = $_SESSION["userID"]; 
    $uploadedFile = $_FILES['imageToUpload'];
    $originalImageName = $uploadedFile['name'];
    $ext = strtolower(pathinfo($originalImageName, PATHINFO_EXTENSION));
    // Check file size
    if ($uploadedFile["size"] > 10485760) {
      $_SESSION["error"] = "File is too large.";
      header('location: /petzbazaar/auctioncreate.php');
      die();
    } 
    // Allow certain file formats
    if($ext != "jpg" && $ext != "png" && $ext != "jpeg" && $ext != "gif" && $ext != "bmp") {
      $_SESSION["error"] = "Sorry, only JPG, JPEG, PNG, BMP & GIF files are allowed.";
      header('location: /petzbazaar/auctioncreate.php');
      die();
    }
    
     // Unique name concatenates MD5 hash of file contents, a hyphen, the current Unix timestamp, and the original extension
     $uniqueImageName = md5_file($uploadedFile['tmp_name']);
     $uniqueImageName .= '-';
     $uniqueImageName .= time();
     $uniqueImageName .= '.' . pathinfo($originalImageName, PATHINFO_EXTENSION);
     /*
     * __DIR__ gives us the directory that this PHP script is running in (for me, "D:\Code\phpstuff")
     * DIRECTORY_SEPARATOR gives us "\" on Windows or "/" on Unix/Linux/MacOS
     * The final result for me would be "D:\Code\phpstuff\uploaded_stuff\1\"
     */
    $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'imageuploads' . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR . 'auctions' . DIRECTORY_SEPARATOR;

    // If the folder we want to store stuff in does not exist, create it
    if(!is_dir($targetFolder)) {
        mkdir($targetFolder);
    }
      // Move the uploaded file to the folder
      move_uploaded_file($uploadedFile['tmp_name'], $targetFolder . $uniqueImageName);
  }    
 



  if(array_key_exists('fileToUpload', $_FILES)) {
    $userId = $_SESSION["userID"]; 
    $uploadedFile = $_FILES['fileToUpload'];
    $originalFileName = $uploadedFile['name'];
    if ($uploadedFile["size"] > 10485760) {
      $_SESSION["error"] = "File is too large. Max file size is 10MB.";
        header('location: /petzbazaar/auctioncreate.php');
        die();
      }
      // Unique name concatenates MD5 hash of file contents, a hyphen, the current Unix timestamp, and the original extension
      $uniqueFileName = md5_file($uploadedFile['tmp_name']);
      $uniqueFileName .= '-';
      $uniqueFileName .= time();
      $uniqueFileName .= '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);
      /*
      * __DIR__ gives us the directory that this PHP script is running in (for me, "D:\Code\phpstuff")
      * DIRECTORY_SEPARATOR gives us "\" on Windows or "/" on Unix/Linux/MacOS
      * The final result for me would be "D:\Code\phpstuff\uploaded_stuff\1\"
      */
    $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'fileuploads' . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR . 'auctions' . DIRECTORY_SEPARATOR;

    // If the folder we want to store stuff in does not exist, create it
    if(!is_dir($targetFolder)) {
        mkdir($targetFolder);
    }
      // Move the uploaded file to the folder
      move_uploaded_file($uploadedFile['tmp_name'], $targetFolder . $uniqueFileName);
  }

   
    $sql = "INSERT INTO auctions (sellerid, uniqueimage, uniquefilename, originalfilename, buyoutprice, reserveprice, startingbid, standardauction, itemname, description, purchased, creationdate, auctionend) Values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, utc_timestamp(), ?)";
    $statement = mysqli_prepare($conn, $sql);
    $sellerid = $_SESSION["userID"];
    $buyoutprice = $_POST["buyoutprice"];
    $reserveprice = $_POST["reserveprice"];
    $startingbid = $_POST["startingbid"];
    $itemname = $_POST["itemname"];
    $standardauction = $_POST["auctiontype"]; //it is 0 if silent 1 if standard.
    $description = $_POST["description"];
    $auctionend = new DateTime('now', new DateTimeZone('UTC')); //this needs to be creation date plus chosen duration.

    $currentDate = new DateTime('now', new DateTimeZone('UTC'));
    switch($_POST["duration"]) {
      case "24 Hours":
        $actionend = $currentDate->modify('+1 day');
      break;
      case "3 Days":
        $actionend = $currentDate->modify('+3 day');
      break;
      case "1 Week":
        $actionend = $currentDate->modify('+7 day');
      break;
      case "2 Weeks":
        $actionend = $currentDate->modify('+14 day');
      break;
    }
    $auctionend = $auctionend->format('Y-m-d H:i:s.u');
    mysqli_stmt_bind_param($statement, "isssiiiisss", $sellerid, $uniqueImageName, $uniqueFileName, $originalFileName, $buyoutprice, $reserveprice, $startingbid, $standardauction, $itemname, $description, $auctionend);
    mysqli_stmt_execute($statement);

    //insert auction into listed auctions in auctions stats

    $_SESSION["message"] = "Auction Created!";
    

}


$messaging = "";
include("indexheader.php");

echo $messaging;



$sql = "SELECT auction_slots from users WHERE id=".$_SESSION["userID"]; 
$result = mysqli_query($conn, $sql);
$slotresult = mysqli_fetch_assoc($result);
$slots = $slotresult["auction_slots"]; //auction_slots
$sql = "SELECT count(*) as activelistings FROM auctions WHERE `purchased` = 0 and sellerid = ".$_SESSION["userID"]; 
$listresult = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($listresult); 
$activelistings = $row["activelistings"];  
//if listing count is less than auction slot count, display form.
if ($row["activelistings"] < $slotresult["auction_slots"]) { //auctionslots
  echo "<div align='left'>";
  echo "<h1>Create an Auction</h1>";
  
  echo "<form action='/petzbazaar/auctioncreate.php' method='post' enctype='multipart/form-data'>";
  echo "<div class='formdiv'>Select image to upload: </div>";
  echo "<input type='file' name='imageToUpload' id='fileToUpload' required><br>";
  echo "<div class='formdiv'>Select file to upload: </div>";
  echo "<input type='file' name='fileToUpload' id='fileToUpload' required>";
  echo "<br>";
  echo "<br>";
  echo "<label for='itemname'>Item Name: </label>";
  echo "<input type='text' id=itemname name=itemname required>";
  echo "<br>";
  echo "<br>";
  echo "<label for='description'>Description:</label>";
  echo "<br>";
  echo "<textarea id='description' placeholder='Describe your auction here!' name='description' rows='4' cols='50'>";
  echo "</textarea>"; 
  echo "<br>";
  echo "<br>";
  echo "<label for='duration'>Duration: </label>";
  echo "<select name='duration' id='duration'>";
  echo "<option value='24 Hours'>24 Hours</option>";
  echo "<option value='3 Days'>3 Days</option>";
  echo "<option value='1 Week'>1 Week</option>";
  echo "<option value='2 Weeks'>2 Weeks</option>";
  echo "</select><br><br>";

  echo "Standard auctions can be created with or without a buyout. Buyout prices must be equal to or greater than the starting bid.<br><br>";
  echo "Silent auctions allow the seller to set a reserve price that is hidden from bidders. This value is the minimum amount the seller wishes to earn from the auction. Users may bid any amount, and the item will sell to the highest bidder unless no bids equal or exceed the reserve price.";
  echo "<br>";
  echo "<br>";

  echo "<input type='radio' id='standard' name='auctiontype' value='1' onclick='toggleAuctionType(\"standard\")'>";
  echo "<label for='standard'>Standard Auction</label>";
  
  echo "<input type='radio' id='silent' name='auctiontype' value='0' onclick='toggleAuctionType(\"silent\")'>";
  echo "<label for='silent'> Silent Auction</label><br><br>";


  echo "<div id='silentContainer' style='display:none;'>";
    echo "<label for='reserveprice'>Reserve Price: </label>";
    echo "<input type='number'  id='reserveprice' name='reserveprice' min='5' value='5' required>(Minimum 5 Coinz. This value is hidden from bidders.)<br><br>";
  echo "</div>";

  
  echo "<div id='standardContainer' style='display:none;'>";
    echo "<label for='startingbid'>Starting Bid: </label>";
    echo "<input type='number'  id='startingbid' name='startingbid' min='5' value='5' required>(Minimum 5 Coinz) <br><br>";

    echo "<label for='buyoutprice'>Buyout Price: </label>";
    echo "<input type='number'  id='buyoutprice' name='buyoutprice' > (Optional)<br><br>";
    echo "<br>";
  echo "</div>";


  echo "<input class ='button' type='submit' value='Create Auction' name='submit'>";
  echo "</form></div><br><br>";
  echo "<div align='left'> You have " . ($slots - $activelistings) . " open auction slot(s). </div><br><br>";
}  
else {
  echo "Your auction block is currently full.<br><br>";
}


if(array_key_exists('message', $_SESSION)) {
  echo "<div class='messagestyle'>" . $_SESSION["message"] . "</div>";
  unset($_SESSION["message"]);
}

if(array_key_exists('error', $_SESSION)) {
  echo "<div class='errorstyle'>" . $_SESSION["error"] . "</div>";
  unset($_SESSION["error"]);
}
?>
<h1> Standard Auctions </h1>
<div id="grid">

<?php
//this is where the users active and silent auctions would show, as well as recent expired auctions that didn't sell
$sql = "SELECT id, uniqueimage, buyoutprice, reserveprice, startingbid, standardauction, creationdate, auctionend, itemname, description, sellerid FROM auctions Where purchased=0 and standardauction=1 and sellerid = ".$_SESSION["userID"]; //will need to get current bid info from other table
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
  // output shop stock data of each row
  while($row = mysqli_fetch_assoc($result)) {
    $sql = "SELECT auctionid, bidder, bid_amount, users.username FROM bid_history INNER JOIN users ON bid_history.bidder=users.id Where auctionid =" . $row["id"];
    $bidresult = mysqli_query($conn, $sql);
    while($bidrow = mysqli_fetch_assoc($bidresult)) {
    echo "<div class='item'>";
    echo "<div class='image'> <img src='/petzbazaar/imageuploads/" . $row["sellerid"] . "/auctions" . $row["uniqueimage"] . "'/> </div>";
    echo "<h1>". $row["itemname"] . "</h1>";
    echo  "Reserve Price: " . $row["buyoutprice"] . " <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/coinz.png'><br>";
    echo  "Current Bid: " . $bidrow["bid_amount"] . " <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/coinz.png'><br>"; //starting or current bid
    echo  "Bid History: " . $bidrow["bidder"] . "<br>"; //number of bidds and bidders
    echo  $row["auctionend"];
    echo "<div class='description'><p>" . $row["description"] . "</p> </div>";
    echo "<div class='style1'>";
    echo "<a href='/petzbazaar/removeauctionlisting.php?id=" . $row["id"] . "' class='button' onclick=\"return confirmation('Do you really want to delete this listing?')\"> X </a>";
    echo "</div>";   
    echo "</div>";
    }
    echo "</div>"  ;
  }
}      
else {
  echo "</div>";
  echo "There are no active auction listings.";   
  }
?>

</div>

<h1> Silent Auctions </h1>
<div id="grid">

<?php
//this is where the users active and silent auctions would show, as well as recent expired auctions that didn't sell
$sql = "SELECT id, uniqueimage, buyoutprice, reserveprice, startingbid, standardauction, creationdate, auctionend itemname, description, sellerid FROM auctions Where purchased=0 and standardauction=0 and sellerid = ".$_SESSION["userID"]; //will need to get current bid info from toher table
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
if (mysqli_num_rows($result) > 0) {
  // output shop stock data of each row
  while($row = mysqli_fetch_assoc($result)) {
    $sql = "SELECT auctionid, bidder, bid_amount, users.username FROM bid_history INNER JOIN users ON bid_history.bidder=users.id Where auctionid =" . $row["id"];
    $bidresult = mysqli_query($conn, $sql);
    $bidrow = mysqli_fetch_assoc($bidresult);
    echo "<div class='item'>";
    echo "<div class='image'> <img src='/petzbazaar/imageuploads/" . $row["sellerid"] . "/auctions" . $row["uniqueimage"] . "'/> </div>";
    echo "<h1>". $row["itemname"] . "</h1>";
    echo  "Reserve Price: " . $row["reserveprice"] . " <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/coinz.png'><br>";
    echo  "Current Bid: " . $bidrow["bid_amount"] . " <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/coinz.png'><br>"; //starting or current bid
    echo  "Bid History: " . $bidrow["bidder"] . "<br>"; //number of bids
    echo  $row["auctionend"];
    echo "<div class='description'><p>" . $row["description"] . "</p> </div>";
    echo "<div class='style1'>";
    echo "<a href='/petzbazaar/removeauctionlisting.php?id=" . $row["id"] . "' class='button' onclick=\"return confirmation('Do you really want to delete this listing?')\"> X </a>";
    echo "</div>";   
    }
    echo "</div>"  ;  
  }  
  else {
    echo "</div>"  ;
  echo "There are no active auction listings.";
}  
?>

<h1> Expired Auctions </h1>
This needs work because it needs to have the different values for expired and standard, and it needs to show ones that aren't purchased and the end date is in the past.<br>
<div id="grid">

<?php
/* 
//this is where the users active and silent auctions would show, as well as recent expired auctions that didn't sell
$sql = "SELECT id, uniqueimage, buyoutprice, reserveprice, startingbid, standardauction, creationdate, auctionend itemname, description, sellerid FROM auctions Where purchased=0 and sellerid = ".$_SESSION["userID"]; //and auctionend is in the past
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);
if (mysqli_num_rows($result) > 0) {
  // output shop stock data of each row
  while($row = mysqli_fetch_assoc($result)) {
    echo "<div class='item'>";
    echo "<div class='image'> <img src='/petzbazaar/imageuploads/" . $row["sellerid"] . "/auctions" . $row["uniqueimage"] . "'/> </div>";
    echo "<h1>". $row["itemname"] . "</h1>";
    if($row["standardauction"] == 1) {
      echo  $row["startingbid"] . " <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/coinz.png'><br>";
      }
      if($row["buyoutprice"] > 0) {
        echo "Buyout Price: " . $row["buyoutprice"]; 
      }
    if($row["reserveprice"] > 0) {
        echo "Reserve Price: " . $row["reserveprice"]; 
      } 
    echo  $row["auctionend"];
    echo "<div class='description'><p>" . $row["description"] . "</p> </div>";
    echo "<div class='style1'>";
    echo "<a href='/petzbazaar/removeauctionlisting.php?id=" . $row["id"] . "' class='button' onclick=\"return confirmation('Do you really want to delete this listing?')\"> X </a>";
    echo "</div>";   
    }
    echo "</div>"  ;  
  }  
  else {
    echo "</div>"  ;
  echo "There are no expired auction listings.";
}  
  */
?>

</div>

</div>



<?php
include("indexfooter.php");
?>