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
    $_SESSION['SESS_FORM'] = '21';

    include_once("sidebar.php");

    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Credit Payment Report
          <small>Preview</small>
        </h1>
      </section>


      <section class="content">

        <div class="box">
          <div class="box-header">
            <h3 class="box-title"> Credit Payment List </h3>
            <a href="credit_collection_print.php" title="Click to Print" style="margin-left: 10px;">
              <button class="btn btn-sm btn-danger" disabled><i class="fa fa-print"></i> Print</button>
            </a>
          </div>
          <!-- /.box-header -->

          <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Invoice no </th>
                  <th>Loading</th>
                  <th>Driver</th>
                  <th>Pay type</th>
                  <th>Chq no</th>
                  <th>Chq Date</th>
                  <th>Bank</th>
                  <th>Amount </th>
                  <th>#</th>
                </tr>
              </thead>
              <tbody>
                <?php
                include("connect.php");
                $tot = 0;
                $result = $db->prepare("SELECT * FROM collection WHERE   type ='0' AND action='0'  ORDER by id DESC");
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                  $user = $row['user_id'];
                  $loading = $row['loading_id'];
                  $result1 = $db->prepare("SELECT * FROM user WHERE EmployeeId ='$user' ");
                  $result1->bindParam(':userid', $date);
                  $result1->execute();
                  for ($i = 0; $row1 = $result1->fetch(); $i++) {
                    $dir = $row1['username'];
                  }

                  $action = 'unload';
                  $result1 = $db->prepare("SELECT * FROM loading WHERE transaction_id ='$loading' ");
                  $result1->bindParam(':userid', $loading);
                  $result1->execute();
                  for ($i = 0; $row1 = $result1->fetch(); $i++) {
                    $action = $row1['action'];
                    $lorry = $row1['lorry_no'];
                  }
                  if ($action == "load") { ?>
                    <tr style="background-color: rgba(var(--bg-text-dark-100), 0.1);opacity: 0.8;">
                    <?php } else { ?>
                    <tr>
                    <?php } ?>

                    <td><?php echo $row['invoice_no']; ?></td>
                    <td>
                      <span class="badge bg-blue"> <i class="fa fa-truck"></i> <?php echo $lorry; ?> </span> <br>
                      <a href="loading_view.php?id=<?php echo $loading ?>" class="badge bg-green">Loading ID: <?php echo $loading ?></a>
                    </td>
                    <td><?php echo $dir; ?></td>
                    <td><?php echo $pay_type = $row['pay_type']; ?></td>
                    <td><?php echo $row['chq_no']; ?></td>
                    <td><?php echo $row['chq_date']; ?></td>
                    <td><?php echo $row['bank']; ?></td>
                    <td><?php echo $row['amount']; ?></td>
                    <td>
                      <?php
                      if ($action == "unload") {
                        if ($pay_type == "chq") { ?>

                          <a href="#" onclick="click_open(1,<?php echo $row['id']; ?>)" class="btn btn-primary btn-sm"><i class="fa fa-pencil"></i></a>

                        <?php } ?>

                        <a href="credit_collection_save.php?id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm"><i class="fa fa-connectdevelop fa-info"></i></a>

                      <?php } else {  ?>
                        <i class="fa fa-refresh fa-spin"></i>
                      <?php }  ?>
                    </td>
                    </tr>
                  <?php $tot += $row['amount'];
                }
                  ?>

              </tbody>
              <tfoot>
              </tfoot>
            </table>

            <h3>Total: <small>Rs.</small> <?php echo $tot; ?> </h3>

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

          <div class="box box-success popup d-none" id="popup_1" style="width: 700px;">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                Edit Payment
                <i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block" id="credit_edit">

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
    function click_open(i, id) {
      $("#popup_" + i).removeClass("d-none");
      $("#container_up").removeClass("d-none");


      var xmlhttp;
      if (window.XMLHttpRequest) {
        xmlhttp = new XMLHttpRequest();
      } else {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      }
      xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          document.getElementById("credit_edit").innerHTML = xmlhttp.responseText;
        }
      }

      xmlhttp.open("GET", "credit_collection_edit.php?id=" + id, true);
      xmlhttp.send();

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

      $(".select2").select2();

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