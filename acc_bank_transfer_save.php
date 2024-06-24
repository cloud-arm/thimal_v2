<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");

$user_id = $_SESSION['SESS_MEMBER_ID'];
$user_name = $_SESSION['SESS_FIRST_NAME'];

$type = $_POST['type'];

$date = date("Y-m-d");
$time = date('H:i:s');

if ($type == 'deposit') {

    $amount = $_POST['amount'];
    $bank = $_POST['bank'];
    $acc_no = $_POST['cash'];

    $load =  0;
    if ($acc_no == 2) {
        $load =  $_POST['load'];

        $result = $db->prepare("SELECT * FROM loading WHERE  transaction_id=:id ");
        $result->bindParam(':id', $load);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $amount = $row['cash_total'];
        }

        $sql = "UPDATE  loading SET bank_action = ?, bank_date = ? WHERE transaction_id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array(1, $date, $load));
    }

    $mn_blc = 0;
    $b_blc = 0;
    $re = $db->prepare("SELECT * FROM bank_balance WHERE id=:id ");
    $re->bindParam(':id', $bank);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $b_blc = $r['amount'];
        $de_name = $r['name'];
    }

    $cr_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM cash WHERE id=:id ");
    $re->bindParam(':id', $acc_no);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $cr_name = $r['name'];
    }

    $cr_blc = $blc - $amount;

    $mn_blc = $b_blc + $amount;

    $sql = "UPDATE  cash SET amount=amount-? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $acc_no));

    $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,loading_id,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('cash_deposit', 'Debit', $bank, $load, $amount, 0,  0, 'cash_deposit', 'Bank Deposit', 0, 'bank_transfer', $cr_name, $acc_no, $cr_blc, $date, $time, $user_id, $user_name));

    $sql = "UPDATE  bank_balance SET amount=amount+? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $bank));

    $sql = "INSERT INTO bank_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('cash_deposit', 'Credit', $acc_no, $amount, 0, $bank, 'cash_deposit', $de_name, $mn_blc, 'bank_transfer', 'Cash', 0, 0, $date, $time, $user_id, $user_name));

    header("location: acc_bank_transfer.php");
} else

if ($type == 'chq') {

    $id = $_POST['id'];
    $bank = $_POST['bank'];

    $re = $db->prepare("SELECT * FROM bank_balance WHERE id =:id ");
    $re->bindParam(':id', $bank);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $bank_name = $r['name'];
    }

    $sql = "UPDATE  payment SET chq_action = ?, deposit_date = ?, bank_id = ?, bank_name = ?, memo = ? WHERE transaction_id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array(1, $date, $bank, $bank_name, 'chq_deposited', $id));

    echo $id;
}

if ($type == 'dep_realize') {

    $id = $_POST['id'];

    $re = $db->prepare("SELECT * FROM payment WHERE transaction_id = :id ");
    $re->bindParam(':id', $id);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $chq_no = $r['chq_no'];
        $chq_date = $r['chq_date'];
        $chq_bank = $r['chq_bank'];
        $amount = $r['amount'];
        $bank = $r['bank_id'];
    }

    $sql = "UPDATE  payment SET chq_action=?, reserve_date = ?, pay_date = ?, memo = ? WHERE transaction_id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array(2, $date, $date, 'chq_realized', $id));

    $mn_blc = 0;
    $b_blc = 0;
    $re = $db->prepare("SELECT * FROM bank_balance WHERE id =:id ");
    $re->bindParam(':id', $bank);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $b_blc = $r['amount'];
        $bank_name = $r['name'];
    }

    $mn_blc = $b_blc + $amount;

    $sql = "UPDATE bank_balance SET amount = amount + ? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $bank));

    $sql = "INSERT INTO bank_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name,chq_no,chq_bank,chq_date,pay_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('chq_deposit', 'Credit', $id, $amount, 2, $bank, 'chq_deposit', $bank_name, $mn_blc, 'bank_deposit', 'Customer chq', 0, 0, $date, $time, $user_id, $user_name, $chq_no, $chq_bank, $chq_date, $date));


    echo $id;
}

