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
    $_SESSION['SESS_FORM'] = '73';

    include_once("sidebar.php");

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Root
          <small>Preview</small>
        </h1>
      </section>

      <section class="content">

        <div class="box box-success">
          <div class="box-header">
            <h3 class="box-title">Root Data</h3>
            <small class="btn btn-success mx-2" style="padding: 5px 10px;" title="Add Root" onclick="click_open(1,0)">Add Root</small>
          </div>
          <!-- /.box-header -->

          <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">

              <thead>
                <tr>
                  <th>Root ID.</th>
                  <th>Name</th>
                  <th>Area</th>
                  <th>#</th>
                </tr>
              </thead>

              <tbody>
                <?php

                $result = $db->prepare("SELECT * FROM root   ");
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                ?>
                  <tr class="record">
                    <td><?php echo $id = $row['root_id']; ?></td>
                    <td><?php echo $name = $row['root_name']; ?></td>
                    <td><?php echo $area = $row['root_area']; ?></td>
                    <td>
                      <a href="#" id="<?php echo $id; ?>" class="delbutton" title="Click to Delete">
                        <button class="btn btn-danger"><i class="fa fa-trash"></i></button>
                      </a>
                      <a onclick="click_open(2,'<?php echo $id; ?>')" href="#" title="Click to Edit">
                        <button class="btn btn-warning"><i class="fa fa-pencil"></i></button>
                      </a>
                      <a href="root_view.php?id=<?php echo $id; ?>" title="Click to View">
                        <button class="btn btn-success"><i class="fa fa-eye"></i></button>
                      </a>

                      <input type="hidden" id="root_name_<?php echo $id; ?>" value="<?php echo $name; ?>">
                      <input type="hidden" id="root_area_<?php echo $id; ?>" value="<?php echo $area; ?>">
                    </td>
                  </tr>
                <?php  } ?>

              </tbody>
            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->

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

          <div class="box box-success popup d-none" id="popup_1">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                New Root
                <i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="root_save.php">

                <div class="row" style="display: block;">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Root name</label>
                      <input type="text" name="root_name" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Area</label>
                      <select class="form-control select2 hidden-search" name="area" style="width: 100%;" autofocus>

                        <option value="Galle"> Galle </option>
                        <option value="Matara"> Matara </option>

                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="hidden" name="id" value="0">
                      <input type="submit" style="margin-top: 23px;" value="Save" class="btn btn-info">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <div class="box box-success popup d-none" id="popup_2">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                Update Root
                <i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="root_save.php">

                <div class="row" style="display: block;">
                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Root name</label>
                      <input type="text" name="root_name" id="popup_name" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <label>Area</label>
                      <select class="form-control select2 hidden-search" id="popup_area" name="area" style="width: 100%;" autofocus>

                        <option value="Galle"> Galle </option>
                        <option value="Matara"> Matara </option>

                      </select>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <div class="form-group">
                      <input type="hidden" id="popup_id" name="id" value="">
                      <input type="submit" style="margin-top: 23px;" value="Update" class="btn btn-info">
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


      let name = $('#root_name_' + id).val();
      let area = $('#root_area_' + id).val();

      $('#popup_id').val(id);
      $('#popup_name').val(name);
      $('#popup_area').val(area).change();

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

      //Initialize Select2 Elements
      $(".select2").select2();
      $('.select2.hidden-search').select2({
        minimumResultsForSearch: -1
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
        if (confirm("Sure you want to delete this Root? There is NO undo!")) {

          $.ajax({
            type: "GET",
            url: "root_dll.php",
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
</body>

</html>