<?php

session_start();
require('functions.php');
require("emailfunctions.php");
$conn = DBConnect();

if ($_POST["password"] != $_POST["passwordconfirm"]) {
    header('location: /petzbazaar/pwresetform.php?0=' . $_POST["0"] . "&1=" . $_POST["1"] . "&error=1");
    die();
}
$sql = "UPDATE users SET password = ?, passwordresettoken = null, passwordresettokendate = null WHERE id=? and userhash = ? and passwordresettoken = ?"; 
$statement = mysqli_prepare($conn, $sql);
$userhash = $_POST["0"];
$passwordresettoken = $_POST["1"];
$password = hashPassword($_POST["password"]);
$id = $_POST["id"]; 
mysqli_stmt_bind_param($statement, "siss", $password, $id, $userhash, $passwordresettoken);
mysqli_stmt_execute($statement);

$title = 'Password Reset';
include("indexheader.php");
echo "Password Reset Successfully. <br><br>";
echo "<a href=/petzbazaar/login.php> Log In </a>";

?>