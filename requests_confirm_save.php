<?php
session_start();
include('connect.php');

$id = $_POST['id'];
$action = $_POST['action'];

if ($action == 1) {
    $result = $db->prepare("SELECT * FROM requests_data WHERE id=:id ");
    $result->bindParam(":id", $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $type = $row['type'];
        $customer_id = $row['customer_id'];
    }

    if ($type == 'customer') {
    }
}

$sql = "UPDATE requests_data SET action=?  WHERE id=?";
$q = $db->prepare($sql);
$q->execute(array($action, $id));

header("location: requests_confirm.php");
