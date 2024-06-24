<?php
session_start();
include('connect.php');

$id = $_GET['id'];

$sql = "UPDATE supply_payment SET dll=? WHERE id=?";
$ql = $db->prepare($sql);
$ql->execute(array(1, $id));
