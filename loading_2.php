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
    $_SESSION['SESS_FORM'] = '4';

    include_once("sidebar.php");

    ?>

    <?php

    date_default_timezone_set("Asia/Colombo");

    $id = $_GET['id'];
    $result = $db->prepare("SELECT * FROM loading WHERE transaction_id='$id'  ");
    $result->bindParam(':userid', $res);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
      $root = $row['root'];
      $lorry = $row['lorry_no'];
      $lorry_id = $row['lorry_id'];
      $driver = $row['driver'];
      $helper1 = $row['helper1'];
      $helper2 = $row['helper2'];
      $helper3 = $row['helper3'];
    }

    $result = $db->prepare("SELECT * FROM employee  ");
    $result->bindParam(':userid', $res);
    $result->execute();
    for ($i = 0; $row = $result->fetch(); $i++) {
      if ($row['id'] == $driver) {
        $driver = $row['username'];
        $driver_pic = $row['pic'];
      }

      if ($row['id'] == $helper1) {
        $helper1 = $row['username'];
        $helper1_pic = $row['pic'];
      }

      if ($row['id'] == $helper2) {
        $helper2 = $row['username'];
        $helper2_pic = $row['pic'];
      }

      if ($row['id'] == $helper3) {
        $helper3 = $row['username'];
        $helper3_pic = $row['pic'];
      }
    }

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Loading
          <small>Preview</small>
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">

        <div class="row">
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-yellow"><i class="fa fa-truck"></i></span>

              <div class="info-box-content">
                <span class="info-box-text">Lorry Number</span>
                <span class="info-box-number" style="margin-top: 10px;"><?php echo $lorry; ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-yellow">
                <div class="info-box-img">
                  <img src="<?php echo $driver_pic; ?>" alt="">
                </div>
              </span>

              <div class="info-box-content">
                <span class="info-box-text">Driver</span>
                <span class="info-box-number" style="margin-top: 10px;"><?php echo ucfirst($driver); ?></span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->

          <!-- fix for small devices only -->
          <div class="clearfix visible-sm-block"></div>

          <?php if ($helper1 > '0') { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-light">
                  <div class="info-box-img">
                    <img src="<?php echo $helper1_pic; ?>" alt="">
                  </div>
                </span>

                <div class="info-box-content">
                  <span class="info-box-text">Helper-1</span>
                  <span class="info-box-number" style="margin-top: 10px;"><?php echo ucfirst($helper1); ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
          <?php } ?>

          <?php if ($helper2 > '0') { ?>
            <div class="col-md-3 col-sm-6 col-xs-12">
              <div class="info-box">
                <span class="info-box-icon bg-light">
                  <div class="info-box-img">
                    <img src="<?php echo $helper2_pic; ?>" alt="">
                  </div>
                </span>

                <div class="info-box-content">
                  <span class="info-box-text">Helper-2</span>
                  <span class="info-box-number" style="margin-top: 10px;"><?php echo ucfirst($helper2); ?></span>
                </div>
                <!-- /.info-box-content -->
              </div>
              <!-- /.info-box -->
            </div>
          <?php } ?>
          <!-- /.col -->
        </div>

        <!-- SELECT2 EXAMPLE -->
        <div class="box box-warning">
          <div class="box-header with-border">
            <h3 class="box-title">Add Loading Product</h3>
            <label style="margin-right: 50px;" class="pull-right"> Root: <?php echo $root; ?></label>
            <?php if ($helper3 > '0') { ?>
              <label style="margin-right: 50px;" class="pull-right"> Helper 3: <?php echo ucfirst($helper3); ?></label>
            <?php } ?>
          </div>

          <div class="box-body">

            <form method="post" action="loading_save_2.php">

              <div class="row">

                <div class="col-md-2"></div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Products</label>
                    <select class="form-control select2" name="product" data-placeholder="Select a Product" style="width: 100%;" autofocus>
                      <?php
                      $result = $db->prepare("SELECT * FROM products  WHERE product_name>=1 ");
                      $result->bindParam(':id', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option value="<?php echo $row['product_id']; ?>"><?php echo $row['gen_name']; ?> </option>
                      <?php
                      }
                      ?>

                      <?php
                      $result = $db->prepare("SELECT * FROM products  WHERE product_id>=9 ");
                      $result->bindParam(':id', $res);
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option value="<?php echo $row['product_id']; ?>"><?php echo $row['gen_name']; ?> </option>
                      <?php
                      }
                      ?>


                    </select>
                  </div>
                </div>

                <div class="col-md-3">
                  <div class="form-group">
                    <label>QTY:</label>
                    <input type="number" value='' class="form-control" name="qty">
                  </div>
                </div>

                <div class="col-md-2" style="margin-top: 23px;">
                  <div class="form-group">
                    <input type="hidden" value='<?php echo $invoice = $_GET['id']; ?>' name="id">
                    <input class="btn btn-info" type="submit" value="Submit">
                  </div>
                </div>

              </div>

            </form>

          </div>

          <div class="box-body">
            <table id="example2" class="table table-bordered">
              <thead>
                <tr>
                  <th>Item Name</th>
                  <th>QTY</th>
                  <th>#</th>

                </tr>
              </thead>
              <tbody>
                <?php
                $result = $db->prepare("SELECT * FROM loading_list WHERE loading_id='$id' ");
                $result->bindParam(':userid', $res);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                ?>
                  <tr class="record">
                    <td><?php echo $row['product_name']; ?></td>
                    <td><?php echo $row['qty']; ?> </td>
                    <td> <a href="#" id="<?php echo $row['transaction_id']; ?>" class="delbutton" title="Click to Delete">
                        <button class="btn btn-danger"><i class="icon-trash">x</i></button></a></td>

                  </tr>
                <?php
                }
                ?>


              </tbody>

            </table>
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
  <!-- ./wrapper -->
  <script src="js/jquery.js"></script>
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

  <script type="text/javascript">
    $(function() {


      $(".delbutton").click(function() {

        //Save the link in a variable called element
        var element = $(this);

        //Find the id of the link that was clicked
        var del_id = element.attr("id");

        //Built a url to send
        var info = 'id=' + del_id;
        if (confirm("Sure you want to delete this Product? There is NO undo!")) {

          $.ajax({
            type: "GET",
            url: "loading_dll.php",
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
  </script>

  <!-- Page script -->
  <script>
    $(function() {
      //Initialize Select2 Elements
      $(".select2").select2();

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