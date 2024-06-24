<?php
include("connect.php");
date_default_timezone_set("Asia/Colombo");

$result = $db->prepare("SELECT * FROM payment WHERE credit_pay_id=:id AND pay_type = 'credit_payment' ");
$result->bindParam(':id', $_GET['id']);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) {
    $result1 = $db->prepare("SELECT * FROM customer WHERE customer_id=:id ");
    $result1->bindParam(':id', $row['customer_id']);
    $result1->execute();
    for ($i = 0; $row1 = $result1->fetch(); $i++) {
        $cus = $row1['customer_name'];
    }
    $lorry = '';
    $result1 = $db->prepare("SELECT * FROM loading WHERE transaction_id =:id ");
    $result1->bindParam(':id', $row['loading_id']);
    $result1->execute();
    for ($i = 0; $row1 = $result1->fetch(); $i++) {
        $lorry = $row1['lorry_no'];
    } ?>
    <tr>
        <td><?php echo $row['transaction_id'];   ?> </td>
        <td>
            <span class="badge bg-blue"> <i class="fa fa-truck"></i> <?php echo $lorry; ?> </span> <br>
            <a href="loading_view.php?id=<?php echo $row['loading_id'] ?>" class="badge bg-green">Loading ID: <?php echo $row['loading_id'] ?></a>
        </td>
        <td><?php echo $row['invoice_no'];   ?> </td>
        <td><?php echo $cus;   ?> </td>
        <td>Rs.<?php echo number_format($row['credit_balance'] + $row['amount'], 2);  ?></td>
        <td><?php echo number_format($row['amount'], 2);   ?></td>
    </tr>
<?php }   ?>