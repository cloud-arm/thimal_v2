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
    $_SESSION['SESS_FORM'] = '6';
    date_default_timezone_set("Asia/Colombo");

    include_once("sidebar.php");

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Empty Loading
          <small>Preview</small>
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">

        <div class="row">
          <div class="col-md-12">
            <div class="box">
              <div class="box-header with-border">
                <h3 class="box-title">New Load</h3>
              </div>

              <div class="box-body">

                <form method="POST" action="empty_loading_save.php">

                  <div class="row">

                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Lorry</label>
                        <select class="form-control select2" name="lorry" style="width:100%">
                          <?php
                          $result = $db->prepare("SELECT * FROM lorry WHERE  action='unload'  ");
                          $result->bindParam(':userid', $res);
                          $result->execute();
                          for ($i = 0; $row = $result->fetch(); $i++) {
                          ?>
                            <option value="<?php echo $row['lorry_id']; ?>"><?php echo $row['lorry_no']; ?> </option>
                          <?php
                          }
                          ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Driver</label>
                        <select class="form-control select2" name="driver" style="width:100%" autofocus>
                          <?php
                          $result = $db->prepare("SELECT * FROM employee  WHERE des_id =1 AND action = 1 ");
                          $result->bindParam(':userid', $res);
                          $result->execute();
                          for ($i = 0; $row = $result->fetch(); $i++) {
                          ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['username']); ?> </option>
                          <?php  }  ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Helper 1</label>
                        <select class="form-control select2" name="h1" style="width:100%" autofocus>
                          <option value="0">Non</option>
                          <?php
                          $result = $db->prepare("SELECT * FROM employee WHERE action = 1  ");
                          $result->bindParam(':userid', $res);
                          $result->execute();
                          for ($i = 0; $row = $result->fetch(); $i++) {
                          ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['username']); ?> </option>
                          <?php  }  ?>
                        </select>
                      </div>
                    </div>

                  </div>

                  <div class="row">

                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Helper 2</label>
                        <select class="form-control select2" name="h2" style="width:100%" autofocus>
                          <option value="0">Non</option>
                          <?php
                          $result = $db->prepare("SELECT * FROM employee WHERE action = 1  ");
                          $result->bindParam(':userid', $res);
                          $result->execute();
                          for ($i = 0; $row = $result->fetch(); $i++) {
                          ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['username']); ?> </option>
                          <?php  }  ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group">
                        <label>Helper 3</label>
                        <select class="form-control select2" name="h3" style="width:100%" autofocus>
                          <option value="0">Non</option>
                          <?php
                          $result = $db->prepare("SELECT * FROM employee WHERE action = 1  ");
                          $result->bindParam(':userid', $res);
                          $result->execute();
                          for ($i = 0; $row = $result->fetch(); $i++) {
                          ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['username']); ?> </option>
                          <?php  }  ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-3">
                      <div class="form-group" style="margin-top: 22px;">
                        <input class="btn btn-info" type="submit" style="width: 100px;" value="Next">
                      </div>
                    </div>

                  </div>

                </form>

              </div>

            </div>
          </div>
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


  <script>
    $(function() {
      $("#example1").DataTable();
      $("#example2").DataTable();
      $('#example3').DataTable({
        "paging": false,
        "lengthChange": false,
        "searching": false,
        "ordering": false,
        "info": false,
        "autoWidth": false
      });
    });
  </script>


  <!-- Page script -->
  <script>
    $(function() {
      //Initialize Select2 Elements
      $(".select2").select2();
      $('select.hidden-search').select2({
        minimumResultsForSearch: -1
      });

      //Date range picker
      $('#reservation').daterangepicker();
      //Date range picker with time picker
      $('#reservationtime').daterangepicker({
        timePicker: true,
        timePickerIncrement: 30,
        format: 'YYYY/MM/DD h:mm A'
      });
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

      //iCheck for checkbox and radio inputs
      $('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
      });
      //Red color scheme for iCheck
      $('input[type="checkbox"].minimal-red, input[type="radio"].minimal-red').iCheck({
        checkboxClass: 'icheckbox_minimal-red',
        radioClass: 'iradio_minimal-red'
      });
      //Flat red color scheme for iCheck
      $('input[type="checkbox"].flat-red, input[type="radio"].flat-red').iCheck({
        checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'
      });

    });
  </script>


</body>

</html>