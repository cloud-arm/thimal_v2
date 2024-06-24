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
        $_SESSION['SESS_FORM'] = '87';

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Customer Payment
                    <small>Report</small>
                </h1>
            </section>
            <!-- Main content -->
            <section class="content">

                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-8">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Type & Date Selector</h3>
                            </div>
                            <?php
                            include("connect.php");
                            date_default_timezone_set("Asia/Colombo");

                            if (isset($_GET['type'])) {
                                $type = $_GET['type'];
                                $dates = $_GET['dates'];
                                $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
                                $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");
                            } else  if (isset($_GET['d1']) & isset($_GET['d2'])) {
                                $type = $_GET['type'];
                                $d1 = $_GET['d1'];
                                $d2 = $_GET['d2'];
                                $dates = date('Y/m/d-Y/m/d');
                            } else {
                                $type = 'all';
                                $d1 = date('Y-m-d');
                                $d2 = date('Y-m-d');
                                $dates = date('Y/m/d-Y/m/d');
                            }

                            ?>

                            <div class="box-body">
                                <form action="" method="GET">
                                    <div class="row" style="margin-bottom: 20px;display: flex;align-items: end;">
                                        <div class="col-lg-1"></div>

                                        <div class="col-lg-5">
                                            <label>Type:</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-exchange"></i>
                                                </div>
                                                <select class="form-control select2 hidden-search" name="type">
                                                    <option <?php if ($type == 'all') { ?> selected <?php } ?> value="all">All</option>
                                                    <option <?php if ($type == 'credit_payment') { ?> selected <?php } ?> value="credit_payment">Credit</option>
                                                    <option <?php if ($type == 'invoice_payment') { ?> selected <?php } ?> value="invoice_payment">Invoice</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-5">
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

                <?php
                include("connect.php");
                date_default_timezone_set("Asia/Colombo");

                $_SESSION['SESS_BACK'] = "sales_payment_rp.php?dates=" . $dates . "&type=" . $type;

                if ($type == 'all') {
                    $paycose = ' ';
                } else {
                    $paycose = " paycose = '" . $type . "' AND ";
                }

                $sql = "SELECT * FROM payment WHERE $paycose action < 3 AND date BETWEEN '$d1' AND '$d2' ORDER BY date ";

                ?>
                <div class="box box-info">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="text-transform: capitalize;">payment</h3>
                    </div>

                    <div class="box-body d-block">
                        <table id="example2" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Pay Type</th>
                                    <th>Chq No</th>
                                    <th>Chq Date</th>
                                    <th>Amount</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tot = 0;
                                $pay = 0;
                                $a = 0;
                                $result = $db->prepare($sql);
                                $result->bindParam(':id', $date);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                    <tr>
                                        <td><?php echo $row['transaction_id'];  ?></td>
                                        <td><?php echo $row['invoice_no']  ?></td>
                                        <td><?php echo $row['date'];  ?></td>
                                        <td><?php echo $row['type'];  ?></td>
                                        <td><?php echo $row['chq_no'];  ?></td>
                                        <td><?php echo $row['chq_date']; ?></td>
                                        <td><?php echo $row['amount']; ?></td>
                                        <?php $tot += $row['amount'] ?>

                                        <td>
                                            <?php if ($row['paycose'] == 'invoice_payment') { ?>
                                                <a href="bill2.php?invo=<?php echo base64_encode($row['invoice_no']); ?>" class="btn btn-primary btn-sm fa fa-eye" title="Click to View Payment"> </a>
                                            <?php } else { ?>
                                                <a href="#" onclick="click_open(1,<?php echo $row['transaction_id']; ?>)" class="btn btn-warning btn-sm fa fa-eye" title="Click to View Payment"> </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                        <div style="padding-left: 25px;margin-top: 20px;">
                            <h4>Total Amount: <small> Rs. </small> <?php echo number_format($tot, 2); ?> </h4>
                        </div>
                    </div>

                </div>
            </section>

        </div>

        <!-- /.content-wrapper -->
        <?php include("dounbr.php"); ?>



        <div class="container-up d-none" id="container_up">
            <div class="container-close" onclick="click_close()"></div>

            <div class="row">
                <div class="col-md-12">

                    <div class="box box-success popup d-none with-scroll" id="popup_1" style="width: 600px;overflow-x: hidden;">
                        <div class="box-header with-border">
                            <h3 class="box-title w-100">Credit Payment <i onclick="click_close()" class="btn p-0 me-2  pull-right fa fa-remove" style="font-size: 25px;"></i></h3>
                        </div>

                        <div class="box-body d-block" id="pay_tbl"> </div>
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
        function click_open(i, id) {

            var xmlhttp;
            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("pay_tbl").innerHTML = xmlhttp.responseText;
                }
            }

            xmlhttp.open("GET", "sales_credit_get.php?id=" + id, true);
            xmlhttp.send();

            $("#popup_" + i).removeClass("d-none");
            $("#container_up").removeClass("d-none");
        }

        function click_close() {
            $(".popup").addClass("d-none");
            $("#container_up").addClass("d-none");
        }
    </script>

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