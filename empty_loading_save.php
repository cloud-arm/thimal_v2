<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$date = date("Y-m-d");
$time = date("h:i:sa");

$lorry_id = $_POST['lorry'];
$driver = $_POST['driver'];
$helper1 = $_POST['h1'];
$helper2 = $_POST['h2'];
$helper3 = $_POST['h3'];


$result = $db->prepare("SELECT * FROM lorry WHERE lorry_id= :id");
$result->bindParam(':id', $lorry_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $lorry = $row['lorry_no'];
}


$result = $db->prepare("SELECT * FROM user WHERE EmployeeId= :id");
$result->bindParam(':id', $driver);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $uid = $row['id'];
}

$result = $db->prepare("SELECT * FROM employee WHERE id= :id  ");
$result->bindParam(':id', $driver);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $dr_name = $row['name'];
}


$load = 0;
$result = $db->prepare("SELECT * FROM loading WHERE lorry_id= :id AND action='load' ");
$result->bindParam(':id', $lorry_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $load = $row['transaction_id'];
}

$type = 'purchases';
$action = "load";

$count = 0;

if ($driver > 0) {
    $count = $count + 1;
}

if ($helper1 > 0) {
    $count = $count + 1;
}

if ($helper2 > 0) {
    $count = $count + 1;
}

if ($helper3 > 0) {
    $count = $count + 1;
}

if ($load == 0) {

    // query
    $sql = "INSERT INTO loading (lorry_no,lorry_id,type,date,action,loading_time,driver,helper1,helper2,helper3,rep,emp_count) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($lorry, $lorry_id, $type, $date, $action, $time, $driver, $helper1, $helper2, $helper3, $dr_name, $count));


    $result = $db->prepare("SELECT * FROM loading WHERE lorry_id= :id AND action='load' ");
    $result->bindParam(':id', $lorry_id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $load = $row['transaction_id'];
    }

    $sql = "UPDATE lorry  SET action=?, loading_id=?, user_id=? WHERE lorry_id=?";
    $q = $db->prepare($sql);
    $q->execute(array($action, $load, $uid, $lorry_id));


    header("location: empty_loading_2.php?id=$load");
} else {
    $err = 1;
}
