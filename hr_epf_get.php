<?php
include("connect.php");
date_default_timezone_set("Asia/Colombo");

$id = $_GET['id'];

$sql = "SELECT * FROM employee WHERE epf_no='$id';";

$result = $db->prepare($sql);
$result->bindParam(':userid', $date);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
   $r_id = $row['epf_no'];
}

if (isset($r_id)) {
   echo "1";
} else {
   echo "0";
}
