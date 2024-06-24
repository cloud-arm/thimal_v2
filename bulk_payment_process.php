<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$date = date("Y-m-d");
$time = date('H:i:s');


$pay_id = $_POST['pay_id'];


$result = $db->prepare("SELECT * FROM payment WHERE  transaction_id=:id  ");
$result->bindParam(':id', $pay_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
  $chq_amount = $row['amount'];
  $pay_action = $row['action'];
  $chq_no = $row['chq_no'];
  $chq_date = $row['chq_date'];
  $chq_bank = $row['chq_bank'];
  $chq_no = $row['chq_no'];
  $bank = $row['bank_id'];
  $pay_type = $row['pay_type'];
  $id = $row['collection_id'];
  $credit_note = $row['credit_note_id'];
}

$result = $db->prepare("SELECT sum(pay_amount) FROM credit_payment WHERE  pay_id=:id AND action='2'  ");
$result->bindParam(':id', $pay_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
  $pay_tot = $row['sum(pay_amount)'];
}


if ($chq_amount == $pay_tot || $pay_type == 'credit_note') {

  $result = $db->prepare("SELECT * FROM credit_payment WHERE  pay_id='$pay_id' AND action='2' ");
  $result->bindParam(':id', $invo);
  $result->execute();
  for ($i = 0; $row = $result->fetch(); $i++) {
    $pay_amount = $row['pay_amount'];
    $tr_id = $row['tr_id'];
    $credit_id = $row['id'];
    $type = $row['type'];
    $id = $row['collection_id'];


    if ($type == "qb") {
      // code...
    } else {


      $sql = "UPDATE payment SET pay_amount = pay_amount + ?, credit_balance = credit_balance - ?, credit_pay_id = ? WHERE transaction_id = ? ";
      $q = $db->prepare($sql);
      $q->execute(array($pay_amount, $pay_amount, $pay_id, $tr_id));


      $res = $db->prepare("SELECT * FROM payment  WHERE transaction_id=:id   ");
      $res->bindParam(':id', $tr_id);
      $res->execute();
      for ($i = 0; $row1 = $res->fetch(); $i++) {
        $payment = $row1['pay_amount'];
        $amount = $row1['amount'];
        $loading_id = $row1['loading_id'];
        $sales_id = $row1['sales_id'];
        $customer_id = $row1['customer_id'];
        $invoice_no = $row1['invoice_no'];
      }

      $credit = $amount - $payment;

      if ($credit == 0) {
        $set_off = date('Y-m-d');

        $sql = "UPDATE payment SET set_off_date = ? WHERE transaction_id=?";
        $q = $db->prepare($sql);
        $q->execute(array($set_off, $tr_id));
      }


      $sql = "INSERT INTO payment (collection_id,pay_amount,amount,type,pay_type,date,chq_no,chq_date,chq_bank,sales_id,customer_id,pay_credit,action,loading_id,credit_balance,time,paycose,invoice_no,credit_pay_id,credit_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
      $q = $db->prepare($sql);
      $q->execute(array($id, $pay_amount, $pay_amount, 'credit_payment', 'credit_payment', $date, $chq_no, $chq_date, $chq_bank, $sales_id, $customer_id, 1, 1, $loading_id, $credit, $time, 'credit', $invoice_no, $pay_id, $tr_id));


      $set_off = date('Y-m-d');
      $action = 1;

      if ($type == "chq") {
        $action = 2;
      }

      $sql = "UPDATE payment SET pay_amount = pay_amount + ?, set_off_date = ?, action = ? WHERE transaction_id = ? ";
      $q = $db->prepare($sql);
      $q->execute(array($pay_amount, $set_off, $action, $pay_id));

      if ($type == "credit_note") {

        $sql = "UPDATE payment SET pay_amount = pay_amount + ?, credit_balance = credit_balance - ?, pay_date = ?, credit_pay_id = ? WHERE transaction_id = ? ";
        $q = $db->prepare($sql);
        $q->execute(array($pay_amount, $pay_amount, $set_off, $pay_id, $credit_note));
      } else {

        $user_id = $_SESSION['SESS_MEMBER_ID'];
        $user_name = $_SESSION['SESS_FIRST_NAME'];

        // get sales
        $sales_id = 0;
        $result1 = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id  ");
        $result1->bindParam(':id', $invoice_no);
        $result1->execute();
        for ($i = 0; $row1 = $result1->fetch(); $i++) {
          $sales_id = $row1['transaction_id'];
        }

        // get payment
        $pay = 0;
        $result1 = $db->prepare("SELECT * FROM payment WHERE invoice_no = :id ORDER BY `transaction_id` DESC LIMIT 1 ");
        $result1->bindParam(':id', $invoice_no);
        $result1->execute();
        for ($i = 0; $row1 = $result1->fetch(); $i++) {
          $pay = $row1['transaction_id'];
        }

        //update customer balance
        $sql = "UPDATE customer SET balance = balance + ? WHERE customer_id = ?";
        $ql = $db->prepare($sql);
        $ql->execute(array($pay_amount, $customer_id));

        //get customer balance
        $result1 = $db->prepare("SELECT * FROM customer WHERE customer_id = :id  ");
        $result1->bindParam(':id', $customer_id);
        $result1->execute();
        for ($i = 0; $row1 = $result1->fetch(); $i++) {
          $cus_balance = $row1['balance'];
        }

        // insert customer record
        $sql = "INSERT INTO customer_record (invoice_no,type,date,pay_type,chq_no,chq_date,time,credit,balance,sales_id,customer_id,user_id,user_name,pay_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array($invoice_no, 'credit', $date, $pay_type, $chq_no, $chq_date, $time, $pay_amount, $cus_balance, $sales_id, $customer_id, $user_id, $user_name, $pay));
      }
    }

    $sql = "UPDATE credit_payment SET action=? WHERE id=?";
    $q = $db->prepare($sql);
    $q->execute(array(0, $credit_id));
  }


  $sql = "UPDATE collection SET type=?  WHERE id=?";
  $q = $db->prepare($sql);
  $q->execute(array(2, $id));

  // Account balancing -----------------
  $user_id = $_SESSION['SESS_MEMBER_ID'];
  $user_name = $_SESSION['SESS_FIRST_NAME'];

  $date = date("Y-m-d");
  $time = date('H:i:s');

  $amount = $chq_amount;

  if ($pay_type == 'cash') {

    $cr_id = 2;
    $cr_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM cash WHERE id= :id ");
    $re->bindParam(':id', $cr_id);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
      $blc = $r['amount'];
      $cr_name = $r['name'];
    }

    $cr_blc = $blc + $amount;

    $sql = "UPDATE  cash SET amount=amount+? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $cr_id));

    $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('credit_collection', 'Credit', $pay_id, $amount, 0, $cr_id, 'credit_payment', $cr_name, $cr_blc, 'collection', 'Credit', 0, 0, $date, $time, $user_id, $user_name));
  }

  if ($pay_type == 'bank') {

    $mn_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM bank_balance WHERE id=:id ");
    $re->bindParam(':id', $bank);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
      $blc = $r['amount'];
      $de_name = $r['name'];
    }

    $mn_blc = $blc + $amount;

    $sql = "UPDATE  bank_balance SET amount=amount+? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $bank));

    $sql = "INSERT INTO bank_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('credit_collection', 'Credit', $pay_id, $amount, 0, $bank, 'credit_payment', $de_name, $mn_blc, 'collection', 'Credit', 0, 0, $date, $time, $user_id, $user_name));
  }
  //------------------------

  header("location: bulk_payment_print.php?id=$pay_id");
} else {

  header("location: bulk_payment.php?id=$pay_id&unit=2&error");
}
