<?php

session_start();
require('functions.php');
CheckLogin();

$title="Town Square";
include("indexheader.php");
$conn = DBConnect();
?>

<div>      
Explore the local scene. New locations are likely to open as the marketplace grows. 
<br>
<br>

     <a href="/petzbazaar/ralphbackyard.php">Ralph's Backyard</a><br>
Dig for buried treasure with Ralph. Unearth coinz, quartz, or a treat for Ralph!

<br>
<br>

<a href="/petzbazaar/fortuneteller.php">The Fortune Teller</a><br>
See what's in the cards for you with a Tarot reading from Master Shagrath! 
<br>(Shares cooldown with Words of Wisdom)
<br>
<br>

<a href="/petzbazaar/wisdomwords.php">Words of Wisdom</a><br>
Visit Rag for thoughtful musings and advice. 
<br>(Shares cooldown with The Fortune Teller)
<br>
<br>

<a href="/petzbazaar/wheel.php">Jesse's Wheel of Wonders</a><br>
Jesse invites you to spin the prize wheel to claim quartz and coinz!
<br>
<br>

<a href="/petzbazaar/clawmachine.php">The Claw Machine</a><br>
Spend coinz for a chance to grab a new pet, or make a donation. <br>
Accepts OWs and P5 breeds. No external breedfiles. <br>
<br>
<br>
</div>





<br>
<?php
include("indexfooter.php");
?>