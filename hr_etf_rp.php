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
    $_SESSION['SESS_FORM'] = '18';

    include_once("sidebar.php");

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          ETF PAYMENT
          <small>List</small>
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">

        <div class="box bg-none">
          <div class="box-body">
            <form action="" method="GET">
              <div class="row flex-center">
                <div class="col-lg-1"></div>
                <div class="col-md-2">
                  <div class="form-group">
                    <select class="form-control select2" name="year" style="width: 100%;" tabindex="1" autofocus>
                      <option> <?php echo date('Y') - 1 ?> </option>
                      <option selected> <?php echo date('Y') ?> </option>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <select class="form-control select2" name="month" style="width: 100%;" tabindex="1" autofocus>
                      <?php for ($x = 1; $x <= 12; $x++) { ?>
                        <option> <?php echo sprintf("%02d", $x); ?> </option>
                      <?php  } ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-2">
                  <div class="form-group">
                    <input class="btn btn-info" type="submit" value="Submit">
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
        <!-- All jobs -->

        <?php
        include("connect.php");
        date_default_timezone_set("Asia/Colombo");

        $year = date('Y');
        $month = date('m');
        $m = date('Y-m');

        if (isset($_GET['year'])) {
          $year = $_GET['year'];
          $month = $_GET['month'];
          $m = $_GET['year'] . '-' . $_GET['month'];
        }

        $sql = "SELECT * FROM `hr_payroll` WHERE date='$m' ";

        ?>
        <div class="box box-info">

          <div class="box-header with-border">
            <h3 class="box-title" style="text-transform: capitalize;">ETF Payments</h3>

            <a href="hr_etf_rp_print.php?year=<?php echo $year; ?>&month=<?php echo $month; ?>" class="btn btn-info btn-sm" style="width: 100px; margin-left:10px;">
              <i class="fa fa-print" style="margin-right: 5px;"></i> Print
            </a>
          </div>

          <div class="box-body">
            <table id="" class="table table-bordered">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Employees Name</th>
                  <th>NIC</th>
                  <th>Member No</th>
                  <th>Contribution</th>
                </tr>
              </thead>
              <tbody>
                <?php
                $total = 0;
                $a = 1;
                $result = $db->prepare($sql);
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {

                  $emp_id = $row['emp_id'];
                  $etf = $row['etf'];

                  $rq = $db->prepare("SELECT * FROM Employees WHERE id='$emp_id' ");
                  $rq->bindParam(':userid', $date);
                  $rq->execute();
                  for ($k = 0; $r = $rq->fetch(); $k++) {
                    $name = $r['name'];
                    $nic = $r['nic'];
                    $id = $r['epf_no'];
                  }
                ?>
                  <tr>
                    <td><?php echo $a;
                        $a++ ?></td>
                    <td><?php echo $name  ?></td>
                    <td><?php echo $nic;  ?></td>
                    <td><?php echo $id  ?></td>
                    <td><?php echo $etf;
                        $total += $etf; ?></td>
                  </tr>
                <?php } ?>

              </tbody>
              <tbody style="border-top: 0;">
                <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>
                    <h4> Total</h4>
                  </td>
                  <td>
                    <h4>Rs.<?php echo $total; ?>.00</h4>
                  </td>
                </tr>
              </tbody>
            </table>

          </div>

        </div>
      </section>

    </div>

    <!-- /.content-wrapper -->
    <?php
    include("dounbr.php");
    ?>
    <div class="control-sidebar-bg"></div>

    <!-- ./wrapper -->

    <!-- jQuery 2.2.3 -->
    <!-- jQuery 2.2.3 -->
    <script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
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



    <!-- Select2 -->
    <script src="../../plugins/select2/select2.full.min.js"></script>
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap datepicker -->
    <script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>
    <!-- bootstrap color picker -->

    <!-- Dark Theme Btn-->
    <script src="https://dev.colorbiz.org/ashen/cdn/main/dist/js/DarkTheme.js"></script>



    <script type="text/javascript">
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
              url: "#",
              data: info,
              success: function() {

              }
            });
            $(this).parents(".record").animate({
                backgroundColor: "#fbc7c7"
              }, "fast")
              .animate({
                opacity: "hide"
              }, "slow");

          }

          return false;

        });

      });



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