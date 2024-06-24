<!DOCTYPE html>
<html>
<?php
include("head.php");
include("connect.php");
date_default_timezone_set("Asia/Colombo");
?>

<body class="hold-transition skin-yellow sidebar-mini ">
    <div class="wrapper" style="overflow-y: hidden;">
        <?php
        include_once("auth.php");
        $r = $_SESSION['SESS_LAST_NAME'];
        $_SESSION['SESS_FORM'] = '41';

        include_once("sidebar.php");

        ?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Transport Report
                    <small>Preview</small>
                </h1>
            </section>
            <!-- Main content -->
            <section class="content">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Sales Payment</h3>
                        <!-- /.box-header -->
                    </div>
                    <div class="form-group">
                        <div class="box-body d-block">
                            <form method="POST" action="transport_payment_save.php">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Invoice</label>
                                            <select class="form-control select2" name="id" style="width: 100%;" tabindex="1" autofocus>
                                                <?php
                                                $result = $db->prepare("SELECT * FROM transport WHERE  transport.pay_amount < transport.amount ");
                                                $result->bindParam(':id', $invo);
                                                $result->execute();
                                                for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                    <option value="<?php echo $row['transaction_id']; ?>"><?php echo $row['invoice_number']; ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input class="form-control" id="datepicker" type="text" value="<?php echo date('Y-m-d'); ?>" name="date" autocomplete="off" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Pay Amount</label>
                                            <input class="form-control" type="number" id="pay_amount" step=".01" value="0" name="amount" autocomplete="off" required>
                                        </div>
                                    </div>

                                    <div class="col-md-2" style="height: 75px;display: flex; align-items: end;">
                                        <div class="form-group">
                                            <input class="btn btn-success" type="submit" value="Submit" style="width: 100px;">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="box box-info">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="text-transform: capitalize;">Transport</h3>
                    </div>

                    <div class="box-body d-block">
                        <table id="example" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Invoice</th>
                                    <th>Date Range</th>
                                    <th>Print Date</th>
                                    <th>Amount</th>
                                    <th>Pay Amount</th>
                                    <th>VAT 18%</th>
                                    <th>Grand Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tot = 0;
                                $result = $db->prepare("SELECT * FROM transport WHERE  action < 2 And amount > 0 ");
                                $result->bindParam(':id', $date);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $amount = $row['amount'];
                                    $vat = $amount - ($row['amount'] / 118 * 100);
                                ?>
                                    <tr>
                                        <td><?php echo $row['transaction_id'];  ?></td>
                                        <td><?php echo $row['invoice_number']  ?></td>
                                        <td>
                                            From: <span class="badge bg-blue"><i class="fa fa-calendar"></i> <?php echo $row['date_from'];  ?> </span> /
                                            To: <span class="badge bg-green"><i class="fa fa-calendar"></i> <?php echo $row['date_to'];  ?> </span>
                                        </td>
                                        <td><?php echo $row['date'];  ?></td>
                                        <td><?php echo number_format($amount - $vat, 2);  ?></td>
                                        <td><?php echo number_format($row['pay_amount'], 2);  ?></td>
                                        <td><?php echo number_format($vat, 2);  ?></td>
                                        <td><?php echo number_format($amount, 2); ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </section>

        </div>

        <!-- /.content-wrapper -->
        <?php include("dounbr.php"); ?>

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

    <script type="text/javascript">
        $(function() {
            $("#example").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });
        });
    </script>

    <script>
        $(function() {
            //Initialize Select2 Elements
            $(".select2").select2();

            //Date range picker
            $('#reservation').daterangepicker();
            //Date range picker with time picker
            //$('#datepicker').datepicker({datepicker: true,  format: 'yyyy/mm/dd '});
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
                endDate: new Date(),
                format: "yyyy-mm-dd"
            });

            $('#datepicker').datepicker({
                autoclose: true
            });

            $('#datepickerd').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy/mm/dd '
            });
            $('#datepickerd').datepicker({
                autoclose: true
            });

        });
    </script>

</body>

</html>