<?php

session_start();
require('functions.php');
CheckLogin();

$title="Jesse's Wheel of Wonders";
include("indexheader.php");
$conn = DBConnect();
$spinaward = "";
$spinimage = "";
$spinmax = "";
function rollvalue($spinroll) {
    if ($spinroll >=1 && $spinroll <=12) {   
        $spinaward = "You received the Spray Bottle!<br><i>(You win nothing)</i><br>"; 
        $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/lose.png'/>";    
    }
    else if ($spinroll >= 13 && $spinroll <= 31){  
        $spinaward = "You received 5 coinz!<br>"; 
        $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/5coinz.png'/>"; 
        
    }  
    else if ($spinroll >= 32 && $spinroll <= 47){    
        $spinaward = "You received 8 coinz!"; 
        $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/8coinz.png'/>"; 
    }    
    else if ($spinroll >= 48 && $spinroll <= 57){
        $spinaward = "You received 10 coinz!"; 
        $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/10coinz.png'/>";         
    } 
    else if ($spinroll >= 58 && $spinroll <= 66){
        $spinaward = "You received 15 coinz!"; 
        $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/15coinz.png'/>";         
        
    } 
    else if ($spinroll >= 67 && $spinroll <= 71){ 
        $spinaward = "You received 20 coinz!"; 
        $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/20coinz.png'/>";         
        
    } 
    else if ($spinroll >= 72 && $spinroll <= 86){
        $spinaward = "You received 1 quartz!"; 
        $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/1quartz.png'/>";         
        
    } 
    else if ($spinroll >= 87 && $spinroll <= 98){
        $spinaward = "You received 2 quartz!"; 
        $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/2quartz.png'/>";         
        
    }
    else if ($spinroll >= 99 && $spinroll <= 100){ 
        $spinaward = "You received 25 coinz and 2 quartz!"; 
        $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/jackpot.png'/>";         
        
    } 
    return [
        "spinaward"=>$spinaward, "spinimage"=>$spinimage
    ];
}

