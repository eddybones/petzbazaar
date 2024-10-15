<?php

session_start();
require('functions.php');
CheckLogin();

$title = "Send a Message";
include("indexheader.php");
$conn = DBConnect();
?>

    <form action="/petzbazaar/sendmessage.php" method="post" enctype="multipart/form-data">
        <label for="user">Select a Recipient:</label>
        <select name="user" id="user" required>
            <option disabled selected value=""> Select a recipient...</option>

            <?php
            $mutations = mutations();
            $muteIDs = muteids_string($mutations);
            $muteCondition = '';
            if($muteIDs != '') {
                $muteCondition = "WHERE id NOT IN ({$muteIDs})";
            }

            $sql = "SELECT id, username FROM users {$muteCondition} ORDER by username";
            $result = mysqli_query($conn, $sql);
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    echo "<option value=" . $row['id'] . ">" . $row['username'] . "</option> <br>";
                }
            } else {
                echo "There are no users.";
            }

            ?>
        </select>
        <br>
        <input type="text" id="subject" placeholder='Subject' name="subject" required>
        <input type="hidden" name="parentID" value="0"/>
        <br>
        <textarea id="message" placeholder='Transcribe your missive here.' name="message" rows="6" cols="80" required>
</textarea> <br>
        <input type="submit" class="button" value="Send Message" name="submit">
    </form>

    <br>
<?php
include("indexfooter.php");
?>