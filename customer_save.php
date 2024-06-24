<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$id = $_POST['id'];
$name = $_POST['name'];
$address = $_POST['address'];
$contact = $_POST['contact'];
$root = $_POST['root'];
$area = $_POST['area'];
$type = $_POST['type'];


if ($id > 0) {
    $acc_name = $_POST['acc_name'];
    $acc_no = $_POST['acc_no'];
    $group = $_POST['group'];
    $credit = $_POST['credit'];
    $vat_no = $_POST['vat_no'];
    $whatsapp = $_POST['whatsapp'];
    $g12 = $_POST['g12'];
    $g5 = $_POST['g5'];
    $g2 = $_POST['g2'];
    $g37 = $_POST['g37'];

    $whtp = 0;
    if (isset($_POST['on_act'])) {
        $whtp = $_POST['on_act'];
    }
}

$result = $db->prepare("SELECT * FROM root WHERE  root_id= :id ");
$result->bindParam(':id', $root);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $root_name = $row['root_name'];
}

$type_name = '';
if ($type == 1) {
    $type_name = 'Channel';
}
if ($type == 2) {
    $type_name = 'Commercial';
}
if ($type == 3) {
    $type_name = 'Apartment';
}

if ($id == 0) {

    $sql = "INSERT INTO customer (customer_name,address,contact,area,root,root_id,type,type_name) VALUES (?,?,?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array($name, $address, $contact, $area, $root_name, $root, $type, $type_name));
} else {

    $sql = "UPDATE customer
        SET customer_name=?, address=?, contact=?, area=?, root=?, root_id=?, acc_name=?, acc_no=?, type=?, credit_period=?, category=?, price_12=?, price_2=?, price_5=?, price_37=?, vat_no=?, whatsapp=?, online_action=?,type_name=?
		WHERE customer_id=?";
    $q = $db->prepare($sql);
    $q->execute(array($name, $address, $contact, $area, $root_name, $root, $acc_name, $acc_no, $type, $credit, $group, $g12, $g2, $g5, $g37, $vat_no, $whatsapp, $whtp, $type_name, $id));

    $sql = "INSERT INTO update_record (update_table,table_id,date,time,user_id,action) VALUES (?,?,?,?,?,?)";
    $q = $db->prepare($sql);
    $q->execute(array('customer', $id, date('Y-m-d'), date('H.i.s'), $_SESSION['SESS_MEMBER_ID'], 'update'));
}

header("location: customer.php");
