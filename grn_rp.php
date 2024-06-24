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
    $_SESSION['SESS_FORM'] = '61';

    include_once("sidebar.php");

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          GRN
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

              $sql = " SELECT * FROM `purchases` WHERE type='GRN' AND dll=0 AND  pu_date BETWEEN '$d1' AND '$d2'  ";

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

          <div class="box-header with-border">
            <h3 class="box-title" style="text-transform: capitalize;">Purchases</h3>

            <span id="tbl_btn"></span>
          </div>

          <div class="box-body d-block">
            <table id="example" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>NO</th>
                  <th>Invoice</th>
                  <th>Lorry NO</th>
                  <th>Location</th>
                  <th>Comment</th>
                  <th>Date</th>
                  <th>Transport</th>
                  <th>Payment</th>
                  <th>Amount</th>
                  <th>Balance</th>
                  <th>#</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $pay_total = 0;
                $bill_total = 0;
                $result = $db->prepare($sql);
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {

                  $bill = $row['amount'];
                  $pay = $row['pay_amount'];
                  $bill_total += $bill;
                  $pay_total += $pay;
                ?>
                  <tr>
                    <td><?php echo $row['transaction_id'];  ?></td>
                    <td><?php echo $row['supplier_invoice'];  ?></td>
                    <td><?php echo $row['lorry_no'];  ?></td>
                    <td><?php echo $row['location'];  ?></td>
                    <td><?php echo $row['remarks'];  ?></td>
                    <td><?php echo $row['pu_date'];  ?></td>
                    <td><?php echo $row['transport'];  ?></td>
                    <td><?php echo $pay; ?></td>
                    <td><?php echo $bill; ?></td>
                    <td><?php echo $bill - $pay;  ?></td>
                    <td>
                      <?php if ($row['date'] == date('Y-m-d')) {  ?>
                        <a href="#" onclick="dll_row(<?php echo $row['transaction_id']; ?>)" class="btn btn-danger btn-sm" title="Click to Delete"> X</a>
                        <form action="grn_dll.php" method="POST" id="dll_<?php echo $row['transaction_id']; ?>">
                          <input type="hidden" value="<?php echo $row['transaction_id']; ?>" name="id">
                        </form>
                      <?php } ?>
                    </td>
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

  <?php
  include("alert.php");
  if (isset($_GET['err'])) {
    if ($_GET['err'] == 1) {
      $message = 'These purchases can\'t be deleted. Here the stock qty is unbalanced';
      Alert($message);
    }
  }
  ?>

  <script type="text/javascript">
    function dll_row(id) {
      if (confirm("Sure you want to delete this invoice? There is NO undo!")) {
        $('#dll_' + id).submit();
      }
      return false;
    }

    $(function() {
      $("#example").DataTable({
        "responsive": true,
        "buttons": ["excel", "pdf", "print"]
      }).buttons().container().appendTo('#tbl_btn');
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