if ($type == 'iss_realize') {

    $id = $_POST['id'];
    $unit = $_POST['unit'];

    if ($unit == 'grn') {

        $re = $db->prepare("SELECT * FROM supply_payment WHERE id = :id ");
        $re->bindParam(':id', $id);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $chq_no = $r['chq_no'];
            $chq_date = $r['chq_date'];
            $amount = $r['amount'];
            $bank = $r['bank_id'];
            $chq_bank = $r['chq_bank'];
        }

        $cr_name = 'GRN Payment';

        $sql = "UPDATE  supply_payment SET action = ?, reserve_date = ?, pay_date = ? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array(2, $date, $date, $id));

        $cr_type = 'grn_payment';
    } else

    if ($unit == 'exp') {

        $re = $db->prepare("SELECT * FROM payment WHERE transaction_id = :id ");
        $re->bindParam(':id', $id);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $chq_no = $r['chq_no'];
            $chq_date = $r['chq_date'];
            $amount = $r['amount'];
            $bank = $r['bank_id'];
            $chq_bank = $r['chq_bank'];
        }

        $re = $db->prepare("SELECT * FROM expenses_records WHERE acc_id = '$bank' AND acc_name = '$chq_bank' ");
        $re->bindParam(':id', $id);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $cr_name = $r['type'];
        }

        $sql = "UPDATE  payment SET chq_action = ?, reserve_date = ?, pay_date = ?, memo = ? WHERE transaction_id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array(2, $date, $date, 'chq_realized', $id));

        $cr_type = 'expenses_payment';
    }

    $mn_blc = 0;
    $b_blc = 0;
    $re = $db->prepare("SELECT * FROM bank_balance WHERE id =:id ");
    $re->bindParam(':id', $bank);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $b_blc = $r['amount'];
        $bank_name = $r['name'];
    }

    $mn_blc = $b_blc - $amount;

    $sql = "UPDATE  bank_balance SET amount=amount-? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $bank));

    $sql = "INSERT INTO bank_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name,chq_no,chq_bank,chq_date,pay_date) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('chq_issue', 'Debit', $id, $amount, 2, 0, 'chq_payment', $cr_name, 0, 'chq_issue', $bank_name, $bank, $mn_blc, $date, $time, $user_id, $user_name, $chq_no, $chq_bank, $chq_date, $date));

    echo $id;
}

if ($type == 'dep_return') {

    $id = $_POST['id'];

    $re = $db->prepare("SELECT * FROM payment WHERE transaction_id = :id ");
    $re->bindParam(':id', $id);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $chq_no = $r['chq_no'];
        $chq_date = $r['chq_date'];
        $amount = $r['amount'];
        $cus = $r['customer_id'];
        $sales_id = $r['sales_id'];
        $invoice_no = $r['invoice_no'];
        $load = $r['loading_id'];
    }

    $sql = "UPDATE  payment SET chq_action=?, reserve_date = ?, memo = ? WHERE transaction_id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array(3, $date, 'chq_returned', $id));

    $sql = "INSERT INTO payment (invoice_no,pay_amount,amount,type,date,time,chq_no,bank_name,chq_date,chq_action,action,sales_id,customer_id,loading_id,pay_type,credit_balance,paycose) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array($invoice_no, 0, $amount, 'credit', $date, $time, $chq_no, '', $chq_date, 0, 2, $sales_id, $cus, $load, 'credit', $amount, 'chq_return'));

    // get sales
    $sales_id = 0;
    $result = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id  ");
    $result->bindParam(':id', $invoice_no);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $sales_id = $row['transaction_id'];
    }

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
    $ql->execute(array($invoice_no, 'debit', $date, $time, $amount, $cus_balance, $sales_id, $cus, $user_id, $user_name));


    echo $id;
}

