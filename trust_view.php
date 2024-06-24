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
    $_SESSION['SESS_FORM'] = '80';

    include_once("sidebar.php");

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Trust
          <small>Preview</small>
        </h1>
      </section>

      <section class="content">

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">Trust Data</h3>
          </div>
          <!-- /.box-header -->

          <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">

              <thead>
                <tr>
                  <th>ID</th>
                  <th>Customer Name</th>
                  <th>Product</th>
                  <th>Qty</th>
                  <th>Receive Qty</th>
                  <th>Date</th>
                  <th>End Date</th>
                  <th>comment</th>
                  <th>Last Update</th>
                  <th>Type</th>
                  <th>status</th>
                  <th>Clear</th>
                </tr>
              </thead>

              <tbody>
                <?php

                $result = $db->prepare("SELECT * FROM trust   ");
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                  $cus_id = $row['customer_id'];
                  $id = $row['transaction_id'];
                ?>
                  <tr class="record">
                    <td><?php echo $row['transaction_id']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['product']; ?> </td>
                    <td><?php echo $row['qty']; ?> </td>
                    <td><?php echo $row['qty_receive']; ?> </td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['end_date']; ?></td>
                    <td><?php echo $row['comment']; ?></td>
                    <td>
                      <?php
                      $result1 = $db->prepare("SELECT * FROM sales WHERE customer_id='$cus_id' and action='1' ORDER by transaction_id DESC limit 0,1 ");
                      $result1->bindParam(':userid', $res);
                      $result1->execute();
                      for ($i = 0; $row1 = $result1->fetch(); $i++) {
                        echo $row1['date'];
                      } ?>
                    </td>
                    <td><small class="label pull-right bg-purple"><?php echo $row['type']; ?></small></td>
                    <td>
                      <?php $dr = $row['status'];
                      if ($dr == "active") { ?>

                        <button class="btn btn-warning"><i class=" glyphicon glyphicon-cog fa-spin"></i></button>

                      <?php }
                      $dr = $row['status'];
                      if ($dr == "processing") { ?>

                        <button class="btn btn-success"><i class=" glyphicon glyphicon-refresh fa-spin"></i></button>

                      <?php }
                      $dr = $row['status'];
                      if ($dr == "clear") { ?>

                        <button class="btn btn-danger"><i class="glyphicon glyphicon-refresh fa-spin"></i></button>

                      <?php } ?>
                    </td>
                    <td>
                      <?php if ($dr == "active" | $dr == "processing") { ?>
                        <a href="#" onclick="click_open(1,<?php echo $row['transaction_id']; ?>)"> <button class="btn btn-warning"><i class="icon-trash">Receive</i></button></a>
                      <?php } ?>
                    </td>

                  </tr>

                <?php } ?>


              </tbody>
              <tfoot>

              </tfoot>
            </table>
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


    <div class="container-up d-none" id="container_up">
      <div class="container-close" onclick="click_close()"></div>
      <div class="row">
        <div class="col-md-12">

          <div class="box box-success popup d-none" style="width: 400px;" id="popup_1">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                Trust Receive
                <i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="trust_receive_save.php">

                <div class="row" style="display: block;">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Location</label>
                      <select class="form-control select2 hidden-search" name="location" style="width: 100%;">

                        <option>Yard</option>

                      </select>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Receive Qty</label>
                      <input type="text" name="qty" class="form-control">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="hidden" name="id" id="trust_id" value="0">
                      <input type="submit" style="margin-top: 23px; width: 100%;" value="Receive" class="btn btn-info">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

        </div>
      </div>
    </div>


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
  <!-- page script -->
  <script>
    function click_open(i, id) {
      $("#popup_" + i).removeClass("d-none");
      $("#container_up").removeClass("d-none");

      if (i == 1) {
        $('#trust_id').val(id);
      }
    }

    function click_close() {
      $(".popup").addClass("d-none");
      $("#container_up").addClass("d-none");
    }
  </script>
  <script>
    $(function() {
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
  </script>

  <script>
    $(function() {
      //Initialize Select2 Elements
      $(".select2").select2();
      $('.select2.hidden-search').select2({
        minimumResultsForSearch: -1
      });
    })
  </script>

</body>

</html>