<?php
session_start();
require('functions.php');
CheckLogin();
$conn = DBConnect();


if($_SERVER["REQUEST_METHOD"] == "POST") {
    $sql = "UPDATE users SET open = ?, storefront = ?, customcss = ? WHERE id=" . $_SESSION["userID"];
    $statement = mysqli_prepare($conn, $sql);
    $storefront = $_POST["storefront"];
    $storecss = $_POST["customcss"];
    $open = $_POST["shopstatus"];
    mysqli_stmt_bind_param($statement, "iss", $open, $storefront, $storecss);
    mysqli_stmt_execute($statement);
}


$title="Account";
include("indexheader.php");
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

<div align="left">

<?php
$transactionz = 0;
$storecss = "";
$storehtml = "";
$shopselect = false;
$shopstatus = "";
echo "<h1>Welcome, " . $_SESSION["username"]."!</h1> <br>";
$sql = "SELECT coinz, quartz, open, transactionz, sales, storefront, customcss, shop_slots FROM users WHERE id =" . $_SESSION["userID"];
$result = mysqli_query(DBConnect(), $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result); 
    $storehtml= $row["storefront"];
    $storecss = $row["customcss"];
    $shopselect = $row["open"];
    $transactionz = $row["transactionz"];
    if ($row['open'] != 0) {
        $shopstatus = "<p style='color:green'>Shop Open!</p>";
    }
    else {
       $shopstatus =  "<p style='color:red'>Shop Closed!</p>";
    }
    echo "Total Shop Slots: ". $row["shop_slots"]. "<br><br>"; 
    echo "Complete <span style='color:green'>" . 10 - ($row["sales"] %10) . "</span> more sale(s) to earn 1 shop slot.<br><br>";
    echo "Complete <span style='color:green'>" . 10 - ($row["transactionz"] %10) . "</span> more transaction(s) to earn 1 quartz.<br><br>";
}

//Get totals for all time
$sql = "SELECT (SELECT COUNT(*) FROM ledger WHERE sellerid = ?) AS TotalSales, (SELECT COUNT(*) FROM ledger WHERE buyerid = ?) AS TotalPurchases";
$statement = mysqli_prepare($conn, $sql);
$buyerid = $_SESSION["userID"];
$sellerid =  $_SESSION["userID"];
mysqli_stmt_bind_param($statement, "ii", $buyerid, $sellerid);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
$Lifetotals = mysqli_fetch_assoc($result);

//Get total sales per month $sql = "SELECT COUNT(*) AS Sales, MONTH(purchasedate) AS `Month`, YEAR(purchasedate) AS `Year` FROM ledger WHERE sellerid = ? AND purchasedate >= DATE_ADD(UTC_TIMESTAMP(), INTERVAL -1 YEAR) GROUP BY YEAR(purchasedate), MONTH(purchasedate) ORDER BY YEAR(purchasedate) DESC, MONTH(purchasedate) DESC";
$sql = "SELECT COUNT(*) AS TotalSales FROM ledger WHERE sellerid = ? AND purchasedate >= DATE_ADD(UTC_TIMESTAMP(), INTERVAL -30 DAY)";
$statement = mysqli_prepare($conn, $sql);
$sellerid =  $_SESSION["userID"];
mysqli_stmt_bind_param($statement, "i", $sellerid);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
$Monthsales = mysqli_fetch_assoc($result);

//Get total purchases per month for the past full year $sql = "SELECT COUNT(*) AS Purchases, MONTH(purchasedate) AS `Month`, YEAR(purchasedate) AS `Year` FROM ledger WHERE buyerid = ? AND purchasedate >= DATE_ADD(UTC_TIMESTAMP(), INTERVAL -1 YEAR) GROUP BY YEAR(purchasedate), MONTH(purchasedate) ORDER BY YEAR(purchasedate) DESC, MONTH(purchasedate) DESC";
$sql = "SELECT COUNT(*) AS TotalPurchases FROM ledger WHERE buyerid = ? AND purchasedate >= DATE_ADD(UTC_TIMESTAMP(), INTERVAL -30 DAY)";
$statement = mysqli_prepare($conn, $sql);
$buyerid = $_SESSION["userID"];
mysqli_stmt_bind_param($statement, "i", $buyerid);
mysqli_stmt_execute($statement);
$result = mysqli_stmt_get_result($statement);
$Monthpurchases = mysqli_fetch_assoc($result);

