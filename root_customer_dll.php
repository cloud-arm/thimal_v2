<?php
session_start();
include('connect.php');

$id = $_GET['id'];


$sql = "UPDATE customer SET area=?, root=?, root_id=?  WHERE customer_id=?";
$q = $db->prepare($sql);
$q->execute(array('', '', 0, $id));
