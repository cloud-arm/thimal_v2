<!DOCTYPE html>
<html>
<?php
include("head.php");
include("connect.php");
date_default_timezone_set("Asia/Colombo");
?>

<body class="hold-transition skin-yellow sidebar-mini sidebar-collapse">
    <div class="wrapper" style="overflow-y: hidden;">
        <?php
        include_once("auth.php");
        $r = $_SESSION['SESS_LAST_NAME'];
        $_SESSION['SESS_FORM'] = '90';


        include_once("sidebar.php");

        ?>
        <style>
        th {
            text-align: center;
            padding: 8px !important;
        }
        </style>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    DEALER SALES
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
                                <h3 class="box-title">Date Selector</h3>
                            </div>

                            <?php
                            date_default_timezone_set("Asia/Colombo");

                            if (isset($_GET['d1'])) {
                                $d1 = $_GET['d1'];
                                $d2 = $_GET['d2'];
                                $date1 = $_GET['d1'];
                                $date2 = $_GET['d2'];
                            } else {
                                $d1 = date("Y-m-d");
                                $d2 = date("Y-m-d");
                                $date1 = date("Y-m-d");
                                $date2 = date("Y-m-d");
                            }

                            $startDate1 = new DateTime($d1);
                            $startDate2 = new DateTime($d1);
                            $endDate = new DateTime($d2);

                            $months = [];
                            $monthSave = [];
                            $monthNumber = [];

                            $sales = array();

                            while ($startDate1 < $endDate) {

                                $d1 = $startDate1->format('Y-m') . '-01';
                                $d2 = $startDate1->format('Y-m') . '-31';
                                $month = $startDate1->format('Y-m');

                                $sales_sum = 0;
                                $result = $db->prepare("SELECT sum(qty) FROM sales_list WHERE action = 0 AND product_id = 1 AND date BETWEEN '$d1' AND '$d2'  ");
                                $result->bindParam(':id', $id);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $sales_sum = $row['sum(qty)'];
                                }

                                $month_sum = 0;
                                $result = $db->prepare("SELECT sum(qty) FROM customer_month_sales WHERE  product_id = 1 AND month = '$month' ");
                                $result->bindParam(':id', $id);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $month_sum = $row['sum(qty)'];
                                }

                                if ($sales_sum > $month_sum) {
                                    $monthSave[] = $month;

                                    // echo 'Sales: ' . $sales_sum . '<br> Customer: ' . $month_sum . '<br> Month: ' . $month . '<br><br><br>';

                                    $d1 = $month . '-01';
                                    $d2 = $month . '-31';

                                    $sql = "SELECT * FROM sales_list WHERE action = 0 AND (date BETWEEN '$d1' AND '$d2') ";
                                    $result = $db->prepare($sql);
                                    $result->bindParam(':userid', $date);
                                    $result->execute();
                                    for ($i = 0; $row = $result->fetch(); $i++) {
                                        $sales[] = ["customer_id" => $row["cus_id"], "date" => $row["date"], "product_id" => $row["product_id"], "product_name" => $row["name"], "qty" => $row["qty"]];
                                    }

                                    $result1 = $db->prepare("DELETE FROM customer_month_sales WHERE  month= :id ");
                                    $result1->bindParam(':id', $month);
                                    $result1->execute();
                                }
                                $monthNumber[] = $month;

                                $startDate1->modify('first day of next month');
                            }

                            while ($startDate2 < $endDate) {
                                $months[] = $startDate2->format('F');
                                $startDate2->modify('first day of next month');
                            }

                            if (isset($_GET['products'])) {
                                $products = $_GET['products'];
                            } else {
                                $products = ["1", "2"];
                            }

                            function get_qty($array, $month, $pro_id, $cus_id)
                            {
                                $qty = 0;
                                foreach ($array as $item) {
                                    $temp_month = date('Y-m', strtotime($item['date']));
                                    if ($temp_month == $month && $item['product_id'] == $pro_id && $item['customer_id'] == $cus_id) {
                                        $qty += $item['qty'];
                                    }
                                }

                                return $qty;
                            }

                            function get_name($array, $id)
                            {
                                return $array[$id];
                            }

                            $short_name = [];
                            $gen_name = [];

                            $result = $db->prepare("SELECT gen_name,product_id,short_name FROM products WHERE product_id < 5 ");
                            $result->bindParam(':id', $date);
                            $result->execute();
                            for ($i = 0; $row = $result->fetch(); $i++) {
                                $short_name[$row['product_id']] = $row['short_name'];
                                $gen_name[$row['product_id']] = $row['gen_name'];
                            }

                            if (count($monthSave)) {
                                $result = $db->prepare("SELECT customer_id,root,customer_name,root_id FROM customer   ORDER BY root ");
                                $result->bindParam(':id', $date);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {

                                    foreach ($monthSave as $month) {

                                        foreach ($products as $pid) {

                                            $sql = "INSERT INTO customer_month_sales (customer_id,customer_name,root_id,root_name,product_id,product_name,short_name,qty,month) VALUES (?,?,?,?,?,?,?,?,?)";
                                            $ql = $db->prepare($sql);
                                            $ql->execute(array($row['customer_id'], $row['customer_name'], $row['root_id'], $row['root'], $pid, get_name($gen_name, $pid), get_name($short_name, $pid), get_qty($sales, $month, $pid, $row['customer_id']), $month));
                                        }
                                    }
                                }
                            }
                            $length = count($products);
                            ?>

                            <div class="box-body">
                                <form action="" method="GET">
                                    <div class="row" style="margin: 20px 0 10px 0;">

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <label>From :</label>
                                                    </div>
                                                    <input type="text" class="form-control" name="d1" id="datepicker1"
                                                        value="<?php echo $date1; ?>" autocomplete="off" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <label>To:</label>
                                                    </div>
                                                    <input type="text" class="form-control" name="d2" id="datepicker2"
                                                        value="<?php echo $date2; ?>" autocomplete="off" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-9">
                                            <div class="form-group">
                                                <div class="input-group">
                                                    <div class="input-group-addon">
                                                        <label>Product</label>
                                                    </div>
                                                    <select class="form-control select2" name="products[]"
                                                        multiple="multiple" data-placeholder="Select products"
                                                        style="width: 100%;">
                                                        <?php
                                                        $result = $db->prepare("SELECT product_id,gen_name FROM products WHERE product_id < 5 ");
                                                        $result->bindParam(':id', $res);
                                                        $result->execute();
                                                        for ($i = 0; $row = $result->fetch(); $i++) {

                                                        ?>
                                                        <option value="<?php echo $row['product_id']; ?>">
                                                            <?php echo $row['gen_name']; ?>
                                                        </option>
                                                        <?php  } ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-2" style="float: right; width: max-content;padding-left: 0;">
                                            <div class="form-group">
                                                <button class="btn btn-info" style="padding: 6px 50px;" type="submit">
                                                    <i class="fa fa-search"></i> Search
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box box-info">

                    <div class="box-header with-border">
                        <h3 class="box-title" style="text-transform: capitalize;">Dealer Sales</h3>
                        <span id="tbl_btn"></span>
                    </div>

                    <div class="box-body d-block">
                        <table id="example" class="table table-bordered" style="border-radius: 0;">
                            <thead>
                                <tr>
                                    <th rowspan="2">ID</th>
                                    <th rowspan="2">ROOT</th>
                                    <th rowspan="2">DEALER</th>

                                    <?php
                                    foreach ($months as $month) {
                                        echo sprintf('<th colspan="%s">%s</th>', $length, strtoupper($month));
                                    }
                                    ?>

                                    <?php
                                    echo sprintf('<th colspan="%s">%s</th>', $length, "AVG");
                                    ?>
                                </tr>
                                <tr>
                                    <?php
                                    foreach ($months as $month) {
                                        foreach ($products as $pid) {
                                            echo sprintf('<th>%s</th>', get_name($short_name, $pid));
                                        }
                                    }
                                    ?>

                                    <?php $total_pro=[];
                                    foreach ($products as $pid) {
                                        echo sprintf('<th>%s</th>', get_name($short_name, $pid));
                                        $total_pro[$pid]=0;
                                    }
                                    ?>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $data =  array();

                                $m1 = date('Y-m', strtotime($date1));
                                $m2 = date('Y-m', strtotime($date2));

                                $sql = "SELECT * FROM customer_month_sales WHERE  (month BETWEEN '$m1' AND '$m2') ";
                                $result = $db->prepare($sql);
                                $result->bindParam(':userid', $date);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $data[] = ["customer_id" => $row["customer_id"], "date" => $row["month"], "product_id" => $row["product_id"], "qty" => $row["qty"]];
                                }

                                $result1 = $db->prepare("SELECT root_name FROM root ORDER BY root_name ");
                                $result1->bindParam(':id', $date);
                                $result1->execute();
                                for ($k = 0; $row1 = $result1->fetch(); $k++) {

                                    $result = $db->prepare("SELECT * FROM customer_month_sales WHERE root_name = :id AND (month BETWEEN '$m1' AND '$m2') GROUP BY customer_id ");
                                    $result->bindParam(':id', $row1['root_name']);
                                    $result->execute();
                                    for ($i = 0; $row = $result->fetch(); $i++) {

                                        $avg_total = [];

                                        foreach ($products as $pid) {
                                            $avg_total[$pid] = 0;
                                        } ?>
                                <tr>
                                    <td><?php echo $row['customer_id']; ?></td>
                                    <td><?php echo $row['root_name']; ?></td>
                                    <td><?php echo $row['customer_name']; ?></td>

                                    <?php foreach ($monthNumber as $month) {
                                                foreach ($products as $pid) { ?>
                                    <td align="center">
                                        <?php
                                                        echo $qty = get_qty($data, $month, $pid, $row['customer_id']);
                                                        $avg_total[$pid] += $qty;
                                                        $total_pro[$pid] += $qty;
                                                        ?>
                                    </td>
                                    <?php }
                                            } ?>

                                    <?php foreach ($products as $pid) { ?>
                                    <td align="right">
                                        <?php echo str_replace(",", "", number_format($avg_total[$pid] / count($monthNumber), 2)); ?>
                                    </td>
                                    <?php } ?>
                                </tr>
                                <?php } ?>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>

                                    <?php foreach ($monthNumber as $month) {
                                            foreach ($products as $pid) { ?>
                                    <td align="center"></td>
                                    <?php }
                                        } ?>

                                    <?php foreach ($products as $pid) { ?>
                                    <td align="center"></td>
                                    <?php }  ?>
                                </tr>
                                <?php } ?>

                            </tbody>
                            <tfoot>
                            <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>

                                    <?php foreach ($monthNumber as $month) {
                                            foreach ($products as $pid) { ?>
                                    <td align="center"><?php echo $total_pro[$pid]; ?></td>
                                    <?php }
                                        } ?>

                                    <?php foreach ($products as $pid) { ?>
                                    <td align="center"></td>
                                    <?php }  ?> 
                                </tr>
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
    <!-- AdminLTE App -->
    <script src="../../dist/js/app.min.js"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="../../dist/js/demo.js"></script>
    <!-- Dark Theme Btn-->
    <script src="https://dev.colorbiz.org/ashen/cdn/main/dist/js/DarkTheme.js"></script>

    <script type="text/javascript">
    $(function() {
        $("#example").DataTable({
            "searching": true,
            "paging": true,
            "responsive": true,
            "lengthChange": true,
            "ordering": false,
            "info": true,
            "autoWidth": true,
            "buttons": ["excel", "pdf", "print"]
        }).buttons().container().appendTo('#tbl_btn');
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
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                        'month').endOf('month')]
                },
                startDate: moment().subtract(29, 'days'),
                endDate: moment()
            },
            function(start, end) {
                $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
                    'MMMM D, YYYY'));
            }
        );

        //Date picker
        $('#datepicker1').datepicker({
            autoclose: true,
            datepicker: true,
            format: 'yyyy-mm-dd'
        });

        $('#datepicker2').datepicker({
            autoclose: true,
            datepicker: true,
            format: 'yyyy-mm-dd'
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
