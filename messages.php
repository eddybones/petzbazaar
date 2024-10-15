<?php
session_start();
require('functions.php');
CheckLogin();
$conn = DBConnect();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    session_start();
    //id is the selected messages
    //error for no messages deleted (empty string)
    if ($_POST["deleteMessages"]=="") {
        $_SESSION["error"] = "No Messages Selected.";
        header('location: /petzbazaar/messages.php');
        die();   
    }
    else {
        $messageids = explode(",",$_POST["deleteMessages"]);
        foreach ($messageids as $id) {
            $sql = "DELETE from messages WHERE receiver_id =" . $_SESSION["userID"] . " and id =?";
            $statement = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($statement, "i", $id); 
            mysqli_stmt_execute($statement);
        }
        $_SESSION["message"] = "Message(s) Deleted.";
            header('location: /petzbazaar/messages.php');   
            die();
    }
   
}

$title="Messages";
include("indexheader.php");
?>




Send and receive messages here.<br>
Messages are automatically deleted after 30 days.
<br>
<br>
<?php
if(array_key_exists('message', $_SESSION)) {
  echo "<div class='messagestyle'>" . $_SESSION["message"] . "</div>";
  unset($_SESSION["message"]);
}

if(array_key_exists('error', $_SESSION)) {
  echo "<div class='errorstyle'>" . $_SESSION["error"] . "</div>";
  unset($_SESSION["error"]);
}
?>


<?php
$sql = "SELECT count(*) as numunreadmessages FROM messages WHERE `read` = 0 and receiver_id =". $_SESSION["userID"];
		$newmessages = mysqli_query($conn, $sql);
        if (mysqli_num_rows($newmessages) > 0) {
            $row = mysqli_fetch_assoc($newmessages);
            if ($row["numunreadmessages"] > 0) {
                echo "You have new messages!";
            }
        }
?>
<br>
<br>
<div class="style1">


<form action="/petzbazaar/messages.php" method="POST">
<a href="/petzbazaar/outbox.php" class="button"> Send Message</a> 
<span id='coinz'> </span>
     <input type="hidden"  name="deleteMessages" id="deleteMessages" value="">
        <input class ="button" type='submit' value="Delete Selected">
<br>
<br>
</div>

<table align="center" style="width:66%" cellspacing="0">
<tr><th colspan="3">Messages</th></tr>

<?php

$sql = "SELECT messages.*, users.username FROM messages INNER JOIN users ON messages.sender_id = users.id WHERE messages.receiver_id = ? ORDER BY timestamp DESC";
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $_SESSION["userID"]);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)){
        if ($row["read"] == true) {
            echo "<tr>"; 
        }
        else {
            echo "<tr class='unread'>";
        }    
        echo "<td style='width:10%'><input type='checkbox' value='". $row['id'] ."' onclick=\"return handleCheck(event)\"></td>";
        echo "<td style='width:90%'>  <a href='/petzbazaar/message.php?id={$row['id']}'>{$row['subject']}</a></td>";
        echo "<td>" . $row['username'] . "</td>";
    }
} 
else {
echo "No Messages"; 
}  


?>

</tr>
</table>
<br>

<?php                   
include("indexfooter.php");
?>