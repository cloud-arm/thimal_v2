<?php

function bank_transaction($id, $type, $amount, $transaction_type, $record_no, $action = 0,$chq = ["no"=>"","date"=>"","bank"=>""])
{
    date_default_timezone_set("Asia/Colombo");

    $userid = $_SESSION['SESS_MEMBER_ID'];
    $username = $_SESSION['SESS_FIRST_NAME'];

    $date = date("Y-m-d");
    $time = date('H:i:s');

    $blc = 0;
    $result = select("bank", "name,amount", "id = '" . $id . "'");
    for ($k = 0; $row = $result->fetch(); $k++) {
        $blc = $row['amount'];
        $name = $row['name'];
    }

    if ($type == 'Credit') {

        $cr_id = $id;
        $cr_type = $transaction_type;
        $cr_name = $name;
        $cr_blc = $blc + $amount;
        $db_type = '';
        $db_name = '';
        $db_id = '';
        $db_blc = '';

        query("UPDATE  bank SET amount = amount + '" . $amount . "' WHERE id = '" . $id . "' ");
    }

    if ($type == 'Debit') {

        $cr_id = '';
        $cr_type = '';
        $cr_name = '';
        $cr_blc = '';
        $db_type = $transaction_type;
        $db_name = $name;
        $db_id = $id;
        $db_blc = $blc - $amount;

        query("UPDATE bank SET amount = amount - '" . $amount . "' WHERE id = '" . $id . "' ");
    }

    $insertData = array(
        "data" => array(
            "transaction_type" => $transaction_type,
            "type" => $type,
            "record_no" => $record_no,
            "amount" => $amount,
            "action" => $action,
            "credit_acc_no" => $cr_id,
            "credit_acc_type" => $cr_type,
            "credit_acc_name" => $cr_name,
            "credit_acc_balance" => $cr_blc,
            "debit_acc_type" => $db_type,
            "debit_acc_name" => $db_name,
            "debit_acc_id" => $db_id,
            "debit_acc_balance" => $db_blc,
            "chq_no" => $chq['no'],
            "chq_date" => $chq['date'],
            "chq_bank" => $chq['bank'],
            "date" => $date,
            "time" => $time,
            "user_id" => $userid,
            "user_name" => $username,
        ),
        "other" => array(
            "data_id" => $id,
            "data_name" => "bank_transaction",
        ),
    );
    return insert("bank_record", $insertData);
}
