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
        $_SESSION['SESS_FORM'] = '62';

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
                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Select Product & Date</h3>
                    </div>
                    <?php

                    if (isset($_GET['type'])) {

                        $type = $_GET['type'];
                        $dates = $_GET['dates'];

                        $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
                        $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");

                        $product = $_GET['product'];
                    } else {
                        $d1 = date('Y-m-d');
                        $d2 = date('Y-m-d');

                        $dates = date('Y/m/d-Y/m/d');
                        $type = 'all';
                        $product = 'all';
                    }
                    ?>

                    <div class="box-body" style="margin-top: 10px;">

                        <form method="get">
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label>Type</label>
                                            </div>
                                            <select class="form-control select2 hidden-search" onchange="select_type(this.value)" name="type" autofocus>
                                                <option value="all"> All Record </option>
                                                <option value="credit"> Credit Record </option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 record">
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

                                <div class="col-md-4 record">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-addon">
                                                <label>Dates :</label>
                                            </div>
                                            <input type="text" class="form-control" name="dates" id="reservation" value="<?php echo $dates; ?>" autocomplete="off" />
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
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
                        <h3 class="box-title">Purchases Report</h3>

                        <span id="tbl_btn"></span>
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

                    if ($type == 'all') {
                        if ($product == 'all') {

                            $sql1 = " SELECT *  FROM purchases_item WHERE (purchases_item.pu_date BETWEEN '$d1' and '$d2') AND purchases_item.product_id > 9 AND purchases_item.action = 'active' GROUP BY purchases_item.product_id "; //get accessory
                            $sql2 = " SELECT *  FROM purchases_item WHERE (purchases_item.pu_date BETWEEN '$d1' and '$d2') AND purchases_item.action= 'active' ORDER BY purchases_item.product_id "; //get all purchase list item
                            $sql3 = " SELECT *  FROM purchases JOIN supply_payment ON purchases.invoice_number = supply_payment.invoice_no WHERE (purchases.pu_date BETWEEN '$d1' and '$d2') GROUP BY supply_payment.invoice_no "; //main array creation
                            $sql4 = " SELECT *, sum(purchases_item.qty)  FROM purchases_item WHERE (purchases_item.pu_date BETWEEN '$d1' and '$d2') AND purchases_item.action = 'active' GROUP BY purchases_item.product_id "; //get all purchase list item sum qty
                        } else {

                            $sql1 = " SELECT *  FROM purchases_item WHERE (purchases_item.pu_date BETWEEN '$d1' and '$d2') AND purchases_item.product_id > 9 AND purchases_item.action = 'active' GROUP BY purchases_item.product_id "; //get accessory
                            $sql2 = " SELECT *  FROM purchases_item WHERE (purchases_item.pu_date BETWEEN '$d1' and '$d2') AND (purchases_item.product_id BETWEEN '$pro1' AND '$pro2') AND purchases_item.action= 'active' ORDER BY purchases_item.product_id "; //get all purchase list item
                            $sql3 = " SELECT *  FROM purchases JOIN supply_payment ON purchases.invoice_number = supply_payment.invoice_no WHERE (purchases.pu_date BETWEEN '$d1' and '$d2') GROUP BY supply_payment.invoice_no "; //main array creation
                            $sql4 = " SELECT *, sum(purchases_item.qty)  FROM purchases_item WHERE (purchases_item.pu_date BETWEEN '$d1' and '$d2') AND (purchases_item.product_id BETWEEN '$pro1' AND '$pro2') AND purchases_item.action = 'active' GROUP BY purchases_item.product_id "; //get all purchase list item sum qty
                        }
                    } else {

                        $sql1 = " SELECT *  FROM purchases_item WHERE purchases_item.product_id > 9 AND purchases_item.action = 'active' GROUP BY purchases_item.product_id "; //get accessory
                        $sql2 = " SELECT *  FROM purchases_item WHERE purchases_item.action= 'active' ORDER BY purchases_item.product_id "; //get all purchase list item
                        $sql3 = " SELECT *  FROM purchases JOIN supply_payment ON purchases.invoice_number = supply_payment.invoice_no WHERE supply_payment.credit_balance > 0 GROUP BY supply_payment.invoice_no "; //main array creation
                        $sql4 = " SELECT *, sum(purchases_item.qty)  FROM purchases_item WHERE purchases_item.action = 'active' GROUP BY purchases_item.product_id "; //get all purchase list item sum qty
                    }
                    ?>

                    <div class="box-body">
                        <table id="example" class="table table-bordered table-striped">

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

                                    <th colspan="2"></th>

                                </tr>

                                <tr>
                                    <th>No</th>
                                    <th>Invoice</th>
                                    <th>Date</th>

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

                                    <th>Pay Type</th>
                                    <th>Amount</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php
                                $purchases_item = array();
                                $purchases = array();
                                $product = array();
                                $tot_credit = 0;
                                $tot_amount = 0;

                                $result = $db->prepare($sql2);
                                $result->bindParam(':id', $id);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {

                                    $data = array('invo' => $row['invoice'], 'pid' => $row['product_id'], 'qty' => $row['qty']);

                                    array_push($purchases_item, $data);
                                }

                                $result = $db->prepare("SELECT * FROM products  ORDER BY product_id  ");
                                $result->bindParam(':id', $id);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    array_push($product, $row['product_id']);
                                }

                                $result = $db->prepare($sql3);
                                $result->bindParam(':id', $id);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) { //row
                                    $invo = $row['invoice_number'];
                                    $id = $row['supplier_invoice'];
                                    $date = $row['pu_date'];
                                    $type = $row['pay_type'];
                                    $amount = $row['amount'];
                                    $credit_balance = $row['credit_balance'];
                                    $action = $row['action'];

                                    $temp = array();

                                    $temp['id'] =  $i + 1;
                                    $temp['invo'] =  $id;
                                    $temp['type'] =  $type;
                                    $temp['date'] =  $date;
                                    $temp['amount'] =  $amount;
                                    $temp['credit_balance'] =  $credit_balance;
                                    $temp['action'] =  $action;
                                    $temp['qty'] = 0;

                                    foreach ($product as $p_id) { //colum
                                        $temp[$p_id] = '';
                                    }

                                    foreach ($purchases_item as $list) {

                                        if ($list['invo'] == $invo) {

                                            foreach ($product as $p_id) { //colum

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

                                    array_push($purchases, $temp);
                                }
                                ?>

                                <?php foreach ($purchases as $list) {
                                    if ($list['qty'] > 0) { ?>

                                        <tr>

                                            <td> <?php echo $list['id']; ?> </td>
                                            <td>
                                                <?php echo $list['invo']; ?>
                                            </td>
                                            <td> <?php echo $list['date']; ?> </td>

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

                                            <td>
                                                <?php echo $list['type']; ?>
                                                <?php if ($list['action'] == 2) { ?>
                                                    <i class="fa fa-check text-green"></i>
                                                <?php } else if ($list['action'] == 3) { ?>
                                                    <i class="fa fa-times text-green"></i>
                                                <?php } ?> <br>
                                                <?php if ($list['credit_balance'] > 0) { ?>
                                                    <span class="badge bg-red"><?php echo number_format($list['credit_balance'], 2); ?></span>
                                                    <?php $tot_credit += $list['credit_balance']; ?>
                                                <?php } else { ?>
                                                    <span class="badge bg-blue">Paid</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <?php echo number_format($list['amount'], 2); ?>
                                                <?php $tot_amount += $list['amount']; ?>
                                            </td>

                                        </tr>

                                <?php }
                                } ?>
                            </tbody>

                            <?php
                            $total = array();

                            foreach ($product as $p_id) {
                                $total[$p_id] = '';
                            }

                            $result = $db->prepare($sql4);
                            $result->bindParam(':id', $id);
                            $result->execute();
                            for ($i = 0; $row = $result->fetch(); $i++) {
                                $total[$row['product_id']] = $row['sum(purchases_item.qty)'];
                            }
                            ?>

                            <tfoot style="background-color: rgb(var(--bg-light-70));">
                                <tr>
                                    <th colspan="2"></th>
                                    <th>Total</th>

                                    <th> <span class="pull-right badge bg-muted"> <?php echo $total['5']; ?> </span>
                                    </th>
                                    <th> <span class="pull-right badge bg-yellow"> <?php echo $total['1']; ?> </span>
                                    </th>
                                    <th> <span class="pull-right badge bg-muted"> <?php echo $total['6']; ?> </span>
                                    </th>
                                    <th> <span class="pull-right badge bg-yellow"> <?php echo $total['2']; ?> </span>
                                    </th>
                                    <th> <span class="pull-right badge bg-muted"> <?php echo $total['7']; ?> </span>
                                    </th>
                                    <th> <span class="pull-right badge bg-yellow"> <?php echo $total['3']; ?> </span>
                                    </th>
                                    <th> <span class="pull-right badge bg-muted"> <?php echo $total['8']; ?> </span>
                                    </th>
                                    <th> <span class="pull-right badge bg-yellow"> <?php echo $total['4']; ?> </span>
                                    </th>

                                    <?php
                                    if ($product == 'all' | $product == 3) {
                                        foreach ($total as $i => $tot) {
                                            if ($i > 9  && $tot > 0) { ?>
                                                <th>
                                                    <span class="pull-right badge bg-muted">
                                                        <?php
                                                        echo $tot;
                                                        ?>
                                                    </span>
                                                </th>

                                    <?php }
                                        }
                                    } ?>

                                    <td><?php echo number_format($tot_credit, 2); ?></td>
                                    <td><?php echo number_format($tot_amount, 2); ?></td>

                                </tr>

                            </tfoot>

                        </table>

                        <div style="padding-left: 20px;margin-top: 20px;">
                            <h4>Total Credit: <small> Rs. </small> <?php echo number_format($tot_credit, 2); ?> </h4>
                        </div>
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


    <!-- page script -->
    <script>
        function select_type(val) {
            if (val == 'credit') {
                $('.record').css('display', 'none');
            } else {
                $('.record').css('display', 'block');
            }
        }

        $(function() {
            $("#example").DataTable({
                "responsive": true,
                "buttons": ["excel", "pdf", "print"]
            }).buttons().container().appendTo('#tbl_btn');
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
    </script>


</body>

</html>