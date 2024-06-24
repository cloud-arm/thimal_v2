<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$id = $_GET['id'];

$result = $db->prepare("DELETE FROM special_price WHERE  id= :id");
$result->bindParam(':id', $id);
$result->execute();

header("location: special_price.php");
