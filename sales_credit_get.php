<?php
include("connect.php");
date_default_timezone_set("Asia/Colombo");

$id = $_GET['id'];
?>


<table id="example2" class="table table-bordered table-hover">
    <thead>
        <tr>
            <th>ID</th>
            <th>Customer</th>
            <th>Invoice</th>
            <th>Date</th>
            <th>Amount</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $a = 0;
        $tot = 0;
        $result = $db->prepare("SELECT * FROM payment WHERE credit_pay_id = :id  AND type= 'credit_payment' ");
        $result->bindParam(':id', $id);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $name = '';
            $result1 = $db->prepare("SELECT customer_name FROM customer WHERE customer_id = :id");
            $result1->bindParam(':id', $row['customer_id']);
            $result1->execute();
            for ($k = 0; $row1 = $result1->fetch(); $k++) {
                $name = $row1['customer_name'];
            }

        ?>
            <tr>
                <td><?php echo $a = $a + 1;  ?></td>
                <td><?php echo $name;  ?></td>
                <td><?php echo $row['invoice_no']  ?></td>
                <td><?php echo $row['date'];  ?></td>
                <td><?php echo $row['pay_amount'];  ?></td>
                <?php $tot += $row['pay_amount'] ?>
            </tr>
        <?php } ?>
    </tbody>
    <tfoot>
    </tfoot>
</table>

<div style="padding-left: 25px;margin-top: 20px;">
    <h4>Total: <small> Rs. </small> <?php echo number_format($tot, 2); ?> </h4>
</div>