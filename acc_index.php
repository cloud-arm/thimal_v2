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
        $_SESSION['SESS_DEPARTMENT'] = 'accounting';
        $_SESSION['SESS_FORM'] = '19';

        include_once("sidebar.php");

        ?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Home
                    <small>Preview</small>
                </h1>

            </section>

            <!-- Main content -->
            <section class="content">



                <?php
                include('connect.php');
                date_default_timezone_set("Asia/Colombo");

                $date = date("Y-m-d");

                ?>

                <div class="row">
                    <div class="col-md-3">
                        <!-- Info Boxes Style 2 -->
                        <div class="info-box bg-yellow">
                            <span class="info-box-icon"><i class="fa-solid fa-dollar"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Payment</span>
                                <?php $date = date('Y-m-d');
                                $result = $db->prepare("SELECT SUM(amount) FROM `payment` WHERE `date` = '$date' ");
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $payment = $row['SUM(amount)'];
                                }
                                if ($payment == null) {
                                    $payment = 0;
                                } ?>
                                <span class="info-box-number"><?php echo $payment; ?></span>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 0%"></div>
                                </div>
                                <span class="progress-description">
                                    Today All Payment
                                </span>
                            </div>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <div class="col-md-3">
                        <!-- /.info-box -->
                        <div class="info-box bg-green">
                            <span class="info-box-icon"><i class="fa-solid fa-money-check-dollar"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Credit CHQ</span>
                                <?php $credit = 0;
                                $result = $db->prepare("SELECT *, payment.amount AS pay FROM payment JOIN bank_balance ON payment.bank_id = bank_balance.id WHERE payment.chq_action = 1 AND payment.paycose = 'invoice_payment' AND payment.pay_type='chq' ORDER BY payment.chq_date ASC ");
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $credit = $row['pay'];
                                } ?>
                                <span class="info-box-number"><?php echo $credit; ?></span>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 0%"></div>
                                </div>
                                <span class="progress-description">
                                    Un Realize Deposit Chq
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <!-- /.info-box -->
                        <div class="info-box bg-blue">
                            <span class="info-box-icon"><i class="fa-solid fa-money-check"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">Debit CHQ</span>
                                <?php $debit = 0;
                                $result = $db->prepare("SELECT *, payment.amount AS pay FROM payment JOIN bank_balance ON payment.bank_id = bank_balance.id WHERE  payment.chq_action = 1 AND payment.paycose = 'expenses_issue' AND payment.pay_type='chq'  ORDER BY payment.chq_date ASC ");
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $date1 = date_create(date('Y-m-d'));
                                    $date2 = date_create($row['chq_date']);
                                    $date_diff = date_diff($date1, $date2);
                                    $date_diff = $date_diff->format("%R%a");
                                    if ($date_diff <= 0) {
                                        $debit += $row['pay'];
                                    }
                                }
                                $result = $db->prepare("SELECT *,supply_payment.amount AS pay FROM supply_payment JOIN bank_balance ON supply_payment.bank_id = bank_balance.id WHERE  supply_payment.action = 1 AND supply_payment.pay_type='Chq'  ORDER BY supply_payment.chq_date ASC ");
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $date1 = date_create(date('Y-m-d'));
                                    $date2 = date_create($row['chq_date']);
                                    $date_diff = date_diff($date1, $date2);
                                    $date_diff = $date_diff->format("%R%a");
                                    if ($date_diff <= 0) {
                                        $debit += $row['pay'];
                                    }
                                } ?>
                                <span class="info-box-number"><?php echo $debit; ?></span>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 0%"></div>
                                </div>
                                <span class="progress-description">
                                    Un Realize Issue Chq
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>
                    <div class="col-md-3">
                        <!-- /.info-box -->
                        <div class="info-box bg-red">
                            <span class="info-box-icon"><i class="fa-solid fa-money-check"></i></span>

                            <div class="info-box-content">
                                <span class="info-box-text">May Be return chq</span>
                                <?php $return = 0;
                                $result = $db->prepare("SELECT SUM(amount) FROM `bank_balance` ");
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $bank = $row['SUM(amount)'];
                                }
                                $return = $debit - ($credit + $bank); ?>
                                <span class="info-box-number">
                                    <?php if ($return > 0) {
                                        echo $return;
                                    } else {
                                        echo 0;
                                    }; ?>
                                </span>

                                <div class="progress">
                                    <div class="progress-bar" style="width: 0%"></div>
                                </div>
                                <span class="progress-description">
                                    May Be Return Chq
                                </span>
                            </div>
                            <!-- /.info-box-content -->
                        </div>
                    </div>
                    <!-- /.info-box -->

                </div> <!-- /.box -->

                <div class="row">
                    <div class="col-md-6">
                        <!-- LINE CHART -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Credit Collection</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="chart">
                                    <canvas id="lineChart1" style="height: 250px"></canvas>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <!-- LINE CHART -->
                        <div class="box box-info">
                            <div class="box-header with-border">
                                <h3 class="box-title">Credit Payment</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse">
                                        <i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="chart">
                                    <canvas id="lineChart2" style="height: 250px"></canvas>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <!-- BAR CHART -->
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Issue Chq Summary</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="chart">
                                    <canvas id="lineChart3" style="height: 250px"></canvas>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                    <div class="col-md-6">
                        <!-- BAR CHART -->
                        <div class="box box-success">
                            <div class="box-header with-border">
                                <h3 class="box-title">Deposit Chq Summary</h3>

                                <div class="box-tools pull-right">
                                    <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool" data-widget="remove"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="box-body">
                                <div class="chart">
                                    <canvas id="lineChart4" style="height: 250px"></canvas>
                                </div>
                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!-- /.box -->
                    </div>
                </div>

            </section>
        </div>
        <!-- /.content -->

        <!-- /.content-wrapper -->
        <?php
        include("dounbr.php");
        ?>
        <!-- /.control-sidebar -->
        <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
        <div class="control-sidebar-bg"></div>
    </div>

    <?php include_once("script.php"); ?>

    <!-- ChartJS -->
    <script src="../../plugins/chartjs/Chart.min.js"></script>
    <!-- FastClick -->
    <script src="../../plugins/fastclick/fastclick.js"></script>

    <script>
        <?php
        function getPayment($month, $para)
        {
            include('connect.php');
            date_default_timezone_set("Asia/Colombo");

            $d1 = date('Y-') . $month . '-01';
            $d2 = date('Y-') . $month . '-31';

            $value = 0;
            if ($para == 'credit') {
                $result = $db->prepare("SELECT SUM(amount) FROM `payment` WHERE `pay_type` = 'credit' AND `paycose` = 'invoice_payment' AND `date` BETWEEN '$d1' AND '$d2' ");
            } else 
            if ($para == 'credit_pay') {
                $result = $db->prepare("SELECT SUM(pay_amount) FROM `payment` WHERE `pay_type` = 'credit' AND `paycose` = 'invoice_payment' AND `date` BETWEEN '$d1' AND '$d2' ");
            } else 
            if ($para == 'payment') {
                $result = $db->prepare("SELECT SUM(amount) FROM `payment` WHERE `pay_type` = 'credit_payment' AND `paycose` = 'credit' AND `date` BETWEEN '$d1' AND '$d2' ");
            }
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                if ($para == 'credit_pay') {
                    $value = $row['SUM(pay_amount)'];
                } else {
                    $value = $row['SUM(amount)'];
                }
            }

            if ($value == null) {
                $value = 0;
            }

            return $value;
        }
        function getIssue($month, $para)
        {
            include('connect.php');
            date_default_timezone_set("Asia/Colombo");

            $d1 = date('Y-') . $month . '-01';
            $d2 = date('Y-') . $month . '-31';

            if ($para == 1) {
                $action1 = ' ';
                $action2 = ' ';
            } else {
                $action1 = 'payment.chq_action = ' . $para . ' AND';
                $action2 = 'supply_payment.action = ' . $para . ' AND';
            }

            $value = 0;
            $result = $db->prepare("SELECT payment.amount AS pay FROM payment JOIN bank_balance ON payment.bank_id = bank_balance.id WHERE $action1 payment.paycose = 'expenses_issue' AND payment.pay_type='chq'  AND `date` BETWEEN '$d1' AND '$d2' ORDER BY payment.chq_date ASC ");
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $value += $row['pay'];
            }
            $result = $db->prepare("SELECT supply_payment.amount AS pay FROM supply_payment JOIN bank_balance ON supply_payment.bank_id = bank_balance.id WHERE $action2 supply_payment.pay_type='Chq'  AND `date` BETWEEN '$d1' AND '$d2' ORDER BY supply_payment.chq_date ASC ");
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $value += $row['pay'];
            }

            return $value;
        }
        function getDeposit($month, $para)
        {
            include('connect.php');
            date_default_timezone_set("Asia/Colombo");

            $d1 = date('Y-') . $month . '-01';
            $d2 = date('Y-') . $month . '-31';

            if ($para == 1) {
                $action = 'payment.chq_action > 0 AND';
            } else {
                $action = 'payment.chq_action = ' . $para . ' AND';
            }

            $value = 0;
            $result = $db->prepare("SELECT *, payment.amount AS pay FROM payment JOIN bank_balance ON payment.bank_id = bank_balance.id WHERE $action payment.paycose = 'invoice_payment' AND payment.pay_type='chq' AND date BETWEEN '$d1' AND '$d2' ORDER BY payment.chq_date ASC ");
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
                $value += $row['pay'];
            }

            return $value;
        }
        ?>

        $(function() {
            var lineChartData1 = {
                labels: [
                    <?php
                    $x = 11;
                    while ($x >= 0) {
                        $d = strtotime("-$x Month");
                        $date = date("Y-m-d", $d);
                        $split = explode("-", $date);
                        $y = $split[0];
                        $m = $split[1];
                        $d = $split[2];
                        $date = mktime(0, 0, 0, $m, $d, $y);
                        $date = date('Y M', $date); ?> '<?php echo $date ?>',
                    <?php $x--;
                    } ?>
                ],
                datasets: [{
                        label: "Credit",
                        fillColor: "rgba(204, 0, 0, 1)",
                        strokeColor: "rgba(204, 0, 0, 1)",
                        pointColor: "rgba(204, 0, 0, 1)",
                        pointStrokeColor: "rgba(204, 0, 0, 1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(204, 0, 0, 1)",
                        data: [
                            <?php
                            $x = 11;
                            while ($x >= 0) {
                                $d = strtotime("-$x Month");
                                $date = date("Y-m-d", $d);
                                $split = explode("-", $date);
                                $y = $split[0];
                                $m = $split[1];
                                $d = $split[2];
                                $date = mktime(0, 0, 0, $m, $d, $y);
                                $date = date('m', $date); ?>
                                <?php echo getPayment($date, 'credit') ?>,
                            <?php $x--;
                            } ?>
                        ],
                    },
                    {
                        label: "Collection",
                        fillColor: "rgba(255,153,0,1)",
                        strokeColor: "rgba(255,153,0,1)",
                        pointColor: "rgba(255,153,0,1)",
                        pointStrokeColor: "rgba(255,153,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(255,153,0,1)",
                        data: [
                            <?php
                            $x = 11;
                            while ($x >= 0) {
                                $d = strtotime("-$x Month");
                                $date = date("Y-m-d", $d);
                                $split = explode("-", $date);
                                $y = $split[0];
                                $m = $split[1];
                                $d = $split[2];
                                $date = mktime(0, 0, 0, $m, $d, $y);
                                $date = date('m', $date); ?>
                                <?php echo getPayment($date, 'credit_pay') ?>,
                            <?php $x--;
                            } ?>
                        ],
                    },
                ],
            };

            var lineChartData2 = {
                labels: [
                    <?php
                    $x = 11;
                    while ($x >= 0) {
                        $d = strtotime("-$x Month");
                        $date = date("Y-m-d", $d);
                        $split = explode("-", $date);
                        $y = $split[0];
                        $m = $split[1];
                        $d = $split[2];
                        $date = mktime(0, 0, 0, $m, $d, $y);
                        $date = date('Y M', $date); ?> '<?php echo $date ?>',
                    <?php $x--;
                    } ?>
                ],
                datasets: [{
                        label: "Credit",
                        fillColor: "rgba(204, 0, 0, 1)",
                        strokeColor: "rgba(204, 0, 0, 1)",
                        pointColor: "rgba(204, 0, 0, 1)",
                        pointStrokeColor: "rgba(204, 0, 0, 1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(204, 0, 0, 1)",
                        data: [
                            <?php
                            $x = 11;
                            while ($x >= 0) {
                                $d = strtotime("-$x Month");
                                $date = date("Y-m-d", $d);
                                $split = explode("-", $date);
                                $y = $split[0];
                                $m = $split[1];
                                $d = $split[2];
                                $date = mktime(0, 0, 0, $m, $d, $y);
                                $date = date('m', $date); ?>
                                <?php echo getPayment($date, 'credit') ?>,
                            <?php $x--;
                            } ?>
                        ],
                    },
                    {
                        label: "Payment",
                        fillColor: "rgba(0,102,255,1)",
                        strokeColor: "rgba(0,102,255,1)",
                        pointColor: "rgba(0,102,255,1)",
                        pointStrokeColor: "rgba(0,102,255,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(0,102,255,1)",
                        data: [
                            <?php
                            $x = 11;
                            while ($x >= 0) {
                                $d = strtotime("-$x Month");
                                $date = date("Y-m-d", $d);
                                $split = explode("-", $date);
                                $y = $split[0];
                                $m = $split[1];
                                $d = $split[2];
                                $date = mktime(0, 0, 0, $m, $d, $y);
                                $date = date('m', $date); ?>
                                <?php echo getPayment($date, 'payment') ?>,
                            <?php $x--;
                            } ?>
                        ],
                    },
                ],
            };

            var lineChartData3 = {
                labels: [
                    <?php
                    $x = -3;
                    while ($x <= 6) {
                        $d = strtotime("$x Month");
                        $date = date("Y-m-d", $d);
                        $split = explode("-", $date);
                        $y = $split[0];
                        $m = $split[1];
                        $d = $split[2];
                        $date = mktime(0, 0, 0, $m, $d, $y);
                        $date = date('Y M', $date); ?> '<?php echo $date ?>',
                    <?php $x++;
                    } ?>
                ],
                datasets: [{
                        label: "Return",
                        fillColor: "rgba(204, 0, 0, 1)",
                        strokeColor: "rgba(204, 0, 0, 1)",
                        pointColor: "rgba(204, 0, 0, 1)",
                        pointStrokeColor: "rgba(204, 0, 0, 1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(204, 0, 0, 1)",
                        data: [
                            <?php
                            $x = -3;
                            while ($x <= 6) {
                                $d = strtotime("$x Month");
                                $date = date("Y-m-d", $d);
                                $split = explode("-", $date);
                                $y = $split[0];
                                $m = $split[1];
                                $d = $split[2];
                                $date = mktime(0, 0, 0, $m, $d, $y);
                                $date = date('m', $date); ?>
                                <?php echo getIssue($date, 3) ?>,
                            <?php $x++;
                            } ?>
                        ],
                    },
                    {
                        label: "Issue",
                        fillColor: "rgba(255,153,0,1)",
                        strokeColor: "rgba(255,153,0,1)",
                        pointColor: "rgba(255,153,0,1)",
                        pointStrokeColor: "rgba(255,153,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(255,153,0,1)",
                        data: [
                            <?php
                            $x = -3;
                            while ($x <= 6) {
                                $d = strtotime("$x Month");
                                $date = date("Y-m-d", $d);
                                $split = explode("-", $date);
                                $y = $split[0];
                                $m = $split[1];
                                $d = $split[2];
                                $date = mktime(0, 0, 0, $m, $d, $y);
                                $date = date('m', $date); ?>
                                <?php echo getIssue($date, 1) ?>,
                            <?php $x++;
                            } ?>
                        ],
                    },
                    {
                        label: "Realize",
                        fillColor: "rgba(0,166,90,1)",
                        strokeColor: "rgba(0,166,90,1)",
                        pointColor: "rgba(0,166,90,1)",
                        pointStrokeColor: "rgba(0,166,90,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(0,166,90,1)",
                        data: [
                            <?php
                            $x = -3;
                            while ($x <= 6) {
                                $d = strtotime("$x Month");
                                $date = date("Y-m-d", $d);
                                $split = explode("-", $date);
                                $y = $split[0];
                                $m = $split[1];
                                $d = $split[2];
                                $date = mktime(0, 0, 0, $m, $d, $y);
                                $date = date('m', $date); ?>
                                <?php echo getIssue($date, 2) ?>,
                            <?php $x++;
                            } ?>
                        ],
                    },
                ],
            };

            var lineChartData4 = {
                labels: [
                    <?php
                    $x = -3;
                    while ($x <= 6) {
                        $d = strtotime("$x Month");
                        $date = date("Y-m-d", $d);
                        $split = explode("-", $date);
                        $y = $split[0];
                        $m = $split[1];
                        $d = $split[2];
                        $date = mktime(0, 0, 0, $m, $d, $y);
                        $date = date('Y M', $date); ?> '<?php echo $date ?>',
                    <?php $x++;
                    } ?>
                ],
                datasets: [{
                        label: "Return",
                        fillColor: "rgba(204, 0, 0, 1)",
                        strokeColor: "rgba(204, 0, 0, 1)",
                        pointColor: "rgba(204, 0, 0, 1)",
                        pointStrokeColor: "rgba(204, 0, 0, 1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(204, 0, 0, 1)",
                        data: [
                            <?php
                            $x = -3;
                            while ($x <= 6) {
                                $d = strtotime("$x Month");
                                $date = date("Y-m-d", $d);
                                $split = explode("-", $date);
                                $y = $split[0];
                                $m = $split[1];
                                $d = $split[2];
                                $date = mktime(0, 0, 0, $m, $d, $y);
                                $date = date('m', $date); ?>
                                <?php echo getDeposit($date, 3) ?>,
                            <?php $x++;
                            } ?>
                        ],
                    },
                    {
                        label: "Deposit",
                        fillColor: "rgba(255,153,0,1)",
                        strokeColor: "rgba(255,153,0,1)",
                        pointColor: "rgba(255,153,0,1)",
                        pointStrokeColor: "rgba(255,153,0,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(255,153,0,1)",
                        data: [
                            <?php
                            $x = -3;
                            while ($x <= 6) {
                                $d = strtotime("$x Month");
                                $date = date("Y-m-d", $d);
                                $split = explode("-", $date);
                                $y = $split[0];
                                $m = $split[1];
                                $d = $split[2];
                                $date = mktime(0, 0, 0, $m, $d, $y);
                                $date = date('m', $date); ?>
                                <?php echo getDeposit($date, 1) ?>,
                            <?php $x++;
                            } ?>
                        ],
                    },
                    {
                        label: "Realize",
                        fillColor: "rgba(0,166,90,1)",
                        strokeColor: "rgba(0,166,90,1)",
                        pointColor: "rgba(0,166,90,1)",
                        pointStrokeColor: "rgba(0,166,90,1)",
                        pointHighlightFill: "#fff",
                        pointHighlightStroke: "rgba(0,166,90,1)",
                        data: [
                            <?php
                            $x = -3;
                            while ($x <= 6) {
                                $d = strtotime("$x Month");
                                $date = date("Y-m-d", $d);
                                $split = explode("-", $date);
                                $y = $split[0];
                                $m = $split[1];
                                $d = $split[2];
                                $date = mktime(0, 0, 0, $m, $d, $y);
                                $date = date('m', $date); ?>
                                <?php echo getDeposit($date, 2) ?>,
                            <?php $x++;
                            } ?>
                        ],
                    },
                ],
            };

            var lineChartOptions = {
                //Boolean - If we should show the scale at all
                showScale: true,
                //Boolean - Whether grid lines are shown across the chart
                scaleShowGridLines: false,
                //String - Colour of the grid lines
                scaleGridLineColor: "rgba(0,0,0,.05)",
                //Number - Width of the grid lines
                scaleGridLineWidth: 1,
                //Boolean - Whether to show horizontal lines (except X axis)
                scaleShowHorizontalLines: true,
                //Boolean - Whether to show vertical lines (except Y axis)
                scaleShowVerticalLines: true,
                //Boolean - Whether the line is curved between points
                bezierCurve: true,
                //Number - Tension of the bezier curve between points
                bezierCurveTension: 0.3,
                //Boolean - Whether to show a dot for each point
                pointDot: false,
                //Number - Radius of each point dot in pixels
                pointDotRadius: 4,
                //Number - Pixel width of point dot stroke
                pointDotStrokeWidth: 1,
                //Number - amount extra to add to the radius to cater for hit detection outside the drawn point
                pointHitDetectionRadius: 20,
                //Boolean - Whether to show a stroke for datasets
                datasetStroke: true,
                //Number - Pixel width of dataset stroke
                datasetStrokeWidth: 2,
                //Boolean - Whether to fill the dataset with a color
                datasetFill: false,
                //String - A legend template
                legendTemplate: '<ul class="<%=name.toLowerCase()%>-legend"><% for (var i=0; i<datasets.length; i++){%><li><span style="background-color:<%=datasets[i].lineColor%>"></span><%if(datasets[i].label){%><%=datasets[i].label%><%}%></li><%}%></ul>',
                //Boolean - whether to maintain the starting aspect ratio or not when responsive, if set to false, will take up entire container
                maintainAspectRatio: true,
                //Boolean - whether to make the chart responsive to window resizing
                responsive: true,
            };

            //-------------
            //- LINE CHART -
            //--------------
            var lineChart1 = new Chart($("#lineChart1").get(0).getContext("2d"));
            lineChart1.Line(lineChartData1, lineChartOptions);

            var lineChart2 = new Chart($("#lineChart2").get(0).getContext("2d"));
            lineChart2.Line(lineChartData2, lineChartOptions);

            var lineChart3 = new Chart($("#lineChart3").get(0).getContext("2d"));
            lineChart3.Line(lineChartData3, lineChartOptions);

            var lineChart4 = new Chart($("#lineChart4").get(0).getContext("2d"));
            lineChart4.Line(lineChartData4, lineChartOptions);
        });
    </script>
</body>

</html>