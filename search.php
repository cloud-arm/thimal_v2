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
        $_SESSION['SESS_FORM'] = '32';


        $_SESSION['SESS_BACK'] = 'search.php';

        include_once("sidebar.php");
        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Search Engine
                    <small>Preview</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Filters</h3>
                            </div>

                            <div class="box-body">
                                <form action="search_get.php" method="">

                                    <div class="row" style="margin-top: 10px;">

                                        <div class="col-lg-1"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label>Type</label>
                                                <select class="form-control select2 hidden-search" id="type_sl" name="type" onchange="select_type(this.options[this.selectedIndex].getAttribute('value'))" style="width: 100%;" tabindex="1" autofocus>
                                                    <option value="number"> Number </option>
                                                    <option value="date"> Date </option>
                                                    <option value="amount"> Amount </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-4 type_sec" id="number" style="display: block;">
                                            <label>Number</label>
                                            <div class="form-group">
                                                <input class="form-control" type="text" id="number_sl" name="number">
                                            </div>
                                        </div>

                                        <div class="col-md-4 type_sec" id="date" style="display: none;">
                                            <label>Date</label>
                                            <div class="form-group">
                                                <input class="form-control" type="text" id="datepicker" name="date" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-4 type_sec" id="amount" style="display: none;">
                                            <label>Amount</label>
                                            <div class="form-group">
                                                <input class="form-control" type="text" id="amount_sl" name="amount">
                                            </div>
                                        </div>

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input class="btn btn-info" type="button" onclick="searching()" style="margin-top: 23px;" value="Search">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row" id="result-box"> </div>
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
        function searching() {
            var type = $('#type_sl').val();
            var number = $('#number_sl').val();
            var amount = $('#amount_sl').val();
            var date = $('#datepicker').val();

            var xmlhttp;
            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("result-box").innerHTML = xmlhttp.responseText;
                }
            }

            xmlhttp.open("GET", "search_get.php?type=" + type + "&number=" + number + "&date=" + date + "&amount=" + amount, true);
            xmlhttp.send();
        }

        function select_type(val) {
            $('.type_sec').css('display', 'none');
            $('#' + val).css('display', 'block');
        }

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
                format: 'yyyy-mm-dd '
            });
            $('#datepicker').datepicker({
                autoclose: true
            });


        });
    </script>

</body>

</html>