<?php

session_start();
require('functions.php');
CheckLogin();
$title = "Donate";
include("indexheader.php");
$conn = DBConnect();
$postSizeError = false;
$imageerror = null;
$fileerror = null;
$message = null;

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $maxPostSize = (int)str_replace('M', '', ini_get('post_max_size'));
    // Convert to bytes
    $maxPostSize *= 1024 * 1024;
    if($_SERVER['CONTENT_LENGTH'] > $maxPostSize) {
        $postSizeError = true;
    }

    if(!$postSizeError) {
        if(array_key_exists('imageToUpload', $_FILES)) {
            $userId = $_SESSION["userID"];
            $uploadedFile = $_FILES['imageToUpload'];
            $originalImageName = $uploadedFile['name'];
            $ext = strtolower(pathinfo($originalImageName, PATHINFO_EXTENSION));
            // Check file size
            if($uploadedFile["size"] > 10485760) {
                $imageerror = "File is too large.";
            }
            // Allow certain file formats
            if($imageerror == null && $ext != "jpg" && $ext != "png" && $ext != "jpeg" && $ext != "gif" && $ext != "bmp") {
                $imageerror = "Sorry, only JPG, JPEG, PNG, BMP & GIF files are allowed.";
            }
            if($imageerror == null) {
                // Unique name concatenates MD5 hash of file contents, a hyphen, the current Unix timestamp, and the original extension
                $uniqueImageName = md5_file($uploadedFile['tmp_name']);
                $uniqueImageName .= '-';
                $uniqueImageName .= time();
                $uniqueImageName .= '.' . pathinfo($originalImageName, PATHINFO_EXTENSION);
                /*
                * __DIR__ gives us the directory that this PHP script is running in (for me, "D:\Code\phpstuff")
                * DIRECTORY_SEPARATOR gives us "\" on Windows or "/" on Unix/Linux/MacOS
                * The final result for me would be "D:\Code\phpstuff\uploaded_stuff\1\"
                */
                $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'clawmachine' . DIRECTORY_SEPARATOR;

                // If the folder we want to store stuff in does not exist, create it
                if(!is_dir($targetFolder)) {
                    mkdir($targetFolder);
                }
                // Move the uploaded file to the folder
                move_uploaded_file($uploadedFile['tmp_name'], $targetFolder . $uniqueImageName);
            }
        }
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(array_key_exists('fileToUpload', $_FILES)) {
                $userId = $_SESSION["userID"];
                $uploadedFile = $_FILES['fileToUpload'];
                $originalFileName = $uploadedFile['name'];
                $ext = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
                if($uploadedFile["size"] > 10485760) {
                    $fileerror = "File is too large. Max file size is 10MB.";
                }
                // Allow certain file formats
                if($imageerror == null && $ext != "pet") {
                    $imageerror = "Sorry, only .pet files are allowed.";
                }
                if($imageerror == null) {
                    // Unique name concatenates MD5 hash of file contents, a hyphen, the current Unix timestamp, and the original extension
                    $uniqueFileName = md5_file($uploadedFile['tmp_name']);
                    $uniqueFileName .= '-';
                    $uniqueFileName .= time();
                    $uniqueFileName .= '.' . pathinfo($originalFileName, PATHINFO_EXTENSION);
                    /*
                    * __DIR__ gives us the directory that this PHP script is running in (for me, "D:\Code\phpstuff")
                    * DIRECTORY_SEPARATOR gives us "\" on Windows or "/" on Unix/Linux/MacOS
                    * The final result for me would be "D:\Code\phpstuff\uploaded_stuff\1\"
                    */
                    $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'clawmachine' . DIRECTORY_SEPARATOR;

                    // If the folder we want to store stuff in does not exist, create it
                    if(!is_dir($targetFolder)) {
                        mkdir($targetFolder);
                    }
                    // Move the uploaded file to the folder
                    move_uploaded_file($uploadedFile['tmp_name'], $targetFolder . $uniqueFileName);
                }
            }
        }
        if($imageerror == null && $fileerror == null) {
            $sql = "INSERT INTO clawmachine (uniqueimage, uniquefilename, originalfilename, claimed, userID, donationdate) Values (?, ?, ?, 0, ?, utc_date())";
            $statement = mysqli_prepare($conn, $sql);
            $userID = $_SESSION["userID"];
            mysqli_stmt_bind_param($statement, "sssi", $uniqueImageName, $uniqueFileName, $originalFileName, $userID);
            mysqli_stmt_execute($statement);

            $sql = "UPDATE users SET clawdonations = clawdonations +1 WHERE id = " . $_SESSION["userID"];
            $result = mysqli_query($conn, $sql);

            $message = "Thank you for your donation!";
        }
    }
}
?>

