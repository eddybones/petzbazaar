<?php

session_start();
require('functions.php');
CheckLogin();

$title="Fortune Teller";
include("indexheader.php");
$conn = DBConnect();
$fortune = "";
$fortuneimage = "";
$fortunecost = "";
$othergame = "";
function Getwoowoo($draw) {
    $fortune = "";
    $fortuneimage = "";
    $sql="";
    if ($draw == 0){
        $sql = "UPDATE users Set coinz = coinz+2, fortunedate = utc_date(), fortunedraw = 0 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune = "<br>\"Embark on a new adventure.\" <br> You found 3 coinz!<br>"; 
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/0Fool.png'/>";
    }
    else if ($draw == 1){
        $sql = "UPDATE users Set coinz = coinz+2, fortunedate = utc_date() , fortunedraw = 1 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune = "<br>\"You have the tools to succeed.\" <br> You found 3 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/1Magician.png'/>";
    }
    else if ($draw == 2){
        $sql = "UPDATE users Set quartz = quartz+1, coinz = coinz-1, fortunedate = utc_date(), fortunedraw = 2 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune = "<br>\"Knowledge gained is a secret revealed.\" <br> You found 1 quartz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/2HP.png'/>";
    }    
    else if ($draw == 3){
        $sql = "UPDATE users Set coinz = coinz+2, fortunedate = utc_date(), fortunedraw = 3 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Nurturing and leadership pair well together.\" <br> You found 3 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/3Empress.png'/>";
    }
    else if ($draw == 4){
        $sql = "UPDATE users Set coinz = coinz+2, fortunedate = utc_date(), fortunedraw = 4 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"With stability comes a sense of security.\" <br> You found 3 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/4Emperor.png'/>";
    }
    else if ($draw == 5){
        $sql = "UPDATE users Set coinz = coinz+1, fortunedate = utc_date(), fortunedraw = 5 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Open your mind to a new source of wisdom\" <br> You found 2 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/5Hierophant.png'/>";
    }
    else if ($draw == 6){
        $sql = "UPDATE users Set quartz = quartz+1, coinz = coinz-1, fortunedate = utc_date(), fortunedraw = 6 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Choose love.\" <br> You found 1 quartz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/6lovers1.png'/>";
    }
    else if ($draw == 7){
        $sql = "UPDATE users Set coinz = coinz+2, fortunedate = utc_date(), fortunedraw = 7 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"A focused effort brings you closer to your dreams.\" <br> You found 3 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/7Chariot.png'/>";
    }
    else if ($draw == 8){
        $sql = "UPDATE users Set coinz = coinz+4, fortunedate = utc_date(), fortunedraw = 8 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Your fortitude grows.\" <br> You found 5 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/8Strength.png'/>";
    }
    else if ($draw == 9){
        $sql = "UPDATE users Set coinz = coinz+2, fortunedate = utc_date(), fortunedraw = 9 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Enjoy your own good company.\" <br> You found 3 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/9Hermit.png'/>";
    }
    else if ($draw == 10){
        $sql = "UPDATE users Set coinz = coinz+3, fortunedate = utc_date(), fortunedraw = 10 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Luck always changes.\" <br> You found 4 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/10Wheel.png'/>";
    }
    else if ($draw == 11){
        $sql = "UPDATE users Set coinz = coinz+1, fortunedate = utc_date(), fortunedraw = 11 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Act without bias.\" <br> You found 2 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/11Justice.png'/>";
    }
    else if ($draw == 12){
        $sql = "UPDATE users Set coinz = coinz+1, fortunedate = utc_date(), fortunedraw = 12 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"A new perspective can be illuminating.\" <br>You found 2 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/12HangedOne.png'/>";
    }
    else if ($draw == 13){
        $sql = "UPDATE users Set fortunedate = utc_date(), fortunedraw = 13 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Let go of things that do not serve you.\" <br> You found 1 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/13Death.png'/>";
    }
    else if ($draw == 14){
        $sql = "UPDATE users Set coinz = coinz+3, fortunedate = utc_date(), fortunedraw = 14 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Art is alchemy.\" <br> You found 4 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/14Temperance.png'/>";
    }
    else if ($draw == 15){
        $sql = "UPDATE users Set fortunedate = utc_date(), fortunedraw = 15 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"We all have vices.\" <br> You found 1 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/15Devil.png'/>";
    }
    else if ($draw == 16){
        $sql = "UPDATE users Set coinz = coinz-1, fortunedate = utc_date(), fortunedraw = 16 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Challenge Accepted.\" <br> You found 0 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/16Tower.png'/>";
    }
    else if ($draw == 17){
        $sql = "UPDATE users Set quartz = quartz+1, coinz = coinz-1, fortunedate = utc_date(), fortunedraw = 17 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Hope guides you.\" <br> You found 1 quartz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/17Star.png'/>";
    }
    else if ($draw == 18){
        $sql = "UPDATE users Set fortunedate = utc_date(), fortunedraw = 18 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Things aren't always as they seem...\" <br> You found 1 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/18Moon.png'/>";
    }
    else if ($draw == 19){
        $sql = "UPDATE users Set coinz = coinz+4, fortunedate = utc_date(), fortunedraw = 19 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"Bask in a new day.\" <br> You found 5 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/19Sun.png'/>";
    }
    else if ($draw == 20){
        $sql = "UPDATE users Set coinz = coinz+3, fortunedate = utc_date(), fortunedraw = 20 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"A call to action.\" <br> You found 4 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/20Judgement.png'/>";
    }
    else if ($draw == 21){
        $sql = "UPDATE users Set quartz = quartz+2, coinz = coinz+4, fortunedate = utc_date(), fortunedraw = 21 Where wisdomdate != utc_date() and fortunedate != utc_date() and id=". $_SESSION["userID"];
        $fortune =  "<br>\"All things bright and beautiful\" <br> You found 2 quartz and 5 coinz!<br>";
        $fortuneimage = "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/21World.png'/>";
    }
    return [
        "sql"=>$sql, "fortune"=>$fortune, "fortuneimage"=>$fortuneimage
    ];

}
?>


