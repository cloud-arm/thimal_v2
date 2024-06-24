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
        $_SESSION['SESS_FORM'] = '83';

        include_once("sidebar.php");

        ?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    All Target
                    <small>Report</small>
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

                            if (isset($_GET['year'])) {
                                $year = $_GET['year'];
                                $month = $_GET['month'];
                            } else {
                                $year = date('Y');
                                $month = date('m');
                            }
                            $d1 = $year . '-' . $month . '-01';
                            $d2 = $year . '-' . $month . '-31';

                            ?>
                            <div class="box-body">
                                <form action="" method="GET">
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-lg-1"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <select class="form-control select2 hidden-search" name="year" style="width: 100%;" tabindex="1" autofocus>
                                                    <option> <?php echo date('Y') - 1 ?> </option>
                                                    <option selected> <?php echo date('Y') ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <select class="form-control select2 hidden-search" name="month" style="width: 100%;" tabindex="1" autofocus>
                                                    <?php for ($x = 1; $x <= 12; $x++) {
                                                        $m = sprintf("%02d", $x); ?>
                                                        <option <?php if ($m == $month) { ?> selected <?php } ?>> <?php echo $m ?> </option>
                                                    <?php  } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input class="btn btn-info" type="submit" value="Search">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- All jobs -->

                <div class="box box-info">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="text-transform: capitalize;">Target Report</h3>
                    </div>

                    <div class="box-body d-block">
                        <table id="example" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Product</th>
                                    <th>Target</th>
                                    <th>Achievement</th>
                                    <th>Balance</th>
                                    <th>Bonus</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                include("connect.php");
                                date_default_timezone_set("Asia/Colombo");

                                $month = $year . '-' . $month;

                                $d1 = $month . '-01';
                                $d2 = $month . '-31';
                                $sql = " SELECT * FROM `target` WHERE  month = '$month' ";
                                $result = $db->prepare($sql);
                                $result->bindParam(':id', $d);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {

                                    $achieve = 0;
                                    $balance = 0;
                                    $bonus = 0;
                                    $target = $row['target'];

                                    $res = $db->prepare("SELECT sum(qty) FROM sales_list WHERE product_id = :id AND action = 0 AND (date BETWEEN '$d1' AND '$d2') ");
                                    $res->bindParam(':id', $row['product_id']);
                                    $res->execute();
                                    for ($k = 0; $ro = $res->fetch(); $k++) {
                                        $achieve = $ro['sum(qty)'];
                                    }

                                    if ($target > $achieve) {
                                        $balance = $target - $achieve;
                                    } else {
                                        $bonus = $achieve - $target;
                                    }

                                    $sql = "UPDATE target  SET achievement = ?, bonus = ?, balance = ? WHERE id=?";
                                    $q = $db->prepare($sql);
                                    $q->execute(array($achieve, $bonus, $balance, $row['id']));
                                ?>
                                    <tr class="record">
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo $row['product_name']; ?></td>
                                        <td><?php echo $row['target']; ?></td>
                                        <td> <?php echo $row['achievement']; ?></td>
                                        <td> <?php echo $row['balance']; ?> </td>
                                        <td> <?php echo $row['bonus']; ?> </td>
                                    </tr>
                                <?php  }  ?>
                            </tbody>

                        </table>
                    </div>

                </div>
            </section>

        </div>

        <!-- /.content-wrapper -->
        <?php
        include("dounbr.php");
        ?>
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
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
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