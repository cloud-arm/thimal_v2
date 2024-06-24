<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$id = $_GET['id'];


$sql = "UPDATE customer  SET category=? WHERE category=?";
$q = $db->prepare($sql);
$q->execute(array(0, $id));

$sql = "DELETE FROM customer_category  WHERE id =?";
$ql = $db->prepare($sql);
$ql->execute(array($id));
