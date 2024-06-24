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

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Error Log Details
                    <small>Preview</small>
                </h1>
            </section>

            <section class="content">

                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title">Error Log</h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">

                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>File</th>
                                    <th>Data Table</th>
                                    <th>message</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                $i = 0;

                                // Read existing JSON data from file
                                $currentData = file_get_contents('log/bin/error.json');

                                // Convert JSON data to PHP array
                                $dataArray = json_decode($currentData, true);

                                foreach ($dataArray as $row) {
                                ?>
                                    <tr>
                                        <td><?php echo $i += 1; ?></td>
                                        <td><?php echo $row['file']; ?></td>
                                        <td><?php echo $row['table']; ?></td>
                                        <td><?php echo $row['message']; ?></td>
                                        <td><?php echo $row['date']; ?></td>
                                        <td><?php echo $row['time']; ?></td>
                                    </tr>
                                <?php  } ?>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->

            </section>
            <!-- /.content -->
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
    <!-- page script -->
    <script>
        $(function() {
            //Initialize Select2 Elements
            $(".select2").select2();

            $("#example1").DataTable({
                "language": {
                    "paginate": {
                        "next": "<i class='fa fa-angle-double-right'></i>",
                        "previous": "<i class='fa fa-angle-double-left'></i>"
                    }
                }
            });
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
</body>

</html>