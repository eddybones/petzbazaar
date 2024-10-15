<?php

session_start();
require('functions.php');
CheckLogin();

$conn = DBConnect();
$title="Mute";
include("indexheader.php");

$message = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "DELETE FROM mute WHERE muter = " . $_SESSION['userID'];
    mysqli_query($conn, $sql);

    $muted = explode(',', $_POST['selected'] ?? '');
    if($muted[0] != "") {
        foreach($muted as $mute) {
            $sql = "INSERT INTO mute (muter, mutee) values (?, ?)";
            $statement = mysqli_prepare($conn, $sql);
            $mute = (int)$mute;
            mysqli_stmt_bind_param($statement, "ii", $_SESSION['userID'], $mute);
            mysqli_stmt_execute($statement);
        }
    }

    $message = 'Changes saved.';
}

$sql = "
SELECT id, username
FROM users
WHERE
    id NOT IN (
		SELECT muter FROM mute WHERE mutee = {$_SESSION['userID']}
    ) AND
    id NOT IN (
		SELECT mutee FROM mute WHERE muter = {$_SESSION['userID']}
    ) AND
	approved = 1 AND
    id NOT IN (1, 2, {$_SESSION['userID']})
ORDER BY username
";

$nonMutedResult = mysqli_query($conn, $sql);
$nonMuted = [];
if(mysqli_num_rows($nonMutedResult)) {
    $nonMuted = mysqli_fetch_all($nonMutedResult);
}

$sql = "
SELECT id, username
FROM users
INNER JOIN mute ON users.id = mute.mutee
WHERE
	mute.muter = {$_SESSION['userID']} AND
	mute.mutee IS NOT NULL AND
	approved = 1 AND
    id NOT IN (1, 2, {$_SESSION['userID']})
ORDER BY username
";
$mutedResult = mysqli_query($conn, $sql);
$muted = [];
if(mysqli_num_rows($mutedResult)) {
    $muted = mysqli_fetch_all($mutedResult);
}
?>

<script>
function shiftOptions(source, target) {
    let sourceElement = document.getElementById(source);
    let targetElement = document.getElementById(target);
    while(sourceElement.selectedOptions.length) {
        let sourceOption = sourceElement.selectedOptions[0];
        targetElement.appendChild(sourceOption.cloneNode(true));
        sourceOption.remove();
    }
}

function updateSelected() {
    let selected = document.getElementById('selected');
    let muted = document.getElementById('muted');
    muted.value = "";
    let toMute = [];
    for(let i = 0; i < muted.options.length; ++i) {
        toMute.push(muted.options[i].value);
    }
    selected.value = toMute.join(',');
}
</script>

<?php
if($message != "") {
    echo "<div class='messagestyle'>{$message}</div>";
}
?>
<h1> Mute List</h1><br>
Adding users to your mute list will hide your shop from them and their shop from you. It will also prevent messaging and filter claw machine donations. Please note that if you mute a user, they are still able to view and purchase items that you donate to the boutique.
<br><br>

<form action="/petzbazaar/mute.php" method="post">
    <div id="mute">

        <input type="hidden" name="selected" id="selected" value="">

        <div>
            <label for="nonmuted">Mutable</label><br>
            <select name="nonmuted[]" id="nonmuted" multiple="multiple">
            <?php
            foreach($nonMuted as $user) {
                echo "<option value=\"{$user[0]}\">{$user[1]}</option>";
            }
            ?>
            </select>
        </div>

        <div id="muteButtons">
            <button onclick="shiftOptions('nonmuted', 'muted'); updateSelected(); return false;">Mute &gt;</button><br>
            <button onclick="shiftOptions('muted', 'nonmuted'); updateSelected(); return false;">&lt; Unmute</button>
        </div>

        <div>
            <label for="muted">Muted</label><br>
            <select name="muted[]" id="muted" multiple="multiple">
            <?php
            foreach($muted as $user) {
                echo "<option value=\"{$user[0]}\">{$user[1]}</option>";
            }
            ?>
            </select>
        </div>

    </div>
    <br><br>
    <input type="submit" value="Save">
</form>

<?php
include("indexfooter.php");
?>