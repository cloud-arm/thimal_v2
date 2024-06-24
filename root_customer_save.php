<?php
session_start();
include('connect.php');

$id = $_POST['id'];
$root = $_POST['root'];

$result = $db->prepare("SELECT * FROM root WHERE  root_id= :id ");
$result->bindParam(':id', $root);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $area = $row['root_area'];
    $root_name = $row['root_name'];
}

$sql = "UPDATE customer SET area=?, root=?, root_id=?  WHERE customer_id=?";
$q = $db->prepare($sql);
$q->execute(array($area, $root_name, $root, $id));


header("location: root_view.php?id=$root");
