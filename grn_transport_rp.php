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
    $_SESSION['SESS_FORM'] = '64';

    include_once("sidebar.php");

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          TRANSPORT
          <small>Report</small>
        </h1>
      </section>
      <!-- Main content -->
      <section class="content">

        <?php
        include("connect.php");
        date_default_timezone_set("Asia/Colombo");

        if (isset($_GET['lorry'])) {

          $lorry = $_GET['lorry'];
          $dates = $_GET['dates'];
        } else {
          $dates = date('Y/m/d-Y/m/d');
        }
        $d1 = date_format(date_create(explode("-", $dates)[0]), "Y-m-d");
        $d2 = date_format(date_create(explode("-", $dates)[1]), "Y-m-d");
        ?>

        <div class="row">
          <div class="col-md-7">
            <div class="box">
              <div class="box-header with-border">
                <h3 class="box-title">Date Selector</h3>
              </div>

              <div class="box-body">
                <form action="" method="GET">
                  <div class="row" style="margin-bottom: 20px;display: flex;align-items: end;">
                    <div class="col-lg-1"></div>
                    <div class="col-lg-4">
                      <label>Lorry No:</label>
                      <div class="input-group">
                        <div class="input-group-addon">
                          <i class="fa fa-truck"></i>
                        </div>
                        <select class="form-control select2" name="lorry" style="width: 100%;" tabindex="8">
                          <option value="0" disabled selected></option>
                          <?php $l = 0;
                          if (isset($_GET['lorry'])) {
                            $l = $_GET['lorry'];
                          }
                          $result = $db->prepare("SELECT * FROM lorry  WHERE type = '0' ");
                          $result->bindParam(':id', $res);
                          $result->execute();
                          for ($i = 0; $row = $result->fetch(); $i++) {
                          ?>
                            <option value="<?php echo $row['lorry_id']; ?>" <?php if ($l == $row['lorry_id']) { ?> selected <?php } ?>><?php echo $row['lorry_no']; ?> </option>
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
                      <input type="submit" class="btn btn-info" value="Search">
                    </div>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <?php if (isset($_GET['lorry'])) {
            $total_gross = 0;
            $total_exp = 0;

            $sql1 = "SELECT * FROM transport_record WHERE lorry_id = '$lorry' AND date BETWEEN '$d1' AND '$d2'";
            $sql2 = " SELECT * FROM expenses_records  WHERE type_id = 3 AND date BETWEEN '$d1' AND '$d2' ";

            $result = $db->prepare($sql1);
            $result->bindParam(':userid', $date);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
              $total_gross += $row['amount'];
            }

            $result = $db->prepare($sql2);
            $result->bindParam(':userid', $date);
            $result->execute();
            for ($i = 0; $row = $result->fetch(); $i++) {
              $total_exp += $row['amount'];
            }
          ?>
            <div class="col-md-5">
              <div class="box">
                <div class="box-header with-border">
                  <h3 class="box-title">Payment Summary</h3>
                </div>
                <div class="box-body">

                  <h4>Total Gross: <small style="margin-right: 20px;">Rs.</small> <?php echo number_format($total_gross, 2); ?></h4>
                  <h4>Total Expenses: <small style="margin-right: 20px;">Rs.</small> <?php echo number_format($total_exp, 2); ?></h4>
                  <h3>NET Profit: <small style="margin-right: 20px;">Rs.</small> <?php echo number_format($total_gross - $total_exp, 2); ?></h3>

                </div>
              </div>
            </div>
          <?php } ?>
        </div>

        <div class="box box-info">

          <div class="box-header with-border">
            <h3 class="box-title" style="text-transform: capitalize;">Transport Payments</h3>
          </div>

          <div class="box-body d-block">
            <table id="example" class="table table-bordered" style="border-radius: 0;">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Invoice</th>
                  <th>Supplier</th>
                  <th>Lorry No</th>
                  <th>Date</th>
                  <th>Amount</th>
                </tr>
              </thead>
              <tbody>
                <?php $total_gross = 0;
                if (isset($_GET['lorry'])) {

                  $result = $db->prepare($sql1);
                  $result->bindParam(':userid', $date);
                  $result->execute();
                  for ($i = 0; $row = $result->fetch(); $i++) {
                    $dll = $row['dll'];
                    if ($dll == 0) { ?>
                      <tr>
                        <td><?php echo $row['id']  ?></td>
                        <td><?php echo $row['invoice_no']  ?></td>
                        <td><?php echo $row['supplier_name']  ?></td>
                        <td><?php echo $row['lorry_no']  ?></td>
                        <td><?php echo $row['date']  ?></td>
                        <td><?php echo $row['amount'];
                            $total_gross += $row['amount'];  ?></td>
                      </tr>
                <?php }
                  }
                } ?>

              </tbody>
              <tfoot>
              </tfoot>
            </table>
            <div style="padding-left: 25px;margin-top: 20px;">
              <h4>Total: <small> Rs. </small> <?php echo number_format($total_gross, 2); ?> </h4>
            </div>
          </div>

        </div>

        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Expenses Payment</h3>
          </div>

          <div class="box-body d-block">
            <table id="example1" class="table table-bordered " style="border-radius: 0;">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Date</th>
                  <th>Type</th>
                  <th>Comment</th>
                  <th>Pay Type</th>
                  <th>Chq Details</th>
                  <th>Amount (Rs.)</th>
                  <th>#</th>
                </tr>
              </thead>
              <tbody>

                <?php $total_exp = 0;
                if (isset($_GET['lorry'])) {
                  $result = $db->prepare($sql2);
                  $result->bindParam(':userid', $date);
                  $result->execute();
                  for ($i = 0; $row = $result->fetch(); $i++) {
                    $dll = $row['dll'];
                    $type = $row['type_id'];
                    if ($dll == 1) {
                      $style = 'opacity: 0.5;cursor: default;';
                    } else {
                      $style = '';
                    }
                ?>

                    <tr class="record" style="<?php echo $style; ?>">
                      <td><?php echo $row['id'];   ?> </td>
                      <td><?php echo $row['date'];   ?> </td>
                      <td>
                        <?php echo $row['type']; ?>
                        <br> <span class="badge bg-green"><?php echo $row['lorry_no']; ?> </span>
                      </td>
                      <td><?php echo $row['comment'];   ?></td>
                      <td><?php echo $row['pay_type'];   ?></td>
                      <td>
                        NO: <span class="badge bg-blue"><?php echo $row['chq_no']; ?> </span> <br>
                        Date: <span class="badge bg-green"><?php echo $row['chq_date']; ?> </span> <br>
                      </td>
                      <td>Rs.<?php echo $row['amount'];
                              $total_exp += $row['amount'];  ?>
                      </td>
                      <td> <?php if ($dll == 0) { ?> <a href="#" id="<?php echo $row['id']; ?>" class="delbutton btn btn-danger btn-sm" title="Click to Delete">
                            <i class="icon-trash">x</i></a><?php } ?>
                      </td>
                    </tr>
                <?php }
                } ?>
              </tbody>
            </table>
            <div style="padding-left: 25px;margin-top: 20px;">
              <h4>Total: <small> Rs. </small> <?php echo number_format($total_exp, 2); ?> </h4>
            </div>
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
      $("#example").DataTable();
      $("#example1").DataTable();
      $('#example2').DataTable({
        "paging": true,
        "lengthChange": false,
        "searching": false,
        "ordering": true,
        "info": true,
        "autoWidth": false
      });
    });
    $(function() {


      $(".delbutton").click(function() {

        //Save the link in a variable called element
        var element = $(this);

        //Find the id of the link that was clicked
        var del_id = element.attr("id");

        //Built a url to send
        var info = 'id=' + del_id;
        if (confirm("Sure you want to delete this Collection? There is NO undo!")) {

          $.ajax({
            type: "GET",
            url: "expenses_dll.php",
            data: info,
            success: function() {}
          });
          $(this).parents(".record").css({
            'opacity': '0.5',
            'cursor': 'default'
          })
          $(this).remove();

        }

        return false;

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