<img src="https://www.mythicsilence.com/malevolent/Images/bazaar/shagrathfortunes.png"><br>

Spend 1 coin to have your fortune told. Fortunes may result in 0-5 coinz or 1-2 quartz.
<br>  
<br>  

<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT coinz from users where id = " . $_SESSION["userID"];
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        if ($row["coinz"]==0) {
            $fortunecost = "Sorry - you do not have enough coinz.";
        }
        else {
    $draw = rand(0, 21);
    $drawdata = Getwoowoo($draw);
    $sql = $drawdata["sql"];
    $fortune = $drawdata["fortune"];
    $fortuneimage = $drawdata["fortuneimage"];
    mysqli_query($conn, $sql);
}
}
$sql = "SELECT fortunedate, fortunedraw, wisdomdate, wisdomdraw FROM users Where id=". $_SESSION["userID"]; 
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    if ($row["fortunedate"] < gmdate("Y-m-d") && $row["wisdomdate"] < gmdate("Y-m-d")) {       
        echo "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/fortuneuntold.png'>";
        echo "<br>";
        echo "<br>";
        echo "<form action='/petzbazaar/fortuneteller.php' method='POST'>";
        echo "<input class ='button' type='submit' value='Draw a Card'>";
        echo "</form>";
    }
    if ($row["fortunedate"] == gmdate("Y-m-d")) {
        $drawdata = Getwoowoo($row["fortunedraw"]);
        $fortune = $drawdata["fortune"];
        $fortuneimage = $drawdata["fortuneimage"];
    } 
    if ($row["wisdomdate"] == gmdate("Y-m-d")) {
        $othergame = "You have already had a consultation today.";
    } 
}  //if fortune is untold, show cardback image, if told show result  - for their draw       
?>


 
<?php
echo $fortuneimage;
echo $fortune;
echo $fortunecost;
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