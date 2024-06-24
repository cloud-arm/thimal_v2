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
        $_SESSION['SESS_FORM'] = '91';
        date_default_timezone_set("Asia/Colombo");

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    ACCOUNTING
                    <small>Preview</small>
                    <?php if (isset($_GET['acc']) || isset($_GET['acc_ex'])) {
                        if (isset($_GET['acc'])) {
                            $_SESSION['SESS_BACK'] = "acc_account.php?acc=" . $_GET['acc'];
                        }
                    ?>
                        <a href="acc_account.php" class="btn btn-warning btn-sm"> <i class="fa-regular fa-circle-left"></i> Back</a>
                    <?php } ?>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row">

                    <?php
                    if (isset($_GET['acc']) && $_GET['acc'] == 1) {

                        $result = select("acc_account_type", "*", "main_id = '" . $_GET['acc'] . "' AND type = 'sub1' AND data_source = 'child_table' ");
                        foreach ($result as $row1) {   ?>
                            <div class="col-md-6">
                                <div class="box box-info">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><?php echo ucfirst($row1['acc_name']); ?></h3>
                                    </div>
                                    <div class="box-body">
                                        <table id="example<?php echo $row1['sn']; ?>" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>ACC_NAME</th>
                                                    <th>Amount</th>
                                                    <th>#</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $result2 = select($row1['child_table'], "id,name,amount");
                                                foreach ($result2 as $row2) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo ucfirst($row2['id']); ?></td>
                                                        <td><?php echo ucfirst($row2['name']); ?></td>
                                                        <td><?php echo ucfirst($row2['amount']); ?></td>
                                                        <td align="center"><a href="acc_account_view.php?acc=<?php echo $row2['id'] . "&acc_name=" . $row1['child_table']; ?>" class="btn btn-sm btn-warning fa fa-file-text-o"></a></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    } else if (isset($_GET['acc']) && $_GET['acc'] == 4) {

                        $result = select("acc_account_type", "*", "main_id = '" . $_GET['acc'] . "' AND type = 'sub1' ");
                        foreach ($result as $row1) {

                            if ($row1['data_source'] == 'child_table') {
                            ?>
                                <div class="col-md-6">
                                    <div class="box box-info">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><?php echo ucfirst($row1['acc_name']); ?></h3>
                                        </div>
                                        <div class="box-body">
                                            <table id="example<?php echo $row1['sn']; ?>" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>ACC_NAME</th>
                                                        <th>DRIVER</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $result2 = select($row1['child_table'], "lorry_id,lorry_no,driver");
                                                    foreach ($result2 as $row2) {
                                                    ?>
                                                        <tr>
                                                            <td><?php echo ucfirst($row2['lorry_id']); ?></td>
                                                            <td><?php echo ucfirst($row2['lorry_no']); ?></td>
                                                            <td><?php echo ucfirst($row2['driver']); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            } else { ?>
                                <div class="col-md-6">
                                    <div class="box box-info">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><?php echo ucfirst($row1['acc_name']); ?></h3>
                                        </div>
                                        <div class="box-body">
                                            <table id="example<?php echo $row1['sn']; ?>" class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>ACC_NAME</th>
                                                        <th>Duration</th>
                                                        <th>Amount</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $result2 = select("acc_assets", "*", "main_id = '" . $_GET['acc'] . "' AND sub_id = '" . $row1['sn'] . "' ");
                                                    foreach ($result2 as $row2) {
                                                    ?>
                                                        <tr>
                                                            <td><?php echo ucfirst($row2['id']); ?></td>
                                                            <td><?php echo ucfirst($row2['acc_name']); ?></td>
                                                            <td><?php echo ucfirst($row2['dep_month']); ?></td>
                                                            <td><?php echo ucfirst($row2['dep_amount']); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            <?php
                            }
                        }
                    } else if (isset($_GET['acc_ex'])) {

                        $result = select("acc_account_type", "acc_name,sn", "sn = '" . $_GET['acc_ex'] . "' ");
                        foreach ($result as $row) {
                            ?>
                            <div class="col-md-6">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><?php echo ucfirst($row['acc_name']); ?></h3>
                                    </div>
                                    <div class="box-body">
                                        <table id="example<?php echo $row['sn']; ?>" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ACC_NAME</th>
                                                    <th>BALANCE</th>
                                                    <th>#</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $result1 = select("acc_account", "name,id,balance", "type_id = '" . $row['sn'] . "' ");
                                                foreach ($result1 as $row1) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo ucfirst($row1['name']); ?></td>
                                                        <td><?php echo ucfirst($row1['balance']); ?></td>
                                                        <td align="center"><a href="acc_account_view.php?account=<?php echo $row1['id']; ?>" class="btn btn-sm btn-warning fa fa-file-text-o"></a></td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else {

                        if (isset($_GET['assets'])) { ?>
                            <div class="col-md-10">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"> New Assets</h3>
                                        <a href="acc_account.php" class="btn-box-tool pull-right" style="padding: 0; margin-right: 5px;font-size: 14px;"><i class="fa fa-times"></i></a>
                                    </div>
                                    <div class="box-body">
                                        <form action="acc_assets_save.php" method="POST">
                                            <div class="row">

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="">Assets Type</label>
                                                        <select name="acc_type" class="form-control select2 hidden-search" id="main_id" onchange="get_value('sub_type','acc_account_get.php?unit=1&val='+this.value)" style="width: 100%;">
                                                            <option value="0" disabled selected>Select Type</option>
                                                            <?php
                                                            $result = select("acc_account_type", "acc_name,sn", "group_name = 'assets' AND type = 'main' AND sn != 1 ");
                                                            foreach ($result as $row) {
                                                                echo sprintf('<option value="%s"> %s </option>', $row['sn'], $row['acc_name']);
                                                            } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label for="">Sub Type</label>
                                                        <select name="sub_type" class="form-control select2 hidden-search" id="sub_type" onchange="select_type(this.value)" style="width: 100%;">
                                                            <option value="0" disabled selected>Select Sub Type</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 veh-sel" style="display: none;">
                                                    <div class="form-group">
                                                        <label>Lorry no</label>
                                                        <input type="text" name="lorry_no" value="" class="form-control" autocomplete="off">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 veh-sel" style="display: none;">
                                                    <div class="form-group">
                                                        <label>Driver</label>
                                                        <select name="driver" class="form-control select2" style="width: 100%;">
                                                            <option value="0">None</option>
                                                            <?php
                                                            $result = select("employee", "id,username", "des_id = 1 ORDER BY des_id  ");
                                                            for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                                <option value="<?php echo $row['id'] ?>"><?php echo ucfirst($row['username']); ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-md-4 build-sel" style="display: none;">
                                                    <div class="form-group">
                                                        <label for="">Account Name</label>
                                                        <input type="text" name="name" class="form-control">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 build-sel" style="display: none;">
                                                    <div class="form-group">
                                                        <label for="">Depreciation Month</label>
                                                        <input type="number" name="dep_month" class="form-control" placeholder="0">
                                                    </div>
                                                </div>

                                                <div class="col-md-4 build-sel" style="display: none;">
                                                    <div class="form-group">
                                                        <label for="">Depreciation Amount</label>
                                                        <input type="number" step=".01" name="dep_amount" class="form-control" placeholder="0.00">
                                                    </div>
                                                </div>

                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <input type="submit" value="Save" class="btn btn-info " style="margin-top: 23px;width: 100px;">
                                                    </div>
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php }

                        if (isset($_GET['expenses'])) {
                            if ($_GET['expenses'] == 'acc') { ?>

                                <div class="col-md-8">
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <h3 class="box-title">Expenses Account</h3>
                                            <a href="acc_account.php" class="btn-box-tool pull-right" style="padding: 0; margin-right: 5px;font-size: 14px;"><i class="fa fa-times"></i></a>
                                        </div>
                                        <div class="box-body">
                                            <form action="acc_expenses_save.php" method="POST">
                                                <div class="row">

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="">Account Type</label>

                                                            <select name="acc_type" class="select2 form-control" style="width: 100%;">
                                                                <?php
                                                                $result = select("acc_account_type", "acc_name,sn", "group_name = 'expenses' AND type = 'main' ");
                                                                foreach ($result as $row) {
                                                                    echo sprintf('<option value="%s"> %s </option>', $row['sn'], ucwords(str_replace("_", " ", $row['acc_name'])));
                                                                }
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="">Account Name</label>
                                                            <input type="text" name="acc_name" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <input type="hidden" name="unit" value="2">
                                                            <input type="submit" value="Save" class="btn btn-info " style="margin-top: 23px;width: 100px;">
                                                        </div>
                                                    </div>

                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="col-md-6">
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"> New Expenses</h3>
                                            <a href="acc_account.php?expenses=acc" class="btn btn-primary btn-xs mx-2" style="border-radius: 5px;"> <i class="fa-solid fa-plus"></i> New Account</a>

                                            <a href="acc_account.php" class="btn-box-tool pull-right" style="padding: 0; margin-right: 5px;font-size: 14px;"><i class="fa fa-times"></i></a>
                                        </div>
                                        <div class="box-body">
                                            <form action="acc_expenses_save.php" method="POST">
                                                <div class="row">

                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="">Account Name</label>
                                                            <input type="text" name="type" class="form-control">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <input type="hidden" name="unit" value="1">
                                                            <input type="submit" value="Save" class="btn btn-info " style="margin-top: 23px;width: 100px;">
                                                        </div>
                                                    </div>

                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php }
                        }

                        echo sprintf('<div class="col-md-12"></div>');

                        $result = select("acc_account_type", "group_name,sn", "sn > 0  GROUP BY group_name");
                        foreach ($result as $row) {
                            ?>
                            <div class="col-md-4">
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h3 class="box-title"><?php echo ucfirst($row['group_name']); ?></h3>
                                        <a href="acc_account.php?<?php echo strtolower($row['group_name']); ?>" class="btn btn-primary btn-xs pull-right" style="border-radius: 5px;"> <i class="fa-solid fa-plus"></i> New <?php echo ucfirst($row['group_name']); ?></a>
                                    </div>
                                    <div class="box-body">
                                        <table id="example<?php echo $row['sn']; ?>" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ACC_NAME</th>
                                                    <th>#</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $result1 = select("acc_account_type", "acc_name,sn", "group_name = '" . $row['group_name'] . "' AND type = 'main' ");
                                                foreach ($result1 as $row1) {
                                                ?>
                                                    <tr>
                                                        <td><?php echo ucfirst($row1['acc_name']); ?></td>
                                                        <?php if ($row['group_name'] == 'expenses') { ?>
                                                            <td align="center"><a href="acc_account.php?acc_ex=<?php echo $row1['sn']; ?>" class="btn btn-sm btn-success fa fa-eye"></a></td>
                                                        <?php } else { ?>
                                                            <td align="center"><a href="acc_account.php?acc=<?php echo $row1['sn']; ?>" class="btn btn-sm btn-success fa fa-eye"></a></td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                    <?php }
                    } ?>
                </div>

            </section>
            <!-- /.content -->
        </div>

        <!-- /.content-wrapper -->
        <?php include("dounbr.php"); ?>


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

    <!-- Page script -->
    <script>
        function select_type(val) {
            let id = $('#main_id').val();

            if (id == 4) {
                if (val == 6) {
                    $('.build-sel').css('display', 'block');
                    $('.veh-sel').css('display', 'none');
                }
                if (val == 5) {
                    $('.veh-sel').css('display', 'block');
                    $('.build-sel').css('display', 'none');
                }
            } else {
                $('.veh-sel').css('display', 'none');
                $('.build-sel').css('display', 'none');
            }
        }

        $(function() {
            <?php
            $result = select("acc_account_type", "sn");
            foreach ($result as $row) {
                echo sprintf('$("#example%s").DataTable({
                    "paging": false,
                    "lengthChange": false,
                    "searching": false,
                    "ordering": false,
                    "info": false,
                    "autoWidth": false
                });', $row['sn']);
            } ?>

            //Initialize Select2 Elements
            $(".select2").select2();
            $('.select2.hidden-search').select2({
                minimumResultsForSearch: -1
            });

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