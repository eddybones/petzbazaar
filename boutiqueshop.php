<?php

session_start();
require('functions.php');
CheckLogin();

$title="Boutique Shop";
include("indexheader.php");
$conn = DBConnect();
?>

<?php
if ($_SESSION["userID"] != 1) {
    die();
}

?>

<?php
// for uploading images for your shop.
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(array_key_exists('imageToUpload', $_FILES)) {
      $userId = $_SESSION["userID"]; 
      $uploadedFile = $_FILES['imageToUpload'];
      $originalImageName = $uploadedFile['name'];
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
      if ($uploadedFile["name"] != "") {
        $originalFileName = $uploadedFile['name'];
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
    
    }
  
    $sql = "INSERT INTO boutique_stock (sellerid, uniqueimage, uniquefilename, originalfilename, price, itemname, description, quantity, itemtype, purchased, stack) 
    Values (?, ?, ?, ?, ?, ?, ?, ?, ?, 0, ?)"; 
    $statement = mysqli_prepare($conn, $sql);
    $price = $_POST["price"];
    $itemname = $_POST["itemname"];
    $description = $_POST["description"];
    $sellerid = $_SESSION["userID"]; 
    $quantity= $_POST["quantity"];
    $itemtype= $_POST["itemtype"];
    $stack = 0;
    if ($quantity > 1) {
      $stack=1;
    }
    mysqli_stmt_bind_param($statement, "isssissiii", $sellerid, $uniqueImageName, $uniqueFileName, $originalFileName, $price, $itemname, $description, $quantity, $itemtype, $stack);
    mysqli_stmt_execute($statement);

}




?>

<?php
  if(array_key_exists('message', $_SESSION)) {
    echo "<div class='messagestyle'>" . $_SESSION["message"] . "</div>";
    unset($_SESSION["message"]);
  }

    echo "<div align='left'>";
    echo "<h1>Create a Boutique Shop Listing</h1>";
    echo "<form action='/petzbazaar/boutiqueshop.php' method='post' enctype='multipart/form-data'>";
    echo "<div class='formdiv'>Select image to upload: </div>";
    echo "<input type='file' name='imageToUpload' id='fileToUpload' required><br>";
    echo "<div class='formdiv'>Select file to upload: </div>";
    echo "<input type='file' name='fileToUpload' id='fileToUpload'>";
    echo "<br>";
    echo "<br>";
    echo "<label for='itemname'>Item Name: </label>";
    echo "<input type='text' id=itemname name=itemname required>";
    echo "<br>";
    echo "<br>";
    echo "<label for='itemtype'>Select an Item Type:</label>";
    echo "<select name='itemtype' id='itemtype' required>";
    echo "<option disabled selected value=''> Select an item type...</option>"; 
    echo "<option value='1'> 1 Hexed Petz </option>"; 
    echo "<option value='2'> 2 Bred Petz </option>"; 
    echo "<option value='3'> 3 Grab Bags </option>"; 
    echo "<option value='4'> 4 Toyz </option>"; 
    echo "<option value='5'> 5 Clothes </option>"; 
    echo "<option value='6'> 6 Shop Upgrades </option>";
    echo "<option value='7'> 7 Special Events/Misc </option></select><br><br>";
    echo "<label for='price'>Quantity: </label>";
    echo "<input type='number'  id='quantity' name='quantity' min='1' value='1' required> Adjust to sell multiple copies of an item in a single listing. Price is per unit.<br><br>";
    echo "<label for='description'>Description:</label>";
    echo "<br>";
    echo "<textarea id='description' placeholder='Describe your sale or auction here!' name='description' rows='4' cols='50'>";
    echo "</textarea>"; 
    echo "<br><br>";
    echo "<label for='price'>Price: </label>";
    echo "<input type='number'  id='price' name='price' min='5' value='5'required> (Minimum 5 Quartz)<br><br>";
    echo "<input class ='button' name = 'shoplisting' type='submit' value='Create Listing' name='submit'>";
    echo "</form></div><br><br>"; 
?>

<div id="grid">

<?php
$sql = "SELECT id, uniqueimage, price, itemname, description, sellerid, quantity, itemtype FROM boutique_stock Where sellerid=1 and purchased=0  ORDER BY itemtype";
$result = mysqli_query($conn, $sql);
$lastitemtype = 0;
if (mysqli_num_rows($result) > 0) {
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
    echo "<div class='style1'>";
    echo "<a href='/petzbazaar/boutique_removelisting.php?id=" . $row["id"] . "' class='button' onclick=\"return confirmation('Do you really want to delete this listing?')\"> X </a>";
    echo "</div>"; 
    echo "</div>"  ;
  }
}  
  else {
  echo "There are no active shop listings.";
}  
?>

</div>