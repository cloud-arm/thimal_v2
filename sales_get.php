<?php
include("connect.php");
date_default_timezone_set("Asia/Colombo");

$id = $_GET['id'];
$type = $_GET['type'];

if ($type == 'price') {

    $sell = 0;
    $result = $db->prepare("SELECT * FROM products WHERE product_id=:id  ");
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
        $sell = $row['sell_price'];
    }

    printf($sell);
}
