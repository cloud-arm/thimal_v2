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

                        <div class="box box-info payment-type" id="normal">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-md-2">
                                        <h3 class="box-title">Normal Payment</h3>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-danger btn-sm" onclick="pay_type('bulk')">Bulk Payment</button>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label>Supplier</label>
                                            </div>
                                            <?php
                                            $result = $db->prepare("SELECT * FROM supplier ");
                                            $result->bindParam(':id', $res);
                                            $result->execute(); ?>
                                            <select class="form-control select2" id="supply" onchange="invo_get('supply')" style="width: 100%;" tabindex="1">
                                                <option value="0" selected disabled> Select Supplier </option>
                                                <?php for ($i = 0; $row = $result->fetch(); $i++) {  ?>
                                                    <option value="<?php echo $row['supplier_id']; ?>"> <?php echo $row['supplier_name']; ?> </option>
                                                <?php  } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="box-body d-block">
                                    <form method="POST" action="grn_payment_save.php">
                                        <div class="row">

                                            <div class="col-md-12">
                                                <div class="form-group" id="bill"></div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Supply Invoice</label>
                                                    <select class="form-control select2" name="invoice" onchange="tbl_get(this.value)" id="invo" style="width: 100%;" tabindex="1">
                                                        <option value="0" selected disabled> select invoice </option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Pay Type</label>
                                                    <select class="form-control select2 hidden-search" onchange="select_pay()" name="pay_type" id="method">
                                                        <option>Cash</option>
                                                        <option>Bank</option>
                                                        <option>Chq</option>
                                                        <option>Credit_note</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3 slt-bank" style="display:none;">
                                                <div class="form-group">
                                                    <label>Account No</label>
                                                    <input class="form-control" type="text" name="acc_no" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3 slt-bank" style="display:none;">
                                                <div class="form-group">
                                                    <label>Bank Name</label>
                                                    <input class="form-control" type="text" name="bank_name" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-4" id="slt-credit" style="display:none;">
                                                <div class="form-group">
                                                    <label>Credit Invoice</label>
                                                    <select class="form-control select2 hidden-search" style="width: 100%;" name="credit_note" id="credit_note">
                                                        <option value="0" selected></option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3 slt-chq" style="display:none;">
                                                <div class="form-group">
                                                    <label>Chq Number</label>
                                                    <input class="form-control" type="text" name="chq_no" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3 slt-chq" style="display:none;">
                                                <div class="form-group">
                                                    <label>Chq Bank</label>
                                                    <?php
                                                    $result = $db->prepare("SELECT * FROM bank_balance ");
                                                    $result->bindParam(':id', $res);
                                                    $result->execute(); ?>
                                                    <select class="form-control select2 hidden-search" name="chq_bank" style="width: 100%;" tabindex="1">
                                                        <option value="0" selected disabled> Select Bank </option>
                                                        <?php for ($i = 0; $row = $result->fetch(); $i++) {  ?>
                                                            <option value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?> </option>
                                                        <?php  } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3 slt-chq" style="display:none;">
                                                <div class="form-group">
                                                    <label>Chq Date</label>
                                                    <input class="form-control" id="datepicker" type="text" name="chq_date" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Pay Amount</label>
                                                    <input class="form-control" step=".01" type="number" name="amount" id="pay_txt" onkeyup="checking()" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Note</label>
                                                    <input class="form-control" type="text" name="note" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-1 ps-0" style="height: 70px; display: flex; align-items: end;">
                                                <div class="form-group">
                                                    <input type="hidden" name="id" id="invo_no">
                                                    <input type="hidden" name="sup_id" id="sup_id_supply">
                                                    <input class="btn btn-success" type="submit" id="submit" value="Submit" disabled>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="box box-warning payment-type" id="bulk" style="display: none; background-color: rgba(var(--bg-dark-50),0.2);">
                            <div class="box-header with-border">
                                <div class="row">
                                    <div class="col-md-2">
                                        <h3 class="box-title">Bulk Payment</h3>
                                    </div>
                                    <div class="col-md-2">
                                        <button class="btn btn-info btn-sm" onclick="pay_type('normal')">Normal Payment</button>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label>Supplier</label>
                                            </div>
                                            <?php
                                            $result = $db->prepare("SELECT * FROM supplier ");
                                            $result->bindParam(':id', $res);
                                            $result->execute(); ?>
                                            <select class="form-control select2" id="bulk_supply" onchange="invo_get('bulk_supply')" style="width: 100%;" tabindex="1">
                                                <option value="0" selected disabled> Select Supplier </option>
                                                <?php for ($i = 0; $row = $result->fetch(); $i++) {  ?>
                                                    <option value="<?php echo $row['supplier_id']; ?>"> <?php echo $row['supplier_name']; ?> </option>
                                                <?php  } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="box-body d-block">
                                    <form method="POST" action="grn_bulk_chq_save.php">
                                        <div class="row" style="display: flex;">

                                            <div class="col-md-3 slt-chq">
                                                <div class="form-group">
                                                    <label>Chq Number</label>
                                                    <input class="form-control" type="text" name="chq_no" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3 slt-chq">
                                                <div class="form-group">
                                                    <label>Chq Date</label>
                                                    <input class="form-control" id="datepicker1" type="text" name="chq_date" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Chq Amount</label>
                                                    <input class="form-control" step=".01" type="number" name="amount" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Note</label>
                                                    <input class="form-control" type="text" name="note" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-1 ps-0" style="height: 70px; display: flex; align-items: end;">
                                                <div class="form-group">
                                                    <input type="hidden" name="sup_id" id="sup_id_bulk_supply">
                                                    <input type="hidden" name="pay_type" value="Chq">
                                                    <input class="btn btn-success" id="btn_bulk" disabled type="submit" value="Submit">
                                                </div>
                                            </div>
                                        </div>
                                    </form>
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
                                            <th>Date</th>
                                            <th>Type</th>
                                            <th>Chq No</th>
                                            <th>Chq Date</th>
                                            <th>Bank Name</th>
                                            <th>Acc No</th>
                                            <th>Amount (Rs.)</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbl"> </tbody>
                                </table>

                            </div>

                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Pending Payment List</h3>
                                <!-- /.box-header -->
                            </div>

                            <div class="box-body d-block">
                                <table id="example2" class="table table-bordered table-hover" style="border-radius: 0;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Invoice</th>
                                            <th>Date</th>
                                            <th>Chq No</th>
                                            <th>Chq Date</th>
                                            <th>Bank Name</th>
                                            <th>Amount (Rs.)</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>

                                    <tbody id="tbl">
                                        <?php
                                        $style = '';
                                        $result = $db->prepare("SELECT * FROM supply_payment WHERE action = 11 AND dll = 0 ");
                                        $result->bindParam(':id', $invo);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                            $pt = $row['pay_type'];
                                            $dll = $row['dll'];
                                            if ($dll == 1) {
                                                $style = 'opacity: 0.5;cursor: default;';
                                            } else {
                                                $style = '';
                                            } ?>

                                            <tr id="bulk_<?php echo $row['id']; ?>" style="<?php echo $style; ?>">
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['invoice_no']; ?></td>
                                                <td><?php echo $row['date']; ?></td>
                                                <td><?php echo $row['chq_no']; ?></td>
                                                <td><?php echo $row['chq_date']; ?></td>
                                                <?php if ($pt == 'Chq') { ?>
                                                    <td><?php echo $row['chq_bank']; ?></td>
                                                <?php } else { ?>
                                                    <td><?php echo $row['bank_name']; ?></td>
                                                <?php } ?>
                                                <td><?php echo $row['amount']; ?></td>
                                                <td>
                                                    <?php if ($dll == 0) { ?><span onclick="bulk_dll_btn ('<?php echo $row['id']; ?>')" class="btn btn-sm btn-danger" title="Click to Delete"> X</span> <?php } ?>
                                                    <a href="grn_bulk_payment.php?id=<?php echo $row['invoice_no']; ?>" class="btn btn-sm btn-success" title="Click to Process"> <i class="fa fa-connectdevelop"></i> </a>
                                                </td>
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

        <div class="control-sidebar-bg"></div>
    </div>


    <?php include_once("script.php"); ?>

    <!-- DataTables -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap datepicker -->
    <script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>
    <!-- SlimScroll 1.3.0 -->
    <script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
    <!-- iCheck 1.0.1 -->
    <script src="../../plugins/iCheck/icheck.min.js"></script>
    <!-- FastClick -->
    <script src="../../plugins/fastclick/fastclick.js"></script>

    <script type="text/javascript">
        function pay_type(type) {
            $('.payment-type').css('display', 'none');
            $('#' + type).css('display', 'block');
        }

        function checking() {

            let val = $("#credit_note").val();
            var info = 'type=cred_set&id=' + val;
            $.ajax({
                type: "GET",
                url: "grn_payment_get.php",
                data: info,
                success: function(res) {
                    $("#cr_blc").val(res);
                }
            });

            let am = $("#pay_txt").val();
            let pt = $("#method").val();
            let blc = parseInt($("#blc").val());
            let cr_blc = parseInt($("#cr_blc").val());

            if (0 >= am) {
                $('#submit').attr("disabled", "");
            } else

            if (pt == 'Credit_Note') {

                if (am <= cr_blc) {
                    $('#submit').removeAttr("disabled");
                } else {
                    $('#submit').attr("disabled", "");
                }

            } else

            if (am <= blc + 1) {
                $('#submit').removeAttr("disabled");
            } else {
                $('#submit').attr("disabled", "");
            }
        }

        function invo_get(id) {
            let val = $("#" + id).val();
            if (id == 'supply') {
                var info = 'type=inv_get&id=' + val;
                $.ajax({
                    type: "GET",
                    url: "grn_payment_get.php",
                    data: info,
                    success: function(res) {
                        $("#invo").empty();
                        $("#invo").append(res);
                    }
                });
            }

            $("#sup_id_" + id).val(val);
            if (id == 'bulk_supply') {
                $("#btn_bulk").removeAttr('disabled');
            } else {
                $("#btn_bulk").attr('disabled', '');
            }
        }

        function tbl_get(val) {

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

            info = 'type=tbl_get&id=' + val;
            $.ajax({
                type: "GET",
                url: "grn_payment_get.php",
                data: info,
                success: function(res) {
                    $("#tbl").empty();
                    $("#tbl").append(res);
                }
            });

            $("#invo_no").val(val);
        }

        function select_pay() {
            var val = $('#method').val();
            if (val == "Bank") {
                $('.slt-bank').css("display", "block");
            } else {
                $('.slt-bank').css("display", "none");
            }

            if (val == "Chq") {
                $('.slt-chq').css("display", "block");
            } else {
                $('.slt-chq').css("display", "none");
            }

            if (val == "Credit_note") {
                $('#slt-credit').css("display", "block");

                let val = $("#supply").val();
                var info = 'type=cred_get&id=' + val;
                $.ajax({
                    type: "GET",
                    url: "grn_payment_get.php",
                    data: info,
                    success: function(res) {
                        $("#credit_note").empty();
                        $("#credit_note").append(res);
                    }
                });

            } else {
                $('#slt-credit').css("display", "none");
            }
        }

        function dll_btn(id) {
            var info = 'id=' + id;
            if (confirm("Sure you want to delete this Collection? There is NO undo!")) {

                $.ajax({
                    type: "GET",
                    url: "grn_payment_dll.php",
                    data: info,
                    success: function(res) {
                        tbl_get();
                        invo_get();
                    }
                });
                tbl_get();
                invo_get();
            }
            return false;
        }

        function bulk_dll_btn(id) {
            var info = 'id=' + id;
            if (confirm("Sure you want to delete this Bulk Payment? There is NO undo!")) {

                $.ajax({
                    type: "GET",
                    url: "grn_credit_note_dll.php",
                    data: info,
                    success: function() {}
                });
                $("#bulk_" + id).animate({
                        backgroundColor: "#fbc7c7"
                    }, "fast")
                    .animate({
                        opacity: "hide"
                    }, "slow");
            }
            return false;
        }

        $(function() {
            $("#example1").DataTable();
            $("#example2").DataTable();
            $('#example3').DataTable({
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
            $('.select2.hidden-search').select2({
                minimumResultsForSearch: -1
            });

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