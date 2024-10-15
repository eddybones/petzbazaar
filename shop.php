<?php

session_start();
require('functions.php');
CheckLogin();
$conn = DBConnect();

if($_SERVER["REQUEST_METHOD"] == "POST") {
  $sql = "SELECT u.shop_slots, COUNT(*) AS stock_count FROM users u INNER JOIN shop_stock s ON u.id = s.sellerId WHERE u.id = {$_SESSION['userID']} AND s.purchased = 0";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);
  if ($row["stock_count"]==$row["shop_slots"]) {
      $_SESSION["error"] = "Your shop is currently full.";
  header('location: /petzbazaar/shop.php');
  die();
  }
  if ($_POST["quantity"] > 75) {
    $_SESSION["error"] = "Quantity cannot be greater than 75.";
    header('location: /petzbazaar/shop.php');
    die();
      }  
  if ($_POST["price"] < 5) {
    $_SESSION["error"] = "Price must be at least 5 coinz.";
    header('location: /petzbazaar/shop.php');
    die();
      }    
  if ($_POST["price"] > 10000) {
      $_SESSION["error"] = "Price must be no greater than 10,000 coinz.";
      header('location: /petzbazaar/shop.php');
      die();
        }    
  if(array_key_exists('imageToUpload', $_FILES)) {
    $userId = $_SESSION["userID"]; 
    $uploadedFile = $_FILES['imageToUpload'];
    $originalImageName = $uploadedFile['name'];
    $ext = strtolower(pathinfo($originalImageName, PATHINFO_EXTENSION));
    // Check file size
    if ($uploadedFile["size"] > 10485760) {
      $_SESSION["error"] = "File is too large.";
      header('location: /petzbazaar/shop.php');
      die();
    } 
    // Allow certain file formats
    if($ext != "jpg" && $ext != "png" && $ext != "jpeg" && $ext != "gif" && $ext != "bmp") {
      $_SESSION["error"] = "Sorry, only JPG, JPEG, PNG, BMP & GIF files are allowed.";
      header('location: /petzbazaar/shop.php');
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
    $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'imageuploads' . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR;

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
        header('location: /petzbazaar/shop.php');
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
    $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'fileuploads' . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR;

    // If the folder we want to store stuff in does not exist, create it
    if(!is_dir($targetFolder)) {
        mkdir($targetFolder);
    }
      // Move the uploaded file to the folder
      move_uploaded_file($uploadedFile['tmp_name'], $targetFolder . $uniqueFileName);
  }




    $sql = "INSERT INTO shop_stock (sellerid, uniqueimage, uniquefilename, originalfilename, price, auction, itemname, description, purchased, quantity, stack, creationdate) Values (?, ?, ?, ?, ?, 0, ?, ?, 0, ?, ?, utc_timestamp())";
    $statement = mysqli_prepare($conn, $sql);
    $price = $_POST["price"];
    $itemname = $_POST["itemname"];
    $description = $_POST["description"];
    $quantity = $_POST["quantity"];
    $sellerid = $_SESSION["userID"];
    $stack = 0;
    if ($quantity > 1) {
      $stack=1;
    }
    mysqli_stmt_bind_param($statement, "isssissii", $sellerid, $uniqueImageName, $uniqueFileName, $originalFileName, $price, $itemname, $description, $quantity, $stack);
    mysqli_stmt_execute($statement);

    $_SESSION["message"] = "Listing Created!";
    

}


$messaging = "";

if(array_key_exists('seller_name', $_GET) == false) {
    $shopid = $_SESSION["userID"];
    $myshop = true;
    $showlistings = true;
}
else {
    $sql = "SELECT id FROM users WHERE username = ?";
    $statement = mysqli_prepare($conn, $sql);
    $sellerName = $_GET['seller_name'];
    mysqli_stmt_bind_param($statement, "s", $sellerName);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);

    if(mysqli_num_rows($result) != 1) {
      $messaging = 'Could not find this shop.';
      $showlistings = false;
      $myshop = false;
      $shopid = 0;
    } else {
      $row = mysqli_fetch_assoc($result);
      $shopid = $row['id'];
      $myshop = false;
      $showlistings = true;
      $sql = "SELECT open from users WHERE id=?"; //id needs to equal the userid that's on the seller page - the get sellerid
      $statement = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($statement, "i", $shopid);
      mysqli_stmt_execute($statement);
      $result = mysqli_stmt_get_result($statement);
      if (mysqli_num_rows($result) > 0) { 
        $row = mysqli_fetch_assoc($result);  
        if ($row["open"] == 0) {
        $messaging = "This shop is currently closed.";
        $showlistings = false;
        } 
      }
    }
}

$mutations = mutations();
$muteIDs = muteids_array($mutations);
$muted = false;
if(in_array($shopid, $muteIDs)) {
    $muted = true;
}

if($myshop) {
  $title = "My Shop";
} else {
  $title = "Shop - $sellerName";
}
include("indexheader.php");

echo $messaging;


