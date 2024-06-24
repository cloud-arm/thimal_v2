<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$complain_no = $_POST['complain_no'];
$location = $_POST['location'];
$lorry = $_POST['lorry'];

$date = date('Y-m-d');

$type = 'damage';
$action = 'sent_company';

$result = $db->prepare("SELECT * FROM lorry WHERE lorry_id=:id ");
$result->bindParam(':id', $lorry);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $lorry_no = $row['lorry_no'];
}

//edit qty
$sql = "UPDATE damage SET action = ?, date = ?, location = ?, lorry_id = ?, lorry_no = ?, position = ?  WHERE complain_no=?";
$q = $db->prepare($sql);
$q->execute(array($action, $date, $location, $lorry, $lorry_no, 2, $complain_no));

// query
$sql = "INSERT INTO damage_order (complain_no,location,date,type,action) VALUES (?,?,?,?,?)";
$q = $db->prepare($sql);
$q->execute(array($complain_no, $location, $date, $type, $action));

header("location: damage_view.php");
