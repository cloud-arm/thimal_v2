<?php
session_start();
include('connect.php');

$ui = $_SESSION['SESS_MEMBER_ID'];
$un = $_SESSION['SESS_FIRST_NAME'];

$invo = $_POST['id'];
$sup_id = $_POST['sup_id'];
$pay_type = $_POST['pay_type'];
$pay_amount = $_POST['amount'];
$note = $_POST['note'];

$bank = 0;
$acc_no = '';
$bank_name = '';
$chq_no = '';
$chq_bank = '';
$chq_date = '';
$credit_note = 0;

if ($pay_type == 'Bank') {
    $acc_no = $_POST['acc_no'];
    $bank_name = $_POST['bank_name'];
}

if ($pay_type == 'Credit_note') {
    $credit_note = $_POST['credit_note'];
}

if ($pay_type == 'Chq') {
    $chq_no = $_POST['chq_no'];
    $bank = $_POST['chq_bank'];
    $chq_date = $_POST['chq_date'];

    $result = $db->prepare("SELECT * FROM bank_balance WHERE id=:id ");
    $result->bindParam(':id', $bank);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $chq_bank = $row['name'];
    }
}


$dic = 0;
$bn = '';
$date = date("Y-m-d");
$time = date('H:i:s');


$result = $db->prepare("SELECT * FROM supply_payment WHERE invoice_no=:id ");
$result->bindParam(':id', $invo);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $sup_invo = $row['supplier_invoice'];
    $sup_name = $row['supply_name'];
}


if ($pay_amount > 0) {

    $blc = 0;
    $result0 = $db->prepare("SELECT * FROM supply_payment WHERE pay_type='Credit' AND supplier_invoice = '$sup_invo' AND invoice_no = '$invo' ");
    $result0->bindParam(':id', $sup_invo);
    $result0->execute();
    for ($k = 0; $row0 = $result0->fetch(); $k++) {
        $blc = $row0['credit_balance'];
        $id = $row0['id'];
    }

    $sql = 'INSERT INTO supply_payment(amount,pay_amount,pay_type,date,pay_date,invoice_no,supply_id,supply_name,supplier_invoice,type,chq_no,chq_bank,chq_date,bank_id,bank_name,acc_no,action,credit_id,credit_note_id,credit_balance) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
    $q = $db->prepare($sql);
    $q->execute(array($pay_amount, $pay_amount, $pay_type, $date, $date, $invo, $sup_id, $sup_name, $sup_invo, 'credit_payment', $chq_no, $chq_bank, $chq_date, $bank, $bank_name, $acc_no, 1, $id, $credit_note, $blc - $pay_amount));


    $sql = "UPDATE  supply_payment SET credit_balance = credit_balance - ?, pay_amount = pay_amount + ?  WHERE id=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($pay_amount, $pay_amount, $id));

    if ($pay_type == 'Credit_note') {

        $sql = "UPDATE  supply_payment SET  credit_balance = credit_balance - ?, pay_amount = pay_amount + ? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($pay_amount, $pay_amount, $credit_note));

        $result0 = $db->prepare("SELECT * FROM supply_payment WHERE id = :id ");
        $result0->bindParam(':id', $credit_note);
        $result0->execute();
        for ($k = 0; $row0 = $result0->fetch(); $k++) {
            $credit_blc = $row0['credit_balance'];
        }

        if ($credit_blc == 0) {
            $sql = "UPDATE  supply_payment SET  close_date = ? WHERE id=?";
            $ql = $db->prepare($sql);
            $ql->execute(array($date, $credit_note));
        }
    }

    if ($pay_type == 'Cash') {

        $cr_id = 2;

        $de_blc = 0;
        $blc = 0;
        $re = $db->prepare("SELECT * FROM cash WHERE id = '$cr_id' ");
        $re->bindParam(':userid', $res);
        $re->execute();
        for ($k = 0; $r = $re->fetch(); $k++) {
            $blc = $r['amount'];
            $cr_name = $r['name'];
        }

        $de_blc = $blc - $pay_amount;

        $cr_type = 'grn_payment';

        $sql = "INSERT INTO transaction_record (transaction_type,type,record_no,amount,action,credit_acc_no,credit_acc_type,credit_acc_name,credit_acc_balance,debit_acc_type,debit_acc_name,debit_acc_id,debit_acc_balance,date,time,user_id,user_name) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $ql = $db->prepare($sql);
        $ql->execute(array('grn_payment', 'Debit', $invo, $pay_amount, 0, 0, $cr_type, 'Cash GRN', 0, $cr_type, $cr_name, $cr_id, $de_blc, $date, $time, $ui, $un));

        $sql = "UPDATE  cash SET amount=? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($de_blc, $cr_id));
    }

    $sql = "UPDATE  purchases SET pay_amount=pay_amount+? WHERE invoice_number=?";
    $ql = $db->prepare($sql);
    $ql->execute(array($pay_amount, $invo));
}

header("location: grn_payment.php");
