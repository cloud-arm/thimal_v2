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
    $_SESSION['SESS_FORM'] = '84';

    include_once("sidebar.php");

    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          SALES COMMISSION
          <small>Report</small>
        </h1>
      </section>
      <!-- Main content -->
      <section class="content">

        <div class="row">
          <div class="col-md-2"></div>
          <div class="col-md-8">
            <div class="box">
              <div class="box-header with-border">
                <h3 class="box-title">Employee & Date Selector</h3>
              </div>
              <?php
              include("connect.php");
              date_default_timezone_set("Asia/Colombo");

              if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $dates = $_GET['dates'];
                $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
                $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");
              } else  if (isset($_GET['d1']) & isset($_GET['d2'])) {
                $id = $_GET['id'];
                $d1 = $_GET['d1'];
                $d2 = $_GET['d2'];
                $dates = date('Y/m/d-Y/m/d');
              } else {
                $id = 0;
                $d1 = date('Y-m-d');
                $d2 = date('Y-m-d');
                $dates = date('Y/m/d-Y/m/d');
              }

              ?>

              <div class="box-body">
                <form action="" method="GET">
                  <div class="row" style="margin-bottom: 20px;display: flex;align-items: end;">
                    <div class="col-lg-1"></div>
                    <div class="col-lg-4">
                      <label>Employee:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-user"></i>
                        </div>
                        <select class="form-control select2" name="id" style="width: 100%;" autofocus tabindex="1">
                          <option value="0" selected disabled></option>
                          <?php
                          $result = $db->prepare("SELECT * FROM employee WHERE action='1'");
                          $result->bindParam(':userid', $ttr);
                          $result->execute();
                          for ($i = 0; $row = $result->fetch(); $i++) {
                          ?>
                            <option <?php if ($id == $row['id']) { ?>selected <?php } ?> value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['username']); ?>
                            </option>
                          <?php
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-5">
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

        <?php
        include("connect.php");
        date_default_timezone_set("Asia/Colombo");

        $sql = "SELECT sales_list.product_id AS pid, sales_list.name AS pname, loading.emp_count AS coun, loading.transaction_id AS lid, sum(sales_list.qty), sum(sales_list.commission) FROM loading JOIN sales_list ON loading.transaction_id = sales_list.loading_id WHERE (loading.driver = '$id' OR loading.helper1 = '$id' OR loading.helper2 = '$id' OR loading.helper3 = '$id') AND loading.date BETWEEN '$d1' AND '$d2' GROUP BY sales_list.product_id ";

        ?>
        <div class="box box-info">

          <div class="box-header with-border">
            <h3 class="box-title" style="text-transform: capitalize;">Commission</h3>
          </div>

          <div class="box-body d-block">
            <table id="example" class="table table-bordered" style="border-radius: 0;">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Product</th>
                  <th>Loading</th>
                  <th>Emp: Count</th>
                  <th>Qty</th>
                  <th>Total Comm</th>
                  <th>Emp Comm</th>
                </tr>
              </thead>
              <tbody>
                <?php $tot = 0;
                $result = $db->prepare($sql);
                $result->bindParam(':id', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                  $commis = $row['sum(sales_list.commission)'];
                  $cont = $row['coun'];
                  if ($cont > 3) {
                    $cont = 3;
                  }
                  $comm = $commis / $cont;
                ?>
                  <tr>
                    <td><?php echo $row['pid']  ?></td>
                    <td><?php echo $row['pname']  ?></td>
                    <td><?php echo $row['lid']  ?></td>
                    <td><?php echo $row['coun']  ?></td>
                    <td><?php echo $row['sum(sales_list.qty)']  ?></td>
                    <td><?php echo number_format($row['sum(sales_list.commission)'], 2)  ?></td>
                    <td><?php echo number_format($comm, 2);
                        $tot += $comm; ?></td>
                  </tr>
                <?php } ?>

              </tbody>
              <tfoot>
              </tfoot>
            </table>
            <div style="padding-left: 25px;margin-top: 20px;">
              <h4>Total: <small> Rs. </small> <?php echo number_format($tot, 2); ?> </h4>
            </div>
          </div>

        </div>
      </section>

    </div>

    <!-- /.content-wrapper -->
    <?php include("dounbr.php"); ?>

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