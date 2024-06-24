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
          if (isset($_GET['type'])) {

            $d1 = $_GET['d1']; //date one
            $d2 = $_GET['d2']; //date two
            $type = $_GET['type'];
            $customer_id = $_GET['cus'];
            $group = $_GET['group'];
            $lorry = $_GET['lorry']; // lorry id
            $customer_type = $_GET['customer_type'];
          } else {
            $d1 = date('Y-m-d'); //date one
            $d2 = date('Y-m-d'); //date two
            $type = 'all';
            $customer_id = 'all';
            $group = 'all';
            $lorry = 'all'; // lorry id
            $customer_type = 'all';
          }
          ?>
          <div class="box-body">

            <form method="get" action="">

              <div class="row">
                <div class="col-md-4">
                  <div class="form-group">
                    <label>Type</label>
                    <select class="form-control select2 hidden-search" name="type">
                      <option value="all">Total Debtors</option>
                      <option value="due">Overdue Debtors</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Customer</label>
                    <select class="form-control select2" name="cus">
                      <option value="all">All Customer</option>
                      <?php
                      $result = $db->prepare("SELECT * FROM customer ");
                      $result->bindParam(':id', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option value="<?php echo $row['customer_id']; ?>"><?php echo $row['customer_name']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Customer Group </label>
                    <select class="form-control select2" name="group">
                      <option value="all">All Group</option>

                      <?php
                      $result = $db->prepare("SELECT * FROM customer_category ");
                      $result->bindParam(':userid', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>


                <div class="col-md-4">
                  <div class="form-group">
                    <label>Lorry </label>
                    <select class="form-control select2" name="lorry">
                      <option value="all"> All Lorry </option>

                      <?php
                      $result = $db->prepare("SELECT * FROM lorry ORDER by lorry_id ASC ");
                      $result->bindParam(':userid', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option><?php echo $row['lorry_no']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>

                <div class="col-md-4">
                  <div class="form-group">
                    <label>Customer Type</label>
                    <select class="form-control select2 hidden-search" name="customer_type">
                      <option value="all">All Type</option>
                      <option value="1">Channel</option>
                      <option value="2">Commercial</option>
                      <option value="3">Apartment</option>
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
            <a style="margin-left: 10px;" href="sales_credit_print.php?type=<?php echo $_GET['type'] ?>&cus=<?php echo $_GET['cus'] ?>&group=<?php echo $_GET['group'] ?>&lorry=<?php echo $_GET['lorry'] ?>&customer_type=<?php echo $_GET['customer_type'] ?>" title="Click to Print" class="btn btn-danger btn-sm"><i class="fa fa-print"></i> Print</a>
            <a style="margin-left: 10px;" href="sales_credit_age_print.php?type=<?php echo $_GET['type'] ?>&cus=<?php echo $_GET['cus'] ?>&group=<?php echo $_GET['group'] ?>&lorry=<?php echo $_GET['lorry'] ?>&customer_type=<?php echo $_GET['customer_type'] ?>" title="Click to Print" class="btn btn-danger btn-sm"><i class="fa fa-print"></i> Print (Age)</a>
            <a style="margin-left: 10px;" href="xl/credit_rp.php" title="Click to Print" class="btn btn-success btn-sm"><i class="fa fa-print"></i> Excel</a>
            </h3>
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
                  <th>Overdue</th>
                  <th>Memo</th>
                  <th>#</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $tot = 0;
                $pay_type = "";

                $_SESSION['SESS_BACK'] = 'sales_credit.php?type=' . $type . '&cus=' . $customer_id . '&lorry=' . $lorry . '&group=' . $group . '&lorry=' . $lorry . '&customer_type=' . $customer_type;

                if ($customer_id == "all") {
                  if ($group == "all") {

                    if ($customer_type == "all") {
                      $customer = $db->prepare("SELECT customer_id,credit_period FROM customer  ORDER BY category DESC");
                    } else {
                      $customer = $db->prepare("SELECT customer_id,credit_period FROM customer WHERE type='$customer_type' ORDER BY category DESC");
                    }
                  } else {
                    $customer = $db->prepare("SELECT customer_id,credit_period FROM customer WHERE category='$group' ORDER BY category DESC");
                  }
                } else {
                  $customer = $db->prepare("SELECT customer_id,credit_period FROM customer WHERE customer_id='$customer_id' ORDER BY category DESC ");
                }

                $customer->bindParam(':userid', $d2);
                $customer->execute();
                for ($i = 0; $row_cus = $customer->fetch(); $i++) {
                  $cus = $row_cus['customer_id'];
                  $limit = $row_cus['credit_period'];
                  $b_tot = 0;
                  $pay_tot = 0;

                  $result2z = $db->prepare("SELECT memo,sales_id,type,action,customer_id,pay_amount,amount,transaction_id,credit_period,invoice_no FROM payment WHERE action='2' and type='credit' and credit_balance>0 and customer_id='$cus' ");
                  $result2z->bindParam(':userid', $d2);
                  $result2z->execute();
                  for ($i = 0; $row = $result2z->fetch(); $i++) {
                    $sales_id = $row['invoice_no'];
                    $limit = $row['credit_period'];

                    if ($lorry == 'all') {
                      $result2 = $db->prepare("SELECT date,name,lorry_no,invoice_number FROM sales WHERE action='1' AND invoice_number='$sales_id'");
                    } else {
                      $result2 = $db->prepare("SELECT date,name,lorry_no,invoice_number FROM sales WHERE action='1' AND invoice_number='$sales_id' AND lorry_no='$lorry' ");
                    }
                    $result2->bindParam(':userid', $d2);
                    $result2->execute();
                    for ($i = 0; $row2 = $result2->fetch(); $i++) {

                      $pay_type = $row['type'];
                      $action = $row['action'];

                      $date1 = $row2['date'];
                      $date =  date("Y-m-d");
                      $sday = strtotime($date1);
                      $nday = strtotime($date);
                      $tdf = abs($nday - $sday);
                      $nbday1 = $tdf / 86400;
                      $rs1 = intval($nbday1);

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
                          <td><?php echo $row['invoice_no']; ?><br>
                            <span class="pull-right badge bg-green"><?php echo $row2['lorry_no']; ?> </span>
                          </td>
                          <td><?php echo $row2['date']; ?></td>
                          <?php
                          $tot += $row['amount'] - $row['pay_amount'];
                          ?>
                          <td><?php echo $row['credit_period'];  ?> Day</td>
                          <td><?php echo number_format($row['amount'] - $row['pay_amount'], 2);
                              $b_tot += $row['amount'] - $row['pay_amount'];
                              if ($row['pay_amount'] > '0') { ?><span class="pull-right badge bg-black"><?php echo $row['pay_amount']; ?></span><?php } ?></td>
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

                  if ($b_tot > 1) {
                    ?>
                    <tr style="background-color: rgb(var(--bg-light-70));">
                      <th><?php echo $cus; ?></th>
                      <th>Total</th>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td><span class="pull-right badge bg-red"><?php echo number_format($b_tot, 1); ?></span></td>
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
  <script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
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
    $(function() {
      $("#example1").DataTable();
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
        "info": true,
        "autoWidth": true
      });

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