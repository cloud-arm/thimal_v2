<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$id = $_POST['id'];
$act = $_POST['act'];

$sql = "UPDATE employee SET action = ? WHERE id = ? ";
$q = $db->prepare($sql);
$q->execute(array($act, $id));

header("location: hr_employee.php");
