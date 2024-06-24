<?php
function vat_transaction($data)
{
    //update vat amount
    echo query("UPDATE vat_account SET amount = amount + '" . $data['vat'] . "' WHERE id = '" . $data['vat_id'] . "' ");

    $vat_acc = '';
    //get vat acc
    $result = select("vat_account", "vat_no", "id = '" . $data['vat_id'] . "'");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $vat_acc = $row['vat_no'];
    }

    // insert vat record
    $insertData = array(
        "data" => array(
            "invoice_no" => $data['invoice'],
            "type" => $data['type'],
            "date" => $data['date'],
            "time" => $data['time'],
            "record_type" => $data['record_type'],
            "acc_id" => $data['vat_id'],
            "acc_no" => $vat_acc,
            "vat" => $data['vat'],
            "value" => $data['value'],
            "vat_no" => $data['vat_no'],
            "user_id" => $data['userid'],
            "user_name" => $data['username'],
        ),
        "other" => array(
            "data_id" => $data['invoice'],
            "data_name" => "vat",
        ),
    );
    insert("vat_record", $insertData);


    return 'successful';
}
