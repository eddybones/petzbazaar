<?php

session_start();
require('functions.php');
CheckLogin();

$title="Send Message Action";
include("indexheader.php");
$conn = DBConnect();
?>

<?php
// Function to send a message
$sender = $_SESSION["userID"];
$recipient = $_POST["user"];
$message = $_POST["message"];
$subject = $_POST["subject"];
$parentID = $_POST["parentID"];
sendMessage($parentID, $sender, $recipient, $message, $subject) ;
echo "Message Sent";

?>

<?php
include("indexfooter.php");
?>