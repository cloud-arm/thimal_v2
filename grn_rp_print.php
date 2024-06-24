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

            <body style=" font-size: 13px; font-family: arial;margin: 0 10px;overflow-x: hidden;">
            <?php } ?>

            <?php if (isset($_GET['print'])) { ?>
                <meta http-equiv="refresh" content="<?php echo $sec; ?>;URL='<?php echo $return; ?>'">
            <?php } ?>
            <div class="wrapper">
                <!-- Main content -->
                <section class="invoice">

                    <?php
                    if (isset($_GET['invo'])) {
                        $invo = $_GET['invo'];
                        $result = $db->prepare("SELECT * FROM purchases WHERE invoice_number =:id ");
                        $result->bindParam(':id', $invo);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                            $sup_invo = $row['supplier_invoice'];
                            $id = $row['transaction_id'];
                            $date = $row['pu_date'];
                            $loc = $row['location'];
                            $lorry = $row['lorry_no'];
                            $amount = $row['amount'];
                            $transport = $row['transport'];
                        }
                        $url = 'invo=' . $_GET['invo'];
                    } else if (isset($_GET['id'])) {

                        $id = $_GET['id'];
                        $result = $db->prepare("SELECT * FROM purchases WHERE transaction_id =:id ");
                        $result->bindParam(':id', $id);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                            $invo = $row['invoice_number'];
                            $sup_invo = $row['supplier_invoice'];
                            $date = $row['pu_date'];
                            $loc = $row['location'];
                            $lorry = $row['lorry_no'];
                            $amount = $row['amount'];
                            $transport = $row['transport'];
                        }
                        $url = 'id=' . $_GET['id'];
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
                        <div class="col-xs-12" style="display: flex;justify-content: center;">
                            <img src="icon/Logo-Laugfs-Gas.png" alt="Logo" style="max-width:100px;"><br>
                        </div>

                        <div class="col-xs-7">
                            <h5>
                                <?php echo $info_name; ?> <br>
                                <?php echo $info_add; ?> <br>
                                VAT Reg: <?php echo $info_vat; ?> <br><br>
                                <b>Invoice: <?php echo $sup_invo; ?> </b> <br>
                                Purchase Date: <?php echo $date; ?><br>
                                Location: <?php echo $loc; ?><br>
                                <?php if ($lorry != '') {
                                    echo "Lorry No: " . $lorry . " <br>";
                                } ?>
                                Date: <?php echo date("Y-m-d"); ?>
                                Time- <?php echo date("H:i:s"); ?>
                            </h5>
                        </div>
                        <!-- /.col -->
                        <div class="col-xs-5 ">
                            <div class="row">
                                <div class="col-xs-12">
                                    <small class="pull-right">
                                        <h5>
                                            Laugfs Gas PLC <br>
                                            101, Maya Avenue, <br>
                                            Colombo 06, Sri Lanka <br>
                                            Tel : +94 11 5 566 222 <br>
                                            Fax : +94 11 5 577 824
                                        </h5>

                                        <h3> INVOICE </h3>

                                        <h5>
                                            Amount: <?php echo $amount; ?><br>
                                            Transport: <?php echo $transport; ?><br>
                                        </h5>
                                    </small>
                                </div>
                            </div>
                        </div>
                        <!-- /.col -->
                    </div>


                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Qty</th>
                                    <th>Cost </th>
                                    <th>Amount </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                date_default_timezone_set("Asia/Colombo");
                                $tot_amount = 0;
                                $num = 0;
                                $result = $db->prepare("SELECT * FROM purchases_item WHERE   invoice='$invo'");
                                $result->bindParam(':userid', $date);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $num += 1;
                                ?>
                                    <tr>
                                        <td><?php echo $num; ?></td>
                                        <td><?php echo $row['name']; ?></td>
                                        <td><?php echo $row['qty']; ?></td>
                                        <td>Rs.<?php echo $row['cost']; ?></td>
                                        <td>Rs.<?php echo $row['amount']; ?></td>
                                        <?php $tot_amount += $row['amount']; ?>
                                    </tr>
                                <?php } ?>

                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>

                        <div class="row">
                            <div class="col-xs-8">

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <tr>
                                            <th>Type</th>
                                            <th>Date</th>
                                            <th>CHQ No.</th>
                                            <th>CHQ Date</th>
                                            <th>Bank</th>
                                            <th>Amount</th>
                                        </tr>

                                        <?php
                                        $result1 = $db->prepare("SELECT * FROM supply_payment WHERE invoice_no='$invo' AND pay_type!='credit' AND action < 4  ");
                                        $result1->bindParam(':userid', $date);
                                        $result1->execute();
                                        for ($i = 0; $row1 = $result1->fetch(); $i++) {
                                        ?>

                                            <tr>
                                                <td><?php echo $row1['pay_type']; ?></td>
                                                <td><?php echo $row1['date']; ?></td>
                                                <td><?php echo $row1['chq_no']; ?></td>
                                                <td><?php echo $row1['chq_date']; ?></td>
                                                <td><?php echo $row1['chq_bank']; ?></td>
                                                <td>Rs.<?php echo number_format($row1['amount'], 2); ?></td>

                                            </tr>
                                        <?php } ?>

                                    </table>
                                </div>
                            </div>

                            <div class="col-xs-4" id="btn-box" style="display: flex;gap: 15px;justify-content: center;">
                                <a href="grn_rp_print.php?<?php echo $url; ?>&print" class="btn btn-danger"> <i class="fa fa-print"></i> Print</a>
                                <span>
                                    <form action="pdf/grn.php" method="GET" style="display: flex;gap: 15px;justify-content: center;" id="form">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                                        <input type="number" class="form-control" style="width: 75%;" name="number" id="num" onkeyup="typing()" value="" placeholder="947******">

                                        <a disabled href="#" id="btn" class="btn btn-success" onclick="btn_whatsapp()"> <i class="fa fa-whatsapp"></i> Whatsapp</a>
                                    </form>
                                </span>
                            </div>
                        </div>

                    </div>

                    <br><br><br>
                    <div class="row">
                        <div class="col-xs-12">
                            __________________ <br> DEALER SIGNATURE
                        </div>
                    </div>

                    <br><br><br>
                    <div class="row">
                        <div class="col-xs-12" style="text-align: center;">
                            <!-- This is a system generated document and signature is not required -->
                        </div>
                    </div>
                </section>
            </div>
            </body>

            <script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
            <script>
                function typing() {
                    if ($('#num').val() > 0) {
                        $('#btn').removeAttr('disabled');
                    } else {
                        $('#btn').attr('disabled', '');
                    }
                }

                function btn_whatsapp() {
                    $('#form').submit();
                }
            </script>

</html>