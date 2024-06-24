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
    date_default_timezone_set("Asia/Colombo");

    include_once("sidebar.php");

    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Add Customer for Group
          <small>Preview</small>
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">

        <!-- SELECT2 EXAMPLE -->
        <div class="box box-warning ">
          <div class="box-header with-border">
            <h3 class="box-title">Add Customer</h3>
            <a href="customer_category.php" class="btn btn-warning btn-sm" style="width: 100px;margin-left: 20px;">Back</a>
          </div>

          <div class="box-body">

            <form method="post" action="customer_group_add.php">
              <div class="row" style="display: flex; justify-content: space-around;">

                <div class="col-md-5">
                  <div class="form-group">
                    <label>Customer</label>
                    <select class="form-control select2" name="cus" style="width: 100%;" autofocus>
                      <?php
                      $result = $db->prepare("SELECT * FROM customer WHERE  category = 0 ");
                      $result->bindParam(':id', $_GET['id']);
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

                <div class="col-md-2">
                  <div class="form-group" style="margin-top: 23px;">
                    <input type="hidden" value='<?php echo $id = $_GET['id']; ?>' name="id">
                    <input class="btn btn-info" type="submit" value="Add Customer">
                  </div>
                </div>

                <div class="col-md-4">
                  <h2 style="text-align: end;">
                    <?php $result2 = $db->prepare("SELECT * FROM customer_category WHERE id='$id' ");
                    $result2->bindParam(':userid', $d2);
                    $result2->execute();
                    for ($i = 0; $row2 = $result2->fetch(); $i++) {
                      echo $row2['name'];
                    } ?>
                  </h2>
                </div>
              </div>
            </form>

            <div class="box-body">
              <table id="example" class="table table-bordered table-hover">
                <thead>
                  <tr>
                    <th> ID</th>
                    <th> Name</th>
                    <th>Address</th>
                    <th>Root</th>
                    <th>Area</th>
                    <th>#</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $result = $db->prepare("SELECT * FROM customer WHERE category='$id' ");
                  $result->bindParam(':userid', $res);
                  $result->execute();
                  for ($i = 0; $row = $result->fetch(); $i++) {
                  ?>
                    <tr class="record">
                      <td><?php echo $row['customer_id']; ?></td>
                      <td><?php echo $row['customer_name']; ?> </td>
                      <td><?php echo $row['address']; ?> </td>
                      <td><?php echo $row['root']; ?> </td>
                      <td><?php echo $row['area']; ?> </td>
                      <td>
                        <a href="#" id="<?php echo $row['customer_id']; ?>" title="Click to Delete" class="btn btn-danger btn-sm btn_dll">
                          <i class="fa fa-trash"></i>
                        </a>
                      </td>
                    </tr>
                  <?php } ?>
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
      $(".btn_dll").click(function() {
        var element = $(this);
        var del_id = element.attr("id");
        var info = 'id=' + del_id;
        if (confirm("Sure you want to delete this customer? There is NO undo!")) {

          $.ajax({
            type: "GET",
            url: "customer_group_dll.php",
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