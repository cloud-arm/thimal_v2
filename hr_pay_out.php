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
        $_SESSION['SESS_FORM'] = '';

        include_once("sidebar.php");

        ?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Salary Payment
                    <small>Preview</small>
                </h1>

            </section>

            <!-- add item -->
            <section class="content">

                <div class="row">
                    <div class="col-md-6">

                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Salary Payment</h3>
                            </div>

                            <div class="box-body d-block">

                                <form method="POST" action="hr_pay_out_save.php" class="w-100">

                                    <div class="row">
                                        <div class="col-md-12" id="details" style="display: none;">
                                            <h4 style="display: flex;justify-content: space-around; margin-bottom: 10px;">
                                                <span><small>Amount : </small> <span id="sp_amount"></span></span>
                                                <span><small>Payment : </small> <span id="sp_payment"></span></span>
                                                <span><small>Balance : </small> <span id="sp_balance"></span></span>
                                            </h4>
                                        </div>

                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <label>Employee</label>
                                                <select class="form-control select2" name="id" id="emp" onchange="select_emp(this.options[this.selectedIndex].getAttribute('amount'),this.options[this.selectedIndex].getAttribute('payment'),this.options[this.selectedIndex].getAttribute('balance'))" style="width: 100%;" tabindex="1" autofocus>
                                                    <option value="0"></option>
                                                    <?php
                                                    $date = $_GET['date'];
                                                    $result = $db->prepare("SELECT * FROM hr_payroll WHERE amount > payment AND date = '$date'");
                                                    $result->bindParam(':userid', $res);
                                                    $result->execute();
                                                    for ($i = 0; $row = $result->fetch(); $i++) { ?>
                                                        <option value="<?php echo $row['id']; ?>" amount="<?php echo $row['amount']; ?>" payment="<?php echo $row['payment']; ?>" day-rate="<?php echo $row['day_rate']; ?>" ot-rate="<?php echo $row['ot_rate']; ?>" balance="<?php echo $row['amount'] - $row['payment']; ?>">
                                                            <?php echo $row['emp_id'] . ' --> '  . $row['name'];   ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-2"></div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Payment</label>
                                                <input type="number" step=".01" name="payment" id="salary_txt" class="form-control" required autocomplete="off" onkeyup="salary_check()">
                                            </div>
                                        </div>

                                        <div class="col-md-2" style="height: 75px;display: flex; align-items: end;padding-left:0;">
                                            <div class="form-group">
                                                <input type="hidden" value="salary" name="type">
                                                <input type="submit" value="Save" class="btn btn-success" id="salary" disabled>
                                            </div>
                                        </div>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <?php
                        $date = $_GET['date'];
                        $etf = 0;
                        $epf = 0;
                        $result = $db->prepare("SELECT * FROM hr_etf_record  WHERE type = 'etf' ORDER BY id ASC ");
                        $result->bindParam(':userid', $res);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                            $etf = $row['froward_balance'];
                        }
                        $result = $db->prepare("SELECT * FROM hr_etf_record  WHERE type = 'epf' ORDER BY id ASC ");
                        $result->bindParam(':userid', $res);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) {
                            $epf = $row['froward_balance'];
                        } ?>
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">ETF & EPF Payment</h3>
                                <span class="pull-right" style="margin: 0 10px 0 20px;">EPF: <?php echo $epf; ?></span>
                                <span class="pull-right">ETF: <?php echo $etf; ?></span>
                            </div>

                            <div class="box-body d-block">

                                <form method="POST" action="hr_pay_out_save.php" class="w-100">

                                    <div class="row">

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Type</label>
                                                <select class="form-control" name="hr_type" id="hr_type" onchange="hr_type_select()" style="width: 100%;" tabindex="1" autofocus>
                                                    <option> </option>
                                                    <option value="etf"> ETF </option>
                                                    <option value="epf"> EPF </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label>Pay Type</label>
                                                <select class="form-control" name="pay_type" id="method" onchange="pay_type_select()" style="width: 100%;" tabindex="1" autofocus>
                                                    <option> Cash </option>
                                                    <option> Chq </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>Month</label>
                                                <select class="form-control" name="month" id="hr_month" style="width: 100%;" onchange="hr_month_select()" tabindex="1" autofocus></select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 slt-chq" style="display: none;">
                                            <div class="form-group">
                                                <label>Account</label>
                                                <select class="form-control select2 hidden-search" name="bank" style="width: 100%;" tabindex="4">

                                                    <?php
                                                    $result = $db->prepare("SELECT * FROM bank_balance ");
                                                    $result->bindParam(':id', $ttr);
                                                    $result->execute();
                                                    for ($i = 0; $row = $result->fetch(); $i++) {
                                                    ?>
                                                        <option value="<?php echo $id = $row['id']; ?>" <?php if ($id == 1) { ?> selected <?php } ?>> <?php echo $row['name'] . '__' . $row['ac_no']; ?> </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-6 slt-chq" style="display:none;">
                                            <div class="form-group">
                                                <label>Chq Number</label>
                                                <input class="form-control" type="text" name="chq_no" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="col-md-6 slt-chq" style="display:none;">
                                            <div class="form-group">
                                                <label>Chq Date</label>
                                                <input class="form-control" id="datepicker" type="text" name="chq_date" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="form-group" style="margin-top: 20px;">
                                                <h4>This month Balance: <span id="etf_blc"></span></h4>
                                            </div>
                                        </div>

                                        <div class="col-md-2" style="margin-top: 10px;">
                                            <div class="form-group">
                                                <input type="hidden" value="etf" name="type">
                                                <input type="submit" value="Pay" class="btn btn-success">
                                            </div>
                                        </div>
                                    </div>

                                </form>

                            </div>
                        </div>
                    </div>

                </div>
                <div class="row">

                    <div class="col-md-6">

                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Payroll Salary Summary</h3>
                            </div>
                            <div class="box-body d-block">
                                <table id="example2" class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Payment</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $date = $_GET['date'];
                                        $result = $db->prepare("SELECT * FROM hr_payroll WHERE date = '$date' ");
                                        $result->bindParam(':id', $date);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {  ?>

                                            <tr class="record">
                                                <td><?php echo $row['id'];   ?> </td>
                                                <td><?php echo $row['emp_id'] . '.  ' . $row['name'];   ?></td>
                                                <td><?php echo $row['payment'];   ?></td>
                                                <td><?php echo $row['amount'];   ?></td>
                                            </tr>

                                        <?php }   ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Payroll ETF & EPF Summary</h3>
                            </div>
                            <div class="box-body d-block">
                                <table id="example2" class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Type</th>
                                            <th>Month</th>
                                            <th>Acc Name</th>
                                            <th>Amount</th>
                                            <th>Balance</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php
                                        $result = $db->prepare("SELECT * FROM hr_etf_record ");
                                        $result->bindParam(':id', $date);
                                        $result->execute();
                                        for ($i = 0; $row = $result->fetch(); $i++) {  ?>

                                            <tr class="record">
                                                <td><?php echo $row['id'];   ?> </td>
                                                <td><?php echo $row['type'];   ?></td>
                                                <td><?php echo $row['month'];   ?></td>
                                                <td><?php echo $row['acc_name'];   ?></td>
                                                <td><?php echo $row['amount'];   ?></td>
                                                <td><?php echo $row['froward_balance'];   ?></td>
                                            </tr>

                                        <?php }   ?>
                                    </tbody>

                                </table>
                            </div>
                        </div>

                    </div>
                </div>

            </section>
            <!-- /.content -->
        </div>

        <!-- /.content-wrapper -->
        <?php include("dounbr.php"); ?>

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


    <script type="text/javascript">
        var value = 0;

        function pay_type_select() {
            var val = $('#method').val();

            if (val == "Chq") {
                $('.slt-chq').css("display", "block");
            } else {
                $('.slt-chq').css("display", "none");
            }
        }

        function hr_month_select() {
            let val = $('#hr_type').val();
            let mon = $('#hr_month').val();
            var info = 'type=' + val + '&month=' + mon + '&unit=2';
            $.ajax({
                type: "GET",
                url: "hr_pay_out_get.php",
                data: info,
                success: function(res) {
                    $('#etf_blc').text(res);
                }
            });
        }

        function hr_type_select() {
            let val = $('#hr_type').val();
            var info = 'type=' + val + '&unit=1';
            $.ajax({
                type: "GET",
                url: "hr_pay_out_get.php",
                data: info,
                success: function(res) {
                    $('#hr_month').empty();
                    $('#hr_month').append(res);
                }
            });
            $('#etf_blc').text('');
        }

        function etf_check() {
            let val = $('#etf_txt').val();
            if (val > 0) {
                $('#etf').removeAttr('disabled');
            } else {
                $('#etf').attr('disabled', '');
            }
        }

        function salary_check() {
            let val = $('#salary_txt').val();
            if (parseInt(value) >= val & val >= 0) {
                $('#salary').removeAttr('disabled');
            } else {
                $('#salary').attr('disabled', '');
            }
        }

        function select_emp(amount, payment, balance) {
            value = amount;
            let val = $('#emp').val();

            $('#sp_amount').text(amount);
            $('#sp_payment').text(payment);
            $('#sp_balance').text(balance);

            if (val != '0') {
                $('#details').css('display', 'block');
                $('#salary').removeAttr('disabled');
            } else {
                $('#details').css('display', 'none');
                $('#salary').attr('disabled', '');
            }
        }

        $(function() {
            $("#example1").DataTable();
            $('#example2').DataTable({
                "paging": true,
                "lengthChange": true,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": true
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


            //Date picker
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