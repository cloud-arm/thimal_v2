<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$cus = $_POST['cus'];
$id = $_POST['id'];

$result = $db->prepare("SELECT * FROM customer_category WHERE id='$id' ");
$result->bindParam(':userid', $d);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $name = $row['name'];
}

$sql = "UPDATE customer  SET category=?,category_name=? WHERE customer_id=?";
$q = $db->prepare($sql);
$q->execute(array($id, $name, $cus));

header("location: customer_group.php?id=$id");
