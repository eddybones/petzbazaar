<?php

session_start();
require('functions.php');
CheckLogin();

$title="Words of Wisdom";
include("indexheader.php");
$conn = DBConnect();
$wisdom = "";
$wisdomimage = "";
$wisdomcost = "";
$othergame = "";
function Getwoowoo($draw) {
    $wisdom = "";
    $wisdomimage = "";
    $sql="";
    if ($draw == 0){ 
        $sql = "UPDATE users Set coinz = coinz+2, wisdomdate = utc_date(), wisdomdraw = 0 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom = "<br>\"Life is a journey, not a destination.\" <br> You found 3 coinz!<br>"; 
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww0.png'/>";
    }
    else if ($draw == 1){
        $sql = "UPDATE users Set coinz = coinz+2, wisdomdate = utc_date() , wisdomdraw = 1 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom = "<br>\"Don't leave the world the way you found it.\" <br> You found 3 coinz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww1.png'/>";
    }
    else if ($draw == 2){
        $sql = "UPDATE users Set quartz = quartz+1, coinz = coinz-1, wisdomdate = utc_date(), wisdomdraw = 2 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom = "<br>\"Before you speak, ask yourself: Is it helpful? Is it necessary? Is it kind?\" <br> You found 1 quartz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww2.png'/>";
    }    
    else if ($draw == 3){
        $sql = "UPDATE users Set coinz = coinz+2, wisdomdate = utc_date(), wisdomdraw = 3 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Anxiety is a waste of imagination.\" <br> You found 3 coinz!<br>"; 
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww3.png'/>";
    }
    else if ($draw == 4){
        $sql = "UPDATE users Set coinz = coinz+2, wisdomdate = utc_date(), wisdomdraw = 4 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Don't judge a pet by its thumbnail image.\" <br> You found 3 coinz!<br>"; 
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww4.png'/>";
    }
    else if ($draw == 5){
        $sql = "UPDATE users Set coinz = coinz+1, wisdomdate = utc_date(), wisdomdraw = 5 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Pay no attention to criticism from someone you would never go to for advice.\" <br> You found 2 coinz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww5.png'/>";
    }
    else if ($draw == 6){
        $sql = "UPDATE users Set quartz = quartz+1, coinz = coinz-1, wisdomdate = utc_date(), wisdomdraw = 6 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"It's better to leave angry words unspoken than to mend a heart those words have broken.\" <br> You found 1 quartz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww6.png'/>";
    }
    else if ($draw == 7){
        $sql = "UPDATE users Set coinz = coinz+2, wisdomdate = utc_date(), wisdomdraw = 7 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Wherever you go, there you are.\" <br> You found 3 coinz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww7.png'/>";
    }
    else if ($draw == 8){
        $sql = "UPDATE users Set coinz = coinz+4, wisdomdate = utc_date(), wisdomdraw = 8 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Run your life as admin.\" <br> You found 5 coinz!<br>"; 
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww8.png'/>";
    }
    else if ($draw == 9){
        $sql = "UPDATE users Set coinz = coinz+2, wisdomdate = utc_date(), wisdomdraw = 9 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Youth and beauty are not synonyms.\" <br> You found 3 coinz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww9.png'/>";
    }
    else if ($draw == 10){
        $sql = "UPDATE users Set coinz = coinz+3, wisdomdate = utc_date(), wisdomdraw = 10 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"The only thing constant is change.\" <br> You found 4 coinz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww10.png'/>";
    }
    else if ($draw == 11){
        $sql = "UPDATE users Set coinz = coinz+1, wisdomdate = utc_date(), wisdomdraw = 11 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"You don't need a good memory if you always tell the truth.\" <br> You found 2 coinz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww11.png'/>";
    }
    else if ($draw == 12){
        $sql = "UPDATE users Set coinz = coinz+1, wisdomdate = utc_date(), wisdomdraw = 12 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Don't just wait to speak - listen.\" <br>You found 2 coinz!<br>"; 
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww12.png'/>";
    }
    else if ($draw == 13){
        $sql = "UPDATE users Set wisdomdate = utc_date(), wisdomdraw = 13 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"It's alright to change your mind.\" <br> You found 1 coinz!<br>"; 
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww13.png'/>";
    }
    else if ($draw == 14){
        $sql = "UPDATE users Set coinz = coinz+3, wisdomdate = utc_date(), wisdomdraw = 14 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Give yourself a balanced diet of food and treats.\" <br> You found 4 coinz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww14.png'/>";
    }
    else if ($draw == 15){
        $sql = "UPDATE users Set wisdomdate = utc_date(), wisdomdraw = 15 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Don't borrow trouble.\" <br> You found 1 coinz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww15.png'/>";
    }
    else if ($draw == 16){
        $sql = "UPDATE users Set coinz = coinz-1, wisdomdate = utc_date(), wisdomdraw = 16 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"If it doesn't smell good, don't eat it.\" <br> You found 0 coinz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww16.png'/>";
    }
    else if ($draw == 17){
        $sql = "UPDATE users Set quartz = quartz+1, coinz = coinz-1, wisdomdate = utc_date(), wisdomdraw = 17 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Shoot for the moon - even if you miss, you'll land among the stars!\" <br> You found 1 quartz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww17.png'/>";
    }
    else if ($draw == 18){
        $sql = "UPDATE users Set wisdomdate = utc_date(), wisdomdraw = 18 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Know the difference between problems and inconveniences.\" <br> You found 1 coinz!<br>"; 
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww18.png'/>";
    }
    else if ($draw == 19){
        $sql = "UPDATE users Set coinz = coinz+4, wisdomdate = utc_date(), wisdomdraw = 19 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Generosity brings its own rewards.\" <br> You found 5 coinz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww19.png'/>";
    }
    else if ($draw == 20){
        $sql = "UPDATE users Set coinz = coinz+3, wisdomdate = utc_date(), wisdomdraw = 20 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Remember to keep backups of your petz and other important files. A backup a day keeps disaster away!\" <br> You found 4 coinz!<br>";
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww20.png'/>";
    }
    else if ($draw == 21){
        $sql = "UPDATE users Set quartz = quartz+2, coinz = coinz+4, wisdomdate = utc_date(), wisdomdraw = 21 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $wisdom =  "<br>\"Build an amazing memory today.\" <br> You found 2 quartz and 5 coinz!<br>"; 
        $wisdomimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/ww21.png'/>";
    }
    return [
        "sql"=>$sql, "wisdom"=>$wisdom, "wisdomimage"=>$wisdomimage
    ];

}
?>


