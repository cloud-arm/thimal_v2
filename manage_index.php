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
    $_SESSION['SESS_DEPARTMENT'] = 'management';
    $_SESSION['SESS_FORM'] = '31';

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
          <div class="col-md-12">
            <!-- BAR CHART -->
            <div class="box box-solid ">
              <div class="box-header ">
                <h3 class="box-title">14 Day Payment Chart</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn  btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="box-body chart-responsive">
                <div class="chart" id="bar-chart"></div>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->

          </div>

          <div class="col-md-12">
            <!-- BAR CHART -->
            <div class="box box-solid ">
              <div class="box-header ">
                <h3 class="box-title">12 Month Sales Chart</h3>
                <div class="box-tools pull-right">
                  <button type="button" class="btn  btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                  </button>
                </div>
              </div>
              <div class="box-body chart-responsive">

                <div class="chart">
                  <canvas id="salesChart" style="height: 250px"></canvas>
                </div>
              </div>
              <!-- /.box-body -->
            </div>
            <!-- /.box -->

          </div>

        </div>

        <div class="row">
          <div class="col-md-8">
            <div class="row">
              <div class="col-md-12">
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
              <div class="col-md-12">
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
              <div class="col-md-12">
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
              <div class="col-md-12">
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
          </div>

          <div class="col-md-4">
            <div class="row">
              <div class="col-md-12">
                <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user-2">
                  <!-- Add the bg color to the header using any of the bg-* classes -->
                  <div class="widget-user-header bg-red">
                    <div class="widget-user-image">
                      <img src="icon/store.svg" alt="">
                    </div>
                    <!-- /.widget-user-image -->
                    <h3 class="widget-user-username">Stores </h3>
                    <h5 class="widget-user-desc">Accessory</h5>
                  </div>
                  <div class="box-footer no-padding">
                    <ul class="nav nav-stacked">
                      <?php
                      $result = $db->prepare("SELECT * FROM products WHERE product_id>=9 AND qty > 0 ");
                      $result->bindParam(':userid', $date);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <li><a href="#"><?php echo $row['gen_name']; ?> <span class="pull-right badge bg-red"><?php echo $row['qty']; ?></span></a></li>
                      <?php } ?>
                    </ul>
                  </div>
                </div>
                <!-- /.widget-user -->
              </div>
              <div class="col-md-12">
                <!-- Widget: user widget style 1 -->
                <div class="box box-widget widget-user-2">
                  <!-- Add the bg color to the header using any of the bg-* classes -->
                  <div class="widget-user-header bg-orange-active">
                    <div class="widget-user-image">
                      <img src="icon/gas.png" alt="">
                    </div>
                    <!-- /.widget-user-image -->
                    <h2 class="widget-user-username">YARD</h2>
                    <h5 class="widget-user-desc">Gas & Cylinders</h5>
                  </div>
                  <div class="box-footer no-padding">
                    <ul class="nav nav-stacked">
                      <?php
                      $result = $db->prepare("SELECT * FROM products WHERE product_name>=1  AND qty > 0  ");

                      $result->bindParam(':userid', $date);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {


                      ?>
                        <li><a href="#"><?php echo $row['gen_name']; ?> <span class="pull-right badge bg-red"><?php echo $row['qty']; ?></span></a></li>
                      <?php } ?>
                      <?php
                      $result = $db->prepare("SELECT * FROM products WHERE product_name='' AND product_id<9  AND qty > 0  ");
                      $result->bindParam(':userid', $date);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                        $pro_id = $row['product_id'];
                        $cha = 0;
                        $result1 = $db->prepare("SELECT * FROM products WHERE product_name='$pro_id'  ");
                        $result1->bindParam(':userid', $date);
                        $result1->execute();
                        for ($i = 0; $row1 = $result1->fetch(); $i++) {
                          $cha = $row1['qty'];
                        }

                      ?>
                        <li><a href="#"><?php echo $row['gen_name']; ?><span class="pull-right badge bg-"><?php echo $row['qty'] - $cha; ?></span>
                            <span class="pull-right badge bg-green"><?php echo $row['qty']; ?></span>
                          </a></li>
                      <?php } ?>
                    </ul>
                  </div>
                </div>
                <!-- /.widget-user -->
              </div>
              <div class="col-md-12">
                <div class="row">

                  <?php
                  $result = $db->prepare("SELECT * FROM loading WHERE action='load' AND type = 'purchases' ");
                  $result->bindParam(':userid', $date);
                  $result->execute();
                  for ($i = 0; $row = $result->fetch(); $i++) {
                    $driver_id = $row['driver'];
                    $result1 = $db->prepare("SELECT * FROM employee WHERE  id=:id  ");
                    $result1->bindParam(':id', $driver_id);
                    $result1->execute();
                    for ($i = 0; $row1 = $result1->fetch(); $i++) {
                      $driver = $row1['name'];
                      $pic = $row1['pic'];
                    }
                  ?>
                    <div class="col-md-12">

                      <div class="box box-widget widget-user">
                        <a href="loading_view.php?id=<?php echo $row['transaction_id']; ?>">
                          <div class="widget-user-header bg-olive">
                            <div class="row">
                              <div class="col-md-4">
                                <div class="widget-user-image right">
                                  <img class="img-circle" src="<?php echo $pic; ?>" alt="">
                                </div>
                              </div>
                              <div class="col-md-8">
                                <h3 class="widget-user-username"><?php echo $row['lorry_no']; ?></h3>
                                <h5 class="widget-user-desc"><?php echo $driver; ?></h5>
                                <h6 class="widget-user-desc center-space">
                                  <span><?php echo $row['date']; ?></span>
                                  <span><?php echo $row['loading_time']; ?></span>
                                </h6>
                              </div>
                            </div>
                          </div>
                        </a>
                        <div class="box-footer no-padding">
                          <ul class="nav nav-stacked">
                            <?php
                            $result1 = $db->prepare("SELECT * FROM loading_list WHERE loading_id=:id ");
                            $result1->bindParam(':id', $row['transaction_id']);
                            $result1->execute();
                            for ($i = 0; $row1 = $result1->fetch(); $i++) {
                              if ($row1['product_code'] > 4) {
                            ?>
                                <li>
                                  <a style="background-color: rgba(var(--bg-dark-75), 0.5);">
                                    <?php echo $row1['product_name']; ?>
                                    <span class="pull-right badge bg-orange"><?php echo $row1['qty']; ?></span>
                                  </a>
                                </li>
                              <?php } else { ?>
                                <li>
                                  <a>
                                    <?php echo $row1['product_name']; ?>
                                    <span class="pull-right badge bg-olive"><?php echo $row1['qty']; ?></span>
                                  </a>
                                </li>
                              <?php }  ?>
                            <?php }  ?>
                        </div>
                      </div>
                      <!-- /.widget-user -->
                    </div>
                  <?php } ?>

                </div>
              </div>
            </div>
          </div>
        </div>


        <div class="row">

          <?php
          $result = $db->prepare("SELECT * FROM loading WHERE action='load' AND type != 'purchases' ");
          $result->bindParam(':userid', $date);
          $result->execute();
          for ($i = 0; $row = $result->fetch(); $i++) {
            $driver_id = $row['driver'];
            $result1 = $db->prepare("SELECT * FROM employee WHERE  id=:id  ");
            $result1->bindParam(':id', $driver_id);
            $result1->execute();
            for ($i = 0; $row1 = $result1->fetch(); $i++) {
              $driver = $row1['name'];
              $pic = $row1['pic'];
            }
          ?>
            <div class="col-md-4">

              <div class="box box-widget widget-user">

                <a href="loading_view.php?id=<?php echo $row['transaction_id']; ?>">
                  <div class="widget-user-header bg-yellow">
                    <div class="row">
                      <div class="col-md-8">
                        <h3 class="widget-user-username"><?php echo $row['lorry_no']; ?></h3>
                        <h5 class="widget-user-desc"><?php echo $driver; ?></h5>
                        <h6 class="widget-user-desc center-space">
                          <span><?php echo $row['date']; ?></span>
                          <span><?php echo $row['loading_time']; ?></span>
                        </h6>
                      </div>
                      <div class="col-md-4">
                        <div class="widget-user-image right">
                          <img class="img-circle" src="<?php echo $pic; ?>" alt="">
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
                <div class="box-footer no-padding">
                  <ul class="nav nav-stacked">
                    <?php
                    $result1 = $db->prepare("SELECT * FROM loading_list WHERE loading_id=:id ");
                    $result1->bindParam(':id', $row['transaction_id']);
                    $result1->execute();
                    for ($i = 0; $row1 = $result1->fetch(); $i++) {
                      if ($row1['product_code'] > 4) {
                    ?>
                        <li>
                          <a style="background-color: rgba(var(--bg-dark-75), 0.5);">
                            <?php echo $row1['product_name']; ?>
                            <span class="pull-right badge bg-blue-active"><?php echo $row1['qty_sold']; ?></span>
                            <span class="pull-right badge bg-blue"><?php echo $row1['qty']; ?></span>
                          </a>
                        </li>
                      <?php } else { ?>
                        <li>
                          <a>
                            <?php echo $row1['product_name']; ?>
                            <span class="pull-right badge bg-green-active"><?php echo $row1['qty_sold']; ?></span>
                            <span class="pull-right badge bg-olive"><?php echo $row1['qty']; ?></span>
                          </a>
                        </li>
                      <?php }  ?>
                    <?php }  ?>
                </div>
              </div>
              <!-- /.widget-user -->
            </div>
          <?php } ?>

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

  <!-- Morris.js charts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="../../plugins/morris/morris.min.js"></script>
  <!-- ChartJS -->
  <script src="../../plugins/chartjs/Chart.min.js"></script>
  <!-- FastClick -->
  <script src="../../plugins/fastclick/fastclick.js"></script>
  <!-- page script -->

  <script>
    $(function() {
      "use strict";

      //BAR CHART
      var bar = new Morris.Bar({
        element: 'bar-chart',
        resize: true,
        data: [
          <?php $x = 0;
          while ($x <= 14) {
            $d = strtotime("-$x Day");
            $date = date("Y-m-d", $d);
            $cash = 0;
            $chq = 0;
            $credit = 0;
            $result1 = $db->prepare("SELECT  amount FROM payment WHERE date='$date' AND action > '0' AND type='cash' ");
            $result1->bindParam(':userid', $date);
            $result1->execute();
            for ($i = 0; $row1 = $result1->fetch(); $i++) {
              $cash += $row1['amount'];
            }
            $result1 = $db->prepare("SELECT  amount FROM payment WHERE date='$date' AND action > '0' AND type='chq' ");
            $result1->bindParam(':userid', $date);
            $result1->execute();
            for ($i = 0; $row1 = $result1->fetch(); $i++) {
              $chq += $row1['amount'];
            }

            $result1 = $db->prepare("SELECT  amount FROM payment WHERE date='$date' AND action > '0' AND type='credit' ");
            $result1->bindParam(':userid', $date);
            $result1->execute();
            for ($i = 0; $row1 = $result1->fetch(); $i++) {
              $credit += $row1['amount'];
            }

            $split = explode("-", $date);
            $y = $split[0];
            $m = $split[1];
            $d = $split[2];
            $date = mktime(0, 0, 0, $m, $d, $y);
            $date = date('M d', $date);

          ?> {
              x: '<?php echo $date; ?>',
              a: <?php echo $cash; ?>,
              b: <?php echo $chq; ?>,
              c: <?php echo $credit; ?>
            },
          <?php $x++;
          } ?>

        ],
        barColors: ['#ff9900', '#8c8c8c', '#cc0000'],
        xkey: 'x',
        ykeys: ['a', 'b', 'c'],
        labels: ['CASH', 'CHQ', 'CREDIT'],
        hideHover: 'auto'
      });
      //BAR CHART



    });
  </script>
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

      var salesChartData = {
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
            $date = date('Y M', $date);


          ?> '<?php echo $date ?>',

          <?php $x--;
          } ?>
        ],
        datasets: [{
            label: "12.5Kg",
            fillColor: "rgba(247,183,0, 1)",
            strokeColor: "rgba(247,183,0, 1)",
            pointColor: "rgba(247,183,0, 1)",
            pointStrokeColor: "rgba(247,183,0, 1)",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(247,183,0, 1)",
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
                // $date = mktime(0, 0, 0, $m, $d, $y);
                $date1 = $y . "-" . $m . "-01";
                $date2 = $y . "-" . $m . "-31";

                $qty = 0;
                $result1 = $db->prepare("SELECT sum(qty) FROM sales_list WHERE  action = '0' AND product_id='1'  AND date BETWEEN '$date1' AND '$date2' ");
                $result1->bindParam(':userid', $date1);
                $result1->execute();
                for ($i = 0; $row1 = $result1->fetch(); $i++) {
                  $qty = $row1['sum(qty)'];
                }

              ?>

                <?php echo $qty; ?>,


              <?php $x--;
              } ?>

            ],
          },
          {
            label: "5Kg",
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
                // $date = mktime(0, 0, 0, $m, $d, $y);
                $date1 = $y . "-" . $m . "-01";
                $date2 = $y . "-" . $m . "-31";

                $qty = 0;
                $result1 = $db->prepare("SELECT sum(qty) FROM sales_list WHERE  action = '0' AND product_id='2'  AND date BETWEEN '$date1' AND '$date2' ");
                $result1->bindParam(':userid', $date1);
                $result1->execute();
                for ($i = 0; $row1 = $result1->fetch(); $i++) {
                  $qty = $row1['sum(qty)'];
                }

              ?>

                <?php echo $qty; ?>,


              <?php $x--;
              } ?>
            ],
          },
          {
            label: "37.5Kg",
            fillColor: "rgba(190,9,42,1)",
            strokeColor: "rgba(190,9,42,1)",
            pointColor: "rgba(190,9,42,1)",
            pointStrokeColor: "rgba(190,9,42,1)",
            pointHighlightFill: "#fff",
            pointHighlightStroke: "rgba(190,9,42,1)",
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
                // $date = mktime(0, 0, 0, $m, $d, $y);
                $date1 = $y . "-" . $m . "-01";
                $date2 = $y . "-" . $m . "-31";

                $qty = 0;
                $result1 = $db->prepare("SELECT sum(qty) FROM sales_list WHERE  action = '0' AND product_id='3'  AND date BETWEEN '$date1' AND '$date2' ");
                $result1->bindParam(':userid', $date1);
                $result1->execute();
                for ($i = 0; $row1 = $result1->fetch(); $i++) {
                  $qty = $row1['sum(qty)'];
                }

              ?>

                <?php echo $qty; ?>,


              <?php $x--;
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

      var lineChart5 = new Chart($("#salesChart").get(0).getContext("2d"));
      lineChart5.Line(salesChartData, lineChartOptions);


    });
  </script>
</body>

</html>