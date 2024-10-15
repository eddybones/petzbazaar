<?php

session_start();
require('functions.php');
CheckLogin();

$title="Boutique Donation Info";
include("indexheader.php");
$conn = DBConnect();
?>

<br>
<br>

Donations for the Petz Bazaar Boutique Shop are Currently OPEN.<br><br>
<div align="left">
Donation Information:<br><br>
~ The boutique stocks petzy items of all kinds. Bred petz, grabbags, hexies, brexies, playscenes, toyz, clothes, stamps - all are welcome!<br><br>
~ Donations to the boutique shop are final and may be purchased by any Petz Bazaar user.<br><br>
~ I like to receive donations on discord (Mythic Silence). If you do not use discord, send me a message here on the Petz Bazaar and we can coordinate via email.<br><br>
~ Please include the following with your donation - <br><ul>
<li> The pet or item file. If you wish to sell more than 1 copy of your donation, such as a stamp, please let me know how many you wish to sell.<br></li>
<li> An image of your donation for the shop listing.<br></li>
<li> Any information you would like included in the item's description. If you have a lot of notes, consider putting some of the information into a readme file to include with the item. Be sure to include your rules in the profile for donated petz!<br></li>
<li> Please let me know if you wish to be told who purchases your donations for your personal records.<br></li>
<li> Desired price (in quartz) for your donation. (The pricing guide below offers general price range suggestions based on the current economy. Average prices are likely to change as time passes.)<br></li>
</ul>
<br>
<br>

Pricing Guidelines for Popular Items:<br>
<ul>
<li> Stamps - 5 Quartz</li>
<li> Toys/Accessories - 15 Quartz </li>
<li> Grabbags (if containing 5 petz) - 15 Quartz</li>
<li> Bred Petz - 20 Quartz</li>
<li> Brexed Petz - 25 Quartz</li>
<li> Hexed Petz - 50+ Quartz</li>
</ul>
<br>
Please note that these prices are suggestions, and an appropriate price for your donation may differ.
<br>
<br>
</div>
Thank you for your interest in supporting the Petz Bazaar!


<?php
include("indexfooter.php");
?>