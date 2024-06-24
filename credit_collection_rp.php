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
        $_SESSION['SESS_FORM'] = '30';

        include_once("sidebar.php");
        ?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Credit Payment Report
                    <small>Preview</small>
                </h1>
            </section>


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

                            $id = 0;
                            $dates = date('Y/m/d-Y/m/d');;

                            if (isset($_GET['id'])) {
                                $id = $_GET['id'];
                                $dates = $_GET['dates'];
                            }
                            $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
                            $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");

                            $_SESSION['SESS_BACK'] = "credit_collection_rp.php?dates=" . $dates . "&id=" . $id;

                            $customer = ' ';
                            if ($id > 0) {
                                $customer = 'customer_id = ' . $id . ' AND ';
                            }

                            $sql = "SELECT * FROM payment WHERE $customer action < 5 AND paycose = 'credit_payment'  AND date BETWEEN '$d1' AND '$d2' ORDER BY transaction_id ";

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

                <div class="box">
                    <div class="box-header">
                        <h3 class="box-title"> Credit Payment List </h3>
                    </div>
                    <!-- /.box-header -->

                    <div class="box-body">
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Invoice no </th>
                                    <th>Loading</th>
                                    <th>Customer</th>
                                    <th>Pay type</th>
                                    <th>Chq no</th>
                                    <th>Chq Date</th>
                                    <th>Bank</th>
                                    <th>Amount </th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                include("connect.php");
                                $tot = 0;
                                $result = $db->prepare($sql);
                                $result->bindParam(':userid', $date);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $user = $row['user_id'];
                                    $loading = $row['loading_id'];

                                    $cus = '';
                                    $result1 = $db->prepare("SELECT * FROM customer WHERE customer_id =:id ");
                                    $result1->bindParam(':id', $row['customer_id']);
                                    $result1->execute();
                                    for ($i = 0; $row1 = $result1->fetch(); $i++) {
                                        $cus = $row1['customer_name'];
                                    }

                                    $action = 'unload';
                                    $lorry = '';
                                    $result1 = $db->prepare("SELECT * FROM loading WHERE transaction_id ='$loading' ");
                                    $result1->bindParam(':userid', $loading);
                                    $result1->execute();
                                    for ($i = 0; $row1 = $result1->fetch(); $i++) {
                                        $action = $row1['action'];
                                        $lorry = $row1['lorry_no'];
                                    }
                                    if ($action == "load") { ?>
                                        <tr style="background-color: rgba(var(--bg-text-dark-100), 0.1);opacity: 0.8;">
                                        <?php } else { ?>
                                        <tr>
                                        <?php } ?>

                                        <td><?php echo $row['invoice_no']; ?></td>
                                        <td>
                                            <span class="badge bg-blue"> <i class="fa fa-truck"></i> <?php echo $lorry; ?> </span> <br>
                                            <a href="loading_view.php?id=<?php echo $loading ?>" class="badge bg-green">Loading ID: <?php echo $loading ?></a>
                                        </td>
                                        <td><?php echo $cus; ?></td>
                                        <td><?php echo $pay_type = $row['pay_type']; ?></td>
                                        <td><?php echo $row['chq_no']; ?></td>
                                        <td><?php echo $row['chq_date']; ?></td>
                                        <td><?php echo $row['chq_bank']; ?></td>
                                        <td><?php echo $row['amount']; ?></td>
                                        <td>
                                            <?php if ($action == "unload") { ?>
                                                <span onclick="click_open(1,<?php echo $row['transaction_id']; ?>)" class="btn btn-warning btn-sm fa fa-eye"></span>
                                                <a href="#" onclick="collection_dll(<?php echo $row['transaction_id']; ?>)" class="btn btn-danger btn-sm fa fa-trash"></a>

                                                <form action="credit_collection_dll.php" method="POST" id="form-dll-<?php echo $row['transaction_id']; ?>">
                                                    <input type="hidden" name="id" value="<?php echo $row['transaction_id']; ?>">
                                                </form>
                                            <?php } else {  ?>
                                                <i class="fa fa-refresh fa-spin"></i>
                                            <?php }  ?>
                                        </td>
                                        </tr>
                                    <?php $tot += $row['amount'];
                                }
                                    ?>

                            </tbody>
                            <tfoot>
                            </tfoot>
                        </table>

                        <h3>Total: <small>Rs.</small> <?php echo $tot; ?> </h3>

                    </div>
                    <!-- /.box-body -->
                </div>


            </section>
            <!-- /.content -->
        </div>
        <!-- /.content-wrapper -->
        <?php
        include("dounbr.php");
        ?>

        <div class="container-up d-none" id="container_up">
            <div class="container-close" onclick="click_close()"></div>
            <div class="row">
                <div class="col-md-12">

                    <div class="box box-success popup d-none" id="popup_1" style="width: 800px;">
                        <div class="box-header with-border">
                            <h3 class="box-title w-100">
                                Edit Payment
                                <i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
                            </h3>
                        </div>

                        <div class="box-body d-block">
                            <table id="example2" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Loading</th>
                                        <th>Invoice no</th>
                                        <th>Customer</th>
                                        <th>Credit Amount (Rs.)</th>
                                        <th>Pay Amount (Rs.)</th>
                                    </tr>
                                </thead>
                                <tbody id="credit_edit">
                                </tbody>
                            </table>
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
    <script>
        function click_open(i, id) {
            $("#popup_" + i).removeClass("d-none");
            $("#container_up").removeClass("d-none");


            var xmlhttp;
            if (window.XMLHttpRequest) {
                xmlhttp = new XMLHttpRequest();
            } else {
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            xmlhttp.onreadystatechange = function() {
                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    document.getElementById("credit_edit").innerHTML = xmlhttp.responseText;
                }
            }

            xmlhttp.open("GET", "credit_collection_get.php?id=" + id, true);
            xmlhttp.send();

        }

        function collection_dll(id) {
            if (confirm("Sure you want to delete this collection? There is NO undo!")) {
                $('#form-dll-' + id).submit();
            }

            return false;
        }

        function click_close() {
            $(".popup").addClass("d-none");
            $("#container_up").addClass("d-none");
        }
    </script>

    <script>
        $(function() {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": false,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": false,
                "autoWidth": true
            });

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


            $('#datepicker').datepicker({
                autoclose: true,
                datepicker: true,
                format: 'yyyy-mm-dd '
            });
            $('#datepicker').datepicker({
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