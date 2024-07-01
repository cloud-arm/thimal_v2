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

            if ($filter == 'group') {
              $group = $_GET['group']; //customer category id
            }

            if ($filter == 'type') {
              $customer_type = $_GET['customer_type']; //customer type 1/2/3
            }

            if ($filter == 'cus') {
              $customer_id = $_GET['cus']; // customer id
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
                    <select class="form-control select2 hidden-search" name="filter" class="form-control" id="p_type" onchange="view_payment_date(this.value);">
                      <option value="all">All Customer</option>
                      <option value="group">Customer Group</option>
                      <option value="type">Customer Type</option>
                      <option value="cus">One Customer</option>
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

                <div class="col-md-4" id="group_view" style="display:none;">
                  <div class="form-group">
                    <label>Customer Group </label>
                    <select class="form-control select2" name="group">

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

                <div class="col-md-4" id="type_view" style="display:none;">
                  <div class="form-group">
                    <label>Customer Type</label>
                    <select class="form-control select2 hidden-search" name="customer_type">
                      <option value="1">Channel</option>
                      <option value="2">Commercial</option>
                      <option value="3">Apartment</option>
                    </select>
                  </div>
                </div>

                <div class="col-md-4" id="cus_view" style="display:none;">
                  <div class="form-group">
                    <label>Customer</label>
                    <select class="form-control select2" name="cus">
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

                  $customer_fill = " AND customer.category = '$group' ";
                } else if ($filter == "type") {

                  $customer_fill = " AND customer.type = '$customer_type' ";
                } else {

                  $customer_fill = " AND customer.customer_id = '$customer_id' ";
                }

                if ($lorry == 'all') {
                  $lorry_fill = " ";
                } else {
                  $lorry_fill = " AND sales.lorry_no = '$lorry' ";
                }

                $customer = array();
                $payment = array();
                $sales = array();

                $sql1 = "SELECT customer_id FROM customer $customer_fill ORDER BY category DESC";
                $sql1 = "SELECT payment.customer_id AS cus_id FROM payment JOIN customer ON customer.customer_id = payment.customer_id WHERE payment.action='2' AND payment.type='credit' AND payment.credit_balance > 0 $customer_fill ORDER BY customer.customer_id";
                $sql2 = "SELECT *,sales.date AS sales_date FROM payment JOIN sales ON payment.sales_id = sales.transaction_id WHERE payment.action='2' AND sales.action='1' AND payment.type='credit' AND payment.credit_balance > 0  ORDER BY payment.customer_id" . $lorry_fill;

                $result = $db->prepare($sql1);
                $result->bindParam(':id', $id);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {

                  $customer[] = $row['cus_id'];
                }

                $result = $db->prepare($sql2);
                $result->bindParam(':id', $id);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {

                  $payment[$row['customer_id']][] = ["customer_id" => $row['customer_id'], "memo" => $row['memo'], "sales_id" => $row['sales_id'], "type" => $row['type'], "action" => $row['action'], "pay_amount" => $row['pay_amount'], "amount" => $row['amount'], "transaction_id" => $row['transaction_id'], "credit_period" => $row['credit_period'], "invoice_no" => $row['invoice_no'], "invoice_number" => $row['invoice_number'], "date" => $row['sales_date'], "name" => $row['name'], "lorry_no" => $row['lorry_no'],];
                }

                echo json_encode($payment);

                ?>

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

      $("div.dt-buttons.btn-group").append('<a href="sales_credit_age_print.php?type=<?php echo $type ?>&cus=<?php echo $customer_id ?>&group=<?php echo $group ?>&lorry=<?php echo $lorry ?>&customer_type=<?php echo $customer_type ?>" title="Click to Print" class="btn btn-secondary buttons-html5">Print (Age)</a>');

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