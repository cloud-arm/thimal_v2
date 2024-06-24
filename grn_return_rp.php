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
    $_SESSION['SESS_FORM'] = '65';

    include_once("sidebar.php");

    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Return
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

              $sql = " SELECT * FROM `purchases_item` JOIN purchases ON purchases_item.invoice = purchases.invoice_number WHERE purchases_item.type='Return' AND purchases_item.action = 'close' AND purchases_item.date BETWEEN '$d1' AND '$d2'  ";
              ?>

              <div class="box-body">
                <form action="" method="GET">
                  <div class="row" style="margin-top: 10px;">
                    <div class="col-lg-1"></div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <select class="form-control " name="year" style="width: 100%;" tabindex="1" autofocus>
                          <option> <?php echo date('Y') - 1 ?> </option>
                          <option selected> <?php echo date('Y') ?> </option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-4">
                      <div class="form-group">
                        <select class="form-control " name="month" style="width: 100%;" tabindex="1" autofocus>
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

          <div class="box-header with-border">
            <h3 class="box-title" style="text-transform: capitalize;">Return</h3>
          </div>

          <div class="box-body d-block">
            <table id="example1" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>NO</th>
                  <th>Invoice</th>
                  <th>Date</th>
                  <th>Product</th>
                  <th>Qty</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $a = 0;
                $bill_total = 0;
                $result = $db->prepare($sql);
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                ?>
                  <tr>
                    <td><?php echo ++$a;  ?></td>
                    <td><?php echo $row['supplier_invoice'];  ?></td>
                    <td><?php echo $row['date'];  ?></td>
                    <td><?php echo $row['name'];  ?></td>
                    <td><?php echo $row['qty'];  ?></td>
                    <td><?php echo $row['amount']; ?></td>
                    <?php $bill_total += $row['amount']; ?>
                  </tr>
                <?php }  ?>

              </tbody>
            </table>
            <div style="padding-left: 20px;margin-top: 20px;">
              <h4>Total: <small> Rs. </small> <?php echo number_format($bill_total, 2); ?> </h4>
            </div>
          </div>

        </div>

        <div class="box box-info">

          <div class="box-header with-border">
            <h3 class="box-title" style="text-transform: capitalize;">Credit Note Payment</h3>
          </div>

          <div class="box-body d-block">
            <table id="example2" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Invoice</th>
                  <th>Date</th>
                  <th>Amount</th>
                  <th>Pay Amount</th>
                  <th>Balance</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $a = 0;
                $pay_total = 0;
                $bill_total = 0;
                $result = $db->prepare("SELECT * FROM supply_payment WHERE pay_type = 'Credit_Note' AND type ='Return' AND close_date = '' ");
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                ?>
                  <tr>
                    <td><?php echo $row['id'];  ?></td>
                    <td><?php echo $row['supplier_invoice'];  ?></td>
                    <td><?php echo $row['date'];  ?></td>
                    <td><?php echo $row['amount'];  ?></td>
                    <td><?php echo $row['pay_amount']; ?></td>
                    <td><?php echo $row['credit_balance']; ?></td>
                    <?php $bill_total += $row['amount']; ?>
                    <?php $pay_total += $row['pay_amount']; ?>
                  </tr>
                <?php }  ?>

              </tbody>
            </table>

            <div style="padding-left: 20px;margin-top: 20px;">
              <h4>Amount: <small> Rs. </small> <?php echo number_format($bill_total, 2); ?> </h4>
              <h4>Payment: <small> Rs. </small> <?php echo number_format($pay_total, 2); ?> </h4>
              <h4>Balance: <small> Rs. </small> <?php echo number_format($bill_total - $pay_total, 2); ?> </h4>
            </div>
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

  <script type="text/javascript">
    $(function() {
      $("#example1").DataTable();
      $("#example2").DataTable();
      $('#example').DataTable({
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