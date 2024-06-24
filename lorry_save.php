<?php
session_start();
date_default_timezone_set("Asia/Colombo");
include('connect.php');

$id = $_POST['id'];
$lorry_no = $_POST['lorry_no'];
$driver = $_POST['driver'];

$action = 'unload';


$name = '';
$result = $db->prepare("SELECT  * FROM employee WHERE id = :id  ");
$result->bindParam(':id', $driver);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $name = $row['name'];
}

$user = 0;
$result = $db->prepare("SELECT  * FROM user WHERE EmployeeId = :id  ");
$result->bindParam(':id', $driver);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $user = $row['id'];
}

if ($id == 0) {
    // query
    $sql = "INSERT INTO lorry (lorry_no,driver,driver_id,action,user_id) VALUES (?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($lorry_no, $name, $driver, $action, $user));
} else {
}

header("location: lorry.php");
