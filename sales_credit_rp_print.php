<!DOCTYPE html>
<html>

<head>
    <?php
    session_start();
    include("connect.php");
    date_default_timezone_set("Asia/Colombo");
    ?>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>CLOUD ARM | Invoice</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">

    <style>
        @media print {
            h5 {
                line-height: 1.5;
            }

            #btn-box {
                display: none !important;
            }

            img {
                width: 300px;
            }

            th {
                text-align: center;
            }

            table.table.table-borderless,
            table.table.table-borderless thead,
            table.table.table-borderless thead tr,
            table.table.table-borderless thead tr td,
            table.table.table-borderless thead tr th,
            table.table.table-borderless tbody,
            table.table.table-borderless tbody tr,
            table.table.table-borderless tbody tr td,
            table.table.table-borderless tbody tr th {
                border: 0;
            }
        }
    </style>
</head>

<body>
    <?php
    $sec = "1";
    $return = $_SESSION['SESS_BACK'];
    ?>
    <?php if (isset($_GET['print'])) { ?>

        <body onload="window.print()" style=" font-size: 13px;font-family: arial;">
        <?php } else { ?>

            <body style=" font-size: 13px; font-family: arial;margin: 0 20px;overflow-x: hidden;">
            <?php } ?>
            <?php
            $sec = "1";
            ?>
            <?php if (isset($_GET['print'])) { ?>
                <meta http-equiv="refresh" content="<?php echo $sec; ?>;URL='<?php echo $return; ?>'">
            <?php } ?>
            <div class="wrapper">
                <!-- Main content -->
                <section class="invoice">

                    <?php

                    $url = 'id=' . $_GET['id'];
                    $cus_id = base64_decode($_GET['id']);

                    $vat_action = 0;
                    $result = $db->prepare('SELECT * FROM customer WHERE  customer_id=:id ');
                    $result->bindParam(':id', $cus_id);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                        $cus_name = $row['customer_name'];
                        $address = $row['address'];
                    }

                    $result = $db->prepare("SELECT * FROM info ");
                    $result->bindParam(':userid', $date);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                        $info_name = $row['name'];
                        $info_add = $row['address'];
                        $info_vat = $row['vat_no'];
                        $info_con = $row['phone_no'];
                    }

                    ?>


                    <div class="row">
                        <!-- accepted payments column -->

                        <div class="col-xs-12" style="display: flex;justify-content: center;">
                            <img src="icon/Logo-Laugfs-Gas.png" alt="Logo" style="max-width:100px;"><br>
                        </div>

                        <div class="col-xs-8">
                            <h5>
                                <?php echo $info_name; ?> <br>
                                <?php echo $info_add; ?> <br>
                                Invoice Date: <?php echo $date; ?><br>
                                Print Date: <?php echo date("Y-m-d"); ?>
                                Time- <?php echo date("H:i:s"); ?>
                            </h5>
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-4 ">
                            <small class="pull-right">
                                <h3> CREDIT REPORT </h3>
                                <h5>
                                    <?php
                                    echo "<b>Customer: </b>" . $cus_name . "<br>";
                                    echo "<b>Address: </b><br>" . $address . "<br>";
                                    ?>
                                </h5>
                            </small>
                        </div>
                        <!-- /.col -->

                    </div>

                    <div class="box-body" style="margin-top: 25px;">

                        <div class="row">
                            <div class="col-xs-12">

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>Invoice</th>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Pay_Amount</th>
                                            <th>Credit_Balance</th>
                                        </tr>

                                        <?php
                                        $result1 = $db->prepare("SELECT * FROM payment WHERE action='2' and type='credit' and credit_balance>0 and customer_id='$cus_id'  ");
                                        $result1->bindParam(':userid', $date);
                                        $result1->execute();
                                        for ($i = 0; $row1 = $result1->fetch(); $i++) {
                                        ?>
                                            <tr>
                                                <td><?php echo $row1['invoice_no']; ?></td>
                                                <td><?php echo $row1['date']; ?></td>
                                                <td align="right"><?php echo number_format($row1['amount'], 2); ?></td>
                                                <td align="right"><?php echo number_format($row1['pay_amount'], 2); ?></td>
                                                <td align="right"><?php echo number_format($row1['credit_balance'], 2); ?></td>
                                            </tr>
                                        <?php } ?>

                                    </table>
                                </div>
                            </div>


                            <?php if (isset($_GET['print'])) {
                                echo '<div class="col-xs-5 pull-right">';
                            } else {
                                echo '<div class="col-xs-5">';
                            }

                            $result1 = $db->prepare("SELECT sum(amount),sum(pay_amount),sum(credit_balance) FROM payment WHERE action='2' and type='credit' and credit_balance>0 and customer_id='$cus_id'  ");
                            $result1->bindParam(':userid', $date);
                            $result1->execute();
                            for ($i = 0; $row1 = $result1->fetch(); $i++) {
                                $tot_amount = $row1['sum(amount)'];
                                $tot_pay_amount = $row1['sum(pay_amount)'];
                                $tot_credit_balance = $row1['sum(credit_balance)'];
                            }

                            ?>

                            <table class="table table-borderless table-striped">
                                <tbody>
                                    <tr>
                                        <td><b>Total Amount:</b></td>
                                        <td><b><?php echo $tot_amount; ?></b></td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Pay Amount:</b></td>
                                        <td><b><?php echo $tot_pay_amount; ?></b></td>
                                    </tr>
                                    <tr>
                                        <td><b>Total Balance:</b></td>
                                        <td><b><?php echo $tot_credit_balance; ?></b></td>
                                    </tr>
                                </tbody>
                            </table>


                            <?php echo '</div>'; ?>


                            <div class="col-xs-4 pull-right" id="btn-box" style="display: flex;gap: 15px;justify-content: center;">
                                <a href="sales_credit_rp_print.php?<?php echo $url; ?>&print" class="btn btn-danger"> <i class="fa fa-print"></i> Print</a>
                                <a href="pdf/credit.php?<?php echo $url; ?>" class="btn btn-success"> <i class="fa fa-whatsapp"></i> Whatsapp</a>
                            </div>
                        </div>

                    </div>

                    <br><br>
                    <div class="row">
                        <div class="col-xs-12">
                            __________________ <br> DEALER SIGNATURE
                        </div>
                    </div>
                </section>
            </div>
            </body>

</html>