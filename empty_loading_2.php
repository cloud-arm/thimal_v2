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
          Loading
          <small>Preview</small>
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">
        <?php
        $result = $db->prepare("SELECT * FROM loading WHERE transaction_id=:id  ");
        $result->bindParam(':id', $_GET['id']);
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

        <div class="row">
          <div class="col-md-12">

            <div class="box box-warning ">
              <div class="box-header with-border">
                <h3 class="box-title">Add Loading Product</h3>
                <?php if ($helper3 > '0') { ?>
                  <label style="margin-right: 50px;" class="pull-right"> Helper 3: <?php echo ucfirst($helper3); ?></label>
                <?php } ?>
              </div>

              <div class="box-body">

                <form method="POST" action="empty_loading_save_2.php">
                  <div class="row">

                    <div class="col-md-4">
                      <div class="form-group">
                        <label>Products</label>
                        <select class="form-control select2" name="product" style="width: 100%;" autofocus>
                          <?php
                          $result = $db->prepare("SELECT * FROM products  WHERE product_id BETWEEN 5 AND 9 ");
                          $result->bindParam(':userid', $res);
                          $result->execute();
                          for ($i = 0; $row = $result->fetch(); $i++) { ?>
                            <option value="<?php echo $row['product_id']; ?>"><?php echo $row['gen_name']; ?> </option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>

                    <div class="col-md-4">
                      <div class="form-group">
                        <label>QTY:</label>
                        <input type="number" class="form-control" min="0" value='' name="qty">
                      </div>
                    </div>

                    <div class="col-md-3" style="margin-top: 23px;">
                      <div class="form-group">
                        <input class="btn btn-info" type="submit" style="width: 100px;" value="Submit">
                        <input type="hidden" value='<?php echo $_GET['id']; ?>' name="id">
                      </div>
                    </div>
                  </div>
                </form>
              </div>

              <div class="box-body">
                <table id="example" class="table table-bordered table-hover">
                  <thead>
                    <tr>
                      <th>No</th>
                      <th>Item Name</th>
                      <th>QTY</th>
                      <th>#</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $result = $db->prepare("SELECT * FROM loading_list WHERE loading_id=:id ");
                    $result->bindParam(':id', $_GET['id']);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                    ?>
                      <tr class="record">
                        <td><?php echo $i + 1; ?></td>
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['qty']; ?> </td>
                        <td>
                          <a href="#" id="<?php echo $row['transaction_id']; ?>" class="btn btn-danger btn-sm btn_dll fa fa-trash" title="Click to Delete"></a>
                        </td>
                      </tr>
                    <?php
                    }
                    ?>
                  </tbody>
                </table>
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

    <?php
    $err = 'd-none';
    $err1 = 'd-none';
    $err2 = 'd-none';
    $err3 = 'd-none';
    $closer = '';
    if (isset($_GET['err'])) {
      if ($_GET['err'] == 1) {
        $err = '';
        $err2 = '';
        $closer = '<div class="container-close" onclick="click_close()"></div>';
      } else if ($_GET['err'] == 2) {
        $err = '';
        $err3 = '';
        $closer = '<div class="container-close" onclick="click_close()"></div>';
      } else {
        $err = 'd-none';
        $closer = '';
      }
      echo $_GET['err'];
    } ?>

    <div class="container-up <?php echo $err; ?>" id="container_up">
      <?php echo $closer; ?>
      <div class="row">
        <div class="col-md-12">

          <div class="box box-success popup  <?php echo $err2; ?>" style="padding: 5px;border: 0;">
            <div class="alert alert-dismissible" style="width: 350px;margin: 0;">
              <button type="button" class="close" onclick="click_close()" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4 class="text-red"><i class="icon fa fa-ban"></i> Alert!</h4>
              Stock qty and Loading qty are unbalanced ..!
            </div>
            <a href="#" class="btn btn-warning" onclick="click_close()" style="margin: 10px 0; width: 100%;"><b>Add New Item</b></a>
          </div>

          <div class="box box-success popup  <?php echo $err3; ?>" style="padding: 5px;border: 0;">
            <div class="alert alert-dismissible" style="width: 350px;margin: 0;">
              <button type="button" class="close" onclick="click_close()" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4 class="text-red"><i class="icon fa fa-ban"></i> Alert!</h4>
              This Item already ADD ..!
            </div>
            <a href="#" class="btn btn-warning" onclick="click_close()" style="margin: 10px 0; width: 100%;"><b>Add New Item</b></a>
          </div>

        </div>
      </div>
    </div>

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
    function click_open(i) {
      $("#popup_" + i).removeClass("d-none");
      $("#container_up").removeClass("d-none");
    }

    function click_close() {
      $(".popup").addClass("d-none");
      $("#container_up").addClass("d-none");
    }
  </script>

  <script type="text/javascript">
    $(function() {


      $(".btn_dll").click(function() {
        var element = $(this);
        var del_id = element.attr("id");
        var info = 'id=' + del_id;
        if (confirm("Sure you want to delete this Product? There is NO undo!")) {

          $.ajax({
            type: "GET",
            url: "empty_loading_dll.php",
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
      $("#example").DataTable();

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
    });
  </script>


</body>

</html>