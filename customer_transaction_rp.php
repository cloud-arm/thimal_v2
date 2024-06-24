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
        $_SESSION['SESS_FORM'] = '85';

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Customer Transaction
                    <small>Report</small>
                </h1>
            </section>
            <!-- Main content -->
            <section class="content">

                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Customer & Date Selector</h3>
                            </div>
                            <?php
                            include("connect.php");
                            date_default_timezone_set("Asia/Colombo");

                            if (isset($_GET['id'])) {
                                $id = $_GET['id'];
                                $dates = $_GET['dates'];
                                $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
                                $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");
                            } else  if (isset($_GET['d1']) & isset($_GET['d2'])) {
                                $id = $_GET['id'];
                                $d1 = $_GET['d1'];
                                $d2 = $_GET['d2'];
                                $dates = date('Y/m/d-Y/m/d');
                            } else {
                                $id = 'all';
                                $d1 = date('Y-m-d');
                                $d2 = date('Y-m-d');
                                $dates = date('Y/m/d-Y/m/d');
                            }

                            ?>
                            <div class="box-body">
                                <form action="" method="GET">
                                    <div class="row" style="margin-bottom: 20px;display: flex;align-items: end;">
                                        <div class="col-lg-1"></div>
                                        <div class="col-lg-4">
                                            <label>Customer:</label>
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <i class="fa fa-user"></i>
                                                </div>
                                                <select class="form-control select2" name="id" style="width: 100%;" autofocus tabindex="1">
                                                    <option value="all">All Customer</option>
                                                    <?php
                                                    $result = $db->prepare("SELECT * FROM customer ORDER BY customer_name ASC");
                                                    $result->bindParam(':userid', $ttr);
                                                    $result->execute();
                                                    for ($i = 0; $row = $result->fetch(); $i++) {
                                                    ?>
                                                        <option <?php if ($id == $row['customer_id']) { ?>selected <?php } ?> value="<?php echo $row['customer_id']; ?>"><?php echo $row['customer_name']; ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
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

                $_SESSION['SESS_BACK'] = "customer_transaction_rp.php?dates=" . $dates . "&id=" . $id;

                $customer = ' ';
                if ($id > 0) {
                    $customer = 'customer_id = ' . $id . ' AND ';
                }

                $sql = "SELECT * FROM customer_record WHERE $customer date BETWEEN '$d1' AND '$d2' ORDER BY id ";

                ?>
                <div class="box box-info">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="text-transform: capitalize;">Transaction</h3>
                    </div>

                    <div class="box-body d-block">
                        <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Customer</th>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Pay Type</th>
                                    <th>Chq No</th>
                                    <th>Chq Date</th>
                                    <th>Type</th>
                                    <th>Credit</th>
                                    <th>Debit</th>
                                    <th>Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $tot = 0;
                                $a = 0;
                                $result = $db->prepare($sql);
                                $result->bindParam(':id', $date);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $result1 = $db->prepare("SELECT customer_name FROM customer WHERE customer_id = :id ");
                                    $result1->bindParam(':id', $row['customer_id']);
                                    $result1->execute();
                                    for ($k = 0; $row1 = $result1->fetch(); $k++) {
                                        $name = $row1['customer_name'];
                                    }
                                ?>
                                    <tr>
                                        <td><?php echo $a = $a + 1;  ?></td>
                                        <td><?php echo $name;  ?></td>
                                        <td><?php echo $row['invoice_no']  ?></td>
                                        <td><?php echo $row['date'];  ?></td>
                                        <td><?php echo $row['pay_type'];  ?></td>
                                        <td><?php echo $row['chq_no'];  ?></td>
                                        <td><?php echo $row['chq_date'];  ?></td>
                                        <td>
                                            <?php if ($row['type'] == 'credit') { ?>
                                                <span class="badge bg-blue">Payment</span>
                                            <?php } ?>
                                            <?php if ($row['type'] == 'debit') { ?>
                                                <span class="badge bg-red">Invoice</span>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($row['type'] == 'credit') { ?>
                                                <?php echo $row['credit'];  ?>
                                            <?php } ?>
                                        </td>
                                        <td>
                                            <?php if ($row['type'] == 'debit') { ?>
                                                <?php echo $row['debit'];  ?>
                                            <?php } ?>
                                        </td>
                                        <td><?php echo $row['balance']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>
                        <div style="padding-left: 25px;margin-top: 20px;">
                            <!-- <h4>Total: <small> Rs. </small> <?php echo number_format($tot, 2); ?> </h4> -->
                        </div>
                    </div>

                </div>
            </section>

        </div>

        <!-- /.content-wrapper -->
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


    <!-- Page script -->
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