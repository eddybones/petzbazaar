<?php
session_start();
require('functions.php');
$title="Petz Bazaar";
include("indexheader.php");
?>
 
<h1>Welcome to The Petz Bazaar!</h1> 
This site is respectfully inspired by its predecessor, <a href ="pugstribute.php"> PUGS</a>, operated by Midnightwolf.<br><br><br>

<?php if(array_key_exists('LoggedIn', $_SESSION) == true && $_SESSION["LoggedIn"] == 1) {
    ?>
Check out the <a href ="usersguide.php"> User's Guide</a> to learn about the site's functions and its rules.<br><br>



<div align="left">
<h1>Halloween Season Announcement</h1>
Spooky season is upon us! The thrills and chills of hexing for this time of year can veer into some NSFW
territory, and my goal is for folks to be able to share their creativity while also keeping the Bazaar a place
that is comfortable for all users. 
<br><br>
~ If you wish to sell an item on the Bazaar that is NSFW, please spoiler the shop listing image and provide
a link in the item description to view the uncensored item. This provides an extra buffer for your shop's patrons.
<br><br>
OR
<br><br>
~ If you are comfortable negotiating trades off of the Bazaar, you can exchange files on another platform, such
as discord, and use the currency transfer system to pay for the item here on the Bazaar.
<br><br>
I hope that these options are an agreeable compromise for keeping the Bazaar welcoming for folks of all
backgrounds without stifling artistic expression. Thank you for your cooperation and, as always, thanks for hanging out on the Petz Bazaar!
<br>
<br>


<h1>Petz Bazaar 2.1 Release Notes</h1>
Currency transfer has been added to the account page. This feature allows you to transfer coinz or quartz to other Bazaar users.  <p style='color:red'> Please utilize this feature for all currency transactions instead of targeted sales in your shop. 
The User's Guide has been updated to include currency transfer information and rules. Don't forget to check it out! <p style='color:red'></p>

<p style='color:green'>Please absolutely feel free to mention in your shop description that you have a "tip jar" or accept donations via the transfer system! I love the idea of tips and don't want to discourage folks from being generous to one another!</p>
<br>
<br>
Future Plans for the Petz Bazaar:<br>
~ Quality of life updates (ie editing shop listings)<br>
~ Auctions<br>
~ Lottery<br>
~ Additional games<br>
~ Badges/Achievements<br>
~ Limited Time Events/Surprise Features and Activities!<br>
<br>
</div>
<br><br>
The night sky is dressed in diamonds, and enchantment and wonder dance on the autumn breeze.  The sounds of merriment emanate from the town square. Perhaps you should go see what the 
excitement is all about?<br><br>
<img src="https://www.mythicsilence.com/malevolent/Images/bazaar/releasegraphic.png"/>

<br>
<br>

<?php
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql= "UPDATE users set coinz = coinz+5, bonus = utc_date() Where id = ". $_SESSION["userID"] . " and bonus != utc_date()";
    $result = mysqli_query($conn, $sql);   
}    


if(array_key_exists('LoggedIn', $_SESSION) == true && $_SESSION["LoggedIn"] == 1) {
    $sql = "SELECT bonus FROM users Where id=". $_SESSION["userID"]; 
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if ($row["bonus"] < gmdate("Y-m-d")) {    
            echo "<form action='/petzbazaar/index.php' method='POST'>";
            echo "<input class ='button' type='submit' value='Claim Daily Bonus Coinz'>";
            echo "</form>";
        } 
        else {
            echo "You have collected today's bonus! <i> (+ 5 Coinz) </i>";
        }   
    }   
}
else {
    echo "Please <a href=\"/petzbazaar/login.php\"> Log In </a> or <a href=\"/petzbazaar/register.php\"> Register </a>";
    }

?>


<br>
<br>


<?php
include("indexfooter.php");
?>

