<!DOCTYPE html>
<html>
<?php
include("head.php");
include("connect.php");
?>

<body class="hold-transition skin-yellow sidebar-mini">
    <div class="wrapper" style="overflow-y: hidden;">
        <?php
        include_once("auth.php");
        $r = $_SESSION['SESS_LAST_NAME'];
        $_SESSION['SESS_FORM'] = '55';
        include_once("sidebar.php");


        $u = $_SESSION['SESS_MEMBER_ID'];
        $load = 0;
        $invo = '';

        if (isset($_GET['id'])) {
            $load = $_GET['id'];
            $invo = $_GET['invo'];
        }

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    GRN
                    <small>Preview</small>
                </h1>

            </section>
            <!-- Main content -->
            <section class="content">
                <!-- SELECT2 EXAMPLE -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">GRN Add</h3>
                                <!-- /.box-header -->
                            </div>

                            <div class="box-body d-block">
                                <form method="POST" action="grn_list_save.php">

                                    <div class="row">

                                        <div class="col-md-12 m-0">
                                            <div class="form-group" id="status"></div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <label>Product</label>
                                                    </div>
                                                    <select class="form-control select2" name="product" id="p_sel" onchange="pro_select()" style="width: 100%;" tabindex="1" autofocus>
                                                        <?php
                                                        $result = $db->prepare("SELECT * FROM products ");
                                                        $result->bindParam(':id', $res);
                                                        $result->execute();
                                                        for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                            <option value="<?php echo $row['product_id']; ?>">
                                                                <?php echo $row['gen_name']; ?>
                                                            </option>
                                                        <?php  } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <label>Qty</label>
                                                    </div>
                                                    <input type="number" step=".01" class="form-control" value="1" name="qty" tabindex="2">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 hidden">
                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <label>Dis %</label>
                                                    </div>
                                                    <input type="number" step=".01" class="form-control" name="dic" tabindex="2">
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-3 hidden">
                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <label>Sell Price</label>
                                                    </div>
                                                    <input type="number" step=".01" id="sell1" class="form-control" name="sell" tabindex="2">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <label>Cost Price</label>
                                                    </div>
                                                    <input type="number" step=".01" id="cost1" class="form-control" name="cost" tabindex="2">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?php echo $load; ?>">
                                                <input type="hidden" name="invo" value="<?php echo $invo; ?>">
                                                <input type="hidden" name="type" value="GRN">
                                                <input class="btn btn-warning" type="submit" value="Save">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="box-body d-block">
                                <table id="example2" class="table table-bordered table-hover" style="border-radius: 0;">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Product Name</th>
                                            <th>QTY</th>
                                            <th>Dic (Rs.)</th>
                                            <th>Cost (Rs.)</th>
                                            <th>Amount (Rs.)</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0;
                                        $style = "";
                                        $result = $db->prepare("SELECT * FROM purchases_item WHERE invoice = '$invo' ");
                                        $result->bindParam(':userid', $res);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                            $pid = $row['product_id'];

                                            $re = $db->prepare("SELECT * FROM products WHERE product_id = '$pid' ");
                                            $re->bindParam(':userid', $res);
                                            $re->execute();
                                            for ($k = 0; $rw = $re->fetch(); $k++) {
                                                $stock = $rw['qty'];
                                            }
                                            if ($stock < 0) {
                                                $style = 'style="color:red" ';
                                            }

                                        ?>
                                            <tr <?php echo $style; ?> class="record">
                                                <td><?php echo $i + 1; ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['qty']; ?></td>
                                                <td><?php echo $row['discount']; ?></td>
                                                <td><?php echo $row['cost']; ?></td>
                                                <td><?php echo $row['amount']; ?></td>
                                                <td> <a href="#" id="<?php echo $row['transaction_id']; ?>" class="dll_btn btn btn-danger btn-sm fa fa-times" title="Click to Delete"></a></td>
                                                <?php $total += $row['amount']; ?>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <h4>Total: <small> Rs. </small> <b><?php echo number_format($total, 2); ?> </b></h4>

                            </div>

                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">GRN Save</h3>
                                <!-- /.box-header -->
                            </div>
                            <div class="form-group">
                                <div class="box-body d-block">
                                    <form method="POST" action="grn_save.php" id="process-form">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Location</label>
                                                    <select class="form-control select2 hidden-search" name="location" onchange="select_location(this.options[this.selectedIndex].getAttribute('value'),this.options[this.selectedIndex].getAttribute('amount'))" style="width: 100%;" tabindex="1" autofocus>
                                                        <option value="0" disabled selected>Select Location</option>
                                                        <?php
                                                        $result = $db->prepare("SELECT * FROM purchases_location WHERE action = 1 ");
                                                        $result->bindParam(':id', $invo);
                                                        $result->execute();
                                                        for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                            <option value="<?php echo $row['id']; ?>" amount="<?php echo $row['transport']; ?>"><?php echo $row['location']; ?></option>
                                                        <?php    } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Purchases Date</label>
                                                    <input class="form-control" id="datepicker2" type="text" name="pu_date" autocomplete="off" required>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Pay Type</label>
                                                    <select class="form-control select2 hidden-search" name="pay_type" onchange="select_pay()" id="method">
                                                        <option>Chq</option>
                                                        <option>Bank</option>
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

                                            <div class="col-md-3 slt-chq" style="display:block;">
                                                <div class="form-group">
                                                    <label>Account No</label>
                                                    <select class="form-control select2 hidden-search" name="acc" style="width: 100%;" tabindex="1" autofocus>
                                                        <?php
                                                        $result = $db->prepare("SELECT * FROM bank_balance ");
                                                        $result->bindParam(':id', $invo);
                                                        $result->execute();
                                                        for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                            <option value="<?php echo $row['id']; ?>"><?php echo $row['name'] . ' | ' . $row['ac_no']; ?></option>
                                                        <?php    } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3 slt-chq" style="display:block;">
                                                <div class="form-group">
                                                    <label>Chq Number</label>
                                                    <input class="form-control" type="text" name="chq_no" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3 slt-chq" style="display:block;">
                                                <div class="form-group">
                                                    <label>Chq Date</label>
                                                    <input class="form-control" id="datepicker1" type="text" name="chq_date" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Supply Invoice</label>
                                                    <input class="form-control" type="text" name="sup_invoice" autocomplete="off" required>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Note</label>
                                                    <input class="form-control" type="text" name="note" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Pay Amount</label>
                                                    <input class="form-control" type="number" step=".01" name="amount" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Transport Amount</label>
                                                    <input class="form-control" type="number" step=".01" id="transport" name="transport" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3" style="height: 75px;display: flex; align-items: end;">
                                                <div class="form-group">
                                                    <input type="hidden" name="id" value="<?php echo $load; ?>">
                                                    <input type="hidden" name="invo" value="<?php echo $invo; ?>">
                                                    <input type="hidden" name="supply" value="1">
                                                    <input type="hidden" name="type" value="GRN">
                                                    <input type="hidden" name="new_invo" id="new_invo" value="0">

                                                    <?php if ($load > 0) { ?>
                                                        <input class="btn btn-success" type="button" onclick="click_open(3)" value="Submit" style="width: 100px;">
                                                    <?php } else { ?>
                                                        <input class="btn btn-info" type="submit" value="Submit" style="width: 100px;">
                                                    <?php } ?>

                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
        <!-- /.content-wrapper -->
        <?php include("dounbr.php"); ?>

        <?php
        $err = '';
        $err1 = '';
        $err2 = 'd-none';
        $err3 = 'd-none';
        $closer = '<div class="container-close" onclick="click_close()"></div>';
        $closer2 = '<a href="index.php" class="container-close"></a>';
        if (isset($_GET['id'])) {
            $err = 'd-none';
            $err1 = 'd-none';
            $closer2 = '';
        }
        if (isset($_GET['err'])) {
            if ($_GET['err'] == 1) {
                $err = '';
                $err2 = '';
                $closer2 = '';
                $closer = '<div class="container-close" onclick="click_close()"></div>';
            } else if ($_GET['err'] == 2) {
                $err = '';
                $err3 = '';
                $closer2 = '';
                $closer = '<div class="container-close" onclick="click_close()"></div>';
            } else {
                $err = 'd-none';
                $closer = '';
                $closer2 = '';
            }
        } ?>

        <div class="container-up <?php echo $err; ?>" id="container_up">
            <?php echo $closer; ?>
            <?php echo $closer2; ?>
            <div class="row">
                <div class="col-md-12">

                    <div class="box box-success popup  <?php echo $err1; ?>" style="width: 300px;">
                        <div class="box-header with-border">
                            <h3 class="box-title"> Select Lorry </h3>
                            <a href="index.php" class="btn me-2 pull-right btn-xs btn-danger"><i class="fa fa-times"></i></a>
                        </div>

                        <div class="box-body d-block">

                            <form method="POST" action="grn_loading_list_save.php">
                                <div class="row" style="display: block;">

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Loading Lorry</label>
                                            <select class="form-control select2" name="id" style="width:100%">
                                                <option value="0"> Without the lorry </option>
                                                <?php
                                                $result = $db->prepare("SELECT * FROM loading WHERE  action='load' AND type = 'purchases' ");
                                                $result->bindParam(':id', $res);
                                                $result->execute();
                                                for ($i = 0; $row = $result->fetch(); $i++) {
                                                ?>
                                                    <option value="<?php echo $row['transaction_id']; ?>"><?php echo $row['lorry_no']; ?> </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <input class="btn btn-warning" type="submit" style="width: 100%;" value="Purchasing">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="box box-success popup  <?php echo $err2; ?>" style="padding: 5px;border: 0;">
                        <div class="alert alert-dismissible" style="width: 350px;margin: 0;">
                            <button type="button" class="close" onclick="click_close()" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4 class="text-red"><i class="icon fa fa-ban"></i> Alert!</h4>
                            This Item already ADD ..!
                        </div>
                        <a href="#" class="btn btn-warning" onclick="click_close()" style="margin: 10px 0; width: 100%;"><b>Add New Item</b></a>
                    </div>

                    <div class="box box-success popup d-none" id="popup_3" style="width: 400px;display: flex;flex-direction: column;justify-content: space-between;">

                        <h4>Do you want to create a new invoice for this loading? </h4>
                        <hr style="margin: 10px 0;border-color:#999;">
                        <div style="display: flex;align-items:center;justify-content:space-around;margin:10px 0;">
                            <button onclick="check_process('no')" style="width: 100px;" class="btn btn-danger">No</button>
                            <button onclick="check_process('yes')" style="width: 100px;" class="btn btn-primary">Yes</button>
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

    <script>
        function click_open(i) {
            $("#popup_" + i).removeClass("d-none");
            $("#container_up").removeClass("d-none");
        }

        function click_close() {
            $(".popup").addClass("d-none");
            $("#container_up").addClass("d-none");
        }

        function check_process(val) {
            if (val == 'yes') {

                if ($('input[name="sup_invoice"]').val() !== '' && $('input[name="pu_date"]').val() !== '' && $('select[name="location"]').val() !== null) {
                    $('#new_invo').val(0);
                    $('#process-form').submit();
                } else {
                    click_close()
                }

                if ($('input[name="sup_invoice"]').val() === '') {
                    $('input[name="sup_invoice"]').css('border-color', 'red');
                    click_close()
                } else {
                    $('input[name="sup_invoice"]').css('border-color', $('input[name="amount"]').css('border-color'));
                }
                if ($('input[name="pu_date"]').val() === '') {
                    $('input[name="pu_date"]').css('border-color', 'red');
                    click_close()
                } else {
                    $('input[name="pu_date"]').css('border-color', $('input[name="amount"]').css('border-color'));
                }
                if ($('select[name="location"]').val() === '') {
                    $('select[name="location"]').css('border-color', 'red');
                    click_close()
                } else {
                    $('select[name="location"]').css('border-color', $('input[name="amount"]').css('border-color'));
                }
            }
            if (val == 'no') {

                if ($('input[name="sup_invoice"]').val() !== '' && $('input[name="pu_date"]').val() !== '' && $('select[name="location"]').val() !== null) {
                    $('#new_invo').val(1);
                    $('#process-form').submit();
                } else {
                    click_close()
                }

                if ($('input[name="sup_invoice"]').val() === '') {
                    $('input[name="sup_invoice"]').css('border-color', 'red');
                    click_close()
                } else {
                    $('input[name="sup_invoice"]').css('border-color', $('input[name="amount"]').css('border-color'));
                }
                if ($('input[name="pu_date"]').val() === '') {
                    $('input[name="pu_date"]').css('border-color', 'red');
                    click_close()
                } else {
                    $('input[name="pu_date"]').css('border-color', $('input[name="amount"]').css('border-color'));
                }
                if ($('select[name="location"]').val() === '') {
                    $('select[name="location"]').css('border-color', 'red');
                    click_close()
                } else {
                    $('select[name="location"]').css('border-color', $('input[name="amount"]').css('border-color'));
                }
            }
        }
    </script>

    <script type="text/javascript">
        function pro_select() {
            let val = $('#p_sel').val();
            var info = 'id=' + val + '&type=GRN&ac=0';
            $.ajax({
                type: "GET",
                url: "grn_status.php",
                data: info,
                success: function(res) {
                    $("#status").empty();
                    $("#status").append(res);
                }
            });
            info = 'id=' + val + '&type=GRN&ac=1';
            $.ajax({
                type: "GET",
                url: "grn_status.php",
                data: info,
                success: function(res) {
                    $("#sell1").val(parseFloat(res));
                }
            });
            info = 'id=' + val + '&type=GRN&ac=2';
            $.ajax({
                type: "GET",
                url: "grn_status.php",
                data: info,
                success: function(res) {
                    $("#cost1").val(parseFloat(res));
                }
            });
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
        }

        function select_location(val, amount) {
            $('#transport').val(amount);
        }

        $(".dll_btn").click(function() {
            var element = $(this);
            var id = element.attr("id");
            var info = 'id=' + id;
            if (confirm("Sure you want to delete this product? There is NO undo!")) {

                $.ajax({
                    type: "GET",
                    url: "grn_list_dll.php",
                    data: info,
                    success: function() {

                    }
                });
                $(this).parents(".record").animate({
                        backgroundColor: "#fbc7c7"
                    }, "fast")
                    .animate({
                        opacity: "hide"
                    }, "slow");
            }
            return false;
        });
    </script>

    <!-- Page script -->
    <script>
        $(function() {
            $('#example2').DataTable();
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
            $('#datepicker1').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy-mm-dd '
            });
            $('#datepicker2').datepicker({
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