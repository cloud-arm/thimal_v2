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
    $_SESSION['SESS_DEPARTMENT'] = 'logistic';
    $_SESSION['SESS_FORM'] = '1';

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


        $result = $db->prepare("SELECT customer_id,credit_period,customer_name FROM customer WHERE credit_period>0 ");
        $result->bindParam(':userid', $d2);
        $result->execute();
        for ($i = 0; $row = $result->fetch(); $i++) {
          $cus = $row['customer_id'];
          $limit = $row['credit_period'];

          $result1 = $db->prepare("SELECT invoice_no FROM payment WHERE action='2' and type='credit' and credit_balance>0 and customer_id=:id ");
          $result1->bindParam(':id', $cus);
          $result1->execute();
          for ($i = 0; $row1 = $result1->fetch(); $i++) {
            $sales_id = $row1['invoice_no'];

            $result2 = $db->prepare("SELECT date FROM sales WHERE action='1' AND invoice_number='$sales_id'");
            $result2->bindParam(':userid', $d2);
            $result2->execute();
            for ($i = 0; $row2 = $result2->fetch(); $i++) {
              $start = $row2['date'];
              $end =  date("Y-m-d");
              $d1 = strtotime($start);
              $d2 = strtotime($end);
              $tdf = abs($d2 - $d1);
              $day = $tdf / 86400;
              $due = intval($day);

              $prd = $due - $limit;
              if ($prd > 0) {
                $sql = "UPDATE  customer SET action=? WHERE customer_id=?";
                $ql = $db->prepare($sql);
                $ql->execute(array(5, $cus));
              }
            }
          }
        }

        ?>

        <div class="row">

          <div class="col-md-4">
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


          <div class="col-md-4">
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
            <div class="col-md-4">

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
          </div>

        </div>

      </section>
    </div>
    <!-- /.content -->

    <!-- /.content-wrapper -->
    <?php include("dounbr.php"); ?>
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
       immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
  </div>


  <?php include_once("script.php"); ?>

  <!-- Morris.js charts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="../../plugins/morris/morris.min.js"></script>
  <!-- FastClick -->
  <script src="../../plugins/fastclick/fastclick.js"></script>

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
    });
  </script>
</body>

</html>