$sql = "SELECT spin, spindate, spinclaim, spinresult FROM users Where id=". $_SESSION["userID"];
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $spinprize = $row["spinclaim"];
    $spintries = $row["spin"];
    $spinday = $row["spindate"];
    $spinroll = $row["spinresult"];
    if ($row["spindate"] < gmdate("Y-m-d")) {
        $sql= "UPDATE users set spinclaim=0, spin=0, spinresult=0 Where id=". $_SESSION["userID"];
        $result = mysqli_query($conn, $sql); 
        $spinprize=0;   
        
    } 
    else if ($spinroll > 0) {
        $rollvalue = rollvalue($spinroll);
        extract($rollvalue);
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if(array_key_exists('spin1', $_POST) && $spinroll ==0) {
        $spinroll = rand(1, 100);
        $rollvalue = rollvalue($spinroll);
        extract($rollvalue);
        $sql = "UPDATE users Set spin = spin +1, spindate = utc_date(), spinresult =". $spinroll ." Where spin <= 1 and id=". $_SESSION["userID"];
        $result = mysqli_query($conn, $sql);   
    }

    if(array_key_exists('spin2', $_POST)) {
        //move the update query to the inside of the ifs and include the prize. ASK: Should it be "where spin <= 1 for these queries? or equals exactly 0 or 1?
        $sql = "SELECT spinresult from users WHERE spinclaim = 0 and id=". $_SESSION["userID"];
        $result = mysqli_query($conn, $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $spinroll = rand(1, 100);
            if ($spinroll >=1 && $spinroll <=8) {   
                $sql = "UPDATE users Set spin = spin +1, spinclaim = 1, spindate = utc_date(), spinresult =". $spinroll ." Where spin <= 1 and id=". $_SESSION["userID"];
                $spinaward = "You received the Spray Bottle!<br><i>(You receive nothing)</i><br>"; 
                $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/lose.png'/>";    
            }
            else if ($spinroll >= 9 && $spinroll <= 31){  
                $sql = "UPDATE users Set spin = spin +1, coinz = coinz +5, spindate = utc_date(), spinclaim = 1, spinresult =". $spinroll ."  Where spin <= 1 and id=". $_SESSION["userID"];
                $spinaward = "You received 5 coinz!<br>"; 
                $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/5coinz.png'/>"; 
                
            }  
            else if ($spinroll >= 32 && $spinroll <= 47){ 
                $sql = "UPDATE users Set spin = spin +1, coinz = coinz +8, spindate = utc_date(), spinclaim = 1, spinresult =". $spinroll ."  Where spin <= 1 and id=". $_SESSION["userID"];   
                $spinaward = "You received 8 coinz!"; 
                $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/8coinz.png'/>"; 
            }    
            else if ($spinroll >= 48 && $spinroll <= 57){
                $sql = "UPDATE users Set spin = spin +1, coinz = coinz +10, spindate = utc_date(), spinclaim = 1, spinresult =". $spinroll ."  Where spin <= 1 and id=". $_SESSION["userID"];
                $spinaward = "You received 10 coinz!"; 
                $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/10coinz.png'/>";         
            } 
            else if ($spinroll >= 58 && $spinroll <= 66){
                $sql = "UPDATE users Set spin = spin +1, coinz = coinz +15, spindate = utc_date(), spinclaim = 1, spinresult =". $spinroll ." Where spin <= 1 and id=". $_SESSION["userID"];
                $spinaward = "You received 15 coinz!"; 
                $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/15coinz.png'/>";         
                
            } 
            else if ($spinroll >= 67 && $spinroll <= 71){ 
                $sql = "UPDATE users Set spin = spin +1, coinz = coinz +20, spindate = utc_date(), spinclaim = 1, spinresult =". $spinroll ." Where spin <= 1 and id=". $_SESSION["userID"];
                $spinaward = "You received 20 coinz!"; 
                $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/20coinz.png'/>";         
                
            } 
            else if ($spinroll >= 72 && $spinroll <= 86){
                $sql = "UPDATE users Set spin = spin +1, quartz = quartz +1, spindate = utc_date(), spinclaim = 1, spinresult =". $spinroll ." Where spin <= 1 and id=". $_SESSION["userID"];
                $spinaward = "You received 1 quartz!"; 
                $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/1quartz.png'/>";         
                
            } 
            else if ($spinroll >= 87 && $spinroll <= 98){
                $sql = "UPDATE users Set spin = spin +1, quartz = quartz +2, spindate = utc_date(), spinclaim = 1,spinresult =". $spinroll ." Where spin <= 1 and id=". $_SESSION["userID"];
                $spinaward = "You received 2 quartz!"; 
                $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/2quartz.png'/>";         
                
            }
            else if ($spinroll >= 99 && $spinroll <= 100){ 
                $sql = "UPDATE users Set spin = spin +1, coinz = coinz +25, quartz = quartz+2, spindate = utc_date(), spinclaim = 1, spinresult =". $spinroll ." Where spin <= 1 and id=". $_SESSION["userID"];
                $spinaward = "You received 25 coinz and 2 quartz!"; 
                $spinimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/jackpot.png'/>";         
                
            }
            $result = mysqli_query($conn, $sql);
        }
    }
    
    if(array_key_exists('claim', $_POST)) {
        //select from users spinroll. Then if statement for each range and an update query to award the prize.
        $sql = "SELECT spinresult from users WHERE spinclaim = 0 and id=". $_SESSION["userID"];
        $result = mysqli_query($conn, $sql);
        $spinroll = $row["spinresult"];
        if ($spinroll >=1 && $spinroll <=12) {   
            $sql = "UPDATE users Set  spinclaim = 1, spindate = utc_date()  Where spin <= 1 and id=". $_SESSION["userID"];
        }
        else if ($spinroll >= 13 && $spinroll <= 31){  
            $sql = "UPDATE users Set coinz= coinz +5, spindate = utc_date(), spinclaim = 1  Where spin <= 1 and id=". $_SESSION["userID"];    
        }  
        else if ($spinroll >= 32 && $spinroll <= 47){ 
            $sql = "UPDATE users Set coinz = coinz +8, spindate = utc_date(), spinclaim = 1 Where spin <= 1 and id=". $_SESSION["userID"];     
        }    
        else if ($spinroll >= 48 && $spinroll <= 57){
            $sql = "UPDATE users Set  coinz = coinz +10, spindate = utc_date(), spinclaim = 1 Where spin <= 1 and id=". $_SESSION["userID"];      
        } 
        else if ($spinroll >= 58 && $spinroll <= 66){
            $sql = "UPDATE users Set  coinz = coinz +15, spindate = utc_date(), spinclaim = 1 Where spin <= 1 and id=". $_SESSION["userID"];
        } 
        else if ($spinroll >= 67 && $spinroll <= 71){ 
            $sql = "UPDATE users Set  coinz = coinz +20, spindate = utc_date(), spinclaim = 1 Where spin <= 1 and id=". $_SESSION["userID"];
        } 
        else if ($spinroll >= 72 && $spinroll <= 86){
            $sql = "UPDATE users Set  quartz = quartz +1, spindate = utc_date(), spinclaim = 1 Where spin <= 1 and id=". $_SESSION["userID"];
        } 
        else if ($spinroll >= 87 && $spinroll <= 98){
            $sql = "UPDATE users Set  quartz = quartz +2, spindate = utc_date(), spinclaim = 1 Where spin <= 1 and id=". $_SESSION["userID"];
        }
        else if ($spinroll >= 99 && $spinroll <= 100){ 
            $sql = "UPDATE users Set  coinz = coinz +25, quartz = quartz+2, spindate = utc_date(), spinclaim = 1 Where spin <= 1 and id=". $_SESSION["userID"];   
        }
        $result = mysqli_query($conn, $sql); 
        $spinaward = "Prize claimed!";    
    }
}

