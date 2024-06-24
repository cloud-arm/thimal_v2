<?php

function acc_transaction($acc_id, $type, $amount, $transaction_type, $record_no, $reference_no = 0, $memo = '', $action = 0, $table = ['id' => 0, 'name' => ''])
{
    date_default_timezone_set("Asia/Colombo");

    $userid = $_SESSION['SESS_MEMBER_ID'];
    $username = $_SESSION['SESS_FIRST_NAME'];

    $date = date("Y-m-d");
    $time = date('H:i:s');

    $result = select("acc_account", "name", "id = '" . $acc_id . "'");
    for ($k = 0; $row = $result->fetch(); $k++) {
        $acc_name = $row['name'];
    }

    if ($type == 'Credit') {

        $credit = $amount;
        $debit = 0;

        // query("UPDATE  cash SET amount = amount + '" . $amount . "' WHERE id = '" . $id . "' ");
    }

    if ($type == 'Debit') {

        $credit = 0;
        $debit = $amount;

        // query("UPDATE  cash SET amount = amount - '" . $amount . "' WHERE id = '" . $id . "' ");
    }

    $insertData = array(
        "data" => array(
            "transaction_type" => $transaction_type,
            "type" => $type,
            "record_no" => $record_no,
            "reference_no" => $reference_no,
            "memo" => $memo,
            "table_id" => $table['id'],
            "table_name" => $table['name'],
            "action" => $action,
            "acc_id" => $acc_id,
            "acc_name" => $acc_name,
            "credit" => $credit,
            "debit" => $debit,
            "date" => $date,
            "time" => $time,
            "userid" => $userid,
            "username" => $username,
        ),
        "other" => array(
            "data_id" => $record_no,
            "data_name" => "acc_transaction",
        ),
    );
    return insert("acc_transaction_record", $insertData);
}
