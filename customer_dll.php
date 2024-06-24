<?php
session_start();
include('config.php');
date_default_timezone_set("Asia/Colombo");



$id = $_GET['id'];

$updateData = ["action" => 5];
update("customer", $updateData, "customer_id= '" . $id . "'");
