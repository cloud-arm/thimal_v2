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
        $_SESSION['SESS_FORM'] = '3';

        include_once("sidebar.php");


        if (isset($_GET['id'])) {
            $invo = $_GET['id'];
        } else {
            $invo = date('ymdhis');
        }

        $u = $_SESSION['SESS_MEMBER_ID'];

        //checking duplicate
        $con = 0;
        $result = $db->prepare("SELECT * FROM sales WHERE invoice_number = :id  ");
        $result->bindParam(':id', $invo);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $con = $row['transaction_id'];
        }

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Sales
                    <small>Preview</small>
                </h1>

            </section>
            <!-- Main content -->
            <section class="content">
                <!-- SELECT2 EXAMPLE -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Product Add</h3>
                                <!-- /.box-header -->
                                <p class="pull-right" id="invoice_no"><?php echo $invo ?></p>
                            </div>

                            <div class="box-body d-block">
                                <form method="POST" action="sales_list_save.php">

                                    <div class="row">

                                        <div class="col-md-12 m-0">
                                            <div class="form-group" id="status"></div>
                                        </div>

                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <label>Product</label>
                                                    </div>
                                                    <select class="form-control select2" name="product" id="pro_sel" onchange="pro_select()" style="width: 100%;" tabindex="1" autofocus>
                                                        <?php
                                                        $result = $db->prepare("SELECT * FROM products  ");
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
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <label>Qty</label>
                                                    </div>
                                                    <input type="number" step=".01" class="form-control" id="qty_in" value="1" name="qty" tabindex="2">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon dic">
                                                        <label>Dis %</label>
                                                    </div>
                                                    <input type="number" step=".01" onkeyup="checking_discount()" id="dic" class="form-control dic" name="dic" tabindex="2">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <label>Price</label>
                                                    </div>
                                                    <input type="number" step=".01" id="price" class="form-control" name="price" tabindex="2">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?php echo $invo; ?>">
                                                <input class="btn btn-warning" type="submit" value="Add Product" id="btn_list" style="width: 100%;">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="box-body d-block">
                                <table id="example2" class="table table-bordered table-hover" style="border-radius: 0;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Product Name</th>
                                            <th>QTY</th>
                                            <th>Dic (Rs.)</th>
                                            <th>Price (Rs.)</th>
                                            <th>Amount (Rs.)</th>
                                            <th>#</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $total = 0;
                                        $style = "";
                                        $result = $db->prepare("SELECT * FROM sales_list WHERE invoice_no = '$invo' ");
                                        $result->bindParam(':id', $res);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                            $pro_id = $row['product_id'];
                                        ?>
                                            <tr class="record">
                                                <td><?php echo $i + 1; ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['qty']; ?></td>
                                                <td><?php echo $row['dic']; ?></td>
                                                <td><?php echo $row['price']; ?></td>
                                                <td><?php echo $row['amount']; ?></td>
                                                <td> <a href="#" id="<?php echo $row['id']; ?>" class="dll_btn btn btn-danger btn-sm fa fa-times" title="Click to Delete"> </a></td>
                                                <?php $total += $row['amount']; ?>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                                <h4>Total: <small> Rs. </small> <b id="bill_total"> <?php echo number_format($total, 2); ?> </b></h4>

                            </div>

                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Sales Payment</h3>
                                <!-- /.box-header -->
                            </div>
                            <div class="form-group">
                                <div class="box-body d-block">
                                    <form method="POST" action="sales_save.php">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Customer</label>
                                                    <select class="form-control select2" id="customer" name="customer" style="width: 100%;" tabindex="1" autofocus>
                                                        <?php
                                                        $result = $db->prepare("SELECT * FROM customer ");
                                                        $result->bindParam(':id', $invo);
                                                        $result->execute();
                                                        for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                            <option value="<?php echo $row['customer_id']; ?>"><?php echo $row['customer_name']; ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Date</label>
                                                    <input class="form-control" id="<?php if ($r == 'admin') { ?>datepicker<?php } ?>" type="text" value="<?php echo date('Y-m-d'); ?>" name="date" autocomplete="off" readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Pay Type</label>
                                                    <select class="form-control select2 hidden-search" name="pay_type" onchange="select_pay()" id="method">
                                                        <option value="Cash">Cash</option>
                                                        <option value="Chq">Chq</option>
                                                        <option value="Credit">Credit</option>
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
                                                    <label>Chq Date</label>
                                                    <input class="form-control" id="datepicker1" type="text" name="chq_date" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3 slt-chq" style="display:none;">
                                                <div class="form-group">
                                                    <label>Bank Name</label>
                                                    <input class="form-control" type="text" name="chq_bank" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3 slt-pay">
                                                <div class="form-group">
                                                    <label>Pay Amount</label>
                                                    <input class="form-control" type="number" id="pay_amount" step=".01" value="0" name="amount" autocomplete="off" required>
                                                </div>
                                            </div>

                                            <div class="col-md-2" style="height: 75px;display: flex; align-items: end;">
                                                <div class="form-group">
                                                    <input type="hidden" name="id" value="<?php echo $invo; ?>">
                                                    <input class="btn btn-success" type="submit" value="Submit" style="width: 100px;">
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
        $err = 'd-none';
        $err1 = 'd-none';
        $err2 = 'd-none';
        $err3 = 'd-none';
        $err4 = 'd-none';
        $closer = '';
        if ($con > 0) {
            $err = '';
            $err1 = '';
        }
        if (isset($_GET['err'])) {
            if ($_GET['err'] == 1) {
                $err = '';
                $err2 = '';
                $closer = '<div class="container-close" onclick="click_close()"></div>';
            } else if ($_GET['err'] == 2) {
                $err = '';
                $err3 = '';
                $closer = '<div class="container-close" onclick="click_close()"></div>';
            } else if ($_GET['err'] == 3) {
                $err = '';
                $err4 = '';
                $closer = '<div class="container-close" onclick="click_close()"></div>';
            } else {
                $err = 'd-none';
                $closer = '';
            }
            echo $_GET['err'];
        } ?>

        <div class="container-up <?php echo $err; ?>" id="container_up">
            <?php echo $closer; ?>
            <div class="row">
                <div class="col-md-12">

                    <div class="box box-success popup  <?php echo $err1; ?>" style="padding: 5px;border: 0;">
                        <div class="alert alert-dismissible" style="width: 350px;margin: 0;">
                            <h4 class="text-red"><i class="icon fa fa-ban"></i> Alert!</h4>
                            This Bill already ADD ..!
                        </div>
                        <a href="sales.php?id=<?php echo date('ymdhis') ?>" class="btn btn-warning" style="margin: 10px 0; width: 100%;"><b>Create New Invoice</b></a>
                    </div>

                    <div class="box box-success popup  <?php echo $err2; ?>" style="padding: 5px;border: 0;">
                        <div class="alert alert-dismissible" style="width: 350px;margin: 0;">
                            <button type="button" class="close" onclick="click_close()" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4 class="text-red"><i class="icon fa fa-ban"></i> Alert!</h4>
                            Stock qty and Sales qty are unbalanced ..!
                        </div>
                        <a href="#" class="btn btn-warning" onclick="click_close()" style="margin: 10px 0; width: 100%;"><b>Add New Item</b></a>
                    </div>

                    <div class="box box-success popup  <?php echo $err3; ?>" style="padding: 5px;border: 0;">
                        <div class="alert alert-dismissible" style="width: 350px;margin: 0;">
                            <button type="button" class="close" onclick="click_close()" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4 class="text-red"><i class="icon fa fa-ban"></i> Alert!</h4>
                            This Item already ADD ..!
                        </div>
                        <a href="#" class="btn btn-warning" onclick="click_close()" style="margin: 10px 0; width: 100%;"><b>Add New Item</b></a>
                    </div>

                    <div class="box box-success popup  <?php echo $err4; ?>" style="padding: 5px;border: 0;">
                        <div class="alert alert-dismissible" style="width: 350px;margin: 0;">
                            <button type="button" class="close" onclick="click_close()" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <h4 class="text-red"><i class="icon fa fa-ban"></i> Alert!</h4>
                            Please add the item you want to sell ..!
                        </div>
                        <a href="#" class="btn btn-warning" onclick="click_close()" style="margin: 10px 0; width: 100%;"><b>Add Items</b></a>
                    </div>

                </div>
            </div>
        </div>

        <div class="control-sidebar-bg"></div>
    </div>

    <!-- jQuery 2.2.3 -->
    <script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="../../plugins/select2/select2.full.min.js"></script>
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
    <!-- AdminLTE App -->
    <script src="../../dist/js/app.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../../dist/js/demo.js"></script>
    <!-- Dark Theme Btn-->
    <script src="https://dev.colorbiz.org/ashen/cdn/main/dist/js/DarkTheme.js"></script>

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

    <script type="text/javascript">
        info = 'id=' + 1 + '&type=price';
        $.ajax({
            type: "GET",
            url: "sales_get.php",
            data: info,
            success: function(res) {
                $("#price").val(parseFloat(res));
            }
        });

        function checking_discount() {
            let val = $('#dic').val();
            if (val > 100) {
                $('#btn_list').attr('disabled', '');
                $('.dic').addClass('border-red');
            } else {
                $('#btn_list').removeAttr('disabled');
                $('.dic').removeClass('border-red');
            }
        }

        function pro_select() {
            let val = $('#pro_sel').val();
            info = 'id=' + val + '&type=price';
            $.ajax({
                type: "GET",
                url: "sales_get.php",
                data: info,
                success: function(res) {
                    $("#price").val(parseFloat(res));
                    $('#qty_in').focus();
                }
            });
        }

        function select_pay() {
            var val = $('#method').val();

            if (val == "Credit") {
                $('.slt-pay').css("display", "none");
            } else {
                $('.slt-pay').css("display", "block");
            }

            if (val == "Chq") {
                $('.slt-chq').css("display", "block");
            } else {
                $('.slt-chq').css("display", "none");
            }
        }

        $(".dll_btn").click(function() {

            var element = $(this);
            var id = element.attr("id");
            var info = 'id=' + id + '&invo=<?php echo $invo; ?>';
            if (confirm("Sure you want to delete this Collection? There is NO undo!")) {

                $.ajax({
                    type: "GET",
                    url: "sales_list_dll.php",
                    data: info,
                    success: function(res) {
                        $('#bill_total').text(res);
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
                format: 'yyyy-mm-dd'
            });

            $("#datepicker").datepicker({
                autoclose: true,
                datepicker: true,
                endDate: new Date(),
                format: "yyyy-mm-dd"
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