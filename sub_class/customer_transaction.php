<?php
function customer_transaction($data)
{

    //update customer balance
    if ($data['type'] == 'credit') {
        echo query("UPDATE customer SET balance = balance + '" . $data['amount'] . "'  WHERE customer_id = '" . $data['customer'] . "' ");
    }

    if ($data['type'] == 'debit') {
        echo query("UPDATE customer SET balance = balance - '" . $data['amount'] . "'  WHERE customer_id = '" . $data['customer'] . "' ");
    }

    //get customer balance
    $result = select("customer", "balance", "customer_id = '" . $data['customer'] . "'");
    for ($i = 0; $row = $result->fetch(); $i++) {
        $cus_balance = $row['balance'];
    }

    // insert customer record
    $insertData = array(
        "data" => array(
            "invoice_no" => $data['invoice'],
            "type" => $data['type'],
            "date" => $data['date'],
            "time" => $data['time'],
            $data['type'] => $data['amount'],
            "balance" => $cus_balance,
            "sales_id" => $data['sales'],
            "customer_id" => $data['customer'],
            "user_id" => $data['userid'],
            "user_name" => $data['username'],
        ),
        "other" => array(
            "data_id" => $data['invoice'],
            "data_name" => "customer",
        ),
    );
    insert("customer_record", $insertData);

    return 'successful';
}
