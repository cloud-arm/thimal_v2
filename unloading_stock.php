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
    $_SESSION['SESS_FORM'] = '7';


    include_once("sidebar.php");

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
          Unloading
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
        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Unloading Product</h3>
            <label style="margin-right: 50px;" class="pull-right"> <?php echo $root; ?></label>
            <?php if ($helper3 > '0') { ?>
              <label style="margin-right: 50px;" class="pull-right"> Helper 3: <?php echo ucfirst($helper3); ?></label>
            <?php } ?>
          </div>

          <div class="box-body">
            <div class="row">

              <div class="box-body">
                <table id="example2" class="table table-bordered">
                  <thead>
                    <tr>
                      <th>Item Name</th>
                      <th>Load QTY</th>
                      <th>Available QTY</th>
                      <th>Unload QTY</th>

                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $result = $db->prepare("SELECT * FROM loading_list WHERE loading_id='$id'  ");
                    $result->bindParam(':userid', $res);
                    $result->execute();
                    for ($i = 0; $row = $result->fetch(); $i++) {
                      $color = "";
                      $unqty = $row['unload_qty'];
                      $sold_qty = $row['qty_sold'];
                      $act = $row['action'];

                      if ($sold_qty < 0) {
                        $color = "#FF7A7D";
                      }

                    ?>
                      <tr class="record" style="background: <?php echo $color; ?> ">
                        <td><?php echo $row['product_name']; ?></td>
                        <td><?php echo $row['qty']; ?> </td>
                        <td><?php echo $row['qty_sold']; ?> </td>
                        <td><?php echo $row['unload_qty']; ?> </td>
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

        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Invoice</h3>
          </div>

          <div class="box-body">

            <table id="example1" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>Invoice no </th>
                  <th>Customer</th>
                  <th>Pay type</th>
                  <th>Amount </th>
                  <th>#</th>

                </tr>
              </thead>
              <tbody>
                <?php

                $result = $db->prepare("SELECT * FROM sales WHERE  loading_id='$id' and action=1 ORDER by invoice_number DESC");
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                  $invo = $row['invoice_number'];

                  $result1 = $db->prepare("SELECT * FROM payment WHERE  invoice_no='$invo' and dll=0 ORDER by transaction_id DESC  ");
                  $result1->bindParam(':userid', $c);
                  $result1->execute();
                  for ($k = 0; $row1 = $result1->fetch(); $k++) {
                    $pay_type = $row1['type'];
                  }

                ?>

                  <tr style="<?php echo $color_code; ?>" id="row_<?php echo $row['transaction_id']; ?>">
                    <td>
                      <?php echo $invo; ?>
                    </td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $pay_type; ?></td>
                    <td><?php echo $row['amount']; ?> </td>
                    <td style="display: flex;gap: 5px;">
                      <a href="#" onclick="bill_dll('<?php echo $row['invoice_number']; ?>','<?php echo $row['transaction_id']; ?>')" class="btn btn-danger btn-sm fa fa-trash"></a>
                    </td>
                  </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
          </div>

        </div>

        <div class="box">
          <div class="box-header with-border">
            <h3 class="box-title">Payment</h3>
          </div>

          <div class="box-body">

            <table id="example3" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>Invoice no </th>
                  <th>Customer</th>
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

                $result = $db->prepare("SELECT * FROM payment WHERE  loading_id='$id' and action>'0'AND dll=0  ORDER by transaction_id DESC");
                $result->bindParam(':userid', $date);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                  $invo = $row['invoice_no'];

                  $cus = '';
                  $result1 = $db->prepare("SELECT * FROM sales WHERE  invoice_number='$invo' and action='1' ");
                  $result1->bindParam(':userid', $c);
                  $result1->execute();
                  for ($k = 0; $row1 = $result1->fetch(); $k++) {

                    $in = $row1['transaction_id'];
                    $cus = $row1['name'];
                  }

                  $paycose = $row['paycose'];

                  $cr = '';
                  $color_code = '';
                  if ($paycose == 'credit_payment') {
                    $color_code = 'background-color:#7FB3D5';
                    $cr = '(credit)';
                  }
                ?>

                  <tr style="<?php echo $color_code; ?>" class="<?php echo $row['invoice_no']; ?>">
                    <td>
                      <?php echo $invo . ' ' . $cr; ?>
                    </td>
                    <td><?php echo $cus; ?></td>
                    <td><?php echo $row['type']; ?></td>
                    <td>
                      <span class="spn_<?php echo $row['transaction_id']; ?>"><?php echo $row['chq_no']; ?></span>

                      <form action="unloading_chq_edit.php" method="POST" class="form_<?php echo $row['transaction_id']; ?>" style="display: none;">
                        <input type="text" name="value" class="form-control" value="<?php echo $row['chq_no']; ?>">
                        <input type="hidden" name="id" value="<?php echo $row['transaction_id']; ?>">
                        <input type="hidden" name="column" value="chq_no">
                        <input type="hidden" name="load" value="<?php echo $_GET['id']; ?>">
                      </form>
                    </td>
                    <td>
                      <span class="spn_<?php echo $row['transaction_id']; ?>"><?php echo $row['chq_date']; ?></span>

                      <form action="unloading_chq_edit.php" method="POST" class="form_<?php echo $row['transaction_id']; ?>" style="display: none;">
                        <input type="text" name="value" class="form-control" value="<?php echo $row['chq_date']; ?>">
                        <input type="hidden" name="id" value="<?php echo $row['transaction_id']; ?>">
                        <input type="hidden" name="column" value="chq_date">
                        <input type="hidden" name="load" value="<?php echo $_GET['id']; ?>">
                      </form>
                    </td>
                    <td>
                      <span class="spn_<?php echo $row['transaction_id']; ?>"><?php echo $row['chq_bank']; ?></span>

                      <form action="unloading_chq_edit.php" method="POST" class="form_<?php echo $row['transaction_id']; ?>" style="display: none;">
                        <input type="text" name="value" class="form-control" value="<?php echo $row['chq_bank']; ?>">
                        <input type="hidden" name="id" value="<?php echo $row['transaction_id']; ?>">
                        <input type="hidden" name="column" value="chq_bank">
                        <input type="hidden" name="load" value="<?php echo $_GET['id']; ?>">
                      </form>
                    </td>
                    <td><?php echo $row['amount']; ?> </td>
                    <td>
                      <?php if ($row['type'] == 'chq') { ?>
                        <button onclick="bill_edit_open('<?php echo $row['transaction_id']; ?>')" class="btn btn-primary btn-sm fa fa-pencil" id="open_btn_<?php echo $row['transaction_id']; ?>"></button>
                        <button onclick="bill_edit_close('<?php echo $row['transaction_id']; ?>')" class="btn btn-info btn-sm fa fa-times" id="close_btn_<?php echo $row['transaction_id']; ?>" style="display: none;"></button>
                      <?php } ?>
                    </td>
                  </tr>
                <?php
                }
                ?>
              </tbody>
            </table>
          </div>

        </div>

        <div class="row">
          <div class="col-md-5">

            <div class="box box-primary">
              <div class="box-header">
                <h3 class="box-title">Summary</h3>
              </div>
              <!-- /.box-header -->
              <?php
              $result = $db->prepare("SELECT sum(amount) FROM payment WHERE  loading_id=:id AND type='cash' AND action >'0'  ORDER by transaction_id DESC");
              $result->bindParam(':id', $id);
              $result->execute();
              for ($i = 0; $row = $result->fetch(); $i++) {
                $cash = $row['sum(amount)'];
              }

              $result = $db->prepare("SELECT sum(amount) FROM expenses_records WHERE  loading_id=:id AND paycose = 'expenses' AND dll=0 ");
              $result->bindParam(':id', $id);
              $result->execute();
              for ($i = 0; $row = $result->fetch(); $i++) {
                $exp = $row['sum(amount)'];
              }

              $result = $db->prepare("SELECT cash_total FROM loading WHERE  transaction_id=:id ");
              $result->bindParam(':id', $id);
              $result->execute();
              for ($i = 0; $row = $result->fetch(); $i++) {
                $avt_cash = $row['cash_total'];
              }
              ?>
              <div class="box-body">
                <table class="table table-borderless table-hover">
                  <tbody>
                    <tr>
                      <td>
                        <h4 style="margin: 0">Cash <small>Rs.</small></h4>
                      </td>
                      <td>
                        <h4 style="margin: 0"><?php echo number_format($cash, 2); ?></h4>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <h4 style="margin: 0">Expenses <small>Rs.</small></h4>
                      </td>
                      <td>
                        <h4 style="margin: 0"><?php echo number_format($exp, 2); ?></h4>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <h3 style="margin: 0">Balance <small>Rs.</small></h3>
                      </td>
                      <td><?php $blc = $cash - $exp; ?>
                        <h3 style="margin: 0"><?php echo number_format($blc, 2); ?></h3>
                      </td>
                    </tr>
                    <tr>
                      <td></td>
                      <td></td>
                    </tr>
                    <tr>
                      <td>
                        <h4 style="margin: 0">Actual Cash <small>Rs.</small></h4>
                      </td>
                      <td>
                        <h4 style="margin: 0"><?php echo number_format($avt_cash, 2); ?></h4>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <h3 style="margin: 0">Difference <small>Rs.</small></h3>
                      </td>
                      <td><?php $diff = $avt_cash - $blc; ?>
                        <?php $cl = '';
                        if ($diff > 0) {
                          $cl = 'color: green;';
                        }
                        if ($diff < 0) {
                          $cl = 'color: red;';
                        }
                        ?>
                        <h3 style="margin: 0; <?php echo $cl; ?>"><?php echo number_format($diff, 2); ?></h3>
                      </td>
                    </tr>

                  </tbody>
                </table>
              </div>
            </div>

          </div>

          <div class="col-md-4">
            <div class="box">
              <div class="box-body">
                <a class="btn btn-danger" style=" width: 100%;" href="unloading_stock_save.php?id=<?php echo $_GET['id']; ?>">
                  <b>Unloading</b>
                </a>
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
      $("#example3").DataTable();
      $('#example4').DataTable({
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
    function bill_edit_open(id) {
      $('.spn_' + id).css('display', 'none');
      $('.form_' + id).css('display', 'block');
      $('#open_btn_' + id).css('display', 'none');
      $('#close_btn_' + id).css('display', 'block');
    }

    function bill_edit_close(id) {
      $('.spn_' + id).css('display', 'block');
      $('.form_' + id).css('display', 'none');
      $('#open_btn_' + id).css('display', 'block');
      $('#close_btn_' + id).css('display', 'none');
    }

    function bill_dll(invo, id) {
      //Built a url to send
      var info = 'id=' + invo;
      if (confirm("Sure you want to delete this Bill? There is NO undo!")) {

        $.ajax({
          type: "GET",
          url: "unloading_invoice_dll.php",
          data: info,
          success: function() {

          }
        });
        $("#row_" + id).animate({
            backgroundColor: "#fbc7c7"
          }, "fast")
          .animate({
            opacity: "hide"
          }, "slow");
        $("." + invo).animate({
            backgroundColor: "#fbc7c7"
          }, "fast")
          .animate({
            opacity: "hide"
          }, "slow");

      }

      return false;

    }
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