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
        $_SESSION['SESS_FORM'] = '13';

        include_once("sidebar.php");

        ?>


        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <h1>
                    Salary Advance
                    <small>Preview</small>
                </h1>
            </section>

            <!-- Main content -->
            <section class="content hidden">

                <!-- SELECT2 EXAMPLE -->
                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Advance</h3>
                    </div>

                    <div class="box-body">

                        <form method="post" action="hr_salary_advance_save.php">

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Name</label>
                                        <select class="form-control select2" name="id" style="width: 100%;" autofocus tabindex="1">
                                            <option value="0" selected disabled></option>
                                            <?php
                                            $result = $db->prepare("SELECT * FROM employee WHERE action='1'");
                                            $result->bindParam(':userid', $ttr);
                                            $result->execute();
                                            for ($i = 0; $row = $result->fetch(); $i++) {
                                            ?>
                                                <option value="<?php echo $row['id']; ?>"><?php echo $row['name']; ?>
                                                </option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Amount</label>
                                        <input type="text" value='' name="amount" class="form-control" tabindex="2">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Comment</label>
                                        <input type="text" value='' name="note" class="form-control" tabindex="3">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label> Date</label>
                                        <input type="text" value='<?php echo date("Y-m-d"); ?> ' id="datepicker" name="date" class="form-control" tabindex="4">
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="form-group" style="margin-top: 24px;">
                                        <input class="btn btn-info" type="submit" value="Submit">
                                    </div>
                                </div>

                            </div>

                        </form>

                    </div>
                </div>
            </section>

            <section class="content">

                <div class="row">
                    <div class="col-md-1"></div>
                    <div class="col-md-10">
                        <div class="box">
                            <div class="box-header with-border">
                                <h3 class="box-title">Employee & Date Selector</h3>
                            </div>

                            <div class="box-body">
                                <form action="" method="GET">
                                    <div class="row" style="margin-bottom: 20px;display: flex;align-items: end;justify-content: center;">
                                        <div class="col-lg-4">
                                            <label>Employee:</label>
                                            <select class="form-control select2" name="id" style="width: 100%;" autofocus tabindex="1">
                                                <option value="0" selected disabled></option>
                                                <?php $id = 1;
                                                if (isset($_GET['id'])) {
                                                    $id = $_GET['id'];
                                                }
                                                $result = $db->prepare("SELECT * FROM employee WHERE action='1'");
                                                $result->bindParam(':userid', $ttr);
                                                $result->execute();
                                                for ($i = 0; $row = $result->fetch(); $i++) {
                                                ?>
                                                    <option <?php if ($id == $row['id']) { ?>selected <?php } ?> value="<?php echo $row['id']; ?>"><?php echo ucfirst($row['username']); ?>
                                                    </option>
                                                <?php
                                                }
                                                ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-3">
                                            <label>Year:</label>
                                            <select class="form-control select2" name="year" style="width: 100%;" tabindex="1" autofocus>
                                                <option> <?php echo date('Y') - 1 ?> </option>
                                                <option selected> <?php echo date('Y') ?> </option>
                                            </select>
                                        </div>

                                        <div class="col-lg-3">
                                            <label>Month:</label>
                                            <select class="form-control select2 hidden-search " name="month" style="width: 100%;" tabindex="1">
                                                <?php for ($x = 1; $x <= 12; $x++) {
                                                    $mo = sprintf("%02d", $x); ?>
                                                    <option <?php if (isset($_GET['month'])) {
                                                                if ($mo == $_GET['month']) {
                                                                    echo 'selected';
                                                                }
                                                            } else {
                                                                if ($mo == date('m')) {
                                                                    echo 'selected';
                                                                }
                                                            } ?>> <?php echo $mo; ?> </option>
                                                <?php  } ?>
                                            </select>
                                        </div>

                                        <div class="col-lg-1">
                                            <input type="submit" class="btn btn-info" value="Apply">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="box box-info">
                    <div class="box-header with-border">
                        <h3 class="box-title">Collection List</h3>
                    </div>
                    <div class="box-body">

                        <div class="box-body">
                            <table id="example1" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Amount (Rs.)</th>
                                        <th>Note</th>
                                        <th>Date</th>
                                        <th>#</th>

                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    if (isset($_GET['id'])) {
                                        $id = $_GET['id'];
                                        $d1 = $_GET['year'] . "-" . $_GET['month'] . "-01";
                                        $d2 = $_GET['year'] . "-" . $_GET['month'] . "-31";
                                        $result = $db->prepare("SELECT * FROM salary_advance WHERE emp_id='$id' AND date BETWEEN '$d1' AND '$d2' ORDER BY id DESC LIMIT 50");
                                    } else {
                                        $result = $db->prepare("SELECT * FROM salary_advance  ORDER BY id DESC LIMIT 50");
                                    }


                                    $result->bindParam(':userid', $date);
                                    $result->execute();
                                    for ($i = 0; $row = $result->fetch(); $i++) {  ?>

                                        <tr class="record">
                                            <td><?php echo $row['id'];   ?> </td>
                                            <td><?php echo $row['name'];   ?></td>
                                            <td>Rs.<?php echo $row['amount'];   ?></td>
                                            <td><?php echo $row['note'];   ?> </td>
                                            <td><?php echo $row['date'];   ?> </td>
                                            <td>
                                                <a href="#" id="<?php echo $row['id']; ?>" class="delbutton" title="Click to Delete">
                                                    <button class="btn btn-danger"><i class="icon-trash">x</i></button></a>
                                            </td>
                                        </tr>
                                    <?php }   ?>
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

        <div class="control-sidebar-bg"></div>
    </div>
    <!-- ./wrapper -->

    <!-- jQuery 2.2.3 -->
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
    <!-- date-range-picker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
    <script src="../../plugins/daterangepicker/daterangepicker.js"></script>
    <!-- bootstrap datepicker -->
    <script src="../../plugins/datepicker/bootstrap-datepicker.js"></script>
    <!-- Dark Theme Btn-->
    <script src="https://dev.colorbiz.org/ashen/cdn/main/dist/js/DarkTheme.js"></script>


    <script type="text/javascript">
        $(function() {


            $(".delbutton").click(function() {

                //Save the link in a variable called element
                var element = $(this);

                //Find the id of the link that was clicked
                var del_id = element.attr("id");

                //Built a url to send
                var info = 'id=' + del_id;
                if (confirm("Sure you want to delete this Collection? There is NO undo!")) {

                    $.ajax({
                        type: "GET",
                        url: "hr_salary_advance_dll.php",
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

            //Date range picker
            $('#reservation').daterangepicker();
            //Date range picker with time picker
            //$('#datepicker').datepicker({datepicker: true,  format: 'yyyy/mm/dd '});
            //Date range as a button


            //Date picker
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

        });
    </script>

</body>

</html>