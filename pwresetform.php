<?php

session_start();
require('functions.php');

$title="PW Reset Form";
include("indexheader.php");
$conn = DBConnect();
?>

<?php 

if(array_key_exists('0', $_GET) == false || array_key_exists('1', $_GET) == false) {
    die();
  }
$sql = "SELECT id from users WHERE userhash = ? and passwordresettoken = ?";
$statement = mysqli_prepare($conn, $sql);
$userhash = $_GET["0"];
$passwordresettoken = $_GET["1"];
mysqli_stmt_bind_param($statement, "ss", $userhash, $passwordresettoken);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);  
if (mysqli_num_rows($result) != 1) {
  die();
}
$row = mysqli_fetch_assoc($result);    

if(array_key_exists('error', $_GET)) {
    echo "<div class='errorstyle'> Passwords do not match. </div>";
  }

?>

<form action="/petzbazaar/pwresetaction.php" method="POST">
    <input type="hidden" name="id" value="<?php echo $row["id"];?>"> 
    <input type="hidden" name="0" value="<?php echo $_GET["0"];?>"> 
    <input type="hidden" name="1" value="<?php echo $_GET["1"];?>"> 
<div class='accountformdiv'>Password: </div><input type="password" name="password" required><br>
<div class='accountformdiv'>Confirm Password: </div><input type="password" name="passwordconfirm"required><br><br>
<input class ="button" type="submit" value="Reset Password">
</form>