if ($myshop==true) {
  $sql = "SELECT shop_slots from users WHERE id=".$_SESSION["userID"];
  $result = mysqli_query($conn, $sql);
  $slotresult = mysqli_fetch_assoc($result);
  $slots = $slotresult["shop_slots"];
  $sql = "SELECT count(*) as activelistings FROM shop_stock WHERE `purchased` = 0 and sellerid = ".$_SESSION["userID"];
  $listresult = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($listresult); 
  $activelistings = $row["activelistings"];  
//if listing count is less than shop slot count, display form.
  if ($row["activelistings"] < $slotresult["shop_slots"]) {
    echo "<div align='left'>";
    echo "<h1>Create a Shop Listing</h1>";
    echo "<form action='/petzbazaar/shop.php' method='post' enctype='multipart/form-data'>";
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
    echo "<label for='price'>Quantity: </label>";
    echo "<input type='number'  id='quantity' name='quantity' min='1' value='1' max='75' required> Adjust to sell multiple copies of an item in a single listing. Price is per unit.<br><br>";
    echo "<label for='description'>Description:</label>";
    echo "<br>";
    echo "<textarea id='description' placeholder='Describe your sale or auction here!' name='description' rows='4' cols='50'>";
    echo "</textarea>"; 
    echo "<br>";
    echo "<input type='checkbox' id='auction' name='auction' value='auction' >";
    echo "<label for='auction'> Auction</label> (Auction feature coming soon!)<br>";
    echo "<br>";
    echo "<label for='price'>Price: </label>";
    echo "<input type='number'  id='price' name='price' min='5' max='10000' value='5' required> (Minimum 5 Coinz)<br><br>";
    echo "<input class ='button' type='submit' value='Create Listing' name='submit'>";
    echo "</form></div><br><br>";
    echo "<div align='left'> You have " . ($slots - $activelistings) . " open shop slot(s). </div><br><br>";
    }  
    else {
      echo "Your shop is currently full.<br><br>";
    }
}

//for marketplace shop viewing records.
if($shopid != 0) {
  $sql = "INSERT INTO shop_views (userId, sellerId, viewed) VALUES (?, ?, UTC_TIMESTAMP()) ON DUPLICATE KEY UPDATE viewed = UTC_TIMESTAMP()";
  $statement = mysqli_prepare($conn, $sql);
  $viewuserid = $_SESSION["userID"];
  $viewsellerid = $shopid;
  mysqli_stmt_bind_param($statement, "ii", $viewuserid, $viewsellerid);
  mysqli_stmt_execute($statement);
}


if(array_key_exists('message', $_SESSION)) {
  echo "<div class='messagestyle'>" . $_SESSION["message"] . "</div>";
  unset($_SESSION["message"]);
}

if(array_key_exists('error', $_SESSION)) {
  echo "<div class='errorstyle'>" . $_SESSION["error"] . "</div>";
  unset($_SESSION["error"]);
}


if($muted) {
    echo 'Could not find this shop.';
    include("indexfooter.php");
    die;
}


$sql = "SELECT ifnull(storefront, '') as storefront from users WHERE id=?";
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $shopid);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
if (mysqli_num_rows($result) > 0) {
  $row = mysqli_fetch_assoc($result);
  echo $row["storefront"];
  echo "<br>";
}
?>

<div id="grid">

<?php
$sql = "SELECT id, uniqueimage, price, itemname, description, sellerid, quantity FROM shop_stock Where sellerid=? and purchased=0";
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $shopid);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
if (mysqli_num_rows($result) > 0 && $showlistings==true) {
  // output shop stock data of each row
  while($row = mysqli_fetch_assoc($result)) {
    echo "<div class='item'>";
    echo "<div class='image'> <img src='/petzbazaar/imageuploads/" . $row["sellerid"] . "/" . $row["uniqueimage"] . "'/> </div>";
    echo "<h1>". $row["itemname"] . "</h1>";
      if($row["quantity"] > 1) {
        echo "Quantity: " . $row["quantity"];
        echo "<br> Price per unit: ";
      }
    echo  $row["price"] . " <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/coinz.png'>";
    echo "<div class='description'><p>" . $row["description"] . "</p> </div>";
    if ($shopid != $_SESSION["userID"]) {
      echo "<form action='/petzbazaar/purchase.php' method='POST'>";
      echo "<input type='hidden' name='id' value=' ". $row["id"] . "'>";
      echo "<input type='hidden' name='seller' value='" . $_GET['seller_name'] . "'>";
      echo "<input class ='button' type='submit' value='Purchase' onclick=\"return confirmation('Do you really want to purchase this listing?')\">";
      echo "</form>";
    }    
    if ($myshop == true) {
      echo "<div class='style1'>";
      echo "<a href='/petzbazaar/removelisting.php?id=" . $row["id"] . "' class='button' onclick=\"return confirmation('Do you really want to delete this listing?')\"> X </a>";
      echo "</div>"; 
      
    }
    echo "</div>"  ;
    
  }
}  
  else {
    echo "</div>"  ;
  echo "There are no active shop listings.";
}  
?>

</div>



<?php
include("indexfooter.php");
?>