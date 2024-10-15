<?php
session_start();
require('functions.php');
CheckLogin();
$conn = DBConnect();
$title="Currency Transfer";
include("indexheader.php");


if($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT coinz, quartz, username, (SELECT username from users WHERE id=?) as recipient_username from users WHERE id=" . $_SESSION["userID"];
    $statement = mysqli_prepare($conn, $sql);
    $recipient_userID = $_POST["user"];
    mysqli_stmt_bind_param($statement, "i", $recipient_userID);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        if (array_key_exists('coinzbutton', $_POST)){
            if ($_POST["coinz"] < 0) {
                $_SESSION["error"] = "Nice Try.";
                
            }
            else {
                if ($row["coinz"] >= $_POST["coinz"]){
                    $sql = "UPDATE users SET coinz = coinz -".$_POST["coinz"] ." WHERE id=" . $_SESSION["userID"];
                    $result = mysqli_query($conn, $sql);
                    $sql = "UPDATE users SET coinz = coinz +".$_POST["coinz"] ." WHERE id=" . $_POST["user"];
                    $result = mysqli_query($conn, $sql);
    
                    //sends message to recipient to inform them of transfer
                    $sender = $_SESSION["userID"];
                    $recipient = $_POST["user"];
                    $message = $row["username"] .  " has transferred coinz to your account!<br>Amount: ". $_POST['coinz'] . " coinz";
                    $subject = "Coinz Transfer";
                    sendMessage(0, $sender, $recipient, $message, $subject) ;

                     //sends message to sender to inform them of transfer
                     $sender = $_POST["user"];
                     $recipient = $_SESSION["userID"];
                     $message = "You have transferred coinz to ". $row["recipient_username"] . "!<br>Amount: ". $_POST['coinz'] . " coinz";
                     $subject = "Coinz Transfer Receipt";
                     sendMessage(0, $sender, $recipient, $message, $subject) ;
                     
                     $_SESSION["message"] = "Transaction Complete!";
                    
                }
                else {
                    $_SESSION["error"] = "You do not have enough coinz for this transaction.";
                    
                }
            }
            
        }       
        if (array_key_exists('quartzbutton', $_POST)){
            if ($_POST["quartz"] < 0) {
                $_SESSION["error"] = "Nice Try.";
                
            }
            else {
                if ($row["quartz"] >= $_POST["quartz"]) {
                    $sql = "UPDATE users SET quartz = quartz -".$_POST["quartz"] ." WHERE id=" . $_SESSION["userID"];
                    $result = mysqli_query($conn, $sql);
                    $sql = "UPDATE users SET quartz = quartz +".$_POST["quartz"] ." WHERE id=" . $_POST["user"];
                    $result = mysqli_query($conn, $sql);
    
                    //sends message to recipient to inform them of transfer
                    $sender = $_SESSION["userID"];
                    $recipient = $_POST["user"];
                    $message = $row["username"] .  " has transferred quartz to your account!<br>Amount: ". $_POST['quartz'] . " quartz";
                    $subject = "Quartz Transfer";
                    sendMessage(0, $sender, $recipient, $message, $subject) ;

                     //sends message to sender to inform them of transfer
                     $sender = $_POST["user"];
                     $recipient = $_SESSION["userID"];
                     $message = "You have transferred quartz to ". $row["recipient_username"] . "!<br>Amount: ". $_POST['quartz'] . " quartz";
                     $subject = "Quartz Transfer Receipt";
                     sendMessage(0, $sender, $recipient, $message, $subject) ;
                     
                    $_SESSION["message"] = "Transaction Complete!";
    
                    
                    
                }
                else {
                    $_SESSION["error"] = "You do not have enough quartz for this transaction.";
                    
                }

            }
            
        }
        
    }
}

if(array_key_exists('message', $_SESSION)) {
    echo "<div class='messagestyle'>" . $_SESSION["message"] . "</div>";
    unset($_SESSION["message"]);
}

if(array_key_exists('error', $_SESSION)) {
    echo "<div class='errorstyle'>" . $_SESSION["error"] . "</div>";
    unset($_SESSION["error"]);
}

?>
<div align="left">
<h1>Currency Transfer</h1><br>
<form action="/petzbazaar/currencytransfer.php" method="post" enctype="multipart/form-data">
        <label for="user">Select a Recipient:</label>
        <select name="user" id="user" required>
            <option disabled selected value=""> Select a recipient...</option>

            <?php 
            $mutations = mutations();
            $muteIDs = muteids_string($mutations);
            $muteCondition = '';
            if($muteIDs != '') {
                $muteCondition = "WHERE id NOT IN ({$muteIDs})";
            }

            $sql = "SELECT id, username FROM users {$muteCondition} ORDER by username";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<option value=" . $row['id'] . ">" . $row['username'] . "</option> <br>";
                }
            } else {
                echo "There are no users.";
            }
            ?>
        </select>
        <br>
        <br>
        <span style="width: 100px; display:inline-block;">
        <input type="number" style="width: 100%;" id="coinz" placeholder="0" name="coinz">
        </span>
        
        <input type="submit"  value="Transfer Coinz" name="coinzbutton">
       <br>
        <span style="width: 100px; display:inline-block;">
        <input type="number" style="width: 100%;"  id="quartz" placeholder="0" name="quartz">
        </span>
       
        <input type="submit"  value="Transfer Quartz" name="quartzbutton">
       
       
        
    </form>
        </div>