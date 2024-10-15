<?php
session_start();
require('functions.php');
$title="PW Reset";
include("indexheader.php");
require("emailfunctions.php");
$conn = DBConnect();
?>

<?php
$message = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "SELECT id, userhash, uuid() as token FROM users WHERE email = ?";
    $statement = mysqli_prepare($conn, $sql);
    $email = $_POST["email"];
    mysqli_stmt_bind_param($statement, "s", $email);
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    if (mysqli_num_rows($result) ==1) {
        $user = mysqli_fetch_assoc($result);
        $sql = "UPDATE users SET passwordresettoken ='" . $user["token"] . "', passwordresettokendate = utc_timestamp() WHERE id =" . $user["id"];
        mysqli_query($conn, $sql);
        sendEmail($_POST["email"], "Petz Bazaar - Password Reset", 'Please follow this link to reset your password: https://mythicsilence.com/petzbazaar/pwresetform.php?0=' . $user["userhash"] . '&1=' . $user["token"]);
        $message = "An email has been sent to reset your password.";
    }
}


?>

<?php
echo $message . "<br><br>";
?>

<form action="/petzbazaar/pwreset.php" method="POST">
<div>E-mail: </div><input type="text" name="email" required><br>

<input class ="button" type="submit" value="Reset Password">
</form>

<?php
include("indexfooter.php");
?>