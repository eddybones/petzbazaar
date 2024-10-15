<?php
session_start();
require('functions.php');
$title="Login";
include("indexheader.php");
?>

<?php 

if(array_key_exists('message', $_SESSION)) {
  echo "<div class='messagestyle'>" . $_SESSION["message"] . "</div>";
  unset($_SESSION["message"]);
}
?>

<form action="/petzbazaar/loginaction.php" method="POST">
  <input type="hidden" name="redirect" value="<?php if(array_key_exists('redirect', $_GET)) { echo $_GET['redirect']; } ?>">
  <div class='loginformdiv' align="right">Username: </div><input type="text" name="username"><br>
  <div class='loginformdiv'align="right">Password: </div><input type="password" name="password"><br>
  <input input class ="button" type="submit" value="Login">
</form>
<br>
<br>
Need to <a href="/petzbazaar/pwreset.php"> Reset Your Password </a>?<br>
<?php
include("indexfooter.php");
?>