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
        $_SESSION['SESS_FORM'] = '56';


        include_once("sidebar.php");
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Credit Note
                    <small>Preview</small>
                </h1>

            </section>

            <!-- add item -->
            <section class="content">

                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Payment</h3>
                    </div>

                    <div class="box-body">
                        <form method="POST" action="grn_credit_note_save.php">
                            <div class="row">

                                <div class="col-md-1"></div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Supplier</label>
                                        <?php
                                        $result = $db->prepare("SELECT * FROM supplier ");
                                        $result->bindParam(':id', $res);
                                        $result->execute(); ?>
                                        <select class="form-control select2" name="supply" style="width: 100%;" tabindex="1">
                                            <option value="0" selected disabled> Select Supplier </option>
                                            <?php for ($i = 0; $row = $result->fetch(); $i++) {  ?>
                                                <option value="<?php echo $row['supplier_id']; ?>"> <?php echo $row['supplier_name']; ?> </option>
                                            <?php  } ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Supply Invoice</label>
                                        <input class="form-control" type="text" name="invoice" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Amount</label>
                                        <input class="form-control" step=".01" type="number" name="amount" id="pay_txt" onkeyup="checking()" autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-1"></div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Note</label>
                                        <input class="form-control" type="text" name="note" autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="form-group" style="margin-top: 23px;">
                                        <input class="btn btn-warning" type="submit" style="width: 100%;" id="submit" value="Submit" disabled>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-hover" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Supplier Invoice</th>
                                    <th>Supplier Name</th>
                                    <th>Amount (Rs.)</th>
                                    <th>#</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $result = $db->prepare("SELECT * FROM supply_payment WHERE pay_type = 'Credit_note' ");
                                $result->bindParam(':userid', $res);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {

                                    $style = '';
                                    if ($row['dll'] == 1) {
                                        $style = 'opacity: 0.5;cursor: default;';
                                    }
                                ?>
                                    <tr id="record_<?php echo $row['id']; ?>" style="<?php echo $style; ?>">
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['invoice_no']; ?></td>
                                        <td><?php echo $row['date']; ?></td>
                                        <td><?php echo $row['supplier_invoice']; ?></td>
                                        <td><?php echo $row['supply_name']; ?></td>
                                        <td><?php echo $row['amount']; ?></td>
                                        <td> <?php if ($row['dll'] == 0) { ?><span onclick="dll_btn ('<?php echo $row['id']; ?>')" class="btn btn-danger btn-sm" id="dll_<?php echo $row['id']; ?>" title="Click to Delete"> X</span> <?php } ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>

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
        function checking() {

            let val = $("#pay_txt").val();

            if (val > 0) {
                $('#submit').removeAttr("disabled");
            } else {
                $('#submit').attr("disabled", "");
            }
        }

        function dll_btn(id) {
            var info = 'id=' + id;
            if (confirm("Sure you want to delete this Credit note? There is NO undo!")) {

                $.ajax({
                    type: "GET",
                    url: "grn_credit_note_dll.php",
                    data: info,
                    success: function() {}
                });
                $("#record_" + id).css({
                    'opacity': '0.5',
                    'cursor': 'default'
                })
                $("#dll_" + id).remove();

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