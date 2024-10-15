<?php
$conn = DBConnect();
?>
<!doctype html>
<html>

<head>
<script type="text/javascript" src="main.js"></script>
<title> <?php echo $title; ?> </title>
<link href="/petzbazaar/indexbazaar.css" type="text/css" rel="stylesheet">

<?php
$shopCSSID = '';
if ($_SERVER["PHP_SELF"] == "/petzbazaar/shop.php") {
	if(array_key_exists('seller_name', $_GET) == false) {
		$shopid = $_SESSION["userID"];
	}
	else {
		$sql = "SELECT id, username FROM users WHERE username = ?";
		$statement = mysqli_prepare($conn, $sql);
		$sellerName = $_GET['seller_name'];
		mysqli_stmt_bind_param($statement, "s", $sellerName);
		mysqli_stmt_execute($statement);
		$result = mysqli_stmt_get_result($statement);
		if(mysqli_num_rows($result) == 1) {
			$row = mysqli_fetch_assoc($result);
			$shopid = $row['id'];
            $shopCSSID = $row['username'];
		} else {
			$shopid = 0;
		}
	}

	if($shopid != 0) {
		$sql = "SELECT ifnull(customcss, '') as customcss from users WHERE id=?";
		$statement = mysqli_prepare($conn, $sql);
		mysqli_stmt_bind_param($statement, "i", $shopid);
		mysqli_stmt_execute($statement);
		$result = mysqli_stmt_get_result($statement);
		if (mysqli_num_rows($result) > 0) {
			$row = mysqli_fetch_assoc($result);
			if (strlen($row["customcss"]) > 0) {
				echo "<style>";
				echo $row["customcss"];
				echo "</style>";
			}
		}
	}
}
?>

</head>

<body>
<main>
	<header>
		<div>
<a href="/petzbazaar/index.php" id="home" ></a>
<div id="userdash">
	<?php
	if(array_key_exists('LoggedIn', $_SESSION) == true && $_SESSION["LoggedIn"] == 1) {
		echo "<p class='style2'>";
		echo "Welcome, " . $_SESSION["username"]."!";
		echo "</p>";
		$sql = "SELECT coinz, quartz, open FROM users WHERE id=". $_SESSION["userID"];
		$result = mysqli_query($conn, $sql);
		$sql = "SELECT count(*) as numunreadmessages FROM messages WHERE `read` = 0 and receiver_id =". $_SESSION["userID"];
		$newmessages = mysqli_query($conn, $sql);
		$newmessageresult = mysqli_fetch_assoc($newmessages);
		if (mysqli_num_rows($result) > 0) {
   	 		$row = mysqli_fetch_assoc($result);
   		 	echo "<img src='https://www.mythicsilence.com/malevolent/Images/bazaar/coinz.png'> "  . $row["coinz"].   "<span id='coinz'> </span> <img src='https://www.mythicsilence.com/malevolent/Images/bazaar/quartz.png'> "   . $row["quartz"].  "<span id='coinz'> </span>" ;
			echo "<span id='coinz' class='style1'><img src='https://www.mythicsilence.com/malevolent/Images/bazaar/message.png'> <a href=/petzbazaar/messages.php> (". $newmessageresult["numunreadmessages"] .")</a>  </span> ";
			if ($row["open"] ==1) {
				echo " Shop: Open <span id='coinz'></span>";
			}
			else {
				echo " Shop: Closed <span id='coinz'></span>";
			}
		}

		echo "<span id='coinz' class='style1'> <a href=\"/petzbazaar/logout.php\"> Log Out</a>  </span> ";
		// Prints the day, date, month, year, time, AM or PM
		echo "<span id='coinz'>" . gmdate("jS \of F h:i A") . "</span> ";
	}
	else {
		echo "<p class='style2'>";
		echo "Welcome, Guest!";
		echo "</p>";
		echo "<span id='coinz' class='style1'> Please <a href=/petzbazaar/login.php> Log In </a> or <a href=/petzbazaar/register.php> Register</a> </span>";
		echo "<span id='coinz'>" . gmdate("jS \of F h:i A") . "</span> ";
	}
?>

</div>
<div id="nav">

<a href="/petzbazaar/shop.php" id="shop" ></a>

<a href="/petzbazaar/account.php" id="account" ></a>

<a href="/petzbazaar/messages.php" id="messages"></a>

<a href="/petzbazaar/marketplace.php" id="marketplace" ></a>

<a href="/petzbazaar/auctionhouse.php" id="auctions" ></a>

<a href="/petzbazaar/boutique.php" id="boutique" ></a>

<a href="/petzbazaar/townsquare.php" id="townsquare" ></a>




</div>
</header>
	<content <?php if ($_SERVER["PHP_SELF"] == "/petzbazaar/index.php") {
		echo 'id="bulb"'; }
    if ($_SERVER["PHP_SELF"] == "/petzbazaar/townsquare.php") {
        echo 'id="post"';
	}
    if(strlen($shopCSSID) > 0) {
        echo 'id="' . $shopCSSID . '"';
    }
    ?>>