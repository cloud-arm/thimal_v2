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
        $_SESSION['SESS_FORM'] = '37';

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Expenses
                    <small>Preview</small>
                </h1>
            </section>

            <!-- /.box -->

            <section class="content">

                <div class="row">
                    <div class="col-md-11">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Type & Date Selector</h3>
                            </div>

                            <?php
                            include("connect.php");
                            date_default_timezone_set("Asia/Colombo");

                            $id = 0;
                            $id1 = 0;
                            $dates = date('Y/m/d-Y/m/d');

                            if (isset($_GET['id'])) {
                                $id = $_GET['id'];
                                $id1 = $_GET['id1'];
                                $dates = $_GET['dates'];
                            }

                            $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
                            $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");

                            $_SESSION['SESS_BACK'] = "customer_transaction_rp.php?dates=" . $dates . "&id=" . $id;

                            $type = ' ';
                            if ($id > 0) {
                                $type = 'type_id = ' . $id . ' AND ';
                            }

                            $sub = ' ';
                            if ($id1 > 0) {
                                $sub = 'sub_type = ' . $id1 . ' AND ';
                            }

                            $sql = " SELECT * FROM expenses_records  WHERE $type $sub dll=0 AND date BETWEEN '$d1' AND '$d2'   ";

                            ?>

                            <div class="box-body">
                                <form action="" method="GET">
                                    <div class="row" style="margin-bottom: 20px;display: flex;align-items: end;">
                                        <div class="col-lg-4">
                                            <label>Type:</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-wrench"></i>
                                                </div>
                                                <select class="form-control select2" name="id" onchange="select_type(this.value)" style="width: 100%;" autofocus tabindex="1">
                                                    <option value="all">All Type</option>
                                                    <?php
                                                    $result = $db->prepare("SELECT * FROM expenses_types ");
                                                    $result->bindParam(':userid', $ttr);
                                                    $result->execute();
                                                    for ($i = 0; $row = $result->fetch(); $i++) {
                                                    ?>
                                                        <option <?php if ($id == $row['sn']) { ?>selected <?php } ?> value="<?php echo $row['sn']; ?>"><?php echo $row['type_name']; ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4" id="sub_sec">
                                            <label>Sub Type:</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-wrench"></i>
                                                </div>
                                                <select <?php if ($id1 == $row['sn']) { ?>selected <?php } ?> class="form-control select2" id="sub_type" name="id1" style="width: 100%;" tabindex="8"></select>
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

                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Expenses List</h3>
                    </div>

                    <div class="box-body d-block">
                        <table id="example1" class="table table-bordered " style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Comment</th>
                                    <th>Pay Type</th>
                                    <th>Vendor</th>
                                    <th>Amount (Rs.)</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $tot = 0;
                                $blc = 0;
                                $pay = 0;
                                $result = $db->prepare($sql);
                                $result->bindParam(':userid', $date);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $dll = $row['dll'];
                                    $type = $row['type_id'];
                                    $pay_type = $row['pay_type'];
                                    if ($dll == 1) {
                                        $style = 'opacity: 0.5;cursor: default;';
                                    } else {
                                        $style = '';
                                    }

                                    if ($row['paycose'] == 'asset') {
                                        $paycose = 'navy';
                                    } else if ($row['paycose'] == 'expenses') {
                                        $paycose = 'maroon';
                                    } else if ($row['paycose'] == 'payment') {
                                        $paycose = 'purple';
                                        $style = 'background-color: rgb(var(--bg-light-70));';
                                    } else {
                                        $paycose = '';
                                    }

                                    if ($row['pay_type'] == 'credit' & $row['pay_amount'] == 0) {
                                        $dll = 0;
                                    } else {
                                        $dll = 1;
                                    }
                                ?>

                                    <tr class="record" style="<?php echo $style; ?>">
                                        <td>
                                            <?php echo $row['id']; ?>.
                                            <span class="badge bg-<?php echo $paycose; ?>"> <?php echo ucfirst($row['paycose']); ?> </span>
                                        </td>
                                        <td>
                                            <?php echo $row['date']; ?> <br>
                                            <?php if ($pay_type == 'credit' && $row['close_date'] == '' && $row['credit_balance'] > 0) { ?>
                                                <span class="badge bg-red"> <i class="fa fa-ban"></i> Unpaid </span>
                                            <?php } else { ?>
                                                <span class="badge bg-gray"> <i class="fa fa-check"></i> Paid </span> <br>
                                                <?php echo $row['close_date']; ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($row['util_id'] > 0) {
                                                echo $row['util_name'];
                                            } else if ($row['sub_type'] > 0) {
                                                echo $row['sub_type_name'];
                                            } else {
                                                echo $row['type'];
                                            }  ?>
                                            <?php if ($type == 2) { ?> <br>
                                                <span class="badge bg-blue">Loading ID: <?php echo $row['loading_id']; ?> </span> <br>
                                                <span class="badge bg-green"> <i class="fa fa-truck"></i> <?php echo $row['lorry_no']; ?> </span>
                                            <?php } ?>
                                            <?php if ($type == 3) { ?> <br>
                                                <span class="badge bg-green"> <i class="fa fa-truck"></i> <?php echo $row['lorry_no']; ?> </span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($type == 1) { ?>
                                                <span class="badge bg-maroon"> Utility </span> <br>
                                            <?php } else  if ($type == 2) { ?>
                                                <span class="badge bg-olive"> Root </span> <br>
                                            <?php } else if ($type == 3) { ?>
                                                <span class="badge bg-orange"> Purchase </span> <br>
                                            <?php } else {  ?>
                                                <span class="badge bg-gray"> Expenses </span> <br>
                                            <?php } ?>
                                            <?php echo $row['comment'];   ?>
                                        </td>
                                        <td>
                                            <?php echo ucfirst($pay_type); ?> <br>
                                            <?php if ($pay_type == 'chq') { ?>
                                                NO: <span class="badge bg-blue"><?php echo $row['chq_no']; ?> </span> <br>
                                                Date: <span class="badge bg-green"><?php echo $row['chq_date']; ?> </span> <br>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php echo $row['vendor_name']; ?>
                                        </td>
                                        <td>
                                            Rs.<?php echo $row['amount'];  ?> <br>
                                            Pay Amount: <?php echo $row['pay_amount']; ?> <br>
                                            <?php if ($type == 1 || $pay_type == 'credit') { ?>
                                                Balance: <?php echo $row['credit_balance']; ?> <br>
                                            <?php } ?>
                                            <?php if ($type == 1) { ?>
                                                Forward Balance: <?php echo $row['util_forward_balance']; ?>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                    <?php if ($row['pay_type'] == 'credit') {
                                        $tot += $row['amount'];
                                        $pay += $row['pay_amount'];
                                    } ?>
                                <?php }   ?>
                            </tbody>
                        </table>
                        <h4>Credit: <small class="ms-2">Rs.</small><?php echo number_format($tot, 2); ?> </h4>
                        <h4>Payment: <small class="ms-2">Rs.</small><?php echo number_format($pay, 2); ?> </h4>
                        <h4>Balance: <small class="ms-2">Rs.</small><?php echo number_format($tot - $pay, 2); ?> </h4>
                    </div>
                </div>

            </section>
            <!-- /.content -->
        </div>

        <!-- /.content-wrapper -->
        <?php include("dounbr.php"); ?>

        <div class="control-sidebar-bg"></div>
    </div>


    <?php include_once("script.php"); ?>

    <!-- DataTables -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
    <script src="../../plugins/jszip/jszip.min.js"></script>
    <script src="../../plugins/pdfmake/pdfmake.min.js"></script>
    <script src="../../plugins/pdfmake/vfs_fonts.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.html5.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.print.min.js"></script>
    <script src="../../plugins/datatables-buttons/js/buttons.colVis.min.js"></script>
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
        $(function() {
            $("#example1").DataTable({
                "responsive": true,
                "lengthChange": false,
                "autoWidth": false,
                "ordering": false,
                "buttons": ["excel", "pdf", "print"]
            }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
            $('#example').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": true,
                "ordering": false,
                "info": false,
                "autoWidth": true
            });
        });

        select_type(<?php echo $id ?>);

        function select_type(val) {

            var xmlhttp;
            if (window.XMLHttpRequest) { // code for IE7+, Firefox, Chrome, Opera, Safari
                xmlhttp = new XMLHttpRequest();
            } else { // code for IE6, IE5
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("sub_type").innerHTML = xmlhttp.responseText;
                }
            }

            xmlhttp.open("GET", "expenses_get.php?end=0&id=" + val, true);
            xmlhttp.send();
        }
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


            $('#datepicker_set').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy-mm-dd '
            });
            $('#datepicker_set').datepicker({
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