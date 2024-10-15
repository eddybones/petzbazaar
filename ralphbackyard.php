<?php

session_start();
require('functions.php');
CheckLogin();

$title="Ralph's Backyard";
include("indexheader.php");
$conn = DBConnect();
$treasure = "";
$treasureimage = "";
$digmax = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $dig = rand(1, 100);
    if ($dig >= 1 && $dig <= 25){
            $sql = "UPDATE users Set Coinz = coinz+5, Digs = Digs+1, digdate= utc_date() Where digs < 5 and id=". $_SESSION["userID"];
            $result = mysqli_query($conn, $sql);   
            $treasure = "You found 5 coinz!<br>"; 
            $treasureimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ralphcoin.png'/>"; 
        
    }

    else if ($dig >= 26 && $dig <= 50){
            $sql = "UPDATE users Set quartz = quartz+1, Digs = Digs+1, digdate= utc_date() Where digs < 5 and id=". $_SESSION["userID"];
            $result = mysqli_query($conn, $sql);   
            $treasure = "You found 1 quartz!<br>"; 
            $treasureimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ralphquartz.png'/>"; 
         
    }  
    else if ($dig >= 51 && $dig <= 75){
            $sql = "UPDATE users Set Digs = Digs+1, digdate= utc_date() Where digs < 5 and id=". $_SESSION["userID"];
            $result = mysqli_query($conn, $sql);   
            $treasure = "You found Ralph's missing toy! He is very grateful.<br> <i>(You receive nothing)<br></i>"; 
            $treasureimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ralphtoy.png'/>"; 
        
            
        
    } 
    else if ($dig >= 76 && $dig <= 100){
            $sql = "UPDATE users Set Digs = Digs+1, digdate= utc_date() Where digs < 5 and id=". $_SESSION["userID"];
            $result = mysqli_query($conn, $sql);   
            $treasure = "You found Ralph's stashed cookies! He would share, but you probably don't want to eat those...<br><i>(You receive nothing)</i><br>"; 
            $treasureimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ralphtreat.png'/>"; 
        
    }  
}

?>
<img src="https://www.mythicsilence.com/malevolent/Images/bazaar/ralphbackyard.png"><br>
Welcome to Ralph's Backyard!<br>
Dig to unearth coinz, quartz, or a treat for Ralph! You may dig a maximum of 5 times per day.
<br>   
<br>



<?php
$sql = "SELECT digs, digdate FROM users Where id=". $_SESSION["userID"]; //form shows if digdate is yesterday OR if digs is less than 5. 
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $digs = $row["digs"];
    if ($row["digdate"] < gmdate("Y-m-d")) { 
        $sql= "UPDATE users set digs=0 Where id=". $_SESSION["userID"];
        $result = mysqli_query($conn, $sql); 
        $digs=0;       
    }
    if ($digs < 5) {
        echo "<form action='/petzbazaar/ralphbackyard.php' method='POST'>";
        echo "<input class ='button' type='submit' value='Dig'>";
        echo "</form>";
    }

    else {
            $digmax = "You have already completed 5 digs today.";
        }
} 

echo $treasureimage;
echo "<br>";
echo $treasure;
echo "<br>";
echo $digmax;
?>

<?php
include("indexfooter.php");
?>