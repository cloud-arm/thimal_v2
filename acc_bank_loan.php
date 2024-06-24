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
    $_SESSION['SESS_FORM'] = '25';

    include_once("sidebar.php");
    ?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <section class="content-header">
        <h1>
          BANK LOAN
          <small>Preview</small>
        </h1>
      </section>

      <!-- Main content -->
      <section class="content">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Loan Payment</h3>
            <small class="btn btn-success btn-sm mx-2" style="padding: 5px 10px;" title="Add new loan" onclick="click_open(1)">Add new loan</small>
          </div>

          <!-- /.box-header -->
          <div class="box-body">
            <form method="post" action="acc_bank_loan_save.php">
              <div class="row" style=" margin:1px;">
                <div class="col-md-3">
                  <div class="form-group">
                    <label>Select Loan</label>
                    <select class="form-control select2 hidden-search" name="loan" style="width: 100%;" onchange="select_loan(this.options[this.selectedIndex].getAttribute('term'))" autofocus tabindex="1">
                      <option value="0" selected disabled>Select Loan</option>
                      <?php
                      $result = $db->prepare("SELECT * FROM bank_loan ");
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) { ?>
                        <option value="<?php echo $row['id']; ?>" term="<?php echo $row['term_amount']; ?>"> <?php echo $row['bank_name']; ?> </option>
                      <?php } ?>
                    </select>

                  </div>
                </div>

                <div class="col-md-3 loan">
                  <div class="form-group">
                    <label>Pay Type</label>
                    <select class="form-control select2 hidden-search" name="pay_type" style="width: 100%;" onchange="select_type(this.options[this.selectedIndex].value)" autofocus tabindex="1">
                      <option>Bank</option>
                      <option>Chq</option>
                    </select>
                  </div>
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
                    <input class="form-control" id="datepicker" type="text" name="chq_date" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-3 loan">
                  <div class="form-group">
                    <label>Pay Amount</label>
                    <input type="number" name="term" step=".01" id="term_txt" class="form-control" tabindex="2" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-3 loan">
                  <div class="form-group">
                    <label>Pay Date</label>
                    <input type="text" id="datepick" name="pay_date" value="<?php echo date("Y-m-d"); ?>" class="form-control" tabindex="3" autocomplete="off">
                  </div>
                </div>

                <div class="col-md-3 loan">
                  <div class="form-group">
                    <label>Bank Account</label>
                    <select class="form-control  select2 hidden-search" name="bank" style="width: 100%;" autofocus tabindex="4">

                      <?php
                      $result = $db->prepare("SELECT * FROM bank_balance ");
                      $result->execute();
                      for ($i = 0; $row = $result->fetch(); $i++) {
                      ?>
                        <option value="<?php echo $id = $row['id']; ?>" <?php if ($id == 1) { ?> selected <?php } ?> balance="<?php echo $id = $row['amount']; ?>"> <?php echo $row['name'] . ' - ' . $row['dep_name']; ?> </option>
                      <?php
                      }
                      ?>
                    </select>

                  </div>
                </div>

                <div class="col-md-1 pe-2 me-2 loan" style="height: 70px;display: flex;align-items: end;">
                  <div class="form-group">
                    <input name="unit" type="hidden" value="1">
                    <input class="btn btn-info" type="submit" id="btn_sub" value="Submit" disabled>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </section>


      <section class="content">
        <div class="box box-info">
          <div class="box-header with-border">
            <h3 class="box-title">Collection List</h3>
          </div>

          <div class="box-body d-block">
            <table id="example" class="table table-bordered table-striped" style="border-radius: 0;">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Date</th>
                  <th>Bank</th>
                  <th>Pay Acc</th>
                  <th>Amount (Rs.)</th>
                  <th>Balance (Rs.)</th>
                  <th>#</th>
                </tr>
              </thead>
              <tbody>

                <?php
                $tot = 0;
                $result = $db->prepare("SELECT * FROM bank_loan_record LIMIT 20 ");
                $result->execute();
                for ($i = 0; $row = $result->fetch(); $i++) {
                  $dll = $row['dll'];
                  if ($dll == 1) {
                    $style = 'opacity: 0.5;cursor: default;';
                  } else {
                    $style = '';
                  }
                ?>
                  <tr class="record" style="<?php echo $style; ?>">
                    <td><?php echo $row['id'];   ?> </td>
                    <td><?php echo $row['date'];   ?> </td>
                    <td><?php echo $row['bank_name'];   ?> </td>
                    <td><?php echo $row['bank_acc_name'] . ' / ' . $row['bank_acc_no'];   ?></td>
                    <td>Rs.<?php echo $row['amount'];
                            $tot += $row['amount']; ?></td>
                    <td>Rs.<?php echo $row['balance'];  ?></td>
                    <td> <?php if ($dll == 0) { ?> <a href="#" id="<?php echo $row['id']; ?>" class="delbutton btn btn-danger" title="Click to Delete">
                          <i class="icon-trash">x</i></a><?php } ?>
                    </td>
                  </tr>
                <?php }   ?>
              </tbody>
            </table>
            <h4>Total Payment: <small class="ms-2">Rs.</small> <?php echo $tot; ?> </h4>
          </div>
        </div>

      </section>
      <!-- /.content -->
    </div>

    <!-- /.content-wrapper -->
    <?php include("dounbr.php"); ?>

    <div class="container-up d-none" id="container_up">
      <div class="container-close" onclick="click_close()"></div>
      <div class="row">
        <div class="col-md-6 with-scroll">

          <div class="box box-success popup d-none" id="popup_1">
            <div class="box-header with-border">
              <h3 class="box-title w-100">
                New Bank Loan
                <i onclick="click_close()" class="btn me-2 pull-right fa fa-remove" style="font-size: 25px"></i>
              </h3>
            </div>

            <div class="box-body d-block">
              <form method="POST" action="acc_bank_loan_save.php">

                <div class="row" style="display: block;">

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Bank Name</label>
                      <input type="text" name="bank_name" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Capital</label>
                      <input type="number" name="capital" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Interest</label>
                      <input type="number" name="interest" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Term</label>
                      <input type="number" name="term" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <label>Term Amount</label>
                      <input type="number" step=".01" name="term_amount" value="" class="form-control" autocomplete="off">
                    </div>
                  </div>

                  <div class="col-md-6">
                    <div class="form-group">
                      <input type="hidden" name="unit" value="2">
                      <input type="submit" style="margin-top: 23px;width: 100px;" value="Save" class="btn btn-info">
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
    function select_loan(val) {
      $('#term_txt').val(val);
      $('#btn_sub').removeAttr('disabled');

    }

    function select_type(val) {
      if (val == "Bank") {
        $('.slt-bank').css("display", "block");
      } else {
        $('.slt-bank').css("display", "none");
      }

      if (val == "Chq") {
        $('.slt-chq').css("display", "block");
      } else {
        $('.slt-chq').css("display", "none");
      }
    }

    $(function() {


      $(".delbutton").click(function() {

        var element = $(this);
        var del_id = element.attr("id");
        var info = 'id=' + del_id;
        if (confirm("Sure you want to delete this Collection? There is NO undo!")) {

          $.ajax({
            type: "GET",
            url: "acc_bank_loan_dll.php",
            data: info,
            success: function() {}
          });
          $(this).parents(".record").css({
            'opacity': '0.5',
            'cursor': 'default'
          })
          $(this).remove();

        }

        return false;

      });

    });



    $(function() {
      $("#example1").DataTable();
      $('#example').DataTable({
        "paging": true,
        "lengthChange": true,
        "searching": true,
        "ordering": true,
        "info": false,
        "autoWidth": true
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
      $('#datepick').datepicker({
        autoclose: true,
        datepicker: true,
        format: 'yyyy-mm-dd '
      });



      $('#datepickerd').datepicker({
        autoclose: true,
        datepicker: true,
        format: 'yyyy/mm/dd '
      });
      $('#datepickerd').datepicker({
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