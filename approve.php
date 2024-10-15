<?php

session_start();
require('functions.php');
CheckLogin();

$title="Approve";
include("indexheader.php");
require("emailfunctions.php");
$conn = DBConnect();
?>

<?php
if ($_SESSION["userID"] != 1) {
    die();
}

?>

<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(array_key_exists("approve", $_POST)) {
        $sql = "UPDATE users SET approved = 1 WHERE id= ?";
        $statement = mysqli_prepare($conn, $sql);
        $id = $_POST["id"];
        mysqli_stmt_bind_param($statement, "i", $id);
        mysqli_stmt_execute($statement);
        sendEmail(trim($_POST['email']), 'Petz Bazaar - Account Approval', 'Your account has been approved! Log in here: https://mythicsilence.com/petzbazaar');
        echo "Account Approved.";
    }
    else {
        $sql = "DELETE from users WHERE id = ?";
        $statement = mysqli_prepare($conn, $sql);
        $id = $_POST["id"];
        mysqli_stmt_bind_param($statement, "i", $id);
        mysqli_stmt_execute($statement);
        echo "Account Deleted.";
    }
}    




$sql = "SELECT id, username, refer, email FROM users WHERE approved = 0";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
    echo "<h1>". $row["username"] . "</h1>";
    echo "<div>" . $row["refer"] . " </div>";
    echo "<form action='/petzbazaar/approve.php' method='POST'>";
    echo "<input type='hidden' name='id' value='". $row["id"] . "'>";
    echo "<input type='hidden' name='email' value='". $row["email"] . "'>";
    echo "<input class ='button' type='submit' name='approve' value='Approve'>";

    echo "</form><br>";
    echo "<form action='/petzbazaar/approve.php' method='POST'>";
    echo "<input type='hidden' name='id' value='". $row["id"] . "'>";
    echo "<input class ='button' type='submit' name='deny' value='Deny'>";
    echo "</form><br>";
         
    }
}       