<!DOCTYPE html>
<html>
<?php
include("head.php");
?>

<body class="hold-transition skin-yellow sidebar-mini">
    <div class="wrapper" style="overflow-y: hidden;">
        <?php
        include_once("auth.php");
        $r = $_SESSION['SESS_LAST_NAME'];
        $_SESSION['SESS_FORM'] = '92';
        date_default_timezone_set("Asia/Colombo");

        include_once("sidebar.php");

        if (isset($_GET['id'])) {
            $record_no = base64_decode($_GET['id']);
        } else {
            $record_no = 'jrn' . date("ymdhis");
        }

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    ACCOUNT TRANSFER
                    <small>Preview</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row">

                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title"> Account Journal</h3>
                            </div>
                            <div class="box-body">
                                <form action="acc_transaction_save.php" method="POST">
                                    <div class="row" style="display: flex;">

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Account</label>
                                                <select name="account" class="form-control select2" style="width: 100%;">
                                                    <option value="0" disabled selected>Select Account</option>
                                                    <?php
                                                    $result = select("acc_account", "id,name");
                                                    foreach ($result as $row) {
                                                        echo sprintf('<option value="%s"> %s </option>', $row['id'], ucwords(str_replace("_", " ", $row['name'])));
                                                    } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Transfer Amount</label>
                                                <input type="number" step=".01" name="amount" class="form-control" placeholder="0.00">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Memo</label>
                                                <input type="text" name="memo" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <label for="">Transfer Type</label>
                                            <div class="form-group" style="display: flex;justify-content: space-between;margin-top: 5px;width: 80%;">
                                                <label style="display: flex;align-items: center;gap: 5px;">
                                                    <input type="radio" value="Credit" name="type" class="flat-red" checked>Credit
                                                </label>
                                                <label style="display: flex;align-items: center;gap: 5px;">
                                                    <input type="radio" value="Debit" name="type" class="flat-red">Debit
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input type="hidden" name="id" value="<?php echo $record_no; ?>">
                                                <input type="submit" value="Add" class="btn btn-info " style="margin-top: 23px;width: 100px;">
                                            </div>
                                        </div>

                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Credit & Debit</h3>
                            </div>
                            <div class="box-body">
                                <table id="example1" class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>ACC_NAME</th>
                                            <th>MEMO</th>
                                            <th>CREDIT</th>
                                            <th>DEBIT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $credit = 0;
                                        $debit = 0;
                                        $result2 = select("acc_transaction_record", "transaction_id,acc_name,credit,debit,type,memo", "record_no = '" . $record_no . "' ");
                                        foreach ($result2 as $row2) {
                                            $credit += $row2['credit'];
                                            $debit += $row2['debit'];
                                        ?>
                                            <tr>
                                                <td><?php echo ucfirst($row2['transaction_id']); ?></td>
                                                <td><?php echo ucfirst($row2['acc_name']); ?></td>
                                                <td><?php echo ucfirst($row2['memo']); ?></td>
                                                <td>
                                                    <?php
                                                    if ($row2['type'] == 'Credit') {
                                                        echo number_format($row2['credit'], 2);
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($row2['type'] == 'Debit') {
                                                        echo number_format($row2['debit'], 2);
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="3">Total:</th>
                                            <th><?php echo number_format($debit, 2); ?></th>
                                            <th><?php echo number_format($debit, 2); ?></th>
                                        </tr>
                                    </tfoot>
                                </table>

                                <div style="padding-left: 20px;margin-top: 20px;">
                                    <h4>Balance: <small> Rs. </small> <?php echo number_format($debit - $credit, 2); ?> </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3 pull-right">
                    <div class="box box-info">
                        <div class="box-body">
                            <button onclick="transaction_process()" class="btn btn-lg btn-danger" style="width: 100%;"><i class="fa fa-connectdevelop"></i> Process</button>
                            <form action="acc_transaction_process.php" method="POST" id="process-form">
                                <input type="hidden" name="id" value="<?php echo $record_no; ?>">
                            </form>
                        </div>
                    </div>
                </div>
        </div>

        </section>
        <!-- /.content -->
    </div>

    <!-- /.content-wrapper -->
    <?php include("dounbr.php"); ?>

    <div class="container-up d-none" id="container_up">
        <div class="container-close" onclick="click_close()"></div>
        <div class="row">
            <div class="col-md-12">

                <div class="box box-success popup d-none" id="popup_1" style="padding: 5px;border: 0;">
                    <div class="alert alert-dismissible" style="width: 350px;margin: 0;">
                        <button type="button" class="close" onclick="click_close()">&times;</button>
                        <h4 class="text-red"><i class="icon fa fa-ban"></i> Alert!</h4>
                        Not balance this Transaction ..!
                    </div>
                </div>

                <div class="box box-success popup d-none" id="popup_2" style="width: 400px;display: flex;flex-direction: column;justify-content: space-between;">

                    <h4>Do you sure process this transaction? </h4>
                    <hr style="margin: 10px 0;border-color:#999;">
                    <div style="display: flex;align-items:center;justify-content:space-around;margin:10px 0;">
                        <button onclick="check_process(0)" style="width: 100px;" class="btn btn-danger">No</button>
                        <button onclick="check_process(1)" style="width: 100px;" class="btn btn-primary">Yes</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

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

    <!-- Page script -->
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
        function transaction_process() {
            let balance = <?php echo $credit - $debit; ?>;
            if (balance == 0) {
                $('#process-form').submit();
            } else {
                click_open(1)
            }
        }

        $(function() {
            <?php
            $result = select("acc_account_type", "sn");
            foreach ($result as $row) {
                echo sprintf('$("#example%s").DataTable({
                    "paging": false,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false
                });', $row['sn']);
            } ?>

            //Initialize Select2 Elements
            $(".select2").select2();
            $('.select2.hidden-search').select2({
                minimumResultsForSearch: -1
            });

            //Date range picker
            $('#reservation').daterangepicker();
            //Date range picker with time picker
            $('#reservationtime').daterangepicker({
                timePicker: true,
                timePickerIncrement: 30,
                format: 'YYYY/MM/DD h:mm A'
            });
            //Date range as a button
            $('#daterange-btn').daterangepicker({
                    ranges: {
                        'Today': [moment(), moment()],
                        'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                        'This Month': [moment().startOf('month'), moment().endOf('month')],
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function(start, end) {
                    $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
                }
            );

            //Date picker
            $('#datepicker').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy/mm/dd '
            });
            $('#datepicker').datepicker({
                autoclose: true
            });

            //Flat red color scheme for iCheck
            $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            })
        });
    </script>
</body>

</html>