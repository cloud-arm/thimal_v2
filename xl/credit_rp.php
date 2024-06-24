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
$fileName = "customer-credit_" . date('Y-m-d') . ".xls";

// Column names 
$fields = array('CUS ID', 'CUSTOMER NAME', 'INVOICE', 'DATE', 'LORRY NO', 'AMOUNT', 'PAY AMOUNT', 'OVERDUE', 'BALANCE');

// Display column names as first row 
$excelData = implode("\t", array_values($fields)) . "\n";


$customer = $db->prepare("SELECT customer_id,credit_period FROM customer  ORDER BY category DESC");
$customer->bindParam(':userid', $d2);
$customer->execute();
for ($i = 0; $row_cus = $customer->fetch(); $i++) {
    $cus = $row_cus['customer_id'];
    $limit = $row_cus['credit_period'];
    $b_tot = 0;
    $pay_tot = 0;

    $result2z = $db->prepare("SELECT memo,sales_id,type,action,customer_id,pay_amount,amount,transaction_id,credit_period,invoice_no FROM payment WHERE action='2' and type='credit' and credit_balance>0 and customer_id='$cus' ");
    $result2z->bindParam(':userid', $d2);
    $result2z->execute();
    for ($i = 0; $row = $result2z->fetch(); $i++) {
        $sales_id = $row['invoice_no'];

        $result2 = $db->prepare("SELECT date,name,lorry_no,invoice_number FROM sales WHERE action='1' AND invoice_number='$sales_id'");
        $result2->bindParam(':userid', $d2);
        $result2->execute();
        for ($i = 0; $row2 = $result2->fetch(); $i++) {
            $date1 = $row2['date'];
            $date =  date("Y-m-d");
            $sday = strtotime($date1);
            $nday = strtotime($date);
            $tdf = abs($nday - $sday);
            $nbday1 = $tdf / 86400;
            $rs1 = intval($nbday1);

            $rs1 = $rs1 - $limit;
            $b_tot = $row['amount'] - $row['pay_amount'];

            $lineData = array($row['customer_id'], $row2['name'], $row['invoice_no'], $row2['date'], $row2['lorry_no'], $row['amount'] - $row['pay_amount'], $row['pay_amount'], $rs1 . ' Day', $b_tot);

            array_walk($lineData, 'filterData');
            $excelData .= implode("\t", array_values($lineData)) . "\n";
        }
    }
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