//your top customers
$sql = "SELECT COUNT(*) AS Purchases, users.username  FROM ledger INNER JOIN users ON ledger.buyerid = users.id WHERE sellerid = ? GROUP BY ledger.buyerid ORDER BY COUNT(*) DESC LIMIT 3";
$statement = mysqli_prepare($conn, $sql);
$sellerid = $_SESSION["userID"];
mysqli_stmt_bind_param($statement, "i", $sellerid);
mysqli_stmt_execute($statement);
$Topcustomersresult = mysqli_stmt_get_result($statement);


//your favorite shops
$sql = "SELECT COUNT(*) AS Favorites, users.username  FROM ledger INNER JOIN users ON ledger.sellerid = users.id WHERE buyerid = ? GROUP BY ledger.sellerid ORDER BY COUNT(*) DESC LIMIT 3";
$statement = mysqli_prepare($conn, $sql);
$buyerid = $_SESSION["userID"];
mysqli_stmt_bind_param($statement, "i", $buyerid);
mysqli_stmt_execute($statement);
$Favoritesresult = mysqli_stmt_get_result($statement);

?>

Shop Status:
<form action="/petzbazaar/account.php" method="POST">
<input type= "radio" value ="1" id= "shopstatus" name="shopstatus" <?php if ($shopselect==true){
    echo "checked";
} ?>>  Open
<input type= "radio" value ="0" id= "shopstatus" name="shopstatus"<?php if ($shopselect==false){
    echo "checked";
} ?>>  Closed
<br>
<?php
//dingle dongle
echo $shopstatus;
?>
<br>
Your shop closes automatically if you have not logged in for 30 days. Open shops with active sales are listed in the marketplace.
<br>
<br>
<a href ="mute.php"> Additional Settings</a><br><br>
<a href ="donation.php"> Claw Machine Donations</a><br><br>
<a href ="currencytransfer.php"> Currency Transfer</a><br><br>



    
<br><br>
<table align="center" style="width:66%" cellspacing="0">
<tr><th colspan="3">Shop Statistics (Last 30 Days)</th></tr>
<td style='width:22%' align="left"> Total Sales: <?php echo $Monthsales["TotalSales"] ?></td>
<td style='width:22%%' align="left">Total Purchases: <?php echo $Monthpurchases["TotalPurchases"] ?> </td>
<td style='width:22%%' align="left">Total Transactions: <?php echo $Monthpurchases["TotalPurchases"] + $Monthsales["TotalSales"]; ?></td>
</table>
<br>
<table align="center" style="width:66%" cellspacing="0">
<tr><th colspan="3">Shop Statistics (Lifetime)</th></tr>
<tr>
<td style='width:22%' align="left"> Total Sales: <?php echo $Lifetotals["TotalSales"] ?> </td>
<td style='width:22%%' align="left">Total Purchases: <?php echo $Lifetotals["TotalPurchases"] ?> </td>
<td style='width:22%%' align="left">Total Transactions: <?php echo $transactionz ?></td>
</tr>
</table>
<br>
<table align="center" style="width:66%" cellspacing="0">
<tr><th colspan="3" align="left">Top Customers: <?php if (mysqli_num_rows($Topcustomersresult) > 0) {
    $customers = [];
    while ($row = mysqli_fetch_assoc($Topcustomersresult)) {
        $customers[] = $row["username"];
    }
    echo implode(", ", $customers);
}?></th></tr>
<tr><th colspan="3" align="left">Favorite Shops: <?php if (mysqli_num_rows($Favoritesresult) > 0) {
    $Favorites = [];
    while ($row = mysqli_fetch_assoc($Favoritesresult)) {
        $Favorites[] = $row["username"];
    }
    echo implode(", ", $Favorites);
 } ?></th></tr>
</table>
<br>





    <label for="description">Storefront Description/HTML:</label>
    <br>
    <textarea id="storefront" placeholder="Describe your shop, list any rules you have, add a banner image, etc." name="storefront" rows="15" cols="120"><?php echo $storehtml;?></textarea>
    <br>
    <br>
    <label for="description">Storefront CSS:</label>
    <br>
    <textarea id="customcss" placeholder="If you would like to customize the appearance and color scheme of your shop page, you may input your custom css styling here. Please only enter valid CSS in this field, and note that it will need to be properly written to display correctly and override the current CSS styling." name="customcss" rows="15" cols="120"><?php echo $storecss;?></textarea>
    <br>
    <br>
    <input class ="button" type="submit" value="Save Changes" name="submit">
    </form><br> 
</div>

<br>
<?php
include("indexfooter.php");
?>