<?php

session_start();
require('functions.php');
CheckLogin();

$title="Messages";
include("indexheader.php");
$conn = DBConnect();
?>

<table align="center" style="width:66%" cellspacing="0">

<?php

$sql = "SELECT sender_id, parentID FROM messages WHERE id = ?";
$statement = mysqli_prepare($conn, $sql);
$id = (int)$_GET["id"];
mysqli_stmt_bind_param($statement, "i", $id);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);

$originalSender = 0;
$parentId = 0;
if(mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $originalSender = $row['sender_id'];
    $parentId = $row['parentID'];
}

$sql = "
SELECT m.parentID, subject, message, m.id, m.sender_id, timestamp, u.username 
FROM messages m
INNER JOIN users u ON m.sender_id = u.id
WHERE m.parentID = ?
ORDER BY m.id ASC;
";
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $parentId);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);

$firstSubject = "";
$replySubject = "";

if(mysqli_num_rows($result) > 0) {
    $firstRow = true;
    while($row = mysqli_fetch_assoc($result)) {
        if($firstRow) {
            echo "<table align='center' style='width:66%' cellspacing='0'>";
            echo "<tr>";
            echo "<td><h1>\"" . $row['subject'] . "\" with " . $row['username'];
            echo "</h1></td>";
            echo "</tr>";
            echo "</table><br>";

            $firstSubject = $row['subject'];
            $firstRow = false;
        }
        if($row['id'] == $id) {
            $style = 'background:#f5f6da';
        } else {
            $style = '';
        }
        echo "<table align='center' style='width:66%' cellspacing='0'>";
        echo "<tr>";
        echo "<td style='$style'><div align='left'> From: " . $row['username'] . " on " . date_format(new DateTime($row['timestamp']), "Y-m-d") . "</div>";
        echo "<br>" . $row['message'];
        echo "</td>";
        echo "</tr>";
        echo "</table><br>";

        $replySubject = $row['subject'];
    }
}
$sql = "UPDATE messages SET `read` = 1 WHERE id=?";
$statement = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($statement, "i", $id);
mysqli_stmt_execute($statement);
?>

<form action="/petzbazaar/sendmessage.php" method="post" enctype="multipart/form-data">
<input type="hidden" name="user" value="<?php echo $originalSender; ?>" />
<input type="hidden" name="parentID" value="<?php echo $parentId; ?>" />
<br>
<input type="text"  id="subject" name="subject" value ="Re: <?php echo $replySubject; ?>" required>
<br>
<textarea id="message" placeholder='Transcribe your missive here.'name="message" rows="6" cols="80" required>
</textarea> <br><br>
<input type="submit" class="button" value="Send Message" name="submit">
</form>

<?php
include("indexfooter.php");
?>