<?php
if($message != null) {
    echo "<div class='messagestyle'>" . $message . "</div>";
}

if($postSizeError != null) {
    echo "<div class='errorstyle'>Uploaded files are too large to process.</div>";
}
if($imageerror != null) {
    echo "<div class='errorstyle'>" . $imageerror . "</div>";
}
if($fileerror != null) {
    echo "<div class='errorstyle'>" . $fileerror . "</div>";
}
if(array_key_exists('message', $_SESSION)) {
    echo "<div class='messagestyle'>" . $_SESSION["message"] . "</div>";
    unset($_SESSION["message"]);
}
?>


<h1> Your Current Donations: </h1>
<div id="grid">
    <?php
    $sql = "SELECT id, userID, uniqueimage, originalfilename, donationdate FROM clawmachine Where userID=? and claimed=0";
    $statement = mysqli_prepare($conn, $sql);
    $clawid = $_SESSION["userID"];
    mysqli_stmt_bind_param($statement, "i", $clawid); //is session userID
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    if(mysqli_num_rows($result) > 0) {
        // output donation data of each row
        while($row = mysqli_fetch_assoc($result)) {
            echo "<div class='item'>";
            echo "<div class='image'> <img src='/petzbazaar/clawmachine/" . $row["uniqueimage"] . "'/> </div>";
            echo "<h1>" . $row["originalfilename"] . "</h1>";
            echo "<div class='description'><p>" . date_format(date_create($row["donationdate"]), "m/d/Y") . "</p> </div>";
            echo "<div class='style1'>";
            echo "<a href='/petzbazaar/removeclawlisting.php?id=" . $row["id"] . "' class='button' onclick=\"return confirmation('Do you really want to delete this listing?')\"> X </a>";
            echo "</div>";
            echo "</div>";

        }
    } else {
        echo "</div>";
        echo "You have no unclaimed donations.";
    }
    ?>
</div>
<br>
<br>

Please note that the claw machine requires both the pet file and an image of the pet. <br>
If your donation was bred with an OW that uses external textures, listing their source in the pet's profile is appreciated!
<br><br>
<form action="/petzbazaar/donation.php" method="POST" enctype="multipart/form-data">
    <div class="formdiv">Select image to upload:</div>
    <input type='file' name='imageToUpload' id='imageToUpload' required><br><br>
    <div class="formdiv">Select file to upload:</div>
    <input type='file' name='fileToUpload' id='fileToUpload' required><br><br>

    <input class="button" type="submit" value="Donate">
</form>
<br>
<br>

<table align="center" style="width:66%" cellspacing="0">
    <tr>
        <th colspan="4">Claim History (7 Days)</th>
    </tr>
    <tr>
        <td style='width:20%' align='left'> Pet Name</td>
        <td style='width:20%' align='left'> Donation Date</td>
        <td style='width:20%' align='left'> Adopted By</td>
        <td style='width:20%' align='left'> Date Claimed</td>
    </tr>
    <?php


    $sql = "SELECT clawmachine.id, userID, originalfilename, donationdate, users.username, claimed, claimdate FROM clawmachine INNER JOIN users ON clawmachine.claimid= users.id Where userID=? and claimed=1";
    $statement = mysqli_prepare($conn, $sql);
    $clawid = $_SESSION["userID"];
    mysqli_stmt_bind_param($statement, "i", $clawid); //is session userID
    mysqli_stmt_execute($statement);
    $result = mysqli_stmt_get_result($statement);
    if(mysqli_num_rows($result) > 0) {
        // output donation data of each row
        while($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td style='width:20%' align='left'>" . $row["originalfilename"] . "</td>";
            echo "<td style='width:20%' align='left'>" . date_format(date_create($row["donationdate"]), "m/d/Y") . "</td>";
            echo "<td style='width:20%' align='left'>" . $row["username"] . "</td>";
            echo "<td style='width:20%' align='left'>" . date_format(date_create($row["claimdate"]), "m/d/Y") . "</td>";
            echo "</tr>";
        }
    }


    ?>



