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
        $_SESSION['SESS_FORM'] = '88';

        include_once("sidebar.php");

        ?>

        <style>
            th {
                vertical-align: bottom;
                text-align: center;
            }

            .th span {
                -ms-writing-mode: tb-rl;
                -webkit-writing-mode: vertical-rl;
                writing-mode: vertical-rl;
                transform: rotate(180deg);
                white-space: nowrap;
            }
        </style>

        <div class="content-wrapper">

            <section class="content">
                <div class="box box-info">

                    <div class="box-header with-border">
                        <h3 class="box-title">Filter</h3>
                    </div>
                    <?php

                    if (isset($_GET['product'])) {
                        $d1 = $_GET['d1']; //date one
                        $d2 = $_GET['d2']; //date two

                        $product = $_GET['product'];
                    } else {
                        $d1 = date('Y-m-d');
                        $d2 = date('Y-m-d');

                        $product = 'all';
                    }
                    ?>
                    <div class="box-body">

                        <form method="get">
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label>Products</label>
                                            </div>
                                            <select class="form-control select2 hidden-search" name="product" autofocus>
                                                <option value="all"> All Product </option>
                                                <option value="1"> Gas </option>
                                                <option value="2"> Cylinder </option>
                                                <option value="3"> Accessory </option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label>From :</label>
                                            </div>
                                            <input type="text" class="form-control" name="d1" id="datepicker" value="<?php echo $d1; ?>" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label>To:</label>
                                            </div>
                                            <input type="text" class="form-control" name="d2" id="datepickerd" value="<?php echo $d1; ?>" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-2" style="float: right; width: max-content;">
                                    <div class="form-group">
                                        <button class="btn btn-info" style="padding: 6px 50px;" type="submit">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>

                            </div>

                        </form>

                    </div>
                    <!-- /.box-body -->
                </div>

                <div class="box ">
                    <div class="box-header with-border">
                        <h3 class="box-title ">Sales Report</h3>
                    </div>
                    <!-- /.box-header -->
                    <?php

                    if ($product == '1') { //product 0 - 5
                        $pro1 = '0';
                        $pro2 = '4';
                    }
                    if ($product == '2') { //product 4 - 9
                        $pro1 = '5';
                        $pro2 = '9';
                    }
                    if ($product == '3') { //product 9 - 50
                        $pro1 = '9';
                        $pro2 = '50';
                    }

                    $_SESSION['SESS_BACK'] = 'sales_shop.php?d1=' . $d1 . '&d2=' . $d1 . '&product=' . $product;

                    if ($product == 'all') {

                        $sql1 = " SELECT product_id,name  FROM sales_list WHERE sales_list.loading_id = 0 AND (sales_list.date BETWEEN '$d1' and '$d2') AND sales_list.product_id > 9 AND sales_list.qty > 0 AND sales_list.action = 0 GROUP BY sales_list.product_id "; //get accessory
                        $sql2 = " SELECT invoice_no,product_id,qty  FROM sales_list WHERE sales_list.loading_id = 0 AND (sales_list.date BETWEEN '$d1' and '$d2') AND sales_list.action= 0  ORDER BY sales_list.product_id "; //get all sales list item
                        $sql3 = " SELECT *, sales.transaction_id AS tid  FROM sales WHERE sales.loading_id = 0 AND (sales.date BETWEEN '$d1' and '$d2') AND sales.action='1' "; //main array creation
                        $sql4 = " SELECT sales_list.product_id AS pid , sum(sales_list.qty)  FROM sales_list WHERE sales_list.loading_id = 0 AND (sales_list.date BETWEEN '$d1' and '$d2') AND sales_list.action = 0 GROUP BY sales_list.product_id "; //get all sales list item sum qty

                    } else {

                        $sql1 = " SELECT product_id,name  FROM sales_list WHERE sales_list.loading_id = 0 AND (sales_list.date BETWEEN '$d1' and '$d2') AND sales_list.product_id > 9 AND sales_list.qty > 0 AND sales_list.action = 0 GROUP BY sales_list.product_id "; //get accessory
                        $sql2 = " SELECT invoice_no,product_id,qty  FROM sales_list WHERE sales_list.loading_id = 0 AND (sales_list.date BETWEEN '$d1' and '$d2') AND (sales_list.product_id BETWEEN '$pro1' AND '$pro2') AND sales_list.action= 0  ORDER BY sales_list.product_id "; //get all sales list item
                        $sql3 = " SELECT *, sales.transaction_id AS tid  FROM sales WHERE sales.loading_id = 0 AND (sales.date BETWEEN '$d1' and '$d2') AND sales.action='1' "; //main array creation
                        $sql4 = " SELECT sales_list.product_id AS pid , sum(sales_list.qty)  FROM sales_list WHERE sales_list.loading_id = 0 AND (sales_list.date BETWEEN '$d1' and '$d2') AND (sales_list.product_id BETWEEN '$pro1' AND '$pro2') AND sales_list.action = 0 GROUP BY sales_list.product_id "; //get all sales list item sum qty
                    }
                    ?>

                    <div class="box-body">
                        <table id="" class="table table-bordered table-striped">

                            <thead>

                                <tr>
                                    <th colspan="3"></th>

                                    <?php if ($product == 'all' | $product != 3) { ?>

                                        <th colspan="2">12.5kg</th>
                                        <th colspan="2">5kg</th>
                                        <th colspan="2">37.5kg</th>
                                        <th colspan="2">2kg</th>

                                    <?php } ?>

                                    <?php
                                    if ($product == 'all' | $product == 3) {
                                        $ass_list = array();
                                        $result = $db->prepare($sql1);
                                        $result->bindParam(':id', $id);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {
                                            array_push($ass_list, $row['product_id']);
                                    ?>
                                            <th class="th"><span> <?php echo $row['name']; ?></span></th>
                                    <?php }
                                    } ?>

                                </tr>

                                <tr>
                                    <th>Invoice</th>
                                    <th>Date</th>
                                    <th>Customer</th>

                                    <?php if ($product == 'all' | $product != 3) { ?>

                                        <th>N</th>
                                        <th>R</th>
                                        <th>N</th>
                                        <th>R</th>
                                        <th>N</th>
                                        <th>R</th>
                                        <th>N</th>
                                        <th>R</th>

                                    <?php } ?>

                                    <?php
                                    if ($product == 'all' | $product == 3) {
                                        foreach ($ass_list as $list) { ?>
                                            <th></th>
                                    <?php }
                                    } ?>

                                    <th>Amount</th>
                                    <th>Margin</th>
                                    <th>#</th>
                                <tr>

                            </thead>

                            <tbody>

                                <?php
                                $sales_list = array();
                                $sales = array();
                                $product_arr = array();

                                $result = $db->prepare($sql2);
                                $result->bindParam(':id', $id);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {

                                    $data = array('invo' => $row['invoice_no'], 'pid' => $row['product_id'], 'qty' => $row['qty']);

                                    array_push($sales_list, $data);
                                }

                                $result = $db->prepare("SELECT * FROM products  ORDER BY product_id  ");
                                $result->bindParam(':id', $id);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    array_push($product_arr, $row['product_id']);
                                }

                                $result = $db->prepare($sql3);
                                $result->bindParam(':id', $id);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) { //row
                                    $invo = $row['invoice_number'];
                                    $cus = $row['name'];
                                    $sales_id = $row['tid'];
                                    $pay_type = $row['type'];
                                    $date = $row['date'];
                                    $amount = $row['amount'];
                                    $profit = $row['profit'];

                                    $temp = array();

                                    $temp['id'] =  $sales_id;
                                    $temp['invo'] =  $invo;
                                    $temp['cus'] =  $cus;
                                    $temp['type'] =  $pay_type;
                                    $temp['date'] =  $date;
                                    $temp['amount'] =  $amount;
                                    $temp['profit'] =  $profit;
                                    $temp['qty'] = 0;

                                    foreach ($product_arr as $p_id) { //colum
                                        $temp[$p_id] = '';
                                    }

                                    foreach ($sales_list as $list) {

                                        if ($list['invo'] == $invo) {

                                            foreach ($product_arr as $p_id) { //colum

                                                if ($p_id == $list['pid']) {
                                                    if ($p_id > 4) {
                                                        $temp[$p_id] = "<span class='pull-right badge bg-muted'> " . $list['qty'] . "</span>";
                                                    } else {
                                                        $temp[$p_id] = "<span class='pull-right badge bg-yellow'> " . $list['qty'] . "</span>";
                                                    }
                                                }
                                            }
                                            $temp['qty'] = $list['qty'];
                                        }
                                    }

                                    array_push($sales, $temp);
                                }
                                ?>

                                <?php foreach ($sales as $list) {
                                    if ($list['qty'] > 0) { ?>

                                        <tr id="row<?php echo $list['id']; ?>">

                                            <td> <?php echo $list['invo']; ?> </td>
                                            <td>
                                                <span class="badge bg-gray"> <i class="fa fa-calendar"></i> <?php echo $list['date']; ?></span>
                                            </td>
                                            <td>
                                                <?php echo $list['cus']; ?>
                                            </td>

                                            <?php if ($product == 'all' | $product != 3) { ?>

                                                <td> <?php echo $list['5']; ?></td>
                                                <td> <?php echo $list['1']; ?> </td>

                                                <td> <?php echo $list['6']; ?></td>
                                                <td><?php echo $list['2']; ?></td>

                                                <td> <?php echo $list['7']; ?></td>
                                                <td><?php echo $list['3']; ?></td>

                                                <td> <?php echo $list['8']; ?> </td>
                                                <td> <?php echo $list['4']; ?> </td>

                                            <?php } ?>

                                            <?php
                                            if ($product == 'all' | $product == 3) {

                                                foreach ($ass_list as $ass) { ?>

                                                    <td> <?php echo $list[$ass]; ?> </td>

                                            <?php }
                                            } ?>

                                            <td> <?php echo $list['amount']; ?> </td>
                                            <td> <?php echo $list['profit']; ?> </td>
                                            <td>
                                                <a href="bill2.php?invo=<?php echo base64_encode($list['invo']); ?>" title="Click to Print" class="btn btn-primary btn-sm fa fa-print"> </a>
                                                <a href="#" onclick="sales_dll(<?php echo $list['id']; ?>)" title="Click to delete" class="btn bg-red btn-sm fa fa-trash"> </a>
                                                <form action="sales_shop_dll.php" method="POST" id="form<?php echo $list['id']; ?>">
                                                    <input type="hidden" name="id" value="<?php echo $list['id']; ?>">
                                                </form>
                                            </td>

                                        </tr>

                                <?php }
                                } ?>
                            </tbody>

                            <?php
                            $total = array();

                            foreach ($product_arr as $p_id) {
                                $total[$p_id] = '';
                            }

                            $result = $db->prepare($sql4);
                            $result->bindParam(':id', $id);
                            $result->execute();
                            for ($i = 0; $row = $result->fetch(); $i++) {
                                $total[$row['pid']] = $row['sum(sales_list.qty)'];
                            }
                            ?>

                            <tfoot style="background-color: rgb(var(--bg-light-70));">
                                <tr>
                                    <th colspan="3" style="text-align: right;">Total</th>

                                    <?php if ($product == 'all' | $product != 3) { ?>

                                        <td> <span class="pull-right badge bg-muted"> <?php echo $total['5']; ?> </span>
                                        </td>
                                        <td> <span class="pull-right badge bg-yellow"> <?php echo $total['1']; ?> </span>
                                        </td>
                                        <td> <span class="pull-right badge bg-muted"> <?php echo $total['6']; ?> </span>
                                        </td>
                                        <td> <span class="pull-right badge bg-yellow"> <?php echo $total['2']; ?> </span>
                                        </td>
                                        <td> <span class="pull-right badge bg-muted"> <?php echo $total['7']; ?> </span>
                                        </td>
                                        <td> <span class="pull-right badge bg-yellow"> <?php echo $total['3']; ?> </span>
                                        </td>
                                        <td> <span class="pull-right badge bg-muted"> <?php echo $total['8']; ?> </span>
                                        </td>
                                        <td> <span class="pull-right badge bg-yellow"> <?php echo $total['4']; ?> </span>
                                        </td>

                                    <?php } ?>

                                    <?php
                                    if ($product == 'all' | $product == 3) {
                                        foreach ($total as $i => $tot) {
                                            if ($i > 9  && $tot > 0) { ?>
                                                <td>
                                                    <span class="pull-right badge bg-muted">
                                                        <?php
                                                        echo $tot;
                                                        ?>
                                                    </span>
                                                </td>

                                    <?php }
                                        }
                                    } ?>

                                    <td colspan="3"></td>

                                </tr>

                            </tfoot>

                        </table>
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
        function sales_dll(id) {
            var info = 'id=' + id;
            if (confirm("Sure you want to delete this Invoice? There is NO undo!")) {

                $('#form' + id).submit();
                $("#row" + id).animate({
                        backgroundColor: "#fbc7c7"
                    }, "fast")
                    .animate({
                        opacity: "hide"
                    }, "slow");

            }
            return false;
        }
    </script>

    <!-- page script -->
    <script>
        $(function() {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false
            });

            $(".select2").select2();
            $('.select2.hidden-search').select2({
                minimumResultsForSearch: -1
            });
        });


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

        function view_payment_date(type) {
            if (type == 'group') {
                document.getElementById('group_view').style.display = 'block';
                document.getElementById('type_view').style.display = 'none';
                document.getElementById('cus_view').style.display = 'none';
            } else if (type == 'type') {
                document.getElementById('type_view').style.display = 'block';
                document.getElementById('group_view').style.display = 'none';
                document.getElementById('cus_view').style.display = 'none';
            } else if (type == 'cus') {
                document.getElementById('type_view').style.display = 'none';
                document.getElementById('group_view').style.display = 'none';
                document.getElementById('cus_view').style.display = 'block';
            } else {
                document.getElementById('type_view').style.display = 'none';
                document.getElementById('group_view').style.display = 'none';
                document.getElementById('cus_view').style.display = 'none';
            }
        }
    </script>


</body>

</html>