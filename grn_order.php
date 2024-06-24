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
        $_SESSION['SESS_FORM'] = '';

        include_once("sidebar.php");

        $u = $_SESSION['SESS_MEMBER_ID'];
        $invo = $_GET['id'];

        $result = $db->prepare("SELECT * FROM purchases_item WHERE user_id=$u AND action='' AND type='Return' ");
        $result->bindParam(':userid', $res);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
            $invo = $row['invoice'];
        }

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Purchases Order
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
                                <h3 class="box-title">Order Add</h3>
                                <!-- /.box-header -->
                            </div>

                            <div class="box-body d-block">
                                <form method="POST" action="grn_list_save.php">

                                    <div class="row">

                                        <div class="col-md-12 m-0">
                                            <div class="form-group" id="status"></div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <label>Product</label>
                                                    </div>

                                                    <select class="form-control select2" name="pr" id="p_sel" onchange="pro_select()" style="width: 100%;" tabindex="1" autofocus>

                                                        <?php
                                                        $result = $db->prepare("SELECT * FROM products WHERE stock_action=1 ");
                                                        $result->bindParam(':userid', $res);
                                                        $result->execute();
                                                        for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                            <option value="<?php echo $row['product_id']; ?>">
                                                                <?php echo $row['product_code']; ?> -<?php echo $row['gen_name']; ?>
                                                            </option>
                                                        <?php    } ?>
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
                                                    <input type="number" class="form-control" value="1" name="qty" tabindex="2">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <div class="input-group date">
                                                    <div class="input-group-addon">
                                                        <label>Cost Price</label>
                                                    </div>
                                                    <input type="number" class="form-control" name="cost" id="cost1" tabindex="2">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?php echo $invo; ?>">
                                                <input type="hidden" name="type" value="Order">
                                                <input class="btn btn-warning" type="submit" value="Save">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="box-body d-block">
                                <table id="example2" class="table table-bordered table-hover" style="border-radius: 0;">
                                    <tr>
                                        <th>Product Name</th>
                                        <th>QTY</th>
                                        <th>Cost (Rs.)</th>
                                        <th>Amount (Rs.)</th>
                                        <th>#</th>
                                    </tr>
                                    <?php $total = 0;
                                    $style = "";
                                    $result = $db->prepare("SELECT * FROM purchases_item WHERE invoice = '$invo' ");
                                    $result->bindParam(':userid', $res);
                                    $result->execute();
                                    for ($i = 0; $row = $result->fetch(); $i++) {
                                        $pro_id = $row['product_id'];

                                        $re = $db->prepare("SELECT * FROM products WHERE product_id = '$pro_id' ");
                                        $re->bindParam(':userid', $res);
                                        $re->execute();
                                        for ($i = 0; $rw = $re->fetch(); $i++) {
                                            $stock = $rw['qty'];
                                        }
                                        if ($stock < 0) {
                                            $style = 'style="color:red" ';
                                        }

                                    ?>
                                        <tr <?php echo $style; ?> class="record">
                                            <td><?php echo $row['name']; ?></td>
                                            <td><?php echo $row['qty']; ?></td>
                                            <td><?php echo $row['cost']; ?></td>
                                            <td><?php echo $row['amount']; ?></td>
                                            <td> <a href="#" id="<?php echo $row['id']; ?>" class="dll_btn btn btn-danger" title="Click to Delete"> X</a></td>
                                            <?php $total += $row['amount']; ?>
                                        </tr>
                                    <?php
                                    }
                                    ?>

                                </table>
                                <h4>Total Rs <b><?php echo number_format($total, 2); ?></h4>

                            </div>

                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="box box-warning">
                            <div class="box-header with-border">
                                <h3 class="box-title">Order Save</h3>
                                <!-- /.box-header -->
                            </div>
                            <div class="form-group">
                                <div class="box-body d-block">
                                    <form method="POST" action="grn_save.php">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Supplier</label>
                                                    <select class="form-control select2" name="supply" style="width: 100%;" tabindex="1" autofocus>
                                                        <?php
                                                        $result = $db->prepare("SELECT * FROM supplier ");
                                                        $result->bindParam(':id', $invo);
                                                        $result->execute();
                                                        for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                            <option value="<?php echo $row['supplier_id']; ?>"><?php echo $row['supplier_name']; ?></option>
                                                        <?php    } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Supply Invoice</label>
                                                    <input class="form-control" type="text" name="sup_invoice" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Note</label>
                                                    <input class="form-control" type="text" name="note" autocomplete="off">
                                                </div>
                                            </div>

                                            <div class="col-md-2" style="height: 75px;display: flex; align-items: end;">
                                                <div class="form-group">
                                                    <input type="hidden" name="id" value="<?php echo $invo; ?>">
                                                    <input type="hidden" name="type" value="Order">
                                                    <input class="btn btn-success" type="submit" value="Submit">
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
        function pro_select() {
            let val = $('#p_sel').val();
            var info = 'id=' + val + '&type=Order&ac=0';
            $.ajax({
                type: "GET",
                url: "grn_status.php",
                data: info,
                success: function(res) {
                    $("#status").empty();
                    $("#status").append(res);
                }
            });
            info = 'id=' + val + '&type=Order&ac=1';
            $.ajax({
                type: "GET",
                url: "grn_status.php",
                data: info,
                success: function(res) {
                    $("#sell1").val(parseFloat(res));
                }
            });
            info = 'id=' + val + '&type=Order&ac=2';
            $.ajax({
                type: "GET",
                url: "grn_status.php",
                data: info,
                success: function(res) {
                    $("#cost1").val(parseFloat(res));
                }
            });

        }

        $(".dll_btn").click(function() {
            var element = $(this);
            var id = element.attr("id");
            var info = 'id=' + id;
            if (confirm("Sure you want to delete this Collection? There is NO undo!")) {

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

        $(function() {
            $(".select2").select2();

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