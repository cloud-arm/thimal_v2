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
    $_SESSION['SESS_DEPARTMENT'] = 'management';
    $_SESSION['SESS_FORM'] = '95';

    include_once("sidebar.php");
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Chart
          <small>Preview</small>
        </h1>

      </section>

      <!-- Main content -->
      <section class="content">


        <div class="box box-info">

          <div class="box-header with-border">
            <h3 class="box-title">Filter</h3>
          </div>
          <?php
          if (isset($_GET['duration'])) {
            $due = $_GET['duration'];
            $duration = $_GET['duration'];
            $type = $_GET['type'];
            $section = $_GET['section'];
            $products = $_GET['products'];
            $dates = [];

            if ($duration <= 30) {

              $currentDate = new DateTime();
              for ($i = 0; $i < $duration; $i++) {
                $date = clone $currentDate;
                $date->modify("-$i days");
                $dates[] = $date->format('Y-m-d');
              }

              $header = $duration . ' Days';
            } else {

              $duration = $duration / 30;
              $currentDate = new DateTime();
              for ($i = 0; $i < $duration; $i++) {
                $date = clone $currentDate;
                $date->modify("-$i month");
                $dates[] = $date->format('Y-m');
              }

              $header = $duration . ' Month';
            }
          } else {

            $due = '7';
            $type = 'bar';
            $section = 'sales';
          }
          $name = ["1" => "12.5kg", "2" => "5kg", "3" => "37.5kg", "4" => "2kg"];
          $key = ["1" => "a", "2" => "b", "3" => "c", "4" => "d"];
          $color = ["1" => "#ff9900", "2" => "#8c8c8c", "3" => "#cc0000", "4" => "#ff9500"];
          ?>
          <div class="box-body">

            <form method="get">
              <div class="row">

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Section</label>
                    <select class="form-control select2 hidden-search" name="section" class="form-control">
                      <option <?php if ($section == 'sales') { ?> selected <?php } ?> value="sales">Sales Data</option>
                      <!-- <option <?php if ($section == 'grn') { ?> selected <?php } ?> value="grn">Purchases Data</option> -->
                    </select>
                  </div>
                </div>

                <div class="col-md-7">
                  <div class="form-group">
                    <label>Product</label>
                    <select class="form-control select2" name="products[]" multiple="multiple" data-placeholder="Select products" style="width: 100%;">
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

              <div class="row">

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Duration</label>
                    <select class="form-control select2 hidden-search" name="duration" class="form-control">
                      <option <?php if ($due == '7') { ?> selected <?php } ?> value="7">7 Days</option>
                      <option <?php if ($due == '14') { ?> selected <?php } ?> value="14">14 Days</option>
                      <option <?php if ($due == '30') { ?> selected <?php } ?> value="30">30 Days</option>
                      <option <?php if ($due == '180') { ?> selected <?php } ?> value="180">6 Moths</option>
                      <option <?php if ($due == '360') { ?> selected <?php } ?> value="360">12 Moths</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Chart Type</label>
                    <select class="form-control select2 hidden-search" name="type" class="form-control">
                      <option <?php if ($type == 'bar') { ?> selected <?php } ?> value="bar">Bar Chart</option>
                      <option <?php if ($type == 'line') { ?> selected <?php } ?> value="line">Line Chart</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-2">
                  <div class="form-group">
                    <button class="btn btn-info" style="padding: 6px 50px;margin-top: 23px;" type="submit">
                      <i class="fa fa-search"></i> Search
                    </button>
                  </div>
                </div>

              </div>

            </form>

          </div>
          <!-- /.box-body -->
        </div>

        <div class="row">
          <div class="col-md-12">
            <!-- BAR CHART -->
            <?php if (isset($_GET['duration'])) { ?>
              <div class="box box-solid ">
                <div class="box-header ">
                  <h3 class="box-title"><?php echo $header; ?> Sales</h3>
                  <div class="box-tools pull-right">
                    <button type="button" class="btn  btn-sm" data-widget="collapse"><i class="fa fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-sm" data-widget="remove"><i class="fa fa-times"></i>
                    </button>
                  </div>
                </div>
                <div class="box-body">
                  <div class="chart" id="bar-chart">
                  </div>
                </div>
                <!-- /.box-body -->
              </div>
              <!-- /.box -->
            <?php } ?>

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

  <!-- Morris.js charts -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="../../plugins/morris/morris.min.js"></script>
  <!-- FastClick -->
  <script src="../../plugins/fastclick/fastclick.js"></script>
  <!-- page script -->

  <script>
    <?php if (isset($_GET['duration'])) { ?>
      $(function() {
        const chartData = {
          data: [
            <?php

            foreach ($dates as $date) {

              if ($_GET['duration'] <= 30) {
                $qty = [];
                foreach ($products as $product) {

                  $qty[$product] = 0;

                  $result = $db->prepare("SELECT sum(qty) FROM sales_list WHERE  action = '0' AND product_id='$product'  AND date ='$date' ");
                  $result->bindParam(':userid', $date);
                  $result->execute();
                  for ($i = 0; $row = $result->fetch(); $i++) {
                    $qty[$product] = $row['sum(qty)'];
                  }
                }

                $split = explode("-", $date);
                $y = $split[0];
                $m = $split[1];
                $d = $split[2];
                $date = mktime(0, 0, 0, $m, $d, $y);
                $month = date('d M', $date);
              } else {

                $d1 = $date . '-01';
                $d2 = $date . '-31';

                $qty = [];
                foreach ($products as $product) {

                  $qty[$product] = 0;

                  $result = $db->prepare("SELECT sum(qty) FROM sales_list WHERE  action = '0' AND product_id='$product' AND date BETWEEN '$d' AND '$d2' ");
                  $result->bindParam(':userid', $date);
                  $result->execute();
                  for ($i = 0; $row = $result->fetch(); $i++) {
                    $qty[$product] = $row['sum(qty)'];
                  }
                }

                $split = explode("-", $date);
                $y = $split[0];
                $m = $split[1];
                $d = 01;
                $date = mktime(0, 0, 0, $m, $d, $y);
                $month = date('m y-M', $date);
              }
              $data = '';

              $data .= sprintf("{ x: '%s',", $month);

              foreach ($products as $product) {
                $data .= sprintf(" %s: '%s',", $key[$product], (int)$qty[$product]);
              }

              $data .= sprintf("},");

              echo $data;
            } ?>

          ],
          name: [
            <?php
            foreach ($products as $product) {
              echo '"' . $name[$product] . '",';
            } ?>
          ],
          key: [
            <?php
            foreach ($products as $product) {
              echo '"' . $key[$product] . '",';
            } ?>
          ],
          color: [
            <?php
            foreach ($products as $product) {
              echo '"' . $color[$product] . '",';
            } ?>
          ],
        };

        <?php
        if (isset($_GET['type']) && $_GET['type'] == 'bar') {
          echo 'barChart(chartData);';
        }
        if (isset($_GET['type']) && $_GET['type'] == 'line') {
          echo 'lineChart(chartData);';
        }
        ?>

        function lineChart(data) {
          var bar = new Morris.Line({
            element: 'bar-chart',
            resize: true,
            data: data.data,
            lineColors: data.color,
            xkey: 'x',
            ykeys: data.key,
            labels: data.name,
            hideHover: 'auto'
          });
        }

        function barChart(data) {
          var bar = new Morris.Bar({
            element: 'bar-chart',
            resize: true,
            data: data.data,
            barColors: data.color,
            xkey: 'x',
            ykeys: data.key,
            labels: data.name,
            hideHover: 'auto'
          });
        }
      });
    <?php } ?>
  </script>
</body>

</html>