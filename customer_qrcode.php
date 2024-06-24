<?php
include "phpqrcode/qrlib.php";

$customer_id = $_GET["id"];

$directory = 'qrcode/';

// Create directory if it doesn't exist
if (!file_exists($directory)) {
	mkdir($directory, 0777, true); // Creates the directory recursively with full permissions
}

$filename = $directory . 'customer.png';

if (isset($customer_id)) {

	$codeString = $customer_id;

	QRcode::png($codeString, $filename);
}

header("location: customer_qrcode_print.php?file=$customer_id");
