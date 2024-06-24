<?php
session_start();
include('connect.php');

$invo = $_POST['id'];
$invo2 = $_POST['id2'];

$date = date("Y-m-d");
$time = date('H:i:s');


$result = $db->prepare("SELECT * FROM supply_payment WHERE invoice_no=:id ");
$result->bindParam(':id', $invo);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $chq_no = $row['chq_no'];
    $chq_date = $row['chq_date'];
    $bank_id = $row['bank_id'];
    $chq_bank = $row['chq_bank'];
    $sup_id = $row['supply_id'];
    $sup_name = $row['supply_name'];
    $bulk_id = $row['id'];
    $chq_amount = $row['amount'];
}

$result = $db->prepare("SELECT sum(amount) FROM bulk_payment WHERE  invoice_no=:id AND  type != 'active'  ");
$result->bindParam(':id', $invo);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $pay_tot = $row['sum(amount)'];
}

if ($chq_amount == $pay_tot) {

    $result = $db->prepare("SELECT * FROM bulk_payment WHERE invoice_no=:id AND type != 'active' ");
    $result->bindParam(':id', $invo);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $sup_invo = $row['supplier_invoice'];
        $grn_invo = $row['grn_invoice_no'];
        $amount = $row['amount'];
        $row_id = $row['id'];

        $res = $db->prepare("SELECT * FROM supply_payment WHERE invoice_no=:id ");
        $res->bindParam(':id', $grn_invo);
        $res->execute();
        for ($j = 0; $r = $res->fetch(); $j++) {
            $invo_date = $r['invoice_date'];
        }

        $cr_blc = 0;
        $blc = 0;
        $res = $db->prepare("SELECT * FROM supply_payment WHERE pay_type='Credit' AND supplier_invoice = '$sup_invo' ");
        $res->bindParam(':id', $sup_invo);
        $res->execute();
        for ($k = 0; $ro = $res->fetch(); $k++) {
            $blc = $ro['credit_balance'];
            $id = $ro['id'];
        }

        $sql = 'INSERT INTO supply_payment(amount,pay_amount,pay_type,date,invoice_no,supply_id,supply_name,supplier_invoice,type,invoice_date,pay_date,credit_id) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)';
        $q = $db->prepare($sql);
        $q->execute(array($amount, $amount, 'Bulk', $date, $grn_invo, $sup_id, $sup_name, $sup_invo, 'credit_payment', $invo_date, $date, $id));

        $cr_blc = $blc - $amount;

        $sql = "UPDATE  supply_payment SET credit_balance = credit_balance - ?, pay_amount = pay_amount + ? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array($amount, $amount, $id));

        if ($cr_blc == 0) {
            $sql = "UPDATE  supply_payment SET close_date = ? WHERE id=?";
            $ql = $db->prepare($sql);
            $ql->execute(array($date, $id));
        }

        $sql = "UPDATE  bulk_payment SET type=? WHERE id=?";
        $ql = $db->prepare($sql);
        $ql->execute(array('active', $row_id));
    }

    $sql = "UPDATE  supply_payment SET action=? WHERE invoice_no=?";
    $ql = $db->prepare($sql);
    $ql->execute(array(1, $invo));


    if ($invo == $invo2) {
        header("location: grn_payment.php");
    } else {
        header("location: grn_bulk_payment.php?id=$invo2");
    }
} else {

    header("location: grn_bulk_payment.php?id=$invo&unit=2&error");
}
