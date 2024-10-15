<?php
require "functions.php";
$conn = DBConnect();
?>

<?php
// Close shops after 30 days of inactivity
$sql = "UPDATE users SET open = 0 WHERE last_login_date <= date_add(utc_timestamp(), interval -30 day)";
mysqli_query($conn, $sql);

// Delete messages older than 30 days
$sql="DELETE from messages WHERE timestamp <= date_add(utc_timestamp(), interval -30 day)";
mysqli_query($conn, $sql);

// Delete purchased items older than 30 days
$sql = "SELECT id, sellerid, uniqueimage, uniquefilename FROM shop_stock WHERE purchased = 1 and purchasedate <= date_add(utc_timestamp(), interval -30 day)";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result))  {
        $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'fileuploads' . DIRECTORY_SEPARATOR . $row["sellerid"] . DIRECTORY_SEPARATOR;
        unlink($targetFolder . $row["uniquefilename"] );
        $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'imageuploads' . DIRECTORY_SEPARATOR . $row["sellerid"] . DIRECTORY_SEPARATOR;
        unlink($targetFolder . $row["uniqueimage"] );
        $sql="DELETE from shop_stock WHERE id =" . $row["id"];
        mysqli_query($conn, $sql);
    }
}

// Delete boutique purchased items older than 30 days
$sql = "SELECT id, sellerid, uniqueimage, uniquefilename FROM boutique_stock WHERE purchased = 1 and purchasedate <= date_add(utc_timestamp(), interval -30 day)";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result))  {
        $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'fileuploads' . DIRECTORY_SEPARATOR . $row["sellerid"] . DIRECTORY_SEPARATOR;
        unlink($targetFolder . $row["uniquefilename"] );
        $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'imageuploads' . DIRECTORY_SEPARATOR . $row["sellerid"] . DIRECTORY_SEPARATOR;
        unlink($targetFolder . $row["uniqueimage"] );
        $sql="DELETE from boutique_stock WHERE id =" . $row["id"];
        mysqli_query($conn, $sql);
    }
}

// Delete claw machine wins after 7 days
$sql = "SELECT id, uniquefilename, uniqueimage FROM clawmachine WHERE claimed = 1 and claimdate <= date_add(utc_timestamp(), interval -7 day)";
$result = mysqli_query($conn, $sql);
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result))  {
        $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'clawmachine' . DIRECTORY_SEPARATOR;
        unlink($targetFolder . $row["uniquefilename"] );
        $targetFolder = __DIR__ . DIRECTORY_SEPARATOR . 'clawmachine' . DIRECTORY_SEPARATOR;
        unlink($targetFolder . $row["uniqueimage"] );
        $sql="DELETE from clawmachine WHERE id =" . $row["id"];
        mysqli_query($conn, $sql);
    }
}
