<?php
session_start();
include('connect.php');

$id = $_POST['id'];
$name = $_POST['root_name'];
$area = $_POST['area'];

if ($id == 0) {
    // query
    $sql = "INSERT INTO root (root_name,root_area) VALUES (?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($name, $area));
} else {
    $sql = "UPDATE root SET root_name=?, root_area=?  WHERE root_id=?";
    $q = $db->prepare($sql);
    $q->execute(array($name, $area, $id));
}

header("location: root.php");