<img src="https://www.mythicsilence.com/malevolent/Images/bazaar/wisdombooth.png"><br>

Spend 1 coin to receive words of wisdom. This knowledge may result in 0-5 coinz or 1-2 quartz.
<br>  
<br>  

<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT coinz from users where id = " . $_SESSION["userID"];
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        if ($row["coinz"]==0) {
            $wisdomcost = "Sorry - you do not have enough coinz.";
        }
        else {
    $draw = rand(0, 21);
    $drawdata = Getwoowoo($draw);
    $sql = $drawdata["sql"];
    $wisdom = $drawdata["wisdom"];
    $wisdomimage = $drawdata["wisdomimage"];
    mysqli_query($conn, $sql);
}
}
$sql = "SELECT fortunedate, fortunedraw, wisdomdate, wisdomdraw FROM users Where id=". $_SESSION["userID"]; 
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    if ($row["fortunedate"] < gmdate("Y-m-d") && $row["wisdomdate"] < gmdate("Y-m-d")) {      //and row wisdomdate are yesterday 
        echo "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/untoldwisdom.png'>"; //wisdom untold image here
        echo "<br>";
        echo "<br>";
        echo "<form action='/petzbazaar/wisdomwords.php' method='POST'>";
        echo "<input class ='button' type='submit' value='Listen'>";
        echo "</form>";
    }
    if ($row["wisdomdate"] == gmdate("Y-m-d")) {
        $drawdata = Getwoowoo($row["wisdomdraw"]); //row wisdomdraw - the wisdom "card they received (Needs to be the untold graphic if they did the other game)
        $wisdom = $drawdata["wisdom"];  //the wisdom text
        $wisdomimage = $drawdata["wisdomimage"]; //the accompanying graphic
        }
    if ($row["fortunedate"] == gmdate("Y-m-d")) {
        $othergame = "You have already had a consultation today.";
    } 
}  //if fortune is untold, show cardback image, if told show result  - for their draw       
?>
 
<?php
echo $wisdomimage;
echo $wisdom;
echo $wisdomcost;
echo $othergame;
?>

<br>
<br>



<br>
<br>

<br>
<?php
include("indexfooter.php");
?>