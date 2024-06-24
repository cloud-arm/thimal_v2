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
    $_SESSION['SESS_FORM'] = '68';

    include_once("sidebar.php");

    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          DAMAGE
          <small>Preview</small>
        </h1>
      </section>

      <section class="content">

        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Damage Data</h3>
          </div>
          <!-- /.box-header -->

          <div class="box-body">
            <table id="example1" class="table table-bordered table-striped">

              <thead>
                <tr>
                  <th>No</th>
                  <th>Customer Name</th>
                  <th>Complain no</th>
                  <th>Product</th>
                  <th>Date</th>
                  <th>Comment</th>
                  <th>Action</th>
                  <th>Type</th>
                  <th>#</th>
                </tr>
              </thead>

              <tbody>
                <?php

                $result = $db->prepare("SELECT * FROM damage  WHERE dll=0 ");
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {

                  $rate = $row['complain_no'];
                  $id = $row['cylinder_no'];

                  if ($row['type'] == "damage") {
                    $type = 'Damage';
                    $crl2 = 'red';
                  } else 

                if ($row['type'] == "clear") {
                    $type = 'Clear';
                    $crl2 = 'maroon';
                  } else 

                if ($row['type'] == "complete") {
                    $type = 'Complete';
                    $crl2 = 'navy';
                  }

                  if ($row['action'] == "register") {
                    $action = 'Register';
                    $crl1 = 'purple';
                  } else

                if ($row['action'] == "sent_company") {
                    $action = 'Sent Company';
                    $crl1 = 'orange';
                  } else

                if ($row['action'] == "delivery_to_customer") {
                    $action = 'Delivery To Customer';
                    $crl1 = 'green';
                  } else

                if ($row['action'] == "receive_yard") {
                    $action = 'Receive To Yard';
                    $crl1 = 'primary';
                  }
                ?>
                  <tr class="record">
                    <td><?php echo $i + 1; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['complain_no']; ?></td>
                    <td><?php echo $row['cylinder_type']; ?></td>
                    <td><?php echo $row['date']; ?></td>
                    <td><?php echo $row['comment']; ?></td>
                    <td>
                      <span class="label pull-right bg-<?php echo $crl1; ?>"><?php echo $action; ?></span>
                    </td>
                    <td>
                      <span class="label pull-right bg-<?php echo $crl2; ?>"><?php echo $type; ?></span>
                    </td>
                    <td style="width: 75px;">
                      <?php if ($row['position'] == 1) { ?>
                        <a href="#" title="Send to Company" onclick="click_open(1,'<?php echo $row['complain_no']; ?>')" class="btn btn-sm btn-warning">
                          <i class="fa fa-send"></i>
                        </a>
                      <?php } ?>
                      <?php if ($row['position'] == 2) { ?>
                        <a href="#" title="Damage Receiver" onclick="click_open(2,'<?php echo $row['complain_no']; ?>')" class="btn btn-sm btn-primary">
                          <i class="fa fa-reply"></i>
                        </a>
                      <?php } ?>
                      <?php if ($row['position'] == 3) { ?>
                        <a href="#" title="Send to Customer" onclick="click_open(3,'<?php echo $row['complain_no']; ?>')" class="btn btn-sm btn-success">
                          <i class="fa fa-recycle"></i>
                        </a>
                      <?php } ?>
                      <a href="damage_profile.php?id=<?php echo $row['complain_no']; ?>" title="Click to View" class="btn btn-sm btn-info">
                        <i class="fa fa-user"></i>
                      </a>
                    </td>
                  </tr>
                <?php } ?>
              </tbody>

              <tfoot></tfoot>
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

          <div class="box box-success popup d-none" id="popup_1">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                Sent Damage to The Company
                <i onclick="click_close()" class="btn pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="damage_company_save.php">

                <div class="row" style="display: block;">

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Complain No</label>
                      <input type="text" name="complain_no" value="" class="form-control comp_no" readonly>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Location</label>
                      <select class="form-control select2 hidden-search" name="location" style="width: 100%;" autofocus>
                        <option>Mabima</option>
                        <option>Hambanthoata</option>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Lorry No</label>
                      <select class="form-control select2" name="lorry" style="width: 100%;" autofocus>
                        <?php
                        include("connect.php");
                        $result = $db->prepare("SELECT * FROM lorry  ");
                        $result->bindParam(':id', $res);
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
                      <input type="submit" style="margin-top: 23px;" value="Send to Company" class="btn btn-warning">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <div class="box box-success popup d-none" id="popup_2">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                Damage Receive
                <i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="damage_receive_save.php">

                <div class="row" style="display: block;">

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Complain No</label>
                      <input type="text" name="complain_no" value="" class="form-control comp_no" readonly>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <input type="submit" style="margin-top: 23px;" value="Receive to Yard" class="btn btn-primary">
                    </div>
                  </div>
                </div>

              </form>
            </div>
          </div>

          <div class="box box-success popup d-none" id="popup_3">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                Sent Damage to The Customer
                <i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="damage_customer_save.php">

                <div class="row" style="display: block;">

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Complain No</label>
                      <input type="text" name="complain_no" value="" class="form-control comp_no" readonly>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <input type="submit" style="margin-top: 23px;" value="Receive to Customer" class="btn btn-success">
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


  <?php include_once("script.php"); ?>

  <!-- DataTables -->
  <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
  <!-- date-range-picker -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
  <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
  <!-- bootstrap datepicker -->
  <script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>
  <!-- FastClick -->
  <script src="../../plugins/fastclick/fastclick.js"></script>


  <script>
    function click_open(i, id) {

      $('.comp_no').val(id);

      $("#popup_" + i).removeClass("d-none");
      $("#container_up").removeClass("d-none");
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

      //Date picker
      $('#datepicker').datepicker({
        autoclose: true,
        datepicker: true,
        format: 'yyyy-mm-dd '
      });

    });
  </script>

</body>

</html>