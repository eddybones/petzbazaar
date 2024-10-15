<?php

// connects to DB. This function can be used anywhere.
function DBConnect() {
    $servername = "localhost";

    if(($_SERVER['SERVER_NAME'] ?? 'mythicsilence.com') == 'mythicsilence.com') {
        $DB = "";
        $username = "";
        $password = "";
    } else {
        $DB = "PBazaar";
        $username = "root";
        $password = "password";
    }

    // Create connection
    $conn = new mysqli($servername, $username, $password, $DB);

    // Check connection
    if($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    //echo "Connected successfully";
    //echo "<br>";
    return $conn;
}

function CheckLogin() {
    if(session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    if(array_key_exists('LoggedIn', $_SESSION) == false || $_SESSION["LoggedIn"] != 1) {
        $redirect = (array_key_exists('REDIRECT_URL', $_SERVER)) ? $_SERVER['REDIRECT_URL'] : '/petzbazaar/index.php';
        header('location: /petzbazaar/login.php?redirect=' . $redirect);
        die();
    }
}

//mega hashword X5
function hashPassword($pwd, $times = 5) {
    for($i = 0; $i < $times; ++$i) {
        $pwd = hash('sha256', $pwd);
    }
    return $pwd;
}

// Function to send a message
function sendMessage($parentID, $sender_id, $receiver_id, $message, $subject) {
    $mutations = mutations();
    if(!in_array($receiver_id, muteids_array($mutations))) {
        $conn = DBConnect();
        $sql = "CALL insertMessage(?, ?, ?, ?, ?)";
        $statement = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($statement, "iiiss", $parentID, $sender_id, $receiver_id, $message, $subject);
        mysqli_stmt_execute($statement);
    }
}

function mutations() {
    $sql = "SELECT
    (SELECT GROUP_CONCAT(mutee) FROM mute WHERE muter = {$_SESSION['userID']} AND mutee NOT IN (1,2)) AS PeopleIMute,
    (SELECT GROUP_CONCAT(muter) FROM mute WHERE mutee = {$_SESSION['userID']} AND muter NOT IN (1,2)) AS PeopleMutingMe;";
    $muteResult = mysqli_query(DBConnect(), $sql);
    $peopleIMute = [];
    $peopleMutingMe = [];
    if(mysqli_num_rows($muteResult)) {
        $row = mysqli_fetch_assoc($muteResult);
        if(trim($row['PeopleIMute'] ?? '') !== '') {
            $peopleIMute = explode(',', $row['PeopleIMute']);
        }
        if(trim($row['PeopleMutingMe'] ?? '') !== '') {
            $peopleMutingMe = explode(',', $row['PeopleMutingMe']);
        }
    }

    return ['peopleIMute' => $peopleIMute, 'peopleMutingMe' => $peopleMutingMe];
}

function muteids_string($mutations) {
    return implode(',', muteids_array($mutations));
}

function muteids_array($mutations) {
    return array_merge($mutations['peopleIMute'], $mutations['peopleMutingMe']);
}



function getStat(string $key) {
    $conn = DBConnect();
    $sql = "SELECT `value` FROM stats WHERE `key` = '" . $key ."'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) == 0) {
        return null;
    }
    return json_decode(mysqli_fetch_assoc($result)['value'], true);
}
function updateStat(array $stat, string $key) {
    $sql = "UPDATE stats SET `value` = '" . json_encode($stat) . "' WHERE `key` = '" . $key . "'";
    mysqli_query(DBConnect(), $sql);
}

const STAT_CLAW = 'clawmachine';
function statClawPlay() {
    $stat = getStat(STAT_CLAW);

    if($stat == null) {
        return;
    }

    ++$stat['plays'];
    ++$stat['coinz'];

    updateStat($stat, STAT_CLAW);
}
function statClawRefund() {
    $stat = getStat(STAT_CLAW);

    if($stat == null) {
        return;
    }

    --$stat['coinz'];

    updateStat($stat, STAT_CLAW);
}

const STAT_BOUTIQUE = 'boutique_purchases';
function statBoutiqueSale(int $quartz) {
    $stat = getStat(STAT_BOUTIQUE);

    if($stat == null) {
        return;
    }

    ++$stat['total_purchases'];
    $stat['total_quartz'] += $quartz;

    updateStat($stat, STAT_BOUTIQUE);
}

const STAT_COINZ = 'coinz_purchases';
function statCoinzSale(int $coinz) {
    $stat = getStat(STAT_COINZ);

    if($stat == null) {
        return;
    }

    ++$stat['total_purchases'];
    $stat['total_coinz_spent'] += $coinz;

    updateStat($stat, STAT_COINZ);
}