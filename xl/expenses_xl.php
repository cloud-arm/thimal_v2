<?php
// Load the database configuration file 
include("../connect.php");

// Filter the excel data 
function filterData(&$str)
{
    $str = preg_replace("/\t/", "\\t", $str);
    $str = preg_replace("/\r?\n/", "\\n", $str);
    if (strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}

// Excel file name for download 
$fileName = "salary-advance" . date('Y-m-d') . ".xls";

// Column names 
$fields = array('ID', 'INVOICE', 'DATE', 'LOADING NO', 'NAME', 'AMOUNT');

// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n";


$expenses = $db->prepare("SELECT * FROM expenses_records WHERE paycose = 'expenses' AND credit_balance = 0 AND type_id = 2 AND sub_type = 4 AND loading_id > 0 ");
$expenses->bindParam(':userid', $d2);
$expenses->execute();
for ($i = 0; $row = $expenses->fetch(); $i++) {

    $lineData = array($row['id'], $row['invoice_no'], $row['date'], $row['loading_id'], ucfirst($row['comment']), $row['amount']);

    array_walk($lineData, 'filterData');
    $excelData .= implode("\t", array_values($lineData)) . "\n";
}


// $result = $db->prepare("SELECT * FROM lorry ");
// $result->bindParam(':userid', $res);
// $result->execute();
// for ($i = 0; $row = $result->fetch(); $i++) {
//     $lineData = array($row['lorry_no'], $row['lorry_no']);
// }

// array_walk($lineData, 'filterData');
// $excelData .= implode("\t", array_values($lineData)) . "\n";

// Headers for download 
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$fileName\"");

// Render excel data 
echo $excelData;

exit;
