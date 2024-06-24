<?php
include("connect.php");
date_default_timezone_set("Asia/Colombo");

$type = $_GET['type'];

if ($type == "number") {
    $number = $_GET['number'];
    $sales = "SELECT * FROM sales WHERE invoice_number = '$number' ORDER BY transaction_id LIMIT 20";
    $purchases1 = "SELECT * FROM purchases WHERE invoice_number = '$number' ORDER BY transaction_id LIMIT 20";
    $purchases2 = "SELECT * FROM purchases WHERE supplier_invoice = '$number' ORDER BY transaction_id LIMIT 20";
    $payment1 = "SELECT * FROM payment WHERE chq_no = '$number' AND paycose != 'expenses_issue' ORDER BY transaction_id LIMIT 20";
    $payment2 = "SELECT * FROM payment WHERE chq_no = '$number' AND paycose = 'expenses_issue' ORDER BY transaction_id LIMIT 20";
    $supplier = "SELECT * FROM supply_payment WHERE invoice_no = '$number' ORDER BY id LIMIT 20";
    $expenses = "SELECT * FROM expenses_records ORDER BY id LIMIT 0";
    $value = $number;
}

if ($type == "date") {
    $date = $_GET['date'];
    $sales = "SELECT * FROM sales WHERE date = '$date' ORDER BY transaction_id LIMIT 0";
    $purchases2 = "SELECT * FROM purchases WHERE date = '$date' ORDER BY transaction_id LIMIT 0";
    $payment1 = "SELECT * FROM payment WHERE chq_date = '$date' AND paycose != 'expenses_issue' ORDER BY transaction_id LIMIT 20";
    $payment2 = "SELECT * FROM payment WHERE chq_date = '$date' AND paycose = 'expenses_issue' ORDER BY transaction_id LIMIT 20";
    $supplier = "SELECT * FROM supply_payment WHERE chq_date = '$date' ORDER BY id LIMIT 20";
    $expenses = "SELECT * FROM expenses_records ORDER BY id LIMIT 0";
    $value = $date;
}

if ($type == "amount") {
    $amount = $_GET['amount'];
    $sales = "SELECT * FROM sales WHERE amount = '$amount' ORDER BY transaction_id LIMIT 20";
    $purchases2 = "SELECT * FROM purchases WHERE amount = '$amount' ORDER BY transaction_id LIMIT 20";
    $payment1 = "SELECT * FROM payment WHERE amount = '$amount' AND paycose != 'expenses_issue' ORDER BY transaction_id LIMIT 20";
    $payment2 = "SELECT * FROM payment WHERE amount = '$amount' AND paycose = 'expenses_issue' ORDER BY transaction_id LIMIT 20";
    $supplier = "SELECT * FROM supply_payment WHERE amount = '$amount' ORDER BY id LIMIT 20";
    $expenses = "SELECT * FROM expenses_records WHERE amount = '$amount' AND paycose = 'expenses' ORDER BY id LIMIT 20";
    $value = $amount;
}

$result = $db->prepare($sales);
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) { ?>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box" style="max-height: 90px;position: relative;">
            <span class="info-box-icon bg-aqua"><i class=" glyphicon glyphicon-list-alt"></i></span>

            <div class="info-box-content">
                <span class="info-box-text"> <b><?php echo $row['name']; ?></b> </span>
                <span class="progress-description">#<?php echo $row['invoice_number']; ?></span>
                <span class="progress-description">Amount: <?php echo $row['amount']; ?></span>
                <span class="progress-description">Balance: <?php echo $row['balance']; ?></span>
                <a href="bill2.php?invo=<?php echo base64_encode($row['invoice_number']); ?>" class="btn btn-xs bg-orange" style="position: absolute;right: 5px;bottom: 5px;">view</a>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <?php }

if ($type == "number") {
    $result = $db->prepare($purchases1);
    $result->bindParam(':id', $id);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) { ?>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box" style="max-height: 90px;position: relative;">
                <span class="info-box-icon bg-green"><i class="fa fa-cart-plus"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text"> <b><?php echo $row['supplier_name']; ?></b> </span>
                    <span class="progress-description">#<?php echo $row['supplier_invoice']; ?></span>
                    <span class="progress-description">Amount: <?php echo $row['amount']; ?></span>
                    <span class="progress-description">Balance: <?php echo $row['amount'] - $row['pay_amount']; ?></span>
                    <a href="grn_rp_print.php?invo=<?php echo $row['invoice_number']; ?>" class="btn btn-xs bg-orange" style="position: absolute;right: 5px;bottom: 5px;">view</a>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
    <?php }
}

