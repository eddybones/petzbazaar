<?php
session_start();
require('functions.php');
$title="Register";
include("indexheader.php");
require("emailfunctions.php");
$conn = DBConnect();
?>

<?php
$username = "";
$email = "";
$refer = "";
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_POST["password"] != $_POST["passwordconfirm"]) {
        $passwordsmatch = false;
        }
        else {
            $passwordsmatch = true;
        }
    if (strlen($_POST["username"]) > 30) {
        $userlength = false;
        }    
        else {
            $userlength = true; 
            }
    $userchar = $_POST["username"];
    // Matches "A" through "Z", "a"  through "z", "0" through "9", "_", "-", "~", and "."
    $match = preg_match('/^[A-Za-z0-9_\-~.]+$/', $userchar);
    if($match === 1) {
        $usercharvalid = true;
    } else {
        $usercharvalid = false;
    }        
    $email = $_POST["email"]; //something to know it is a real email format. 
    $match = preg_match('/^[A-Za-z0-9_\-.@]+$/', $email);
    if($match === 1) {
        $validemail = true;
        }
        else {
            $validemail = false;
        }
    if (strlen(trim($_POST["refer"])) == 0) {
        $refer = false;
    }    
        else {
            $refer = true;
        }
    if ($passwordsmatch && $userlength && $validemail && $refer && $usercharvalid) {  
        $sql = "INSERT into users (username, email, password, userhash, refer, fortunedate, digdate, bonus, wisdomdate, spindate) VALUES (?, ?, ?, ?, ?, date_add(utc_date(), interval -1 day), date_add(utc_date(), interval -1 day), date_add(utc_date(), interval -1 day), date_add(utc_date(), interval -1 day), date_add(utc_date(), interval -1 day))"; 
        $statement = mysqli_prepare($conn, $sql);
        $username = trim($_POST["username"]);
        $email = trim($_POST["email"]);
        $password = hashPassword($_POST["password"]);
        $userhash = hash("joaat", $username);
        $reference = trim($_POST["refer"]);
        mysqli_stmt_bind_param($statement, "sssss", $username, $email, $password, $userhash, $reference);
        mysqli_stmt_execute($statement);
        sendEmail("mythicsilence55@gmail.com", "Account Awaiting Approval", "Go check it out.");
        echo "Thank you for registering - your account has been submitted for approval.";
        die();
    } 
    echo "<div>";
        if ($passwordsmatch == false) {
            echo "Passwords did not match.";
        }
        if ($userlength == false) {
            echo "Usernames cannot be more than 30 characters.";
        }
        if ($usercharvalid == false) {
            echo "Username contains invalid characters.";
        }
        if ($validemail == false) {
            echo "Please enter a valid email address.";
        }
        if ($refer == false) {
            echo "Please share how you learned about Petz Bazaar.";
        }
    echo "</div>";
    $username = $_POST["username"];
    $email = $_POST["email"];
    $refer = $_POST["refer"];
}

?>
<script>
    function validate() {
        let refer = document.getElementById('refer');
        if(refer.value.trim() == '') {
            refer.setCustomValidity('Please fill out this field.');
            refer.reportValidity();
            return false;
        }
        return true;
    }
</script>

New Here? <br><br>
<form action="/petzbazaar/register.php" method="POST" onsubmit="return validate()">
<div class="accountformdiv">Username: </div><input type="text" name="username" value="<?php echo $username; ?>" required><br> Note: Your username will be your shop name. <br>
<div class="accountformdiv">E-mail: </div><input type="text" name="email" value = "<?php echo $email; ?>" required><br>
<div class='accountformdiv'>Password: </div><input type="password" name="password" required><br>
<div class='accountformdiv'>Confirm Password: </div><input type="password" name="passwordconfirm" required><br><br>
<label for='refer'>How Did You Hear About Petz Bazaar?</label><br>
<textarea id="refer" name='refer' value = "<?php echo $refer; ?>" rows='4' cols='55' required></textarea><br><br>
<input class ="button" type="submit" value="Create Account">
</form>




<?php
include("indexfooter.php");
?>