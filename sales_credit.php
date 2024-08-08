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
    $_SESSION['SESS_FORM'] = '86';
    date_default_timezone_set("Asia/Colombo");

    include_once("sidebar.php");

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Debtor Report
          <small>Preview</small>
        </h1>
      </section>

      <section class="content">
        <div class="box box-warning">

          <div class="box-header with-border">
            <h3 class="box-title">Filter</h3>
          </div>
          <?php

          $con1 = 'none';
          $con2 = 'none';
          $con3 = 'none';
          $filter = 'all';
          $group = 0;
          $customer_type = 0;
          $customer_id = 0;

          if (isset($_GET['type'])) {

            $filter = $_GET['filter']; // customer filter

            if ($filter == 'group') {
              $group = $_GET['group']; //customer category id
              $con1 = 'block';
            }

            if ($filter == 'type') {
              $customer_type = $_GET['customer_type']; //customer type 1/2/3
              $con2 = 'block';
            }

            if ($filter == 'cus') {
              $customer_id = $_GET['cus']; // customer id
              $con3 = 'block';
            }

            $type = $_GET['type']; // credit type
            $lorry = $_GET['lorry']; // lorry id
          } else {

            $type = 'all';
            $filter = 'all';
            $lorry = 'all'; // lorry id
          }

          $_SESSION['SESS_BACK'] = 'sales_credit.php?type=' . $type . '&cus=' . $customer_id . '&lorry=' . $lorry . '&group=' . $group . '&lorry=' . $lorry . '&customer_type=' . $customer_type;

          ?>
          <div class="box-body">

            <form method="get" action="">

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Type</label>
                    <select class="form-control select2 hidden-search" name="type">
                      <option <?php if ($type == 'all') { ?> selected <?php } ?> value="all">Total Debtors</option>
                      <option <?php if ($type == 'due') { ?> selected <?php } ?> value="due">Overdue Debtors</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Customer</label>
                    <select class="form-control select2 hidden-search" name="filter" class="form-control" onchange="view_payment_date(this.value);">
                      <option <?php if ($filter == 'all') { ?> selected <?php } ?> value="all">All Customer</option>
                      <option <?php if ($filter == 'group') { ?> selected <?php } ?> value="group">Customer Group</option>
                      <option <?php if ($filter == 'type') { ?> selected <?php } ?> value="type">Customer Type</option>
                      <option <?php if ($filter == 'cus') { ?> selected <?php } ?> value="cus">One Customer</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>Lorry </label>
                    <select class="form-control select2" name="lorry" style="width: 100%;">
                      <option <?php if ($lorry == 'all') { ?> selected <?php } ?> value="all"> All Lorry </option>

                      <?php
                      $result = $db->prepare("SELECT * FROM lorry ORDER by lorry_id ASC ");
                      $result->bindParam(':userid', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option <?php if ($lorry == $row['lorry_no']) { ?> selected <?php } ?> value="<?php echo $row['lorry_no']; ?>"><?php echo $row['lorry_no']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-3" id="group_view" style="display:<?php echo $con1; ?>;">
                  <div class="form-group">
                    <label>Customer Group </label>
                    <select class="form-control select2" name="group" style="width: 100%;">

                      <?php
                      $result = $db->prepare("SELECT * FROM customer_category ");
                      $result->bindParam(':userid', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option <?php if ($group == $row['id']) { ?> selected <?php } ?> value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-3" id="type_view" style="display:<?php echo $con2; ?>;">
                  <div class="form-group">
                    <label>Customer Type</label>
                    <select class="form-control select2 hidden-search" name="customer_type" style="width: 100%;">
                      <option <?php if ($customer_type == "1") { ?> selected <?php } ?> value="1">Channel</option>
                      <option <?php if ($customer_type == "2") { ?> selected <?php } ?> value="2">Commercial</option>
                      <option <?php if ($customer_type == "3") { ?> selected <?php } ?> value="3">Apartment</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-6" id="cus_view" style="display:<?php echo $con3; ?>;">
                  <div class="form-group">
                    <label>Customer</label>
                    <select class="form-control select2" name="cus" style="width: 100%;">
                      <?php
                      $result = $db->prepare("SELECT * FROM customer ");
                      $result->bindParam(':id', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option <?php if ($customer_id == $row['customer_id']) { ?> selected <?php } ?> value="<?php echo $row['customer_id']; ?>"><?php echo $row['customer_name']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <button class="btn btn-info" style="width: 123px; margin-top: 23px;" type="submit">
                      <i class="fa fa-search"></i> Search
                    </button>
                  </div>
                </div>
              </div>

            </form>
          </div>
        </div>
      </section>

      <section class="content">

        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Debtor Report </h3>
            <span id="tbl_btn">

            </span>
          </div>
          <!-- /.box-header -->

          <div class="box-body">
            <table id="example2" class="table table-bordered ">
              <thead>
                <tr>
                  <th>Cus_id</th>
                  <th>Customer</th>
                  <th>Invoice</th>
                  <th>Date</th>
                  <th>Limit</th>
                  <th>Amount</th>
                  <th>Pay</th>
                  <th>Overdue</th>
                  <th>Memo</th>
                  <th>#</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $tot = 0;
                $pay_type = "";

                if ($filter == "all") {

                  $customer_fill = " ";
                } else if ($filter == "group") {

                  $customer_fill = " WHERE customer.category = '$group' ";
                } else if ($filter == "type") {
                  if ($customer_type == 1) {
                    $customer_fill = " WHERE customer.type = '$customer_type' AND (customer.category != '8' AND customer.category != '16') ";
                  } else {
                    $customer_fill = " WHERE customer.type = '$customer_type' ";
                  }
                } else {

                  $customer_fill = " WHERE customer.customer_id = '$customer_id' ";
                }

                if ($lorry == 'all') {
                  $lorry_fill = " ";
                } else {
                  $lorry_fill = " WHERE sales.lorry_no = '$lorry' ";
                }

                $customer = array();
                $payment = array();
                $sales = array();

                $sql1 = "SELECT customer_id,credit_period FROM customer $customer_fill ORDER BY category DESC";
                // $sql1 = "SELECT payment.customer_id AS cus_id FROM payment JOIN customer ON customer.customer_id = payment.customer_id WHERE payment.action='2' AND payment.type='credit' AND payment.credit_balance > 0 $customer_fill ORDER BY customer.customer_id";
                // $sql2 = "SELECT *,sales.date AS sales_date FROM payment JOIN sales ON payment.sales_id = sales.transaction_id WHERE payment.action='2' AND sales.action='1' AND payment.type='credit' AND payment.credit_balance > 0  ORDER BY payment.customer_id" . $lorry_fill;

                // $result = $db->prepare($sql1);
                // $result->bindParam(':id', $id);
                // $result->execute();
                // for ($i = 0; $row = $result->fetch(); $i++) {

                //   $customer[] = $row['cus_id'];
                // }

                // $result = $db->prepare($sql2);
                // $result->bindParam(':id', $id);
                // $result->execute();
                // for ($i = 0; $row = $result->fetch(); $i++) {

                //   $payment[$row['customer_id']][] = ["customer_id" => $row['customer_id'], "memo" => $row['memo'], "sales_id" => $row['sales_id'], "type" => $row['type'], "action" => $row['action'], "pay_amount" => $row['pay_amount'], "amount" => $row['amount'], "transaction_id" => $row['transaction_id'], "credit_period" => $row['credit_period'], "invoice_no" => $row['invoice_no'], "invoice_number" => $row['invoice_number'], "date" => $row['sales_date'], "name" => $row['name'], "lorry_no" => $row['lorry_no'],];
                // }

                $result = $db->prepare($sql1);
                $result->bindParam(':userid', $d2);
                $result->execute();
                for ($i = 0; $row_cus = $result->fetch(); $i++) {
                  $cus = $row_cus['customer_id'];
                  $limit = $row_cus['credit_period'];
                  $b_tot = 0;
                  $pay_tot = 0;

                  $result1 = $db->prepare("SELECT memo,sales_id,type,action,customer_id,pay_amount,amount,transaction_id,credit_period,invoice_no FROM payment WHERE action='2' AND type='credit' AND customer_id='$cus' ");
                  $result1->bindParam(':userid', $d2);
                  $result1->execute();
                  for ($i = 0; $row = $result1->fetch(); $i++) {
                    $sales_id = $row['sales_id'];
                    $limit = $row['credit_period'];

                    if ($lorry == 'all') {
                      $lorry_fill = " ";
                    } else {
                      $lorry_fill = " AND sales.lorry_no = '$lorry' ";
                    }

                    $result2 = $db->prepare("SELECT date,name,lorry_no,invoice_number FROM sales WHERE action='1' AND transaction_id='$sales_id' " . $lorry_fill);
                    $result2->bindParam(':userid', $d2);
                    $result2->execute();
                    for ($i = 0; $row2 = $result2->fetch(); $i++) {

                      $pay_type = $row['type'];
                      $action = $row['action'];

                      $date = $row2['date'];
                      $now =  date("Y-m-d");
                      $start = strtotime($date);
                      $end = strtotime($now);
                      $time_dff = abs($end - $start);
                      $intval = $time_dff / 86400;
                      $rs1 = intval($intval);

                      if ($type == 'due') {
                        $level = $rs1 - $limit;
                      } else {
                        $level = $rs1;
                      }
                      $coo = $limit;
                      $rs1 = $rs1 - $limit;

                      if ($level >= 0) { ?>

                        <tr>
                          <td><?php echo $row['customer_id']; ?></td>
                          <td><?php echo $row2['name']; ?></td>
                          <td><?php echo $row['sales_id']; ?><br>
                            <span class="pull-right badge bg-green"><?php echo $row2['lorry_no']; ?> </span>
                          </td>
                          <td><?php echo $row2['date']; ?></td>
                          <?php
                          $tot += $row['amount'] - $row['pay_amount'];
                          ?>
                          <td><?php echo $row['credit_period'];  ?> Day</td>
                          <td><?php echo number_format($row['amount'] - $row['pay_amount'], 2);
                              $b_tot += $row['amount'] - $row['pay_amount'];
                              ?></td>
                          <td><?php echo $row['pay_amount']; ?></span></td>
                          <td><?php echo $rs1;  ?> Day</td>
                          <td><?php echo $row['memo']; ?></td>
                          <td>
                            <a href="bill2.php?invo=<?php echo base64_encode($row2['invoice_number']); ?>" title="Click to View" class="btn btn-primary btn-sm fa fa-eye"></a>
                          </td>
                        </tr>

                    <?php
                      }
                    }
                  }

                  if ($b_tot > 1) { ?>

                    <tr style="background-color: rgb(var(--bg-light-70));">
                      <th><?php echo $cus; ?></th>
                      <th>Total</th>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td><span class="pull-right badge bg-red"><?php echo number_format($b_tot, 1); ?></span></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td>
                        <a href="sales_credit_rp_print.php?id=<?php echo base64_encode($cus); ?>" title="Click to View " class="btn btn-warning btn-sm fa fa-eye"></a>
                      </td>
                    </tr>

                <?php
                  }
                } ?>

              </tbody>

            </table>

            <div style="padding-left: 20px;margin-top: 20px;">
              <h3>Total Credit: <small> Rs. </small> <?php echo number_format($tot, 2); ?> </h3>
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
  <!-- SlimScroll -->
  <script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="../../plugins/fastclick/fastclick.js"></script>
  <!-- AdminLTE App -->
  <script src="../../dist/js/app.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../../dist/js/demo.js"></script>
  <script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>
  <!-- Dark Theme Btn-->
  <script src="https://dev.colorbiz.org/ashen/cdn/main/dist/js/DarkTheme.js"></script>
  <!-- page script -->
  <script>
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

    $(function() {
      $("#example2").DataTable({
        "responsive": true,
        "ordering": false,
        "buttons": ["excel", "pdf", "print"]
      }).buttons().container().appendTo('#tbl_btn');

      // $("div.dt-buttons.btn-group").append('<a href="sales_credit_age_print.php?type=<?php echo $type ?>&cus=<?php echo $customer_id ?>&group=<?php echo $group ?>&lorry=<?php echo $lorry ?>&customer_type=<?php echo $customer_type ?>" title="Click to Print" class="btn btn-secondary buttons-html5">Print (Age)</a>');

      $(".select2").select2();

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