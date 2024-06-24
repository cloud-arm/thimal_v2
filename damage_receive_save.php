<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$complain_no = $_POST['complain_no'];


$date = date('Y-m-d');

$type = 'clear';
$action = 'receive_yard';
$location = 'Yard';


//edit qty
$sql = "UPDATE damage  SET date = ?, action = ?,type = ?,location = ?, position = ? WHERE complain_no = ?";
$q = $db->prepare($sql);
$q->execute(array($date, $action, $type, $location, 3, $complain_no));


// query
$sql = "INSERT INTO damage_order (complain_no,location,date,type,action) VALUES (?,?,?,?,?)";
$q = $db->prepare($sql);
$q->execute(array($complain_no, $location, $date, $type, $action));

header("location: damage_view.php");
