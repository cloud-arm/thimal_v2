<!DOCTYPE html>
<html>
<?php
include("head.php");
include("connect.php");
date_default_timezone_set("Asia/Colombo");
?>

<body class="hold-transition skin-yellow sidebar-mini">
  <div class="wrapper" style="overflow-y: hidden;">
    <?php
    include_once("auth.php");
    $r = $_SESSION['SESS_LAST_NAME'];
    $_SESSION['SESS_FORM'] = '22';

    include_once("sidebar.php");

    $balance = 0;
    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          Payment
          <small>Preview</small>
        </h1>

      </section>

      <!-- Main content -->
      <section class="content">

        <!-- SELECT2 EXAMPLE -->
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Bulk Payment</h3>
          </div>


          <?php if (isset($_GET['id'])) { ?>
            <!-- /.box -->
            <div class="box-body">

              <div class="row">

                <?php
                $pay_id = $_GET['id'];
                $result = $db->prepare("SELECT * FROM payment WHERE  transaction_id=:id ");
                $result->bindParam(':id', $pay_id);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                  $chq_amount = $row['amount'];
                } ?>

                <div class="col-md-7">

                  <div class="row" style="display: flex;flex-direction: column;justify-content: space-between;">

                    <div class="col-md-12">

                      <form method="post" action="bulk_payment_bill_add.php">

                        <div class="row">

                          <div class="col-md-10">
                            <div class="form-group">
                              <label>Invoice NO</label>
                              <select class="form-control select2" name="invo" style="width: 100%;" autofocus>
                                <?php
                                $res = $db->prepare("SELECT * FROM payment WHERE type='credit' AND action='2' AND credit_balance > 0 ");
                                $res->bindParam(':id', $ttr);
                                $res->execute();
                                for ($j = 0; $ro = $res->fetch(); $j++) {
                                  $customer_id = $ro['customer_id'];

                                  $res1 = $db->prepare("SELECT * FROM customer WHERE customer_id =:id ");
                                  $res1->bindParam(':id', $customer_id);
                                  $res1->execute();
                                  for ($k = 0; $ro1 = $res1->fetch(); $k++) {
                                    $name = $ro1['customer_name'];
                                  }
                                ?>
                                  <option value="<?php echo $ro['transaction_id']; ?>"><?php echo $ro['sales_id']; ?> __ <?php echo $name; ?> __Rs.<?php echo $ro['credit_balance']; ?> </option>
                                <?php
                                }
                                ?>
                                <option value="qb">Old Bill Payment (QB System) </option>
                              </select>
                            </div>
                          </div>

                          <div class="col-md-4">
                            <div class="form-group">
                              <label>Pay Amount</label>
                              <input type="number" step=".01" name="amount" class="form-control " tabindex="4" autocomplete="off">
                            </div>
                          </div>

                          <div class="col-md-2">
                            <div class="form-group" style="margin-top: 23px;">
                              <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                              <button type="submit" class="btn btn-info btn-flat">Add to list</button>
                            </div>
                          </div>

                        </div>

                      </form>

                    </div>

                    <div class="col-md-8" style="margin-top: 20px;">
                      <div class="alert alert-warning alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                        <h4><i class="icon fa fa-warning"></i> Alert!</h4>
                        Please click process button to complete the process.
                      </div>
                    </div>

                    <div class="col-md-12" style="margin-top: 20px;">
                      <?php
                      $res = $db->prepare("SELECT sum(pay_amount) FROM credit_payment WHERE pay_id='$pay_id'  AND dll = 0 ");
                      $res->bindParam(':userid', $ttr);
                      $res->execute();
                      for ($a = 0; $ro = $res->fetch(); $a++) {
                        $pay_tot = $ro['sum(pay_amount)'];
                      }
                      $balance = $chq_amount - $pay_tot;

                      if ($balance < 0) { ?>

                        <h1 style='color:red !important;'>
                        <?php } else { ?>

                          <h1 style='color:green  !important;'>
                          <?php } ?>

                          Balance: <small>Rs.</small> <?php echo number_format($balance, 2); ?></h1>


                    </div>

                  </div>

                </div>

                <?php
                $pay_id = $_GET['id'];
                $result = $db->prepare("SELECT * FROM payment WHERE  transaction_id=:id ");
                $result->bindParam(':id', $pay_id);
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                  $type = $row['type'];
                  if ($type == "chq") { ?>

                    <div class="col-md-5">
                      <div class="callout callout-warning">
                        <h4 class="pull-left"><?php echo $row['chq_bank']; ?></h4>
                        <h4 class="pull-right"><?php echo $row['chq_date']; ?></h4>
                        <br><br>
                        <h4>Narangoda Group </h4>
                        <hr style="margin-bottom: 10px;">
                        <button type="button" class="btn btn-default btn-lg pull-right">Rs. <?php echo $row['amount']; ?></button>
                        <br><br>
                        <hr>
                        <center>
                          <h4>
                            <?php echo $row['chq_no']; ?> -xxxxx': xxxxxxxx;'
                          </h4>
                        </center>
                      </div>
                    </div>

                  <?php }

                  if ($type == "bank") { ?>

                    <div class="col-md-5">
                      <div class="callout callout-success">
                        <h2>Bank Transfer</h2>
                        <table class="table table-bordered table-striped">
                          <thead>

                            <tr>
                              <th>Reference No.</th>
                              <th><?php echo $row['chq_no']; ?></th>
                            </tr>

                            <tr>
                              <th>Bank</th>
                              <th><?php echo $row['chq_bank']; ?></th>
                            </tr>

                            <tr>
                              <th>Date</th>
                              <th><?php echo $row['chq_date']; ?></th>
                            </tr>

                            <tr>
                              <th>Amount</th>
                              <th><?php echo $row['amount']; ?></th>
                            </tr>

                          </thead>
                        </table>

                      </div>
                    </div>

                  <?php }

                  if ($type == "cash") { ?>

                    <div class="col-md-5">
                      <div class="callout callout-default">

                        <img src="icon/money.png" alt="" style="width:130px">

                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>Type</th>
                              <th>Cash</th>
                            </tr>

                            <tr>
                              <th>Amount</th>
                              <th><?php echo $row['amount']; ?></th>
                            </tr>

                          </thead>
                        </table>

                      </div>
                    </div>

                <?php }
                } ?>


              </div>

              <form action="bulk_payment_process_copy.php" method="POST" id="process-form">
                <input type="hidden" name="pay_id" value="<?php echo $_GET['id']; ?>">
              </form>

              <a class="btn btn-lg btn-danger pull-right" onclick="process()">
                <i class="fa fa-connectdevelop fa-info"></i> Process
              </a>

            </div>

            <div class="box-body">

              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Invoice no</th>
                    <th>Customer</th>
                    <th>Credit Amount (Rs.)</th>
                    <th>Pay Amount (Rs.)</th>
                    <th>#</th>

                  </tr>
                </thead>
                <tbody>

                  <?php $id = $_GET['id'];
                  $result = $db->prepare("SELECT * FROM credit_payment WHERE pay_id='$id'  ");
                  $result->bindParam(':userid', $date);
                  $result->execute();
                  for ($i = 0; $row = $result->fetch(); $i++) {  ?>
                    <tr class="record">
                      <td><?php echo $row['id'];   ?> </td>
                      <td><?php echo $row['invoice_no'];   ?> </td>
                      <td><?php echo $row['action'];   ?> </td>
                      <td>Rs.<?php echo $row['credit_amount'];   ?></td>
                      <td><?php echo $row['pay_amount'];   ?></td>
                      <td>

                        <a href="bulk_payment_list_dll.php?id=<?php echo $row['id']; ?>&pay_id=<?php echo $_GET['id'];   ?>" title="Click to Delete">
                          <button class="btn btn-danger"><i class="icon-trash">x</i></button>
                        </a>

                      </td>
                    </tr>

                  <?php }   ?>
                </tbody>

              </table>

            </div>

          <?php  } else { ?>

            <div class="box-body">
              <form method="POST" action="bulk_payment_save.php">

                <div class="row">
                  <div class="col-md-3">
                    <label>Pay Type</label>
                    <select name="type" style="width: 100%;" class="form-control select2 hidden-search" onchange="select_pay(this.options[this.selectedIndex].getAttribute('value'))">
                      <option value="cash">Cash</option>
                      <option value="chq">Chq</option>
                      <option value="bank">Bank Transfer</option>
                    </select>
                  </div>

                  <div class="col-md-3 slt-chq" style="display:none;">
                    <div class="form-group">
                      <label>Chq Number</label>
                      <input class="form-control" type="text" name="chq_no" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-3 slt-chq" style="display:none;">
                    <div class="form-group">
                      <label>Chq Date</label>
                      <input class="form-control" id="datepicker1" type="text" name="chq_date" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-3 slt-bank" style="display:none;">
                    <div class="form-group">
                      <label>Bank Acc</label>
                      <?php
                      $result = $db->prepare("SELECT * FROM bank_balance ");
                      $result->bindParam(':id', $res);
                      $result->execute(); ?>
                      <select class="form-control select2 hidden-search" name="bank" style="width: 100%;" tabindex="1">
                        <option value="0" selected disabled> Select Bank </option>
                        <?php for ($i = 0; $row = $result->fetch(); $i++) {  ?>
                          <option value="<?php echo $row['id']; ?>"> <?php echo $row['name']; ?> </option>
                        <?php  } ?>
                      </select>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group">
                      <label>Amount</label>
                      <input class="form-control" step=".01" type="number" name="amount" autocomplete="off" required>
                    </div>
                  </div>

                  <div class="col-md-3">
                    <div class="form-group" style="margin-top: 23px;">
                      <input class="btn btn-info" type="submit" style="width: 100px;" value="Bulk Save">
                    </div>
                  </div>

                </div>

              </form>
            </div>


          <?php } ?>

        </div>
      </section>

    </div>

    <!-- /.content-wrapper -->
    <?php
    include("dounbr.php");
    ?>

    <?php
    $error_id = 0;
    $unit = 0;
    $err = 'd-none';
    if (isset($_GET['error'])) {
      $error_id = $_GET['error'];
      $unit = $_GET['unit'];
      $err = '';
    } ?>

    <div class="container-up <?php echo $err; ?>" id="container_up">
      <div class="container-close" onclick="click_close()"></div>
      <div class="row">
        <div class="col-md-12">

          <div class="box box-success popup <?php echo $err; ?>" id="popup_1" style="padding: 5px;border: 0;">
            <div class="alert alert-danger alert-dismissible" style="width: 350px;margin: 0;">
              <button type="button" class="close" onclick="click_close()" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-ban"></i> Alert!</h4>
              <?php

              if ($unit == 1) {
                if ($error_id == 1) {
                  echo "The amount paid is more than the credit amount ..!";
                } else {
                  echo "This Bill already ADD ..!";
                }
              }

              if ($unit == 2) {
                echo "Unbalance CHQ amount to Payment total ..!";
              }

              if ($unit == 3) {
                echo "This chq already Save ..!";
              }

              ?>
            </div>
          </div>

          <div class="box box-success popup d-none" id="popup_2" style="padding: 5px;border: 0;">
            <div class="alert alert-danger alert-dismissible" style="width: 350px;margin: 0;">
              <button type="button" class="close" onclick="click_close()" data-dismiss="alert" aria-hidden="true">&times;</button>
              <h4><i class="icon fa fa-ban"></i> Alert!</h4>
              Unbalance CHQ amount to Payment total
            </div>
          </div>

          <div class="box box-success popup d-none" id="popup_3" style="width: 358px;display: flex;flex-direction: column;justify-content: space-between;">

            <h4>Sure you want to process this ? </h4>
            <hr style="margin: 10px 0;border-color:#999;">
            <div style="display: flex;align-items:center;justify-content:space-around;margin:10px 0;">
              <button onclick="check_process('cancel')" class="btn btn-primary">Cancel</button>
              <button onclick="check_process('process')" class="btn btn-danger">Process</button>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="control-sidebar-bg"></div>
  </div>
  <!-- ./wrapper -->


  <!-- jQuery 2.2.3 -->
  <script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
  <!-- Bootstrap 3.3.6 -->
  <script src="../../bootstrap/js/bootstrap.min.js"></script>
  <!-- DataTables -->
  <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
  <script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
  <!-- SlimScroll -->
  <script src="../../plugins/slimScroll/jquery.slimscroll.min.js"></script>
  <!-- FastClick -->
  <script src="../../plugins/fastclick/fastclick.js"></script>
  <!-- AdminLTE App -->
  <script src="../../dist/js/app.min.js"></script>
  <!-- AdminLTE for demo purposes -->
  <script src="../../dist/js/demo.js"></script>
  <!-- Select2 -->
  <script src="../../plugins/select2/select2.full.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
  <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
  <!-- bootstrap datepicker -->
  <script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>
  <!-- Dark Theme Btn-->
  <script src="https://dev.colorbiz.org/ashen/cdn/main/dist/js/DarkTheme.js"></script>
  <!-- bootstrap color picker -->

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

  <script>
    function select_pay(val) {

      if (val == "bank") {
        $('.slt-chq').css("display", "none");
        $('.slt-bank').css("display", "block");
      } else
      if (val == "chq") {
        $('.slt-bank').css("display", "none");
        $('.slt-chq').css("display", "block");
      } else {
        $('.slt-chq').css("display", "none");
        $('.slt-bank').css("display", "none");
      }

    }

    function process() {
      let balance = <?php echo $balance; ?>;

      if (balance > 0) {
        $("#popup_2").removeClass("d-none");
        $("#container_up").removeClass("d-none");
      } else {
        $("#popup_3").removeClass("d-none");
        $("#container_up").removeClass("d-none");
      }
    }

    function check_process(val) {
      if (val == 'process') {
        $('#process-form').submit();
      }
      if (val == 'cancel') {
        $(".popup").addClass("d-none");
        $("#container_up").addClass("d-none");
      }
    }
  </script>

  <script type="text/javascript">
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


  <!-- Page script -->
  <script>
    $(function() {
      //Initialize Select2 Elements
      $(".select2").select2();
      $('.select2.hidden-search').select2({
        minimumResultsForSearch: -1
      });

      //Date range picker
      $('#reservation').daterangepicker();
      //Date range picker with time picker
      //$('#datepicker').datepicker({datepicker: true,  format: 'yyyy/mm/dd '});
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
        format: 'yyyy-mm-dd '
      });
      $('#datepicker1').datepicker({
        autoclose: true,
        datepicker: true,
        format: 'yyyy-mm-dd '
      });
      $('#datepicker').datepicker({
        autoclose: true
      });



      $('#datepicker_2end').datepicker({
        autoclose: true,
        datepicker: true,
        format: 'yyyy-mm-dd '
      });
      $('#datepicker_2end').datepicker({
        autoclose: true
      });

    });


    function view_payment_date(type) {
      if (type == 'bank') {
        document.getElementById('chq_pay').style.display = 'none';
        document.getElementById('bank_pay').style.display = 'block';
        document.getElementById('cash_pay').style.display = 'none';
        document.getElementById('coupon').style.display = 'none';
        document.getElementById('2kg').style.display = 'none';
      } else if (type == 'chq') {
        document.getElementById('chq_pay').style.display = 'block';
        document.getElementById('bank_pay').style.display = 'none';
        document.getElementById('cash_pay').style.display = 'none';
        document.getElementById('coupon').style.display = 'none';
        document.getElementById('2kg').style.display = 'none';
      } else if (type == 'cash') {
        document.getElementById('chq_pay').style.display = 'none';
        document.getElementById('bank_pay').style.display = 'none';
        document.getElementById('cash_pay').style.display = 'block';
        document.getElementById('coupon').style.display = 'none';
        document.getElementById('2kg').style.display = 'none';
      } else if (type == 'coupon') {
        document.getElementById('chq_pay').style.display = 'none';
        document.getElementById('bank_pay').style.display = 'none';
        document.getElementById('cash_pay').style.display = 'none';
        document.getElementById('2kg').style.display = 'none';
        document.getElementById('coupon').style.display = 'block';
      } else if (type == '2kg') {
        document.getElementById('chq_pay').style.display = 'none';
        document.getElementById('coupon').style.display = 'none';
        document.getElementById('bank_pay').style.display = 'none';
        document.getElementById('cash_pay').style.display = 'none';
        document.getElementById('2kg').style.display = 'block';
      } else {
        document.getElementById('chq_pay').style.display = 'none';
        document.getElementById('bank_pay').style.display = 'none';
        document.getElementById('cash_pay').style.display = 'none';
        document.getElementById('coupon').style.display = 'none';
        document.getElementById('2kg').style.display = 'none';
      }
    }
  </script>
</body>

</html>