?>
<img src="https://www.mythicsilence.com/malevolent/Images/bazaar/wheelofwonders.png"><br>
Welcome to Jesse's Wheel of Wonders!<br>
Spin the wheel for a chance to earn coinz, quartz, or, on very rare occasions, a jackpot of both 25 coinz and 2 quartz.<br>
You may claim or reject the result of your first spin. If you reject your first result the second will be claimed automatically. You may claim one prize from the wheel per day.<br>
<br>   
<br>



<?php
echo $spinimage;
echo "<br>";
echo $spinaward;
echo "<br>";


$sql = "SELECT spin, spindate, spinclaim, spinresult FROM users Where id=". $_SESSION["userID"];
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $spinprize = $row["spinclaim"];
    $spintries = $row["spin"];
    $spinday = $row["spindate"];
    if ($row["spindate"] < gmdate("Y-m-d")) {
        $sql= "UPDATE users set spinclaim=0, spin=0, spinresult=0 Where id=". $_SESSION["userID"];
        $result = mysqli_query($conn, $sql); 
        $spinprize=0;       
    }
    if ($spintries ==0 && $spinprize ==0) {  //less than or equal to 1 //name attribute to id button. This lets you do certain actions in the post based on the button clicked
        echo "<form action='/petzbazaar/wheel.php' method='POST'>";
        echo "<button name='spin1' class='button'>Spin</button>";
        echo "</form>";
    }
    if ($spintries ==1 && $spinprize ==0) {  //claim or retry
        echo "<form action='/petzbazaar/wheel.php' method='POST'>";
        echo "<button name='spin2' class='button'>Spin Again</button>";
        echo "<br><br>OR<br><br>";
        echo "<button name='claim' class='button'>Claim Prize</button>";
        echo "</form>";
    }
    if ($spinprize ==1) {
        $spinmax = "You have already spun the wheel today.";
    }
} 


echo $spinmax;
?>

<?php
include("indexfooter.php");
?>