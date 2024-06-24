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
    $_SESSION['SESS_FORM'] = '11';

    include_once("sidebar.php");

    ?>


    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Profile
                <small>Preview</small>
            </h1>
        </section>



        <?php $g = $_GET['id']; ?>
        <!-- Main content -->
        <section class="content">

            <div class="row">
                <div class="col-md-3">

                    <!-- SELECT2 EXAMPLE -->


                    <!-- /.box about me -->
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">About Me</h3>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body">

                            <ul class="list-group list-group-unbordered">
                                <?php $id = $_GET["id"];
                                $result = $db->prepare("SELECT * FROM employee WHERE id='$id' ");
                                $result->bindParam(':userid', $res);
                                $result->execute();
                                for ($i = 0; $row = $result->fetch(); $i++) {
                                    $ot = $row['ot'];
                                    $well = $row['well'];
                                    $action = $row['action'];
                                    $des = $row['des_id'];
                                    $pass = $row['password'];
                                    $dis = 'none';
                                    if ($des == 1) {
                                        $dis = 'block';
                                    }
                                ?>
                                    <li class="list-group-item">
                                        <b>Name:</b> <i><?php echo $name = $row['name']; ?></i>
                                    </li>
                                    <li class="list-group-item">
                                        <b>User Name:</b> <i><?php echo $user = $row['username']; ?></i>
                                    </li>
                                    <li class="list-group-item">
                                        <b>NIC:</b> <i><?php echo $nic = $row['nic']; ?></i>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Address:</b> <i><?php echo $address = $row['address']; ?></i>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Contact:</b> <i><?php echo $contact = $row['phone_no']; ?></i>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Designation:</b> <i><?php echo $row['des']; ?></i>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Hour Rate:</b> <i><?php echo $rate = $row['hour_rate']; ?></i>
                                    </li>
                                    <li class="list-group-item">
                                        <b>EPF No:</b> <i><?php echo $epf_no = $row['epf_no']; ?></i>
                                    </li>
                                    <li class="list-group-item">
                                        <b>EPF Amount:</b> <i><?php echo $epf_amount = $row['epf_amount']; ?></i>
                                    </li>
                                <?php } ?>

                            </ul>
                            <a href="hr_employee.php" class="btn btn-sm btn-info" style="width: 100%;">Back</a>
                        </div>
                        <!-- /.box-body -->
                    </div>
                </div>
                <!-- /.col (left) -->


                <div class="col-md-9">
                    <div class="nav-tabs-custom">
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#settings" data-toggle="tab">Settings</a></li>
                        </ul>
                        <div class="tab-content">
                            <!-- /.tab-pane -->
                            <div class="active tab-pane" id="settings">
                                <form method="post" action="hr_employee_save.php" class="form-horizontal" enctype="multipart/form-data">
                                    <div class="row" style="margin-right: 10px;">
                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="name" value="<?php echo $name; ?>" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Nick Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="nickname" value="<?php echo $user; ?>" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Contact</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="phone_no" value="<?php echo $contact; ?>" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">NIC</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="nic" value="<?php echo $nic; ?>" autocomplete="off" placeholder="NIC">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Address</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="address" value="<?php echo $address; ?>" autocomplete="off" placeholder="Address">

                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-2 control-label">Designation</label>
                                            <div class="col-sm-10">
                                                <select class="form-control select2 hidden-search" name="des" onchange="des_select(this.options[this.selectedIndex].getAttribute('value'))" style="width: 100%;" autofocus>
                                                    <option value="0" disabled selected>Select Designation</option>
                                                    <?php
                                                    $result = $db->prepare("SELECT * FROM employees_des ");
                                                    $result->bindParam(':userid', $res);
                                                    $result->execute();
                                                    for ($i = 0; $row = $result->fetch(); $i++) {
                                                    ?>
                                                        <option <?php if ($row['id'] == $des) { ?> selected <?php } ?> value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?>
                                                        </option>
                                                    <?php
                                                    }
                                                    ?>
                                                </select>

                                            </div>
                                        </div>

                                        <div class="form-group drive_sec" style="display: <?php echo $dis; ?>">
                                            <label class="col-sm-2 control-label">User Name:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control" type="text" value="<?php echo $user; ?>" placeholder="User Name" name="username" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="form-group drive_sec" style="display: <?php echo $dis; ?>">
                                            <label class="col-sm-2 control-label">Password:</label>
                                            <div class="col-sm-10">
                                                <input class="form-control" type="password" value="<?php echo $pass; ?>" name="password" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="inputEmail" class="col-sm-2 control-label">Day Rate:</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="rate" value="<?php echo $rate; ?>" id="inputEmail" placeholder="Hour Rate:" required>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="" class="col-sm-2 control-label">EPF No</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="epf_no" value="<?php echo $epf_no; ?>" id="" placeholder="EPF no" autocomplete="off">
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <label for="" class="col-sm-2 control-label">EPF Amount</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="epf_amount" value="<?php echo $epf_amount; ?>" id="" placeholder="EPF Amount" autocomplete="off">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="" class="col-sm-2 control-label">OT</label>
                                            <div class="col-sm-10">
                                                <select class="form-control select2 hidden-search" name="ot" id="">
                                                    <option <?php if ($ot == 1) { ?> selected <?php } ?> value="1">Eligible</option>
                                                    <option <?php if ($ot == 0) { ?> selected <?php } ?> value="0">Not Eligible</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="" class="col-sm-2 control-label">Welfare</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" name="well" value="<?php echo $well; ?>" id="">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="" class="col-sm-2 control-label">User Pic</label>
                                            <div class="col-sm-10">
                                                <label class="form-control" for="img-file" style="cursor: pointer;">
                                                    <input accept=".jpg, .jpeg, .png" name="image" id="img-file" type="file" style="opacity: 0;position: absolute;cursor: pointer;">
                                                    <i class="fa fa-cloud-upload"></i> Image Upload
                                                </label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <div class="checkbox">
                                                    <label>
                                                        <input name="submit" id="submit" type="checkbox"> I agree to <a href="#">Submit</a>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                                                <input type="hidden" name="end" value="0">
                                                <button type="submit" class="btn btn-danger">Submit</button>
                                            </div>
                                        </div>


                                        <div class="form-group">
                                            <div class="col-sm-offset-2 col-sm-10">
                                                <?php if ($action == 0) { ?>
                                                    <span class="btn btn-success btn-sm pull-right" onclick="disabled_emp('enb')"> <i class="fa fa-check"></i> Enable </span>
                                                <?php } else { ?>
                                                    <span class="btn btn-danger btn-sm pull-right" onclick="disabled_emp('dis')"> <i class="fa fa-warning"></i> Disable</span>
                                                <?php } ?>
                                            </div>
                                        </div>

                                    </div>
                                </form>
                                <form action="hr_employee_disabled.php" method="POST" id="emp-dis">
                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                                    <input type="hidden" name="act" value="0">
                                </form>
                                <form action="hr_employee_disabled.php" method="POST" id="emp-enb">
                                    <input type="hidden" name="id" value="<?php echo $_GET['id']; ?>">
                                    <input type="hidden" name="act" value="1">
                                </form>
                            </div>
                            <!-- /.tab-pane -->
                        </div>
                        <!-- /.tab-content -->
                    </div>
                    <!-- /.nav-tabs-custom -->
                </div>
                <!-- /.box-body -->


                <!-- /.box -->

                <!-- /.col (right) -->
            </div>
            <!-- /.row -->

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
    <!-- ./wrapper -->

    <!-- jQuery 2.2.3 -->
    <script src="../../plugins/jQuery/jquery-2.2.3.min.js"></script>
    <!-- Bootstrap 3.3.6 -->
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <!-- DataTables -->
    <script src="../../plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="../../plugins/datatables/dataTables.bootstrap.min.js"></script>
    <!-- Select2 -->
    <script src="../../plugins/select2/select2.full.min.js"></script>
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

    <!-- Page script -->
    <script>
        function disabled_emp(ty) {
            if (confirm("Sure you want to Disabled this Employee? There is NO undo!")) {
                if (ty == 'dis') {
                    $('#emp-dis').submit();
                }
                if (ty == 'enb') {
                    $('#emp-enb').submit();
                }
            }
            return false;
        }

        function des_select(id) {
            if (id == 1) {
                $('.drive_sec').css('display', 'block');
            } else {
                $('.drive_sec').css('display', 'none');
            }
        }
        $(function() {
            //Initialize Select2 Elements
            $(".select2").select2();
            $('.select2.hidden-search').select2({
                minimumResultsForSearch: -1
            });

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
                        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1,
                            'month').endOf('month')]
                    },
                    startDate: moment().subtract(29, 'days'),
                    endDate: moment()
                },
                function(start, end) {
                    $('#daterange-btn span').html(start.format('MMMM D, YYYY') + ' - ' + end.format(
                        'MMMM D, YYYY'));
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