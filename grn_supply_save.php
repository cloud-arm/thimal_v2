<?php
session_start();
include('connect.php');

$id = $_POST['id'];
$name = $_POST['name'];
$address = $_POST['address'];
$contact = $_POST['contact'];
$person = $_POST['person'];
$mobile = $_POST['mobile'];
$email = $_POST['email'];


if ($id == '0') {
    $sql = "INSERT INTO supplier (supplier_name,supplier_contact,supplier_address,contact_person,mobile,email,action) VALUES (?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($name, $contact, $address, $person, $mobile, $email, 1));
} else {
    $sql = "UPDATE  supplier SET supplier_name =?, supplier_contact =?, supplier_address =?, contact_person =?, mobile =?, email =? WHERE supplier_id =?";
    $ql = $db->prepare($sql);
    $ql->execute(array($name, $contact, $address, $person, $mobile, $email, $id));
}

header("location: grn_supply.php?id=0");