$result = $db->prepare($purchases2);
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) { ?>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box" style="max-height: 90px;position: relative;">
            <span class="info-box-icon bg-green"><i class="fa fa-cart-plus"></i></span>

            <div class="info-box-content">
                <span class="info-box-text"> <b><?php echo $row['supplier_name']; ?></b> </span>
                <span class="progress-description">#<?php echo $row['supplier_invoice']; ?></span>
                <span class="progress-description">Amount: <?php echo $row['amount']; ?></span>
                <span class="progress-description">Balance: <?php echo $row['amount'] - $row['pay_amount']; ?></span>
                <a href="grn_rp_print.php?invo=<?php echo $row['invoice_number']; ?>" class="btn btn-xs bg-orange" style="position: absolute;right: 5px;bottom: 5px;">view</a>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
<?php }

$result = $db->prepare($expenses);
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) { ?>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box" style="max-height: 90px;position: relative;">
            <span class="info-box-icon bg-red"><i class="fa fa-wrench"></i></span>

            <div class="info-box-content">
                <span class="info-box-text"> <b><?php echo $row['type']; ?></b> </span>
                <span class="progress-description">#<?php echo $row['date']; ?></span>
                <span class="progress-description">Amount: <?php echo $row['amount']; ?></span>
                <span class="progress-description">Balance: <?php echo $row['amount'] - $row['pay_amount']; ?></span>
                <a href="expenses_rp_print.php?id=<?php echo $row['id']; ?>" class="btn btn-xs bg-orange" style="position: absolute;right: 5px;bottom: 5px;">view</a>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
<?php }

$result = $db->prepare($payment1);
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) { ?>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box" style="max-height: 90px;position: relative;">
            <span class="info-box-icon bg-purple"><i class="fa-solid fa-money-check"></i></span>

            <div class="info-box-content">
                <span class="info-box-text"> <b><?php echo $row['type']; ?></b> </span>
                <span class="progress-description">#<?php echo $row['invoice_no']; ?></span>
                <span class="progress-description">Amount: <?php echo $row['amount']; ?></span>
                <span class="progress-description">Balance: <?php echo $row['amount'] - $row['pay_amount']; ?></span>
                <?php if ($row['paycose'] == "invoice_payment") { ?>
                    <a href="bill2.php?invo=<?php echo base64_encode($row['invoice_no']); ?>" class="btn btn-xs bg-orange" style="position: absolute;right: 5px;bottom: 5px;">view</a>
                <?php } ?>
                <?php if ($row['paycose'] == "credit") { ?>
                    <a href="bulk_payment_print.php?id=<?php echo $row['credit_pay_id']; ?>" class="btn btn-xs bg-orange" style="position: absolute;right: 5px;bottom: 5px;">view</a>
                <?php } ?>
                <?php if ($row['paycose'] == "credit_payment") { ?>
                    <a href="bulk_payment_print.php?id=<?php echo $row['transaction_id']; ?>" class="btn btn-xs bg-orange" style="position: absolute;right: 5px;bottom: 5px;">view</a>
                <?php } ?>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
<?php }

$result = $db->prepare($payment2);
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) { ?>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box" style="max-height: 90px;position: relative;">
            <span class="info-box-icon bg-maroon"><i class="fa-solid fa-money-check"></i></span>

            <div class="info-box-content">
                <span class="info-box-text"> <b><?php echo $row['type']; ?></b> </span>
                <span class="progress-description">#<?php echo $row['invoice_no']; ?></span>
                <span class="progress-description">Amount: <?php echo $row['amount']; ?></span>
                <span class="progress-description">Balance: <?php echo $row['amount'] - $row['pay_amount']; ?></span>
                <a href="expenses_rp_print.php?id=<?php echo $row['id']; ?>" class="btn btn-xs bg-orange" style="position: absolute;right: 5px;bottom: 5px;">view</a>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
<?php }

$result = $db->prepare($supplier);
$result->bindParam(':id', $id);
$result->execute();
for ($i = 0; $row = $result->fetch(); $i++) { ?>
    <div class="col-md-3 col-sm-6 col-xs-12">
        <div class="info-box" style="max-height: 90px;position: relative;">
            <span class="info-box-icon bg-blue"><i class="fa-solid fa-cubes"></i></span>

            <div class="info-box-content">
                <span class="info-box-text"> <b><?php echo $row['type']; ?></b> </span>
                <span class="progress-description">#<?php echo $row['invoice_no']; ?></span>
                <span class="progress-description">Amount: <?php echo $row['amount']; ?></span>
                <span class="progress-description">Balance: <?php echo $row['amount'] - $row['pay_amount']; ?></span>
                <a href="grn_rp_print.php?invo=<?php echo $row['invoice_no']; ?>" class="btn btn-xs bg-orange" style="position: absolute;right: 5px;bottom: 5px;">view</a>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
<?php }
