<!DOCTYPE html>
<html>
<?php
include("head.php");
include("connect.php");
date_default_timezone_set("Asia/Colombo");
?>

<body class="hold-transition skin-yellow sidebar-mini">
    <div class="wrapper" style="overflow-y: hidden;">
        <?php
        include_once("auth.php");
        $r = $_SESSION['SESS_LAST_NAME'];
        $_SESSION['SESS_FORM'] = '58';

        include_once("sidebar.php");

        $invo = $_GET['id'];

        $result = $db->prepare("SELECT * FROM bulk_payment WHERE type != 'active' LIMIT 1 ");
        $result->bindParam(':id', $invo);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            // $invo = $row['invoice_no'];
        }

        $result = $db->prepare("SELECT * FROM supply_payment WHERE invoice_no=:id ");
        $result->bindParam(':id', $invo);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $chq_no = $row['chq_no'];
            $chq_date = $row['chq_date'];
            $chq_bank = $row['chq_bank'];
            $bank_id = $row['bank_id'];
            $amount = $row['amount'];
            $sup_id = $row['supply_id'];
        }

        $result = $db->prepare("SELECT sum(amount) FROM bulk_payment WHERE invoice_no =:id  ");
        $result->bindParam(':id', $invo);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $payment = $row['sum(amount)'];
        }

        $balance = $amount - $payment;
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Supplier
                    <small>Payment</small>
                </h1>

            </section>

            <!-- add item -->
            <section class="content">

                <div class="row">
                    <div class="col-md-12">

                        <div class="box box-info">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-md-2">
                                        <h3 class="box-title">Bulk Payment</h3>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label>Supplier</label>
                                            </div>
                                            <?php
                                            $result = $db->prepare("SELECT * FROM supplier WHERE supplier_id = '$sup_id' ");
                                            $result->bindParam(':id', $res);
                                            $result->execute();
                                            for ($i = 0; $row = $result->fetch(); $i++) {  ?>
                                                <input type="hidden" id="supply" value="<?php echo $row['supplier_id']; ?>">
                                                <input type="text" class="form-control" value="<?php echo $row['supplier_name']; ?>" readonly>
                                            <?php  } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="box-body d-block">
                                    <div class="row">
                                        <div class="col-md-7" style="margin-top: 25px;">
                                            <form method="POST" action="grn_bulk_list_save.php">
                                                <div class="row">

                                                    <div class="col-md-12">
                                                        <div class="form-group" id="bill"></div>
                                                    </div>

                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label>Supply Invoice</label>
                                                            <select class="form-control select2" name="invoice" id="invo" onchange="tbl_get()" style="width: 100%;" tabindex="1">
                                                                <option value="0" selected disabled> select invoice </option>
                                                                <?php
                                                                $result = $db->prepare("SELECT * FROM supply_payment WHERE dll=0 AND supply_id=:id AND pay_type='Credit'  AND credit_balance>0 GROUP BY supplier_invoice DESC");
                                                                $result->bindParam(':id', $sup_id);
                                                                $result->execute();
                                                                for ($i = 0; $row = $result->fetch(); $i++) {
                                                                    $grn = $row['invoice_no'];
                                                                    $con = 0;
                                                                    $res = $db->prepare("SELECT * FROM bulk_payment WHERE invoice_no=:id AND grn_invoice_no='$grn' ");
                                                                    $res->bindParam(':id', $invo);
                                                                    $res->execute();
                                                                    for ($i = 0; $ro = $res->fetch(); $i++) {
                                                                        $con = $ro['id'];
                                                                    }
                                                                    if ($con == 0) { ?>
                                                                        <option value="<?php echo $row['invoice_no']; ?>"> <?php echo $row['supplier_invoice']; ?> </option>
                                                                <?php }
                                                                } ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-5">
                                                        <div class="form-group">
                                                            <label>Pay Amount</label>
                                                            <input class="form-control" step=".01" type="number" name="amount" id="pay_txt" onkeyup="checking()" autocomplete="off">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2" style="height: 70px; display: flex; align-items: end;">
                                                        <div class="form-group">
                                                            <input type="hidden" name="id" value="<?php echo $invo; ?>">
                                                            <input class="btn btn-success" type="submit" id="submit" value="Submit" disabled>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                            <?php if ($balance < 0) { ?>
                                                <h1 style='color:red !important;margin: 80px 0 0 50px; position: absolute;'>
                                                <?php } else { ?>
                                                    <h1 style='color:green !important;margin: 80px 0 0 50px; position: absolute;'>
                                                    <?php } ?>

                                                    Balance: <small>Rs.</small> <?php echo number_format($balance, 2); ?>
                                                    </h1>


                                        </div>

                                        <!-- Chq details ---------------------------->

                                        <div class="col-md-5">
                                            <div class="callout callout-warning">
                                                <h4 class="pull-left"><?php echo $chq_bank; ?></h4>
                                                <h4 class="pull-right"><?php echo $chq_date; ?></h4>
                                                <br><br>
                                                <h4>Narangoda Group </h4>
                                                <hr style="margin-bottom: 10px;">
                                                <button type="button" class="btn btn-default btn-lg pull-right">Rs. <?php echo $amount; ?></button>
                                                <br><br>
                                                <hr>
                                                <center>
                                                    <h4>
                                                        <?php echo $chq_no; ?> -xxxxx': xxxxxxxx;'
                                                    </h4>
                                                </center>
                                            </div>
                                        </div>
                                        <!-- Chq details ---------------------------->
                                    </div>

                                    <form action="grn_bulk_payment_save.php" id="process-form" method="POST">
                                        <input type="hidden" name="id" value="<?php echo $invo; ?>">
                                        <input type="hidden" name="id2" value="<?php echo $_GET['id']; ?>">
                                    </form>
                                    <a class="btn btn-lg btn-danger pull-right" onclick="process()" style="margin-right: 20px;margin-top: 20px;margin-bottom: 20px;">
                                        <i class="fa fa-connectdevelop"></i> Process
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Payment List</h3>
                                <!-- /.box-header -->
                            </div>

                            <div class="box-body d-block">
                                <table id="example1" class="table table-bordered table-hover" style="border-radius: 0;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Invoice</th>
                                            <th>Supply Invoice</th>
                                            <th>Date</th>
                                            <th>Credit Balance</th>
                                            <th>Amount (Rs.)</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbl">
                                        <?php
                                        $style = '';
                                        $result = $db->prepare("SELECT * FROM bulk_payment WHERE invoice_no =:id  ");
                                        $result->bindParam(':id', $invo);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                            $dll = $row['dll'];
                                            if ($dll == 1) {
                                                $style = 'opacity: 0.5;cursor: default;';
                                            } else {
                                                $style = '';
                                            } ?>

                                            <tr id="record_<?php echo $row['id']; ?>" style="<?php echo $style; ?>">
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['invoice_no']; ?><br><span class="badge bg-green"><?php echo $row['type']; ?></span></td>
                                                <td><?php echo $row['supplier_invoice']; ?></td>
                                                <td><?php echo $row['date']; ?></td>
                                                <td><?php echo $row['forward_balance']; ?></td>
                                                <td><?php echo $row['amount']; ?></td>
                                                <td> <?php if ($dll == 0) { ?><span onclick="dll_btn ('<?php echo $row['id']; ?>')" class="btn btn-danger" title="Click to Delete"> X</span> <?php } ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>

                            </div>

                        </div>
                    </div>
                </div>

            </section>
            <!-- /.content -->
        </div>

        <?php include("dounbr.php"); ?>

        <?php
        $error_id = 0;
        $unit = 0;
        $err = 'd-none';
        if (isset($_GET['error'])) {
            $error_id = $_GET['error'];
            $unit = $_GET['unit'];
            $err = '';
        } ?>

        <div class="container-up <?php echo $err; ?>" id="container_up">
            <div class="container-close" onclick="click_close()"></div>
            <div class="row">
                <div class="col-md-12">

                    <div class="box box-success popup <?php echo $err; ?>" id="popup_1" style="padding: 5px;border: 0;">
                        <div class="alert alert-danger alert-dismissible" style="width: 350px;margin: 0;">
                            <button type="button" class="close" onclick="click_close()" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                            <?php

                            if ($unit == 1) {
                                if ($error_id == 'invalid') {
                                    echo "The amount paid is more than the credit amount ..!";
                                } else {
                                    echo "This Bill already ADD ..!";
                                }
                            }

                            if ($unit == 2) {
                                echo "Unbalance CHQ amount to Payment total ..!";
                            }

                            ?>
                        </div>
                    </div>

                    <div class="box box-success popup d-none" id="popup_2" style="padding: 5px;border: 0;">
                        <div class="alert alert-danger alert-dismissible" style="width: 350px;margin: 0;">
                            <button type="button" class="close" onclick="click_close()" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4><i class="icon fa fa-ban"></i> Alert!</h4>
                            Unbalance CHQ amount to Payment total
                        </div>
                    </div>

                    <div class="box box-success popup d-none" id="popup_3" style="width: 358px;display: flex;flex-direction: column;justify-content: space-between;">

                        <h4>Sure you want to process this ? </h4>
                        <hr style="margin: 10px 0;border-color:#999;">
                        <div style="display: flex;align-items:center;justify-content:space-around;margin:10px 0;">
                            <button onclick="check_process('cancel')" class="btn btn-primary">Cancel</button>
                            <button onclick="check_process('process')" class="btn btn-danger">Process</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="control-sidebar-bg"></div>
    </div>


    <?php include_once("script.php"); ?>

    <!-- DataTables -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
    <!-- SlimScroll -->
    <script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- FastClick -->
    <script src="../../plugins/fastclick/fastclick.js"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap datepicker -->
    <script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>
    <!-- bootstrap color picker -->

    <script>
        function click_open(i) {
            $("#popup_" + i).removeClass("d-none");
            $("#container_up").removeClass("d-none");
        }

        function click_close() {
            $(".popup").addClass("d-none");
            $("#container_up").addClass("d-none");
        }
    </script>
    <script>
        function process() {
            let balance = <?php echo $balance; ?>;

            if (balance > 0) {
                $("#popup_2").removeClass("d-none");
                $("#container_up").removeClass("d-none");
            } else {
                $("#popup_3").removeClass("d-none");
                $("#container_up").removeClass("d-none");
            }
        }

        function check_process(val) {
            if (val == 'process') {
                $('#process-form').submit();
            }
            if (val == 'cancel') {
                $(".popup").addClass("d-none");
                $("#container_up").addClass("d-none");
            }
        }
    </script>

    <script type="text/javascript">
        function checking() {

            let am = $("#pay_txt").val();
            let blc = <?php echo $balance; ?>;

            if (0.01 >= am | blc == 0) {
                $('#submit').attr("disabled", "");
            } else {
                $('#submit').removeAttr("disabled");
            }
        }

        function tbl_get() {
            let val = $("#invo").val();

            var info = 'type=bill_get&id=' + val;
            $.ajax({
                type: "GET",
                url: "grn_payment_get.php",
                data: info,
                success: function(res) {
                    $("#bill").empty();
                    $("#bill").append(res);
                }
            });

            info = 'type=tbl_get&id=<?php echo $invo; ?>';
            $.ajax({
                type: "GET",
                url: "grn_bulk_get.php",
                data: info,
                success: function(res) {
                    $("#tbl").empty();
                    $("#tbl").append(res);
                }
            });

            $("#invo_no").val(val);
        }

        function dll_btn(id) {
            var info = 'id=' + id;
            if (confirm("Sure you want to delete this Collection? There is NO undo!")) {

                $.ajax({
                    type: "GET",
                    url: "grn_bulk_list_dll.php",
                    data: info,
                    success: function() {
                        tbl_get();
                    }
                });
                tbl_get();
            }
            return false;
        }

        $(function() {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": true
            });
        });
    </script>


    <!-- Page script -->
    <script>
        $(function() {
            //Initialize Select2 Elements
            $(".select2").select2();


            //Date range picker
            $('#reservation').daterangepicker();
            //Date range picker with time picker
            //$('#datepicker').datepicker({datepicker: true,  format: 'yyyy/mm/dd '});
            //Date range as a button


            //Date picker
            $('#datepicker').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy-mm-dd '
            });
            $('#datepicker1').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy-mm-dd '
            });
            $('#datepicker').datepicker({
                autoclose: true
            });

            $('#datepickerd').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy-mm-dd '
            });
            $('#datepickerd').datepicker({
                autoclose: true
            });

        });
    </script>


</body>

</html>