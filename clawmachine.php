<?php

session_start();
require('functions.php');
CheckLogin();

$title = "Claw Machine";
include("indexheader.php");
$conn = DBConnect();
$petwin = "";
$petprize = "";
$petlose = "";

$mutations = mutations();
$muteIDs = muteids_array($mutations);
$muteConditionForWin = '';
$muteConditionForCount = '';
if(count($muteIDs) > 0) {
    $imploded = implode(',', $muteIDs);
    $muteConditionForWin = "id NOT IN ({$imploded}) AND";
    $muteConditionForCount = "AND userID NOT IN ({$imploded})";
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(array_key_exists('reset', $_POST)) {
        unset($_SESSION["clawclaim"]);
    } else {
        $sql = "SELECT coinz from users where id = " . $_SESSION["userID"];
        $result = mysqli_query($conn, $sql);
        $row = mysqli_fetch_assoc($result);
        if ($row["coinz"]==0) {
            $petlose = "Sorry - you do not have enough coinz.";
        }
        else {
            $sql = "UPDATE users SET Coinz = coinz-1 WHERE id = " . $_SESSION["userID"];
            $result = mysqli_query($conn, $sql);

            statClawPlay();

            $play = rand(1, 3);
            if($play >= 2) {
                $sql = "SELECT id, uniqueimage FROM clawmachine WHERE {$muteConditionForWin} claimed = 0 AND userID != " . $_SESSION['userID'] . " ORDER BY rand() limit 1";
                $result = mysqli_query($conn, $sql);
                if(mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_assoc($result);
                    $_SESSION["clawclaim"] = $row["id"];
                    $petwin = "You win!";
                    $petprize = "clawmachine/" . $row["uniqueimage"];
                } else {
                    // This shouldn't happen, but it could based on muting, maybe.
                    $petlose = "The claw machine got stuck. We're refunding your coinz.";
                    $sql = "UPDATE users SET coinz = coinz + 1 WHERE id = " . $_SESSION["userID"];
                    $result = mysqli_query($conn, $sql);

                    statClawRefund();
                }
            } else {
                $petlose = "You dropped the pet - try again?";
            }
        } 
    }
}

$sql = "SELECT count(*) as clawpetz FROM clawmachine WHERE claimed = 0 {$muteConditionForCount}";
$clawpetz = mysqli_query($conn, $sql);
$clawpetzresult = mysqli_fetch_assoc($clawpetz);

$sql = "SELECT count(*) as myclawpetz FROM clawmachine WHERE claimed = 0 AND userID = " . $_SESSION['userID'];
$result = mysqli_query($conn, $sql);
$myclawpetz = mysqli_fetch_assoc($result);
?>

<?php
if(array_key_exists('message', $_SESSION)) {
    echo "<div class='messagestyle'>" . $_SESSION["message"] . "</div>";
    unset($_SESSION["message"]);
}

if(array_key_exists('error', $_SESSION)) {
    echo "<div class='errorstyle'>" . $_SESSION["error"] . "</div>";
    unset($_SESSION["error"]);
}
?>


    <img src="https://www.mythicsilence.com/malevolent/Images/bazaar/clawmachine.png"><br>
    <br>
    Spend 1 coinz for a chance to grab a random pet.
    <br> Petz may be bred with OWs, require P5 breeds in P4, or need to be converted from P5.
    <br>
    <br>
    The claw machine currently contains <?php echo $clawpetzresult["clawpetz"] ?>  petz.
    <?php
    if($myclawpetz['myclawpetz'] > 0) {
        echo "<br>(" . $myclawpetz['myclawpetz'] . " petz donated by you. You will claw past your own donations.)";
    }
    ?>
    <br>
    <br>

<?php
if($clawpetzresult["clawpetz"] == 0) {
    echo "The claw machine is currently empty.";
} 
    else {
    if($clawpetzresult["clawpetz"] > $myclawpetz['myclawpetz']) {
    ?>
    <form action="/petzbazaar/clawmachine.php" method="POST">
        <input class="button" type="submit" value="Play">
    </form>
    <br>
    <?php
    }
}


if(strlen($petprize) != 0) {
    echo "<img src=$petprize> <br>";
    echo $petwin;
    echo "<br><br>";
    echo "<form action='/petzbazaar/clawmachineclaim.php' method='POST'>";
    echo "<input class ='button' name = 'claim' type='submit' value='Adopt Now'>";
    echo "</form>";
    echo "<br>";
    echo "<form action='/petzbazaar/clawmachine.php' method='POST'>"; //do we need to submit for returning the pet, or can they just not download it and the file stays?
    echo "<input class ='button' name = 'reset' type='submit' value='This Pet is Meant for Someone Else'>";
    echo "</form>";
} else {
    echo $petlose;
}
?>

    <br>
    <br>
    <br>
    Help keep the claw machine full!<br><br>
    <div class="style1">
    <a href="/petzbazaar/donation.php" class="button"> Donate</a>
</div>

<?php
include("indexfooter.php");
?>