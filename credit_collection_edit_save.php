<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$date = date('Y-m-d');
$user_id = $_SESSION['SESS_MEMBER_ID'];
$user = $_SESSION['SESS_FIRST_NAME'];

$id = $_POST['id'];
$chq_no = $_POST['chq_no'];
$chq_date = $_POST['chq_date'];
$bank = $_POST['bank'];
$amount = $_POST['amount'];


$result = $db->prepare("SELECT * FROM collection WHERE id=:id  ");
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
  $loading_id = $row['loading_id'];
  $customer_id = $row['customer_id'];
  $amount_o = $row['amount'];
  $chq_no_o = $row['chq_no'];
  $chq_date_o = $row['chq_date'];
  $bank_o = $row['bank'];
}


//-------- Amount ------------//
if ($amount > 0) {

  $sql = "INSERT INTO payment_edit (tr_id,loading_id,user,user_id,date,colum,new,old) VALUES (?,?,?,?,?,?,?,?)";
  $q = $db->prepare($sql);
  $q->execute(array($id, $loading_id, $user, $user_id, $date, 'amount', $amount, $amount_o));

  $sql = "UPDATE payment SET amount=? WHERE collection_id=?";
  $q = $db->prepare($sql);
  $q->execute(array($amount, $id));

  $sql = "UPDATE collection SET amount=? WHERE id=?";
  $q = $db->prepare($sql);
  $q->execute(array($amount, $id));
}

//---------chq_no-----------//
if ($chq_no != "") {

  $sql = "INSERT INTO payment_edit (tr_id,loading_id,user,user_id,date,colum,new,old) VALUES (?,?,?,?,?,?,?,?)";
  $q = $db->prepare($sql);
  $q->execute(array($id, $loading_id, $user, $user_id, $date, 'chq_no', $chq_no, $chq_no_o));

  $sql = "UPDATE payment SET chq_no=? WHERE collection_id=?";
  $q = $db->prepare($sql);
  $q->execute(array($chq_no, $id));

  $sql = "UPDATE collection SET chq_no=? WHERE id=?";
  $q = $db->prepare($sql);
  $q->execute(array($chq_no, $id));
}

//----------- date ----------//
if ($chq_date != "") {

  $sql = "INSERT INTO payment_edit (tr_id,loading_id,user,user_id,date,colum,new,old) VALUES (?,?,?,?,?,?,?,?)";
  $q = $db->prepare($sql);
  $q->execute(array($id, $loading_id, $user, $user_id, $date, 'chq_date', $chq_date, $chq_date_o));

  $sql = "UPDATE payment SET chq_date=? WHERE collection_id=?";
  $q = $db->prepare($sql);
  $q->execute(array($chq_date, $id));

  $sql = "UPDATE collection SET chq_date=? WHERE id=?";
  $q = $db->prepare($sql);
  $q->execute(array($chq_date, $id));
}

//-------- Bank ------------//
if ($bank != "") {

  $sql = "INSERT INTO payment_edit (tr_id,loading_id,user,user_id,date,colum,new,old) VALUES (?,?,?,?,?,?,?,?)";
  $q = $db->prepare($sql);
  $q->execute(array($id, $loading_id, $user, $user_id, $date, 'bank', $bank, $bank_o));

  $sql = "UPDATE payment SET chq_bank=? WHERE collection_id=?";
  $q = $db->prepare($sql);
  $q->execute(array($bank, $id));

  $sql = "UPDATE collection SET bank=?  WHERE id=?";
  $q = $db->prepare($sql);
  $q->execute(array($bank, $id));
}

header("location: credit_collection.php");
