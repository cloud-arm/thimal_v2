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
    $_SESSION['SESS_FORM'] = '76';

    include_once("sidebar.php");

    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          CATEGORY
          <small>Preview</small>
        </h1>
      </section>

      <section class="content">

        <div class="row">
          <div class="col-md-10">

            <div class="box">
              <div class="box-header">
                <h3 class="box-title">Customer Category List</h3>
                <small class="btn btn-success btn-sm mx-2" style="padding: 5px 10px;" title="Add New Category" onclick="click_open(1)">Add New Category</small>
              </div>

              <div class="box-body">
                <table id="example1" class="table table-bordered  table-hover">
                  <thead>

                    <tr>
                      <th>id</th>
                      <th>Category</th>
                      <th>Customer Count</th>
                      <th>#</th>
                      <th>#</th>
                    </tr>
                  </thead>

                  <tbody>
                    <?php

                    $result = $db->prepare("SELECT * FROM customer_category ");
                    $result->bindParam(':id', $d);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                      $id = 0;
                      $res = $db->prepare("SELECT * FROM customer WHERE category = :id ");
                      $res->bindParam(':id', $row['id']);
                      $res->execute();
                      for ($i = 0; $ro = $res->fetch(); $i++) {
                        $id = $i + 1;
                      }
                    ?>
                      <tr class="record">

                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $id; ?></td>
                        <td>
                          <a href="customer_group.php?id=<?php echo $row['id']; ?>" title="Click to ADD" class="btn btn-primary btn-sm">
                            Add Customer GROUP
                          </a>
                        </td>
                        <td>
                          <a href="#" id="<?php echo $row['id']; ?>" title="Click to Delete" class="btn btn-danger btn-sm btn_dll">
                            <i class="fa fa-trash"></i>
                          </a>
                        </td>
                      </tr>
                    <?php  }  ?>
                  </tbody>


                </table>

              </div>
              <!-- /.box-body -->
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

    <div class="container-up d-none" id="container_up">
      <div class="container-close" onclick="click_close()"></div>
      <div class="row">
        <div class="col-md-12">

          <div class="box box-success popup d-none" id="popup_1" style="width: 500px;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                New Customer Category
                <i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="customer_category_save.php">

                <div class="row" style="display: block;">

                  <div class="col-md-9">
                    <div class="form-group">
                      <label>Category Name</label>
                      <input type="text" name="name" class="form-control">
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group" style="display: flex;justify-content:center">
                      <input type="hidden" name="unit" value="1">
                      <input type="submit" style="margin-top:23px;" value="Save" class="btn btn-info">
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
  <!-- ./wrapper -->


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
  <!-- page script -->
  <script>
    $(function() {
      $(".btn_dll").click(function() {
        var element = $(this);
        var del_id = element.attr("id");
        var info = 'id=' + del_id;
        if (confirm("Sure you want to delete this Customer Category? There is NO undo!")) {
          $.ajax({
            type: "GET",
            url: "customer_category_dll.php",
            data: info,
            success: function() {}
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