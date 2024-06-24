<!DOCTYPE html>
<html>

<head>
    <?php
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
    <!-- Theme style -->

    <style>
        @media print {

            h5.h5 {
                line-height: 1.5;
                text-align: center;
                width: 100%;
                margin-top: 0;
            }

            h5 {
                line-height: 1.5;
                width: max-content;
            }

            hr {
                margin: 0 0 10px 0;
                border-color: rgba(0, 0, 0, 0.2);
            }

            table.table,
            table.table thead,
            table.table thead tr,
            table.table thead tr td,
            table.table thead tr th,
            table.table tbody,
            table.table tbody tr,
            table.table tbody tr td,
            table.table tbody tr th {
                border: 0;
                padding: 5px 10px 0 10px;
            }

            small {
                display: flex;
                width: 100%;
                align-items: center;
                justify-content: center;
                gap: 5px;
            }
        }
    </style>
</head>

<body onload="window.print() ">
    <?php
    $sec = "1";
    ?>
    <meta http-equiv="refresh" content="<?php echo $sec; ?>;URL='index.php'">
    <div class="wrapper">
        <!-- Main content -->
        <section class="invoice">

            <?php
            $invo = base64_decode($_GET['id']);
            $id = 0;

            $result = $db->prepare("SELECT * FROM payment  WHERE invoice_no = :id  AND pay_type = 'Credit' ");
            $result->bindParam(':id', $invo);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $type = $row['type'];
                $amount = $row['amount'];
                $balance = $row['credit_balance'];
                $cus_id = $row['customer_id'];
            }

            $result = $db->prepare("SELECT * FROM payment  WHERE invoice_no = :id  AND pay_type != 'Credit' ");
            $result->bindParam(':id', $invo);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $id = $row['transaction_id'];
                $type = $row['type'];
                $amount = $row['amount'];
                $balance = $row['credit_balance'];
                $chq_no = $row['chq_no'];
                $chq_bank = $row['chq_bank'];
                $chq_date = $row['chq_date'];
                $cus_id = $row['customer_id'];
            }

            $result = $db->prepare('SELECT * FROM customer WHERE  customer_id=:id ');
            $result->bindParam(':id', $cus_id);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $vat_no = $row['vat_no'];
                $name = $row['customer_name'];
                $address = $row['address'];
                $contact = $row['contact'];
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

            $vat_action = 0;
            if (strlen($vat_no) > 0) {
                $vat_action = 1;
            }
            ?>

            <div class="row">
                <!-- accepted payments column -->
                <div class="col-xs-12">

                    <small><img src="icon/narangoda.jpeg" style="width: 160px;" alt=""></small>
                    <h5 class="h5">
                        <b><?php echo $info_name; ?></b> <br>
                        <?php echo $info_add; ?> <br>
                        <?php if ($vat_action == 1) {
                            echo "VAT Reg: " . $info_vat . " <br>";
                        } ?>
                        <?php echo $info_con; ?> <br>

                    </h5>

                </div>
                <br>
                <br>
                <div class="col-xs-12">
                    <h5>
                        <b>
                            <?php if ($vat_action == 1) {
                                echo "TAX INVOICE";
                            } else {
                                echo "INVOICE";
                            } ?>
                        </b> <br>

                        No: <?php echo $invo; ?> <br>

                        Date: <?php echo date("Y-m-d"); ?>
                        Time- <?php echo date("H:i:s"); ?>
                    </h5>
                </div>
                <!-- /.col -->
                <div class="col-xs-12">
                    <h5>
                        <?php echo $name; ?> <br>
                        <?php if ($vat_action == 1) {
                            echo "VAT No:" . $vat_no . " <br>";
                        } ?>
                        <?php echo $address; ?> <br>
                    </h5>
                </div>

                <!-- /.col -->
            </div>
            <br>
            <div class="box-body">
                <table id="example1" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Price </th>
                            <th>Amount </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $tot_amount = 0;
                        $id = base64_decode($_GET['id']);
                        $result = $db->prepare("SELECT * FROM sales_list WHERE invoice_no = :id ");
                        $result->bindParam(':id', $id);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) { ?>
                            <tr>
                                <td>
                                    <?php echo $i + 1; ?> <?php echo $row['name'];   ?>
                                </td>
                                <td>
                                    <?php echo $row['qty'];   ?>
                                </td>
                                <td>
                                    <?php if ($vat_action == 1) {
                                        echo number_format((($row['price'] / 118) * 100), 2);
                                    } else {
                                        echo $row['price'];
                                    } ?>
                                </td>
                                <td align="right">
                                    <?php if ($vat_action == 1) {
                                        echo number_format((($row['price'] / 118) * 100) * $row['qty'], 2);
                                    } else {
                                        echo $row['amount'];
                                    } ?>
                                </td>
                                <?php $tot_amount += $row['amount']; ?>
                            </tr>
                        <?php }   ?>
                    </tbody>
                </table>

                <table class=" table ">
                    <tbody>

                        <?php if ($vat_action == 1) { ?>
                            <tr>
                                <td>Sub Total: </td>
                                <td align="right">Rs.<?php echo number_format((($tot_amount / 118) * 100), 2); ?></td>
                            </tr>
                            <tr>
                                <td>VAT: </td>
                                <td align="right">Rs.<?php echo number_format((($tot_amount / 118) * 18), 2); ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td><b>Total: </b></td>
                            <td align="right"><b>Rs.<?php echo number_format($tot_amount, 2); ?></b></td>
                        </tr>
                    </tbody>
                </table>

                <hr>

                <table class=" table ">
                    <tbody>
                        <tr>
                            <td>
                                <?php echo $type; ?>: 
                                <?php if ($type == 'Chq') {
                                    echo $chq_no . ' | ' . $chq_date;
                                } ?>
                            </td>
                            <td align="right">Rs.<?php echo number_format($amount, 2); ?></td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <br><br>
            <br><br>
            <br><br>
            <div class="row">
                <div class="col-xs-12">
                    __________________ <br> DEALER SIGNATURE
                </div>
            </div>

            <br>
            <div class="row">
                <div class="col-xs-12" style="text-align: center;">
                    This is a system generated document and signature is not required
                </div>
            </div>
            <br><br>
            <small><img src="icon/r.png" style="width: 20px;" alt=""> Cloud arm</small>
        </section>
    </div>
</body>

</html>