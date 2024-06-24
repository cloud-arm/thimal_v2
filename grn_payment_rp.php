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
    $_SESSION['SESS_FORM'] = '63';


    include_once("sidebar.php");

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          SUPPLIER PAYMENT
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

              if (isset($_GET['dates'])) {
                $dates = $_GET['dates'];
              } else {
                $dates = date('Y/m/d-Y/m/d');
              }

              $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
              $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");

              $sql = "SELECT * FROM supply_payment";
              if ($dates != '') {
                $sql = "SELECT * FROM supply_payment WHERE action < 4 AND date BETWEEN '$d1' AND '$d2'";
              }
              ?>

              <div class="box-body">
                <form action="" method="GET">
                  <div class="row" style="margin-bottom: 20px;display: flex;align-items: end;">
                    <div class="col-lg-1"></div>
                    <div class="col-lg-8">
                      <label>Date range:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control pull-right" id="reservation" name="dates" value="<?php echo $dates; ?>">
                      </div>
                    </div>

                    <div class="col-lg-2">
                      <input type="submit" class="btn btn-info" value="Apply">
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>

        <div class="box box-info">

          <div class="box-header with-border">
            <h3 class="box-title" style="text-transform: capitalize;">Payments</h3>
          </div>

          <div class="box-body d-block">
            <table id="example" class="table table-bordered" style="border-radius: 0;">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Invoice</th>
                  <th>Type</th>
                  <th>Date</th>
                  <th>Chq No</th>
                  <th>Chq Date</th>
                  <th>Amount</th>
                  <th>Payment</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $tot1 = 0;
                $tot2 = 0;
                $result = $db->prepare($sql);
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                  $dll = $row['dll'];
                  if ($dll == 0) { ?>
                    <tr>
                      <td><?php echo $row['id']  ?></td>
                      <td>
                        <?php if ($row['type'] == 'bulk_payment') { ?>
                          <?php echo $row['invoice_no']  ?>
                        <?php } else { ?>
                          <?php echo $row['supplier_invoice']  ?>
                        <?php } ?>
                      </td>
                      <td><?php echo $row['pay_type']  ?></td>
                      <td><?php echo $row['date']  ?></td>
                      <td><?php echo $row['chq_no']  ?></td>
                      <td><?php echo $row['chq_date']  ?></td>
                      <?php if ($row['type'] == 'bulk_payment' | $row['type'] == 'GRN' & $row['pay_type'] == 'Credit') { ?>
                        <td>
                          <?php echo $row['amount']; ?>
                          <?php $tot1 += $row['amount']; ?>
                        </td>
                        <td></td>
                      <?php } else { ?>
                        <td></td>
                        <td>
                          <?php echo $row['amount']; ?>
                        </td>
                        <?php $tot2 += $row['amount']; ?>
                      <?php }  ?>
                    </tr>
                <?php }
                } ?>

              </tbody>
              <tfoot>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <th>
                  <h4>Total:</h4>
                </th>
                <th>
                  <h4><?php echo number_format($tot1, 2); ?></h4>
                </th>
                <th>
                  <h4><?php echo number_format($tot2, 2); ?></h4>
                </th>
              </tfoot>
            </table>
          </div>

        </div>
      </section>

    </div>

    <!-- /.content-wrapper -->
    <?php include("dounbr.php"); ?>

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
      $('#example').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": false,
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