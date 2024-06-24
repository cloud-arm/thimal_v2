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
        $_SESSION['SESS_FORM'] = '89';
        date_default_timezone_set("Asia/Colombo");

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Loading Sales Replace
                    <small>Preview</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row">

                    <div class="col-md-8">
                        <!-- SELECT2 EXAMPLE -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Select Invoice</h3>
                            </div>


                            <?php
                            $nowDate = new DateTime();
                            $nowDate->sub(new DateInterval('P30D'));
                            $backDate = $nowDate->format('Y-m-d');
                            $date = date('Y-m-d');
                            ?>

                            <div class="box-body">

                                <form method="post" action="sales_loading_sales.php">

                                    <div class="row">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Loading</label>
                                                <select class="form-control select2" onchange="select_loading(this.value)" id="loading" name="loading" style="width:100%">
                                                    <?php
                                                    $result = select("loading", "transaction_id,lorry_no", " date BETWEEN '" . $backDate . "' AND '" . $date . "'");
                                                    foreach ($result as $row) {
                                                    ?>
                                                        <option value="<?php echo $row['transaction_id']; ?>"><?php echo 'Loading ID:' . $row['transaction_id'] . ' __ Lorry No:' . $row['lorry_no']; ?> </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Invoice</label>
                                                <select class="form-control select2" id="invoice" name="sales_id" style="width:100%" autofocus>
                                                    <option value="0" disabled selected>Select Invoice</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group" style="margin-top: 22px;">
                                                <input class="btn btn-info" type="submit" value="Next">
                                            </div>
                                        </div>

                                    </div>

                                </form>
                                <!-- /.box -->

                            </div>

                        </div>

                    </div>
                </div>

            </section>
            <!-- /.content -->
        </div>

        <!-- /.content-wrapper -->
        <?php
        include("dounbr.php");
        ?>
        <!-- /.control-sidebar -->
        <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 2.2.3 -->
    <script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="../../plugins/select2/select2.full.min.js"></script>
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
    <!-- Page script -->
    <script>
        select_loading($('#loading').val());

        function select_loading(val) {
            var xmlhttp;
            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("invoice").innerHTML = xmlhttp.responseText;
                }
            }

            xmlhttp.open("GET", "sales_loading_get.php?unit=1&val=" + val, true);
            xmlhttp.send();
        }

        $(function() {
            //Initialize Select2 Elements
            $(".select2").select2();

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

        });
    </script>
</body>

</html>