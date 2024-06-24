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
        $_SESSION['SESS_FORM'] = '44';

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

                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Date Selector</h3>
                            </div>

                            <?php
                            include("connect.php");
                            date_default_timezone_set("Asia/Colombo");

                            if (isset($_GET['dates'])) {
                                $dates = $_GET['dates'];
                                $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
                                $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");
                            } else 
                            if (isset($_GET['d1']) & isset($_GET['d2'])) {
                                $d1 = $_GET['d1'];
                                $d2 = $_GET['d2'];
                            } else {
                                $d1 = date('Y-m-d');
                                $d2 = date('Y-m-d');
                                $dates = date('Y/m/d-Y/m/d');
                            }

                            $sql = "SELECT * FROM transport WHERE  action = 2 AND pay_date BETWEEN '$d1' AND '$d2' ";

                            ?>

                            <div class="box-body">
                                <form action="" method="GET">
                                    <div class="row" style="margin-bottom: 20px;display: flex;align-items: end;">
                                        <div class="col-lg-1"></div>
                                        <div class="col-lg-8">
                                            <label>Date range:</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-calendar"></i>
                                                </div>
                                                <input type="text" class="form-control pull-right" id="reservation" name="dates" value="<?php echo $dates; ?>">
                                            </div>
                                        </div>

                                        <div class="col-lg-2">
                                            <input type="submit" class="btn btn-info" value="Apply">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box box-info">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="text-transform: capitalize;">Transport</h3>
                    </div>

                    <div class="box-body d-block">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Invoice</th>
                                    <th>Date Range</th>
                                    <th>Print Date</th>
                                    <th>Bill Amount</th>
                                    <th>Pay Amount</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tot = 0;
                                $result = $db->prepare($sql);
                                $result->bindParam(':id', $date);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                ?>
                                    <tr>
                                        <td><?php echo $row['transaction_id'];  ?></td>
                                        <td><?php echo $row['invoice_number']  ?></td>
                                        <td>
                                            From: <span class="badge bg-blue"><i class="fa fa-calendar"></i> <?php echo $row['date_from'];  ?> </span> /
                                            To: <span class="badge bg-green"><i class="fa fa-calendar"></i> <?php echo $row['date_to'];  ?> </span>
                                        </td>
                                        <td><?php echo $row['date'];  ?></td>
                                        <td><?php echo number_format($row['amount'], 2);  ?></td>
                                        <td><?php echo number_format($row['pay_amount'], 2); ?></td>
                                        <td><?php echo number_format($row['amount'] - $row['pay_amount'], 2); ?></td>
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
                format: 'yyyy/mm/dd '
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