if ($type == 'iss_return') {

    $id = $_POST['id'];
    $unit = $_POST['unit'];

    if ($unit == 'grn') {

        $re = $db->prepare("SELECT * FROM supply_payment WHERE id = :id ");
        $re->bindParam(':id', $id);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $chq_no = $r['chq_no'];
            $amount = $r['amount'];
            $invoice_date = $r['invoice_date'];
            $invoice_no = $r['invoice_no'];
            $supply_id = $r['supply_id'];
            $supply_name = $r['supply_name'];
            $supplier_invoice = $r['supplier_invoice'];
            $type = $r['type'];
        }

        $sql = "UPDATE  supply_payment SET action=?, reserve_date = ? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array(3, $date, $id));

        $sql = 'INSERT INTO supply_payment (amount,pay_amount,pay_type,date,invoice_date,invoice_no,supply_id,supply_name,supplier_invoice,type,credit_balance) VALUES (?,?,?,?,?,?,?,?,?,?,?)';
        $q = $db->prepare($sql);
        $q->execute(array($amount, 0, 'Credit', $date, $invoice_date, $invoice_no, $supply_id, $supply_name, $supplier_invoice, $type, $amount));
    }

    if ($unit == 'exp') {

        $re = $db->prepare("SELECT * FROM payment WHERE transaction_id = :id ");
        $re->bindParam(':id', $id);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $chq_no = $r['chq_no'];
        }

        $sql = "UPDATE  payment SET chq_action=?, reserve_date = ? WHERE transaction_id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array(3, $date, $id));
    }

    echo $id;
}

if ($type == 'withdraw') {

    $bank = $_POST['bank'];
    $amount = $_POST['amount'];
    $acc_no = $_POST['cash'];

    $cr_blc = 0;
    $b_blc = 0;
    $re = $db->prepare("SELECT * FROM bank_balance WHERE id =:id ");
    $re->bindParam(':id', $bank);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $b_blc = $r['amount'];
        $cr_name = $r['name'];
    }

    $cr_blc = $b_blc - $amount;

    $mn_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM cash WHERE id=:id ");
    $re->bindParam(':id', $acc_no);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $de_name = $r['name'];
    }

    $mn_blc = $blc + $amount;

    $sql = "UPDATE  cash SET amount=amount+? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $acc_no));

    $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('cash_withdraw', 'Credit', $bank, $amount, 0, $acc_no, 'cash_withdraw', $de_name, $mn_blc, 'bank_withdraw', $cr_name, 0, 0, $date, $time, $user_id, $user_name));

    $sql = "UPDATE  bank_balance SET amount=amount-? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $bank));

    $sql = "INSERT INTO bank_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('cash_withdraw', 'Debit', $acc_no, $amount, 0, 0, 'Cash', $de_name, 0, 'bank_withdraw', $cr_name, $bank, $cr_blc, $date, $time, $user_id, $user_name));


    header("location: acc_bank_transfer.php");
}

if ($type == 'chargers') {

    $bank = $_POST['bank'];
    $desc = $_POST['desc'];
    $chr_date = $_POST['date'];
    $amount = $_POST['amount'];


    $bn_blc = 0;
    $blc = 0;
    $re = $db->prepare("SELECT * FROM bank_balance WHERE id =:id ");
    $re->bindParam(':id', $bank);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $blc = $r['amount'];
        $bn_name = $r['name'];
    }

    $bn_blc = $blc - $amount;

    $sql = "UPDATE  bank_balance SET amount=amount-? WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($amount, $bank));

    $acc_no = 0;
    $re = $db->prepare("SELECT count(id) FROM bank_record WHERE transaction_type = 'bank_charges' ");
    $re->bindParam(':id', $bank);
    $re->execute();
    for ($k = 0; $r = $re->fetch(); $k++) {
        $acc_no = $r['count(id)'];
    }
    $acc_no = $acc_no + 1;

    $sql = "INSERT INTO bank_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
    $ql = $db->prepare($sql);
    $ql->execute(array('bank_charges', 'Debit', $chr_date, $amount, 0, 0, 'bank_payment', 'Bank Charges', 0, 'bank_charges', $bn_name, $bank, $bn_blc, $date, $time, $user_id, $user_name));


    header("location: acc_bank_transfer.php");
}
