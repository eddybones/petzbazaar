<?php
session_start();
require("functions.php");
if(array_key_exists('username', $_POST) == false || array_key_exists('password', $_POST) == false) {
    header('location: /petzbazaar/login.php');
    die();
}
$username = strtolower($_POST["username"]);
$password = $_POST["password"];
$hashword = hashPassword($password);
$redirect = $_POST['redirect'];
$conn = DBConnect();

// Prepare the query
$statement = mysqli_prepare($conn, "SELECT * FROM users WHERE lcase(username) = ? AND password = ?");

// Bind the parameters in order
mysqli_stmt_bind_param($statement, "ss", $username, $hashword);

// Run the query
mysqli_stmt_execute($statement);


$result = mysqli_stmt_get_result($statement);

if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    if ($user["approved"] != 1) {
        $_SESSION["message"] = "Your account is currently pending approval.";
            header('location: /petzbazaar/login.php');   
            die();
    }
    $_SESSION["LoggedIn"] = True;
    $_SESSION["userID"] =$user["id"];
    $_SESSION["username"] =$user["username"];
    $sql = "UPDATE users SET last_login_date = utc_timestamp() WHERE id=" . $_SESSION["userID"];
    mysqli_query($conn, $sql);
    if(strlen($redirect) > 0) {
        header('location: ' . $redirect);
    } else {
        header('location: /petzbazaar/index.php');
    }
    die();
    
}
else { header('location: /petzbazaar/login.php');
    die();
}



