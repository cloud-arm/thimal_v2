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
                margin-bottom: 0;
            }

            h4 span {
                float: right;
            }

            h3 {
                line-height: 1.5;
                font-weight: 600;
                text-decoration: underline;
            }

            #btn-box {
                display: none !important;
            }

            a {
                color: #3c8dbc !important;
                text-decoration: underline;
            }

            hr {
                border-color: #000 !important;
                text-decoration: underline;
                margin: 0 !important;
            }


            table thead tr th {
                text-align: center;
            }
        }
    </style>
</head>

<?php
$sec = "1";
$return = $_SESSION['SESS_BACK'];
?>

<?php
if (isset($_GET['print'])) {
    $invo = base64_decode($_GET['invo']);
    $sql = "UPDATE transport_list SET type=? WHERE invoice_no=? ";
    $q = $db->prepare($sql);
    $q->execute(array('active',  $invo));

    $sql = "UPDATE transport SET type=? WHERE invoice_number=? ";
    $q = $db->prepare($sql);
    $q->execute(array('active',  $invo));
} ?>

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
                $result = $db->prepare("SELECT * FROM info ");
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                    $info_name = $row['name'];
                    $info_add = $row['address'];
                    $info_vat = $row['vat_no'];
                    $info_con = $row['phone_no'];
                    $info_mail = $row['email'];
                }

                $invo = base64_decode($_GET['invo']);
                $result = $db->prepare("SELECT * FROM transport WHERE invoice_number =:id ");
                $result->bindParam(':id', $invo);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                    $date = $row['date'];
                    $d1 = $row['date_from'];
                    $d2 = $row['date_to'];
                    $tot = $row['amount'];
                }
                $url = 'invo=' . $_GET['invo'];
                ?>
                <div class="row">

                    <div class="col-xs-12" style="display: flex;justify-content: center;">

                    </div>

                    <div class="col-xs-12 pull-right">
                        <img src="icon/Logo-Laugfs-Gas.png" alt="Logo" style="width:150px;" class="pull-right"><br><br>
                        <h5 style="text-align: right;">
                            <b><?php echo $info_name; ?></b> <br>
                            VAT Reg: <?php echo $info_vat; ?> <br>
                            <?php echo $info_add; ?> <br>
                            <?php echo $info_con; ?> <br>
                            <a href="#" style="color:blue"><?php echo $info_mail; ?></a>
                        </h5>
                    </div>

                    <div class="col-xs-12">
                        <hr>
                    </div>

                    <div class="col-xs-12">
                        <h3 style="text-align: center;">
                            TAX INVOICE
                        </h3>
                    </div>

                    <div class="col-xs-7">
                        <h5>
                            <b>INVOICE TO:</b> <br>
                            Laugfs Gas PLC <br>
                            101, Maya Avenue, Colombo 06 <br>
                            VAT Reg: 114372218-7000 <br>
                        </h5>
                    </div>

                    <div class="col-xs-5 pull-right">
                        <h5 style="float:right">
                            <b> Date:</b> <?php echo date("Y-m-d"); ?> <br>
                            <b>Invoice:</b> <?php echo $invo; ?> <br>
                        </h5>
                    </div>

                    <div class="col-xs-12">
                        <h5>
                            <b style="margin-right: 50px;">Description</b> : <?php echo $dec = 'Gas transport (From Laugfs to Narangoda Group, Galle)'; ?> <br>
                            <b style="margin-right: 50px;">Date Period</b> : <?php echo $d1 . ' To ' . $d2; ?> <br>
                        </h5>
                    </div>


                </div>

                <div class="box-body" style="margin-top: 25px;">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>NO</th>
                                            <th>DATE</th>
                                            <th>VEHICLE NO</th>
                                            <th>INVOICE NO</th>
                                            <th>5kg</th>
                                            <th>12.5kg</th>
                                            <th>37.5kg</th>
                                            <th>FROM</th>
                                            <th>RATE AMOUNT WITHOUT VAT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        date_default_timezone_set("Asia/Colombo");

                                        $transport = array();
                                        $transport_list = array();
                                        $product = array();
                                        $tot1 = 0;
                                        $tot2 = 0;
                                        $tot3 = 0;
                                        $tot4 = 0;

                                        $result = $db->prepare("SELECT * FROM transport_list WHERE invoice_no = :id AND dll=0  ");
                                        $result->bindParam(':id', $invo);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {

                                            $data = array('invo' => $row['sup_invoice'], 'pid' => $row['product_id'], 'qty' => $row['qty'], 'amount' => $row['amount']);

                                            array_push($transport_list, $data);
                                        }

                                        $result = $db->prepare("SELECT * FROM transport_list WHERE invoice_no = :id AND dll=0 GROUP BY product_id ");
                                        $result->bindParam(':id', $invo);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                            array_push($product, $row['product_id']);
                                        }

                                        $result = $db->prepare("SELECT * FROM transport_list WHERE invoice_no = :id AND dll=0 GROUP BY sup_invoice ");
                                        $result->bindParam(':id', $invo);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                            $in = $row['sup_invoice'];
                                            $temp = array();

                                            $temp['amount'] = 0;
                                            $temp['i'] =  $i + 1;
                                            $temp['invoice_no'] =  $row['sup_invoice'];
                                            $temp['location'] =  $row['location'];
                                            $temp['date'] =  $row['pu_date'];
                                            $temp['lorry_no'] =  $row['lorry_no'];

                                            foreach ($product as $p_id) {
                                                $temp[$p_id] = '';
                                            }

                                            foreach ($transport_list as $list) {

                                                if ($list['invo'] == $in) {
                                                    $temp['amount'] +=  ($list['amount'] / 118) * 100;

                                                    foreach ($product as $p_id) {

                                                        if ($p_id == $list['pid']) {
                                                            $temp[$p_id] =  $list['qty'];
                                                        }
                                                    }
                                                }
                                            }

                                            array_push($transport, $temp);
                                        }

                                        foreach ($transport as $list) {
                                        ?>
                                            <tr>
                                                <td><?php echo $list['i'];  ?></td>
                                                <td><?php echo $list['date']  ?></td>
                                                <td><?php echo $list['lorry_no'];  ?></td>
                                                <td><?php echo $list['invoice_no'];  ?></td>
                                                <td><?php echo $list['2']; ?></td>
                                                <td><?php echo $list['1']; ?></td>
                                                <td><?php echo $list['3']; ?></td>
                                                <td><?php echo $list['location']; ?></td>
                                                <td align="right"><?php echo number_format($list['amount'], 2); ?></td>
                                            </tr>
                                            <?php
                                            $tot1 += (int)$list['1'];
                                            $tot2 += (int)$list['2'];
                                            $tot3 += (int)$list['3'];
                                            $tot4 += $list['amount'];
                                            ?>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="4">Total</td>
                                            <td><?php echo $tot2; ?></td>
                                            <td><?php echo $tot1; ?></td>
                                            <td><?php echo $tot3; ?></td>
                                            <td colspan="2" align="right"><?php echo number_format($tot4, 2); ?></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <?php if (isset($_GET['print'])) { ?>
                            <div class="col-xs-5 pull-right" style="margin-top: 30px;">
                            <?php } else { ?>
                                <div class="col-xs-8" style="margin-top: 30px;">
                                <?php } ?>
                                <h4>Sub Total: <small> Rs. </small> <span><?php $tot = ($tot / 118) * 100;
                                                                            echo number_format($tot, 2); ?> </span> </h4>
                                <h4>VAT 18%: <small> Rs. </small> <span><?php $vat = $tot / 100 * 18;
                                                                        echo number_format($vat, 2); ?></span> </h4>
                                <h4>Grand Total: <small> Rs. </small> <span><?php echo number_format($tot + $vat, 2); ?></span> </h4>
                                </div>

                                <div class="col-xs-3" id="btn-box" style="display: flex;gap: 15px;justify-content: center;flex-direction: column;">
                                    <a href="transport_rp_print.php?<?php echo $url; ?>&print" class="btn btn-danger"> <i class="fa fa-print"></i> Print</a>
                                    <span style="display: none;">
                                        <form action="pdf/transport.php" method="GET" style="display: flex;gap: 15px;justify-content: center;" id="form">
                                            <input type="hidden" name="id" value="<?php echo $invo; ?>">
                                            <input type="number" class="form-control" style="width: 75%;" name="number" id="num" onkeyup="typing()" value="" placeholder="947******">

                                            <a disabled href="#" id="btn" class="btn btn-success" onclick="btn_whatsapp()"> <i class="fa fa-whatsapp"></i> Whatsapp</a>
                                        </form>
                                    </span>
                                </div>

                            </div>

                            <br><br>
                            <div class="row">
                                <div class="col-xs-4">
                                    __________________ <br> Prepared By
                                </div>
                                <div class="col-xs-4">
                                    __________________ <br> Approved By
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