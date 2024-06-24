<?php
include("config.php");
date_default_timezone_set("Asia/Colombo");

$unit = $_GET['unit'];

if ($unit == 1) {
    $val = $_GET['val'];

    echo '<option value="0" disabled selected>Select Invoice</option>';

    $result = select("sales", "transaction_id,invoice_number", " loading_id = '" . $val . "' AND action < 5 ");
    foreach ($result as $row) {

        echo sprintf(
            '<option value="%s"> %s </option>',
            $row['transaction_id'],
            $row['invoice_number']
        );
    }
}

if ($unit == 2) {
    $val = $_GET['val'];
    $load = $_GET['load'];

    $result = select("loading_list", "unload_qty", " loading_id = '" . $load . "' AND product_code = '" . $val . "'");
    foreach ($result as $row) {

        echo $row['unload_qty'];
    }
}
