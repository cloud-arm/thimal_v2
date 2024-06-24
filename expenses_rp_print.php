<!DOCTYPE html>
<html>

<head>
    <?php
    session_start();
    include("connect.php");
    date_default_timezone_set("Asia/Colombo");
    $invo = $_GET['id'];
    $co = substr($invo, 0, 2);
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
                $id = $_GET['id'];
                $url = 'id=' . $_GET['id'];
                $result = $db->prepare("SELECT * FROM info ");
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                    $info_name = $row['name'];
                    $info_add = $row['address'];
                    $info_vat = $row['vat_no'];
                    $info_con = $row['phone_no'];
                }

                if (isset($_GET['invo'])) {

                    $invo = $_GET['invo'];
                    $result = $db->prepare("SELECT * FROM expenses_records WHERE invoice_no =:id ");
                    $result->bindParam(':id', $invo);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                        $id = $row['id'];
                        $date = $row['date'];
                        $type = $row['type'];
                        $type_id = $row['type_id'];
                        $sub_type = $row['sub_type'];
                        $amount = $row['amount'];
                        $chq_no = $row['chq_no'];
                        $chq_date = $row['chq_date'];
                        $vendor = $row['vendor_id'];
                    }
                    $url = 'invo=' . $_GET['invo'];
                } else if (isset($_GET['id'])) {

                    $id = $_GET['id'];
                    $result = $db->prepare("SELECT * FROM expenses_records WHERE  id=:id  ");
                    $result->bindParam(':id', $id);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                        $invo = $row['invoice_no'];
                        $date = $row['date'];
                        $type = $row['type'];
                        $type_id = $row['type_id'];
                        $sub_type = $row['sub_type'];
                        $amount = $row['amount'];
                        $chq_no = $row['chq_no'];
                        $chq_date = $row['chq_date'];
                        $vendor = $row['vendor_id'];
                    }
                    $url = 'id=' . $_GET['id'];
                }
                ?>


                <div class="row">
                    <div class="col-xs-12" style="display: flex;justify-content: center;">
                        <img src="icon/Logo-Laugfs-Gas.png" alt="Logo" style="max-width:100px;"><br>
                    </div>

                    <div class="col-xs-8">
                        <h5>
                            <?php echo $info_name; ?> <br>
                            <?php echo $info_add; ?> <br>
                            <?php echo "VAT Reg: " . $info_vat; ?><br>
                            <b>Invoice: <?php echo $invo; ?> </b> <br>
                            Invoice Date: <?php echo $date; ?><br>
                            Print Date: <?php echo date("Y-m-d"); ?>
                            Time- <?php echo date("H:i:s"); ?>
                        </h5>
                    </div>
                    <!-- /.col -->
                    <div class="col-xs-4 ">
                        <small class="pull-right">
                            <h3>
                                INVOICE
                            </h3>
                            <h5>
                                <b>Type: </b> <?php echo $type; ?> <br>
                                <b>Amount: </b> <?php echo $amount; ?> <br>
                                <?php if ($type == 'chq') { ?>
                                    <b>CHQ No: </b> <?php echo $chq_no; ?> <br>
                                    <b>CHQ Date: </b> <?php echo $chq_date; ?> <br>
                                <?php } ?>
                            </h5>
                        </small>
                    </div>
                </div>

                <div class="box-body" style="margin-top: 25px;">
                    <div class="row">
                        <?php if (isset($_GET['print'])) { ?>
                            <div class="col-xs-12">
                            <?php } else { ?>
                                <div class="col-xs-8">
                                <?php } ?>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <?php if ($sub_type > 0) { ?>
                                                    <th>Sub Type</th>
                                                <?php } ?>
                                                <?php if ($type_id == 2) { ?>
                                                    <th>Loading ID</th>
                                                <?php } ?>
                                                <?php if ($type_id == 2 | $type_id == 3) { ?>
                                                    <th>Lorry NO</th>
                                                <?php } ?>
                                                <th>Type</th>
                                                <?php if ($vendor > 0) { ?>
                                                    <th>Vendor</th>
                                                <?php } ?>
                                                <th>Amount (Rs.)</th>

                                            </tr>
                                        </thead>
                                        <tbody>

                                            <?php
                                            $result = $db->prepare("SELECT * FROM expenses_records WHERE  invoice_no=:id AND paycose='payment' ");
                                            $result->bindParam(':id', $invo);
                                            $result->execute();
                                            for ($i = 0; $row = $result->fetch(); $i++) {
                                            ?>
                                                <tr>

                                                    <td><?php echo $row['id'];   ?> </td>
                                                    <?php if ($sub_type > 0) { ?>
                                                        <td><?php echo $row['sub_type_name'];   ?> </td>
                                                    <?php } ?>
                                                    <?php if ($type_id == 2) { ?>
                                                        <td><?php echo $row['loading_id'];   ?> </td>
                                                    <?php } ?>
                                                    <?php if ($type_id == 2 | $type_id == 3) { ?>
                                                        <td><?php echo $row['lorry_no'];   ?> </td>
                                                    <?php } ?>
                                                    <td><?php echo $row['pay_type'];   ?> </td>
                                                    <?php if ($vendor > 0) { ?>
                                                        <td><?php echo $row['vendor_name'];   ?> </td>
                                                    <?php } ?>
                                                    <td>Rs.<?php echo $row['amount'];   ?></td>

                                                </tr>
                                            <?php }   ?>
                                        </tbody>

                                    </table>
                                </div>
                                </div>

                                <div class="col-xs-3" id="btn-box" style="display: flex;gap: 15px;justify-content: center;flex-direction: column;">
                                    <a href="expenses_rp_print.php?<?php echo $url; ?>&print" class="btn btn-danger"> <i class="fa fa-print"></i> Print</a>
                                    <span>
                                        <form action="pdf/expenses.php" method="GET" style="display: flex;gap: 15px;justify-content: center;" id="form">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <input type="number" class="form-control" style="width: 75%;" name="number" id="num" onkeyup="typing()" value="" placeholder="947******">

                                            <a disabled href="#" id="btn" class="btn btn-success" onclick="btn_whatsapp()"> <i class="fa fa-whatsapp"></i> Whatsapp</a>
                                        </form>
                                    </span>
                                </div>
                            </div>
                    </div>

                    <br><br>
                    <div class="row">
                        <div class="col-xs-12">
                            __________________ <br> DEALER SIGNATURE
                        </div>
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