<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");


$date = date("Y-m-d");
$time = date('H:i:s');

$userid = $_SESSION['SESS_MEMBER_ID'];
$username = $_SESSION['SESS_FIRST_NAME'];

$sales_id = $_POST['id'];

$result = $db->prepare("SELECT * FROM sales WHERE transaction_id = :id  ");
$result->bindParam(':id', $sales_id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $invoice = $row['invoice_number'];
    $amount = $row['amount'];
    $vat = $row['vat'];
    $value = $row['value'];
    $vat_no = $row['cus_vat_no'];
    $cus = $row['customer_id'];
    $pay_type = $row['type'];
}

$sql = "UPDATE sales SET  action=? WHERE transaction_id=?";
$ql = $db->prepare($sql);
$ql->execute(array(5, $sales_id));

$sql = "UPDATE sales_list SET  action=? WHERE invoice_no=?";
$ql = $db->prepare($sql);
$ql->execute(array(5, $invoice));


// inventory records -----------------------------
$result = $db->prepare("SELECT * FROM inventory WHERE invoice_no = :id ");
$result->bindParam(':id', $invoice);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $id = $row['product_id'];
    $name = $row['name'];
    $qty = $row['qty'];
    $sell = $row['sell'];
    $cost = $row['cost'];
    $qty_blc = $row['balance'];
    $st_id = $row['stock_id'];

    $st_qty = 0;
    $re = $db->prepare("SELECT * FROM stock WHERE id=:id  ");
    $re->bindParam(':id', $st_id);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $st_qty = $r['qty_balance'];
    }

    // update stock qty
    $sql = "UPDATE stock SET qty_balance = qty_balance + ? WHERE id = ?";
    $ql = $db->prepare($sql);
    $ql->execute(array($qty, $st_id));

    // set inventory record
    $sql = "INSERT INTO inventory (product_id,name,invoice_no,type,balance,qty,date,sell,cost,stock_id) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($id, $name, $invoice, 'in', $st_qty + $qty, $qty, $date, $sell, $cost, $st_id));

    // update product qty
    $sql = "UPDATE products SET qty = qty + ? WHERE product_id = ?";
    $ql = $db->prepare($sql);
    $ql->execute(array($qty, $id));
}


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
$ql->execute(array($invoice, 'credit', $date, $time, $amount, $cus_balance, $sales_id, $cus, $userid, $username));

//update reimbursement
$sql = "UPDATE reimbursement SET type = '', dll = ? WHERE invoice_no = ?";
$ql = $db->prepare($sql);
$ql->execute(array(1, $invoice));

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
$ql->execute(array($invoice, 'Debit', $date, $time, 'invoice_delete', $vat_id, $vat_acc, $vat, $value, $vat_no, $username, $userid));

//update reimbursement
$sql = "UPDATE payment SET action = ?, dll = ? WHERE invoice_no = ?";
$ql = $db->prepare($sql);
$ql->execute(array(11, 1, $invoice));


if ($pay_type == 'Cash') {
    // Account balancing -----------------
    $user_id = $_SESSION['SESS_MEMBER_ID'];
    $user_name = $_SESSION['SESS_FIRST_NAME'];

    $date = date("Y-m-d");
    $time = date('H:i:s');

    $cr_id = 1;
    $cr_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM cash WHERE id= :id ");
    $re->bindParam(':id', $cr_id);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $cr_name = $r['name'];
    }

    $cr_blc = $blc - $amount;

    $sql = "UPDATE  cash SET amount=amount-? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $cr_id));

    $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,loading_id,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('sales_delete', 'Debit', $invoice, 0, $amount, 0, 0, 'sales_cash', 'Sales', 0, 'sales_delete', 'Yard sales delete', $cr_id, $cr_blc, $date, $time, $user_id, $user_name));
}


if ($pay_type != 'Credit') {

    //update customer balance
    $sql = "UPDATE customer SET balance = balance - ? WHERE customer_id = ?";
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
    $sql = "INSERT INTO customer_record (invoice_no,type,date,time,debit,balance,sales_id,customer_id,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($invoice, 'debit', $date, $time, $amount, $cus_balance, $sales_id, $cus, $userid, $username));
}

$return = $_SESSION['SESS_BACK'];

header("location: $return");
