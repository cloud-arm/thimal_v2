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
    $_SESSION['SESS_FORM'] = '72';

    include_once("sidebar.php");

    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Lorry
          <small>Preview</small>
        </h1>
      </section>

      <section class="content">

        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">Lorry Data</h3>
            <small class="btn btn-success btn-sm mx-2" style="padding: 5px 10px;" title="Add Lorry" onclick="click_open(1)">Add Lorry</small>
          </div>
          <!-- /.box-header -->

          <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">

              <thead>
                <tr>
                  <th>ID</th>
                  <th>Lorry No</th>
                  <th>Driver</th>
                  <th>Action</th>
                  <th>#</th>
                </tr>
              </thead>

              <tbody>
                <?php
                $result = $db->prepare("SELECT * FROM lorry   ");
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                ?>
                  <tr class="record">
                    <td><?php echo $row['lorry_id']; ?></td>
                    <td><?php echo $row['lorry_no']; ?></td>
                    <td><?php echo $row['driver']; ?></td>
                    <td><?php echo $row['action']; ?></td>
                    <td>
                      <a href="#" id="<?php echo $row['lorry_id']; ?>" class="delbutton" title="Click to Delete">
                        <button class="btn btn-danger"><i class="icon-trash">x</i></button></a>
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
      <a href="product.php" class="container-close" onclick="click_close()"></a>
      <div class="row">
        <div class="col-md-12">

          <div class="box box-success popup d-none" id="popup_1" style="width: 450px;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                New Lorry
                <i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="lorry_save.php">

                <div class="row" style="display: block;">
                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Lorry no</label>
                      <input type="text" name="lorry_no" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <label>Driver</label>
                      <select name="driver" class="form-control select2" style="width: 100%;">
                        <option value="0" disabled></option>
                        <?php
                        $result = $db->prepare("SELECT  * FROM employee WHERE des_id = 1 ORDER BY des_id  ");
                        $result->bindParam(':id', $date);
                        $result->execute();
                        for ($i = 0; $row = $result->fetch(); $i++) { ?>
                          <option value="<?php echo $row['id'] ?>"><?php echo ucfirst($row['username']); ?></option>
                        <?php } ?>
                        <option value="0">None</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-12">
                    <div class="form-group">
                      <input type="hidden" name="id" value="0">
                      <input type="submit" style="margin-top: 23px; width:100%" value="Save" class="btn btn-success">
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
    function click_open(i) {
      $("#popup_" + i).removeClass("d-none");
      $("#container_up").removeClass("d-none");
    }

    function click_close() {
      $(".popup").addClass("d-none");
      $("#container_up").addClass("d-none");
    }
  </script>
  <!-- page script -->
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

      //Initialize Select2 Elements
      $(".select2").select2();
      $('.select2.hidden-search').select2({
        minimumResultsForSearch: -1
      });
    });
  </script>

  <script type="text/javascript">
    function product_dll(id) {

      var info = 'id=' + id;
      if (confirm("Sure you want to delete this product? There is NO undo!")) {

        $.ajax({
          type: "GET",
          url: "lorry_dll.php",
          data: info,
          success: function() {}
        });

        $("#record_" + id).animate({
            backgroundColor: "#fbc7c7"
          }, "fast")
          .animate({
            opacity: "hide"
          }, "slow");

      }

      return false;

    }
  </script>
</body>

</html>