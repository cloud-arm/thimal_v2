<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$complain_no = $_POST['complain_no'];

$date = date('Y-m-d');

$location = 'Customer';
$type = 'complete';
$action = 'delivery_to_customer';


$result = $db->prepare("SELECT * FROM damage WHERE  complain_no=:id  ");
$result->bindParam(':id', $complain_no);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {

	$id = $row['product_id'];
}


$sql = "UPDATE products SET damage = damage - ? WHERE product_id = ?";
$q = $db->prepare($sql);
$q->execute(array(1, $id));


//edit qty
$sql = "UPDATE damage  SET date = ?, action = ?, type = ?, location = ?, position = ? WHERE complain_no = ?";
$q = $db->prepare($sql);
$q->execute(array($date, $action, $type, $location, 4, $complain_no));


// query
$sql = "INSERT INTO damage_order (complain_no,location,date,type,action) VALUES (?,?,?,?,?)";
$q = $db->prepare($sql);
$q->execute(array($complain_no, $location, $date, $type, $action));

header("location: damage_view.php");
