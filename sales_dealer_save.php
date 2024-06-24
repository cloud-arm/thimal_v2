<?php
session_start();
include('connect.php');
date_default_timezone_set("Asia/Colombo");


$d1 = $_GET['d1'];
$d2 = $_GET['d2'];
$dd1 = $_GET['d1'];
$dd2 = $_GET['d2'];


$startDate1 = new DateTime($d1);
$startDate2 = new DateTime($d1);
$endDate = new DateTime($d2);

$months = [];
$monthNumber = [];

while ($startDate1 < $endDate) {

    $d1 = $startDate1->format('Y-m') . '-01';
    $d2 = $startDate1->format('Y-m') . '-31';
    $month = $startDate1->format('Y-m');

    $sales_sum = 0;
    $result = $db->prepare("SELECT sum(qty) FROM sales_list WHERE action = 0 AND product_id = 1 AND date BETWEEN '$d1' AND '$d2'  ");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $sales_sum = $row['sum(qty)'];
    }

    $month_sum = 0;
    $result = $db->prepare("SELECT sum(qty) FROM customer_month_sales WHERE  product_id = 1 AND month = '$month' ");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $month_sum = $row['sum(qty)'];
    }

    if ($sales_sum > $month_sum) {
        $monthNumber[] = $month;

        $result1 = $db->prepare("DELETE FROM customer_month_sales WHERE  month= :id ");
        $result1->bindParam(':id', $month);
        $result1->execute();
    }

    $startDate1->modify('first day of next month');
}

while ($startDate2 < $endDate) {
    $months[] = $startDate2->format('F');
    $startDate2->modify('first day of next month');
}

if (isset($_GET['products'])) {
    $products = $_GET['products'];
} else {
    $products = ["1", "2"];
}


$sales = array();

function get_qty($array, $month, $pro_id, $cus_id)
{
    $qty = 0;
    foreach ($array as $item) {
        $timestamp = strtotime($item['date']);
        $temp_month = date('Y-m', $timestamp);
        if ($temp_month == $month && $item['product_id'] == $pro_id && $item['customer_id'] == $cus_id) {
            $qty += $item['qty'];
        }
    }

    return $qty;
}

function get_name($db, $id)
{
    $result = $db->prepare("SELECT gen_name FROM products WHERE product_id = :id  ");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        return $row['gen_name'];
    }
}

$sql = "SELECT *,sales.name AS customer_name FROM sales JOIN sales_list ON sales.invoice_number = sales_list.invoice_no WHERE  sales.action = 1 AND (sales.date BETWEEN '$dd1' AND '$dd2') ";
$result = $db->prepare($sql);
$result->bindParam(':userid', $date);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $sales[] = ["customer_id" => $row["customer_id"], "customer_name" => $row["customer_name"], "root" => $row["root"], "date" => $row["date"], "product_id" => $row["product_id"], "qty" => $row["qty"]];
}

$result = $db->prepare("SELECT customer_id,root,customer_name,root_id FROM customer WHERE  action = 0   ORDER BY root ");
$result->bindParam(':id', $date);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    echo 'Test 1';

    foreach ($monthNumber as $month) {

        echo 'Test 2';

        foreach ($products as $pid) {
            echo 'Test 3';

            $sql = "INSERT INTO customer_month_sales (customer_id,customer_name,root_id,root_name,product_id,product_name,qty,month) VALUES (?,?,?,?,?,?,?,?)";
            $ql = $db->prepare($sql);
            $ql->execute(array($row['customer_id'], $row['customer_name'], $row['root_id'], $row['root'], $pid, get_name($db, $pid), get_qty($sales, $month, $pid, $row['customer_id']), $month));
        }
    }
}



// header("location: sales_dealer_rp.php?d1=$dd1&d2=$dd2&products=$products");
