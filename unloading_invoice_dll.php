<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$userid = $_SESSION['SESS_MEMBER_ID'];
$username = $_SESSION['SESS_FIRST_NAME'];

$date = date("Y-m-d");
$time = date('H:i:s');

$id = $_GET['id'];

$result = $db->prepare("SELECT * FROM sales_list WHERE  invoice_no= :userid ");
$result->bindParam(':userid', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
	$qty = $row['qty'];
	$cod = $row['product_id'];
	$loading_id = $row['loading_id'];


	$sql = "UPDATE loading_list  SET qty_sold=qty_sold+? WHERE product_code=? AND loading_id=?";
	$q = $db->prepare($sql);
	$q->execute(array($qty, $cod, $loading_id));
}


$sql = "UPDATE sales  SET action=? WHERE invoice_number=?";
$q = $db->prepare($sql);
$q->execute(array(5, $id));


$sql = "UPDATE payment  SET action=?, dll=? WHERE invoice_no=?";
$q = $db->prepare($sql);
$q->execute(array(0, 1, $id));


$sql = "UPDATE sales_list  SET action=? WHERE invoice_no=?";
$q = $db->prepare($sql);
$q->execute(array(1, $id));

// get sales
$sales_id = 0;
$result = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id  ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
	$sales_id = $row['transaction_id'];
	$cus = $row['customer_id'];
	$amount = $row['amount'];
	$vat = $row['vat'];
	$value = $row['value'];
	$vat_no = $row['cus_vat_no'];
	$pay_type = $row['type'];
}

if ($pay_type == 'credit' || $pay_type == 'Credit') {
	//update customer balance
	$sql = "UPDATE customer SET balance = balance + ? WHERE customer_id = ?";
	$ql = $db->prepare($sql);
	$ql->execute(array($amount, $cus));

	//get customer balance
	$result = $db->prepare("SELECT * FROM customer WHERE customer_id = :id  ");
	$result->bindParam(':id', $cus);
	$result->execute();
	for ($i = 0; $row = $result->fetch(); $i++) {
		$cus_balance = $row['balance'];
	}

	// insert customer record
	$sql = "INSERT INTO customer_record (invoice_no,type,date,time,credit,balance,sales_id,customer_id,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?)";
	$ql = $db->prepare($sql);
	$ql->execute(array($id, 'credit', $date, $time, $amount, $cus_balance, $sales_id, $cus, $userid, $username));
}

$vat_id = 1;
//update vat amount
$sql = "UPDATE vat_account SET amount = amount - ? WHERE id = ?";
$ql = $db->prepare($sql);
$ql->execute(array($vat, $vat_id));

$vat_acc = '';
//get vat acc
$result = $db->prepare("SELECT * FROM vat_account WHERE id = :id ");
$result->bindParam(':id', $vat_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
	$vat_acc = $row['vat_no'];
}

// insert vat record
$sql = "INSERT INTO vat_record (invoice_no,type,date,time,record_type,acc_id,acc_no,vat,value,vat_no,user_name,user_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)";
$ql = $db->prepare($sql);
$ql->execute(array($id, 'Debit', $date, $time, 'invoice_delete', $vat_id, $vat_acc, $vat, $value, $vat_no, $username, $userid));
