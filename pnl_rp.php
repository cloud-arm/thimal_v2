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
        $_SESSION['SESS_FORM'] = '82';

        include_once("sidebar.php");

        ?>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    PROFIT AND LOSS
                    <small>Report</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content">

                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-6">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Date Selector</h3>
                            </div>
                            <?php
                            include("connect.php");
                            date_default_timezone_set("Asia/Colombo");

                            if (isset($_GET['year'])) {
                                $year = $_GET['year'];
                                $month = $_GET['month'];
                            } else {
                                $year = date('Y');
                                $month = date('m');
                            }
                            $d1 = $year . '-' . $month . '-01';
                            $d2 = $year . '-' . $month . '-31';

                            ?>

                            <div class="box-body">
                                <form action="" method="GET">
                                    <div class="row" style="margin-top: 10px;">
                                        <div class="col-lg-1"></div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <select class="form-control select2 hidden-search" name="year" style="width: 100%;" tabindex="1" autofocus>
                                                    <option> <?php echo date('Y') - 1 ?> </option>
                                                    <option selected> <?php echo date('Y') ?> </option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <select class="form-control select2 hidden-search" name="month" style="width: 100%;" tabindex="1" autofocus>
                                                    <?php for ($x = 1; $x <= 12; $x++) {
                                                        $m = sprintf("%02d", $x); ?>
                                                        <option <?php if ($m == $month) { ?> selected <?php } ?>> <?php echo $m ?> </option>
                                                    <?php  } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <input class="btn btn-info" type="submit" value="Search">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- All jobs -->

                <div class="box box-info">

                    <div class="box-header">
                        <h3 class="box-title" style="text-transform: capitalize;">PROFIT AND LOSS</h3>
                    </div>

                    <div class="box-body">
                        <table id="example0" class="table table-bordered table-striped">

                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Forward Balance</th>
                                    <th>Month Balance</th>
                                    <th>Received/Paid Amount</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th>01. Gross Income</th>
                                    <td colspan="3"></td>
                                    <td> <?php ?> </td>
                                </tr>

                                <?php $pay_total = 0;
                                $pend_total = 0; ?>

                                <?php $gross_in = 0;
                                $total_income = 0;  ?>

                                <?php
                                $month_sales = 0;
                                $month_payment = 0;
                                $credit_blc = 0;
                                $credit_trans = 0;
                                $month_trans = 0;
                                $month_trans_pay = 0;

                                $rq = $db->prepare("SELECT sum(amount) FROM sales WHERE action = 1 AND date BETWEEN '$d1' AND '$d2' ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                    $month_sales = $r['sum(amount)'];
                                }

                                $rq = $db->prepare("SELECT sum(amount) FROM payment WHERE  action = 1 AND (date BETWEEN '$d1' AND '$d2') ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                    $month_payment += $r['sum(amount)'];
                                }

                                $rq = $db->prepare("SELECT sum(amount) FROM payment WHERE  chq_action = 2 AND  action = 2 AND (reserve_date BETWEEN '$d1' AND '$d2') ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                    $month_payment += $r['sum(amount)'];
                                }

                                $rq = $db->prepare("SELECT *, purchases_item.qty AS qt FROM purchases_item JOIN purchases ON purchases_item.invoice = purchases.invoice_number JOIN products ON purchases_item.product_id = products.product_id WHERE purchases_item.action = 'active' AND (products.transport1>0 OR products.transport2>0) AND purchases.pu_date BETWEEN '$d1' AND '$d2'  ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                    $trans = 0;
                                    if ($r['location_id'] == 1) {
                                        $trans = $r['transport1'];
                                    }
                                    if ($r['location_id'] == 2) {
                                        $trans = $r['transport2'];
                                    }

                                    $month_trans += $r['qt'] * $trans;
                                }
                                ?>

                                <tr>
                                    <td style="padding-left: 50px;">Sales income</td>
                                    <td> 0.00 </td>
                                    <td> <?php echo number_format($month_sales, 2);
                                            $pend_total += $month_sales; ?> </td>
                                    <td> <?php echo number_format($month_payment, 2);
                                            $pay_total += $month_payment; ?> </td>
                                    <td align="center"></td>
                                </tr>
                                <tr>
                                    <td style="padding-left: 50px;">Transport income</td>
                                    <td> <?php echo number_format($credit_trans, 2); ?> </td>
                                    <td> <?php echo number_format($month_trans, 2);
                                            $pend_total += $month_trans; ?> </td>
                                    <td> <?php echo number_format($month_trans_pay, 2);
                                            $pay_total += $month_trans_pay; ?> </td>
                                    <td align="center"></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td> <?php $gross_in = $pend_total;
                                            $total_income = $pay_total; ?> </td>
                                    <th> <?php echo number_format($pend_total, 2); ?> </th>
                                    <th> <?php echo number_format($pay_total, 2); ?> </th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                </tr>
                            </tbody>


                            <!-- ***************--------------- Labour start -----------------***************** -->
                            <tbody style="border-top:0;">
                                <tr>
                                    <th>04. Labour</th>
                                    <td colspan="3"></td>
                                    <td> <?php ?> </td>
                                </tr>
                                <?php $pay_total = 0;
                                $pend_total = 0; ?>

                                <?php $gross_pay = 0;
                                $total_pay = 0; ?>

                                <?php
                                $tot_off = 0;
                                $adv_off = 0;
                                $pay_off = 0;
                                $pay_com = 0;
                                $rq = $db->prepare("SELECT SUM(hr_payroll.day_pay),SUM(hr_payroll.ot) FROM `hr_payroll` JOIN employee ON hr_payroll.emp_id = employee.id WHERE employee.type < 4 AND hr_payroll.date = '$m'  ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                    $tot_off += $r['SUM(hr_payroll.day_pay)'];
                                    $tot_off += $r['SUM(hr_payroll.ot)'];
                                }

                                $rq = $db->prepare("SELECT SUM(hr_payroll.payment),SUM(hr_payroll.commis) FROM `hr_payroll` JOIN employee ON hr_payroll.emp_id = employee.id WHERE employee.type < 4 AND  (hr_payroll.pay_date BETWEEN '$d1' AND '$d2') ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                    $pay_off += $r['SUM(hr_payroll.payment)'];
                                    $pay_off -= $r['SUM(hr_payroll.commis)'];
                                }

                                $rq = $db->prepare("SELECT SUM(salary_advance.amount) FROM `salary_advance` JOIN employee ON salary_advance.emp_id = employee.id WHERE employee.type < 4 AND salary_advance.action = 0 AND salary_advance.date BETWEEN '$d1' AND '$d2' ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                    $adv_off = $r['SUM(salary_advance.amount)'];
                                }

                                $pay_off += $adv_off;

                                $tot_work = 0;
                                $pay_work = 0;
                                $adv_work = 0;
                                $rq = $db->prepare("SELECT SUM(hr_payroll.day_pay),SUM(hr_payroll.ot) FROM `hr_payroll` JOIN employee ON hr_payroll.emp_id = employee.id WHERE employee.type = 4 AND hr_payroll.date = '$m'  ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                    $tot_work += $r['SUM(hr_payroll.day_pay)'];
                                    $tot_work += $r['SUM(hr_payroll.ot)'];
                                }

                                $rq = $db->prepare("SELECT SUM(hr_payroll.payment),SUM(hr_payroll.commis) FROM `hr_payroll` JOIN employee ON hr_payroll.emp_id = employee.id WHERE employee.type = 4 AND  (hr_payroll.pay_date BETWEEN '$d1' AND '$d2') ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                    $pay_work += $r['SUM(hr_payroll.payment)'];
                                    $pay_work -= $r['SUM(hr_payroll.commis)'];
                                }


                                $rq = $db->prepare("SELECT SUM(salary_advance.amount) FROM `salary_advance` JOIN employee ON salary_advance.emp_id = employee.id WHERE employee.type = 4 AND salary_advance.action = 0 AND salary_advance.date BETWEEN '$d1' AND '$d2' ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                    $adv_work = $r['SUM(salary_advance.amount)'];
                                }

                                $pay_work += $adv_work;



                                $tot_comm = 0;
                                $pay_comm = 0;

                                ?>

                                <tr>
                                    <td style="padding-left: 50px;">Salary</td>
                                    <td>0.00</td>
                                    <td> <?php echo number_format($tot_off, 2);
                                            $pend_total += $tot_off; ?> </td>
                                    <td> <?php echo number_format($pay_off, 2);
                                            $pay_total += $pay_off; ?> </td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td style="padding-left: 50px;">Commission</td>
                                    <td> 0.00 </td>
                                    <td> <?php echo number_format($tot_comm, 2);
                                            $pend_total += $tot_comm; ?> </td>
                                    <td> <?php echo number_format($pay_comm, 2);
                                            $pay_total += $pay_comm; ?> </td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td> <?php $gross_pay += $pend_total;
                                            $total_pay += $pay_total; ?> </td>
                                    <th> <?php echo number_format($pend_total, 2); ?> </th>
                                    <th> <?php echo number_format($pay_total, 2); ?> </th>
                                    <td></td>

                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                </tr>
                            </tbody>

                            <tbody style="border-top:0;">

                                <tr>
                                    <th>05. Expenses</th>
                                    <td colspan="3"></td>
                                    <td><?php ?></td>
                                </tr>

                                <?php $pay_total = 0;
                                $pend_total = 0; ?>

                                <?php
                                $rq = $db->prepare("SELECT *, sum(expenses_records.util_bill_amount),sum(expenses_records.amount) FROM utility_bill JOIN expenses_records ON utility_bill.id = expenses_records.util_id WHERE expenses_records.acc_id = 1  AND (expenses_records.date BETWEEN '$d1' AND '$d2') GROUP BY expenses_records.util_id ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                ?>
                                    <tr>
                                        <td style="padding-left: 50px;"><?php echo $r['name']; ?></td>
                                        <td> <?php echo $r['credit']; ?> </td>
                                        <td>
                                            <?php echo $r['sum(expenses_records.util_bill_amount)'];
                                            $pend_total += $r['sum(expenses_records.util_bill_amount)']; ?> </td>
                                        <td>
                                            <?php echo $r['sum(expenses_records.amount)'];
                                            $pay_total += $r['sum(expenses_records.amount)']; ?> </td>
                                        <td></td>
                                    </tr>
                                <?php } ?>

                                <?php
                                $rq = $db->prepare("SELECT *,sum(amount),sum(pay_amount) FROM expenses_records WHERE util_id = 0 AND type_id > 1 AND paycose = 'expenses' AND expenses_records.date BETWEEN '$d1' AND '$d2' GROUP BY expenses_records.type_id  ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                ?>
                                    <tr>
                                        <td style="padding-left: 50px;"><?php echo $r['type']; ?></td>
                                        <td> 0.00 </td>
                                        <td> <?php echo $r['sum(amount)'];
                                                $pend_total += $r['sum(amount)']; ?> </td>
                                        <td> <?php echo $r['sum(pay_amount)'];
                                                $pay_total += $r['sum(pay_amount)']; ?> </td>
                                        <td></td>
                                    </tr>
                                <?php } ?>

                                <tr>
                                    <td></td>
                                    <td> <?php ?> </td>
                                    <th> <?php echo number_format($pend_total, 2); ?> </th>
                                    <th> <?php echo number_format($pay_total, 2); ?> </th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                </tr>
                            </tbody>
                            <?php $gross_pay += $pend_total;
                            $total_pay += $pay_total; ?>

                            <tbody style="border-top:0;">

                                <tr>
                                    <th>07. Loan & Bank charges</th>
                                    <td colspan="3"></td>
                                    <td><?php ?></td>
                                </tr>

                                <?php $pay_total = 0;
                                $pend_total = 0; ?>

                                <?php
                                $rq = $db->prepare("SELECT *,bank_loan.balance AS blc, payment.amount AS am FROM payment JOIN bank_loan_record ON bank_loan_record.invoice_no = payment.invoice_no JOIN bank_loan ON bank_loan.id = bank_loan_record.loan_id WHERE (payment.reserve_date BETWEEN '$d1' AND '$d2') ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                ?>
                                    <tr>
                                        <td style="padding-left: 50px;"><?php echo $r['bank_name']; ?></td>
                                        <td> <?php echo $r['blc']; ?> </td>
                                        <td> <?php echo $r['term_amount'];
                                                $pend_total += $r['term_amount']; ?> </td>
                                        <td> <?php echo $r['am'];
                                                $pay_total += $r['am']; ?> </td>
                                        <td></td>
                                    </tr>
                                <?php } ?>
                                <?php
                                $rq = $db->prepare("SELECT *,bank_loan.balance AS blc, bank_record.amount AS am FROM bank_record JOIN bank_loan_record ON bank_loan_record.bank_acc_id = bank_record.debit_acc_id JOIN bank_loan ON bank_loan.id = bank_loan_record.loan_id WHERE (bank_loan_record.date BETWEEN '$d1' AND '$d2') ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                ?>
                                    <tr>
                                        <td style="padding-left: 50px;"><?php echo $r['bank_name']; ?></td>
                                        <td> <?php echo $r['blc']; ?> </td>
                                        <td> <?php echo $r['term_amount'];
                                                $pend_total += $r['term_amount']; ?> </td>
                                        <td> <?php echo $r['am'];
                                                $pay_total += $r['am']; ?> </td>
                                        <td></td>
                                    </tr>
                                <?php } ?>

                                <?php
                                $rq = $db->prepare("SELECT sum(amount) FROM bank_record  WHERE transaction_type = 'bank_charges' AND date BETWEEN '$d1' AND '$d2' ");
                                $rq->bindParam(':userid', $date);
                                $rq->execute();
                                for ($i = 0; $r = $rq->fetch(); $i++) {
                                    $bank_char = $r['sum(amount)'];
                                }
                                ?>
                                <tr>
                                    <td style="padding-left: 50px;">Bank charges</td>
                                    <td> 0.00 </td>
                                    <td> <?php echo number_format($bank_char, 2);
                                            $pend_total += $bank_char; ?> </td>
                                    <td> <?php echo number_format($bank_char, 2);
                                            $pay_total += $bank_char; ?> </td>
                                    <td></td>
                                </tr>

                                <tr>
                                    <td></td>
                                    <td> <?php ?> </td>
                                    <th> <?php echo number_format($pend_total, 2); ?> </th>
                                    <th> <?php echo number_format($pay_total, 2); ?> </th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td colspan="5"></td>
                                </tr>
                            </tbody>
                            <?php $gross_pay += $pend_total;
                            $total_pay += $pay_total; ?>

                            <tbody style="border-top:0;">
                                <tr>
                                    <td class="bd-none"></td>
                                    <th class="bd-none">Gross Income</th>
                                    <td style="background: var(--cl1);border: 0;"> <?php echo number_format($gross_in, 2); ?> </td>
                                    <th style="background: var(--cl2);border: 0;">Total Income</th>
                                    <td class="bd-none"> <?php echo number_format($total_income, 2); ?> </td>
                                </tr>
                                <tr>
                                    <td class="bd-none"></td>
                                    <th class="bd-none">Gross Payment</th>
                                    <td class="bd-none"> <?php echo number_format($gross_pay, 2); ?> </td>
                                    <th class="bd-none">Total Payment</th>
                                    <td class="bd-none"> <?php echo number_format($total_pay, 2); ?> </td>
                                </tr>

                                <tr>
                                    <td class="bd-none"></td>
                                    <td class="bd-none"></td>
                                    <th class="bd-none"> <?php echo number_format($gross_in - $gross_pay, 2); ?> </th>
                                    <td class="bd-none"></td>
                                    <th class="bd-none"> <?php echo number_format($total_income - $total_pay, 2); ?> </th>
                                </tr>
                            </tbody>

                        </table>
                    </div>

                </div>
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
        function dll_row(id) {
            if (confirm("Sure you want to delete this invoice? There is NO undo!")) {
                $('#dll_' + id).submit();
            }
            return false;
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